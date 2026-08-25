<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\FeeStructure;
use App\Models\Program;
use App\Models\ProgramLevel;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class SyncJbiAcademicCatalog extends Command
{
    protected $signature = 'jbi:sync-academic-catalog
        {--source=https://jbiuniversity.com/tuition-fees/ : Official tuition page}
        {--session=2026/2027 : Academic session}
        {--dry-run : Parse and report without writing}';

    protected $description = 'Import JBI programme levels, schools, programmes, and published fees from the official website';

    private const LEVELS = [
        'certificate' => ['Certificate', 'CERT', '6 months–1 year', 2],
        'advanced diploma' => ['Advanced Diploma', 'ADVDIP', '1 year', 2],
        'diploma' => ['Diploma', 'DIP', '2 years', 4],
        "bachelor's" => ["Bachelor's Degree", 'BACH', '3–4 years', 8],
        "master's" => ["Master's Degree", 'MASTER', '1–2 years', 4],
        'doctorate' => ['Doctorate Degree', 'PHD', '2–4 years', 8],
    ];

    private const FACULTY_CODES = [
        'School of Theology and Ministry' => 'FTM',
        'School of Leadership and Management' => 'FLM',
        'School of Strategy and Innovation' => 'FSI',
        'School of Business' => 'FOB',
        'School of Technology' => 'FOT',
        'School of Engineering' => 'FOE',
        'School of Science' => 'FOS',
        'School of Music and Media' => 'FMM',
    ];

    public function handle(): int
    {
        $source = (string) $this->option('source');
        $session = (string) $this->option('session');
        $catalog = $this->scrape($source);

        $feeRows = collect($catalog)->sum(fn ($faculty) => count(array_filter($faculty['programs'], fn ($program) => $program['local_semester'] !== null)) * 2);
        $this->info(sprintf('Parsed %d faculties, %d programme entries, and %d currency fee rows.', count($catalog), collect($catalog)->sum(fn ($f) => count($f['programs'])), $feeRows));

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        DB::transaction(function () use ($catalog, $source, $session) {
            $academicYear = AcademicYear::updateOrCreate(
                ['year' => 2026],
                ['name' => $session, 'start_date' => '2026-01-01', 'end_date' => '2027-12-31', 'is_active' => true]
            );

            $levels = [];
            foreach (self::LEVELS as [$name, $code, $duration, $semesters]) {
                $levels[$code] = ProgramLevel::updateOrCreate(
                    ['code' => $code],
                    ['name' => $name, 'description' => "$duration | up to $semesters semesters", 'is_active' => true]
                );
            }

            foreach ($catalog as $facultyData) {
                $faculty = Faculty::withTrashed()->updateOrCreate(
                    ['code' => $facultyData['code']],
                    ['name' => $facultyData['name'], 'description' => 'Official JBI University academic school.', 'email' => 'admission@jbiuniversity.com', 'is_active' => true, 'deleted_at' => null]
                );
                $departmentName = str_replace('School of ', 'Department of ', $facultyData['name']);
                $department = Department::withTrashed()->updateOrCreate(
                    ['code' => $facultyData['code'].'-DEPT'],
                    ['faculty_id' => $faculty->id, 'name' => $departmentName, 'description' => 'Primary department for published programmes in '.$facultyData['name'].'.', 'is_active' => true, 'deleted_at' => null]
                );

                foreach ($facultyData['programs'] as $row) {
                    $level = $levels[$row['level_code']];
                    $programCode = $facultyData['code'].'-'.$row['level_code'].'-'.strtoupper(substr(sha1($row['name']), 0, 7));
                    $program = Program::updateOrCreate(
                        ['code' => $programCode],
                        [
                            'department_id' => $department->id,
                            'program_level_id' => $level->id,
                            'name' => $row['name'],
                            'description' => ($row['high_demand'] ? 'High-demand programme. ' : '').'Published in the JBI University '.$session.' tuition schedule.',
                            'is_active' => true,
                        ]
                    );

                    if ($row['local_semester'] !== null) {
                        $this->syncFee($program, $level, $academicYear, $source, $session, 'ZAR', 'local', $row['local_semester'], $row['local_total_min'], $row['local_total_max']);
                        $this->syncFee($program, $level, $academicYear, $source, $session, 'USD', 'international', $row['international_semester'], $row['international_total_min'], $row['international_total_max']);
                    }
                }
            }

            $this->syncAdmissionFees($levels, $academicYear, $session);
        });

        $this->info('JBI academic catalog synchronized successfully. Existing matching records were updated, not duplicated.');
        return self::SUCCESS;
    }

    private function scrape(string $source): array
    {
        $response = Http::timeout(60)->retry(2, 1000)->get($source);
        if (!$response->successful()) {
            throw new RuntimeException("Could not download official tuition page (HTTP {$response->status()}).");
        }

        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML($response->body());
        libxml_clear_errors();
        $xpath = new DOMXPath($document);
        $catalog = [];

        foreach ($xpath->query("//details[contains(@class,'jbi-accordion')]") as $details) {
            $titleNode = $xpath->query(".//span[contains(@class,'jbi-acc-title')]", $details)->item(0);
            if (!$titleNode) {
                continue;
            }
            $name = preg_replace('/^\d+\.\s*/', '', $this->clean($titleNode->textContent));
            if (!isset(self::FACULTY_CODES[$name])) {
                continue;
            }

            $programs = [];
            $levelCode = null;
            foreach ($xpath->query('.//tbody/tr', $details) as $tr) {
                if ($tr instanceof DOMElement && str_contains($tr->getAttribute('class'), 'jbi-level-row')) {
                    $levelCode = $this->levelCode($this->clean($tr->textContent));
                    continue;
                }
                $cells = $xpath->query('./td', $tr);
                if (!$levelCode || $cells->length < 5) {
                    continue;
                }
                $rawName = $this->clean($cells->item(0)->textContent);
                $highDemand = str_contains($rawName, '(High Demand)');
                $programs[] = [
                    'name' => trim(str_replace(['(High Demand)', 'Mgt'], ['', 'Management'], $rawName)),
                    'level_code' => $levelCode,
                    'high_demand' => $highDemand,
                    'local_semester' => $this->money($cells->item(1)->textContent),
                    'international_semester' => $this->money($cells->item(2)->textContent),
                    ...$this->totals($cells->item(3)->textContent, 'local'),
                    ...$this->totals($cells->item(4)->textContent, 'international'),
                ];
            }
            $catalog[] = ['name' => $name, 'code' => self::FACULTY_CODES[$name], 'programs' => $programs];
        }

        if (count($catalog) !== 8) {
            throw new RuntimeException('Expected 8 official faculties but parsed '.count($catalog).'. The source page structure may have changed.');
        }
        return $catalog;
    }

    private function syncFee(Program $program, ProgramLevel $level, AcademicYear $year, string $source, string $session, string $currency, string $region, float $semesterAmount, ?float $totalMin, ?float $totalMax): void
    {
        FeeStructure::updateOrCreate(
            ['program_id' => $program->id, 'academic_year_id' => $year->id, 'currency' => $currency, 'student_region' => $region, 'type' => 'tuition'],
            [
                'program_level_id' => $level->id,
                'name' => $program->name.' — '.$level->name.' Tuition ('.$currency.')',
                'description' => 'Published tuition per semester for '.$session.'.',
                'amount' => $semesterAmount,
                'total_amount' => $totalMin,
                'total_amount_max' => $totalMax,
                'academic_session' => $session,
                'source_url' => $source,
                'frequency' => 'semester',
                'applicable_to' => ['roles' => ['student'], 'program_ids' => [$program->id], 'student_region' => $region],
                'is_mandatory' => true,
                'is_active' => true,
            ]
        );
    }

    private function syncAdmissionFees(array $levels, AcademicYear $year, string $session): void
    {
        $amounts = ['CERT' => 60, 'ADVDIP' => 80, 'DIP' => 60, 'BACH' => 80, 'MASTER' => 100, 'PHD' => 100];
        foreach ($amounts as $code => $amount) {
            foreach (['local', 'international'] as $region) {
                FeeStructure::updateOrCreate(
                    ['program_level_id' => $levels[$code]->id, 'academic_year_id' => $year->id, 'student_region' => $region, 'type' => 'registration', 'program_id' => null],
                    ['name' => $levels[$code]->name.' Admission & Registration Fee (USD)', 'description' => 'Approved one-time, non-refundable admission and registration fee.', 'currency' => 'USD', 'amount' => $amount, 'total_amount' => $amount, 'total_amount_max' => $amount, 'academic_session' => $session, 'source_url' => null, 'frequency' => 'one_time', 'applicable_to' => ['roles' => ['student'], 'program_level_ids' => [$levels[$code]->id], 'student_region' => $region], 'is_mandatory' => true, 'is_active' => true]
                );
            }
        }
    }

    private function levelCode(string $label): ?string
    {
        $label = strtolower($label);
        foreach (self::LEVELS as $needle => [, $code]) {
            if (str_contains($label, $needle)) {
                return $code;
            }
        }
        return null;
    }

    private function money(string $value): ?float
    {
        if (trim($value) === '-') {
            return null;
        }
        preg_match('/[\d][\d\s,]*/', $value, $match);
        return isset($match[0]) ? (float) str_replace([' ', ','], '', $match[0]) : null;
    }

    private function totals(string $value, string $prefix): array
    {
        preg_match_all('/[\d][\d\s,]*/', $value, $matches);
        $numbers = array_values(array_filter(array_map(fn ($number) => (float) str_replace([' ', ','], '', $number), $matches[0] ?? [])));
        return [$prefix.'_total_min' => $numbers[0] ?? null, $prefix.'_total_max' => $numbers[1] ?? ($numbers[0] ?? null)];
    }

    private function clean(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', html_entity_decode($value)));
    }
}
