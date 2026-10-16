<?php
$content = file_get_contents('e:/Projects/JBI/resources/views/faculty/timetables/index.blade.php');

$replacement = <<<EOD
    <!-- View Toggle -->
    <div class="d-flex justify-content-end mb-3">
        <div class="btn-group btn-group-sm shadow-sm rounded-3" role="group">
            <input type="radio" class="btn-check" name="viewToggle" id="btnGrid" autocomplete="off" checked onchange="toggleTimetableView('grid')">
            <label class="btn btn-outline-primary fw-bold px-3 bg-white" for="btnGrid"><i class="bi bi-grid-3x3 me-1"></i>Grid</label>

            <input type="radio" class="btn-check" name="viewToggle" id="btnList" autocomplete="off" onchange="toggleTimetableView('list')">
            <label class="btn btn-outline-primary fw-bold px-3 bg-white" for="btnList"><i class="bi bi-list-task me-1"></i>Planner</label>
        </div>
    </div>

    @if(count(\$slots) > 0)
        <!-- Timetable Grid Table -->
        <div id="grid-view" class="card border-0 shadow-sm rounded-3 overflow-hidden animate__animated animate__fadeIn">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-bordered table-hover text-center align-middle mb-0 small">
                        <thead class="bg-light sticky-top" style="z-index: 10;">
                            <tr>
                                <th style="width: 120px;" class="bg-secondary bg-opacity-10 text-uppercase fw-bold py-2 border-bottom-0">TIME</th>
                                @foreach(\$days as \$day)
                                    <th class="text-uppercase fw-bold py-2 border-bottom-0">{{ \$day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\$timeSlots as \$slotText)
                                @php
                                    list(\$slotStart, \$slotEnd) = explode(' - ', \$slotText);
                                @endphp
                                <tr>
                                    <td class="fw-bold bg-light text-muted">{{ \$slotText }}</td>
                                    @foreach(\$days as \$day)
                                        @php
                                            \$matching = \$slots->filter(function(\$item) use (\$day, \$slotStart) {
                                                return strcasecmp(\$item->day_of_week, \$day) === 0 && 
                                                       substr(\$item->start_time, 0, 5) === substr(\$slotStart, 0, 5);
                                            })->first();
                                        @endphp
                                        <td class="p-2" style="height: 80px;">
                                            @if(\$matching)
                                                <div class="p-2 rounded-3 shadow-sm text-white position-relative" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); min-height: 75px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                                                    <div class="fw-bold text-uppercase text-white mb-1" style="letter-spacing: 0.5px; font-size: 0.85rem;">
                                                        {{ \$matching->course?->code }}
                                                    </div>
                                                    <div class="text-white text-truncate fw-medium mx-auto" style="max-width: 130px; font-size: 0.75rem; opacity: 0.9;" title="{{ \$matching->course?->title }}">
                                                        {{ \$matching->course?->title }}
                                                    </div>
                                                    <div class="badge bg-white text-primary mt-2 px-2 py-1 shadow-sm rounded-pill" style="font-size: 0.7rem;">
                                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ \$matching->room_venue }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted opacity-25" style="font-size: 0.7rem;">--</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Planner / List View -->
        <div id="list-view" class="d-none animate__animated animate__fadeIn">
            <div class="row g-4">
EOD;

$content = str_replace('    <!-- Timetable Slots Grid -->', $replacement, $content);

// Remove the old checking condition and row start because they are in the EOD block
$content = str_replace('    @if(count($slots) > 0)
        <div class="row g-4">', '', $content);

$scripts = <<<EOD
@endsection

@push('scripts')
<script>
    function toggleTimetableView(view) {
        const gridView = document.getElementById('grid-view');
        const listView = document.getElementById('list-view');
        const btnGrid = document.getElementById('btnGrid').nextElementSibling;
        const btnList = document.getElementById('btnList').nextElementSibling;

        if (view === 'grid') {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
            
            btnGrid.classList.remove('bg-white');
            btnGrid.classList.add('btn-primary', 'text-white');
            btnList.classList.add('bg-white');
            btnList.classList.remove('btn-primary', 'text-white');
        } else {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            
            btnList.classList.remove('bg-white');
            btnList.classList.add('btn-primary', 'text-white');
            btnGrid.classList.add('bg-white');
            btnGrid.classList.remove('btn-primary', 'text-white');
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        if(document.getElementById('btnGrid')) {
            document.getElementById('btnGrid').nextElementSibling.classList.remove('bg-white');
            document.getElementById('btnGrid').nextElementSibling.classList.add('btn-primary', 'text-white');
        }
    });
</script>
<style>
    .btn-check:checked + .btn-outline-primary {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
</style>
@endpush
EOD;

$content = str_replace('@endsection', $scripts, $content);

$content = str_replace('            @endforeach
        </div>
    @else', '            @endforeach
            </div>
        </div>
    @else', $content);

file_put_contents('e:/Projects/JBI/resources/views/faculty/timetables/index.blade.php', $content);
echo "Faculty Timetable view updated.\n";
