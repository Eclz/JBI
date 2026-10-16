<?php

$migrationsPath = __DIR__ . '/database/migrations/';
$files = scandir($migrationsPath);

$updates = [
    'create_quality_reviews_table.php' => <<<EOD
            \$table->id();
            \$table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('cascade');
            \$table->foreignId('dean_id')->nullable()->constrained('users')->onDelete('cascade');
            \$table->string('title');
            \$table->text('review_notes');
            \$table->json('metrics')->nullable();
            \$table->enum('status', ['Draft', 'Pending Registrar Approval', 'Approved', 'Rejected'])->default('Draft');
            \$table->text('registrar_feedback')->nullable();
            \$table->timestamps();
EOD,
    'create_faculty_evaluations_table.php' => <<<EOD
            \$table->id();
            \$table->foreignId('faculty_id')->nullable()->constrained('users')->onDelete('cascade');
            \$table->foreignId('evaluator_id')->nullable()->constrained('users')->onDelete('cascade'); // The Dean
            \$table->string('academic_year');
            \$table->integer('performance_score');
            \$table->text('comments');
            \$table->enum('status', ['Draft', 'Pending Approval', 'Approved', 'Rejected'])->default('Draft');
            \$table->timestamps();
EOD,
    'create_budget_requests_table.php' => <<<EOD
            \$table->id();
            \$table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            \$table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('cascade'); // The Dean
            \$table->string('title');
            \$table->text('description');
            \$table->decimal('amount', 15, 2);
            \$table->enum('status', ['Draft', 'Pending Finance Approval', 'Approved', 'Rejected'])->default('Draft');
            \$table->text('finance_notes')->nullable();
            \$table->timestamps();
EOD,
    'create_student_issues_table.php' => <<<EOD
            \$table->id();
            \$table->foreignId('student_id')->nullable()->constrained('users')->onDelete('cascade');
            \$table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('cascade');
            \$table->string('issue_type'); // Academic, Disciplinary, Welfare
            \$table->text('description');
            \$table->text('action_taken')->nullable();
            \$table->enum('status', ['Open', 'In Progress', 'Resolved', 'Escalated'])->default('Open');
            \$table->timestamps();
EOD,
    'create_external_partnerships_table.php' => <<<EOD
            \$table->id();
            \$table->foreignId('managed_by')->nullable()->constrained('users')->onDelete('set null'); // The Dean
            \$table->string('organization_name');
            \$table->string('partnership_type'); // Industry, Donor, Alumni
            \$table->text('objectives');
            \$table->decimal('funding_amount', 15, 2)->default(0);
            \$table->enum('status', ['Active', 'Pending', 'Concluded'])->default('Pending');
            \$table->timestamps();
EOD
];

foreach ($files as $file) {
    foreach ($updates as $key => $schema) {
        if (str_ends_with($file, $key)) {
            $content = file_get_contents($migrationsPath . $file);
            $content = preg_replace('/\$table->id\(\);\s*\$table->timestamps\(\);/', $schema, $content);
            file_put_contents($migrationsPath . $file, $content);
            echo "Updated migration: $file\n";
        }
    }
}
