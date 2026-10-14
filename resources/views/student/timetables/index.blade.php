@extends('layouts.app')

@section('title', ucfirst($type) . ' Timetable')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark text-uppercase mb-0">
            {{ strtoupper($type) }} TIMETABLE FOR {{ $studentProfile?->academic_year ?? '2026/2027' }} SEMESTER {{ $semesterNum == 1 ? 'I' : 'II' }}
        </h5>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-download me-1"></i>DOWNLOAD
        </button>
    </div>

    <!-- Navigation Tabs -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom-0 pb-0">
            <ul class="nav nav-tabs border-bottom-0">
                <li class="nav-item">
                    <a class="nav-link {{ $type === 'teaching' ? 'active fw-bold text-primary border-bottom border-primary border-3' : 'text-muted' }}" href="{{ route('student.timetables.teaching') }}">
                        <i class="bi bi-journal-text me-1"></i>TEACHING TIMETABLE
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $type === 'tests' ? 'active fw-bold text-primary border-bottom border-primary border-3' : 'text-muted' }}" href="{{ route('student.timetables.tests') }}">
                        <i class="bi bi-patch-check me-1"></i>TESTS TIMETABLE
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $type === 'exams' ? 'active fw-bold text-primary border-bottom border-primary border-3' : 'text-muted' }}" href="{{ route('student.timetables.exams') }}">
                        <i class="bi bi-pencil-square me-1"></i>EXAMS TIMETABLE
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Year Filters & View Toggle -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="btn-group btn-group-sm shadow-sm rounded-3">
            @for($y = 1; $y <= 4; $y++)
                <a href="{{ route('student.timetables.' . $type, ['year' => $y]) }}" class="btn {{ $yearOfStudy == $y ? 'btn-primary text-white fw-bold' : 'btn-outline-secondary bg-white' }}">
                    YEAR {{ $y }}
                </a>
            @endfor
        </div>
        <div class="btn-group btn-group-sm shadow-sm rounded-3" role="group">
            <input type="radio" class="btn-check" name="viewToggle" id="btnGrid" autocomplete="off" checked onchange="toggleTimetableView('grid')">
            <label class="btn btn-outline-primary fw-bold px-3 bg-white" for="btnGrid"><i class="bi bi-grid-3x3 me-1"></i>Grid</label>

            <input type="radio" class="btn-check" name="viewToggle" id="btnList" autocomplete="off" onchange="toggleTimetableView('list')">
            <label class="btn btn-outline-primary fw-bold px-3 bg-white" for="btnList"><i class="bi bi-list-task me-1"></i>Planner</label>
        </div>
    </div>

    <!-- Timetable Grid Table -->
    <div id="grid-view" class="card border-0 shadow-sm rounded-3 overflow-hidden animate__animated animate__fadeIn">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-bordered table-hover text-center align-middle mb-0 small">
                    <thead class="bg-light sticky-top" style="z-index: 10;">
                        <tr>
                            <th style="width: 120px;" class="bg-secondary bg-opacity-10 text-uppercase fw-bold py-2 border-bottom-0">TIME</th>
                            @foreach($days as $day)
                                <th class="text-uppercase fw-bold py-2 border-bottom-0">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeSlots as $slotText)
                            @php
                                list($slotStart, $slotEnd) = explode(' - ', $slotText);
                            @endphp
                            <tr>
                                <td class="fw-bold bg-light text-muted">{{ $slotText }}</td>
                                @foreach($days as $day)
                                    @php
                                        $matching = $slots->filter(function($item) use ($day, $slotStart) {
                                            return strcasecmp($item->day_of_week, $day) === 0 && 
                                                   substr($item->start_time, 0, 5) === substr($slotStart, 0, 5);
                                        })->first();
                                    @endphp
                                    <td class="p-2" style="height: 80px;">
                                        @if($matching)
                                            @php
                                                $lecturerName = $matching->faculty?->name ?? $matching->course?->instructor?->name ?? 'TBA';
                                            @endphp
                                            <div class="p-2 rounded-3 shadow-sm text-white position-relative" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); min-height: 75px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                                                <div class="fw-bold text-uppercase text-white mb-1" style="letter-spacing: 0.5px; font-size: 0.85rem; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                                                    {{ $matching->course?->code }}
                                                </div>
                                                <div class="text-white text-truncate fw-medium mx-auto" style="max-width: 130px; font-size: 0.75rem; opacity: 0.9;" title="{{ $matching->course?->title }}">
                                                    {{ $matching->course?->title }}
                                                </div>
                                                <div class="badge bg-white text-primary mt-2 px-2 py-1 shadow-sm rounded-pill" style="font-size: 0.7rem; font-weight: 700;">
                                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $matching->room_venue }}
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
            @php $hasClasses = false; @endphp
            @foreach($days as $day)
                @php
                    $daySlots = $slots->filter(function($item) use ($day) {
                        return strcasecmp($item->day_of_week, $day) === 0;
                    })->sortBy('start_time');
                @endphp
                
                @if($daySlots->count() > 0)
                    @php $hasClasses = true; @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                            <div class="card-header border-0 bg-primary bg-gradient text-white py-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-day me-2"></i>{{ $day }}</h5>
                                <span class="badge bg-white text-primary rounded-pill">{{ $daySlots->count() }} Classes</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($daySlots as $item)
                                        @php
                                            $lecturerName = $item->faculty?->name ?? $item->course?->instructor?->name ?? 'TBA';
                                        @endphp
                                        <div class="list-group-item p-3 border-bottom position-relative" style="transition: background 0.2s;" onmouseover="this.classList.add('bg-light')" onmouseout="this.classList.remove('bg-light')">
                                            <div class="position-absolute top-0 start-0 h-100 bg-primary" style="width: 4px;"></div>
                                            <div class="d-flex justify-content-between align-items-start mb-2 ms-2">
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-primary">{{ $item->course?->code }}</h6>
                                                    <div class="fw-semibold text-dark fs-6">{{ $item->course?->title }}</div>
                                                </div>
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle py-2 px-2 ms-2" style="font-size: 0.75rem;">
                                                    <i class="bi bi-clock me-1"></i>{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-3 ms-2 small text-muted">
                                                <div class="fw-medium"><i class="bi bi-person-badge me-1 text-primary"></i>{{ $lecturerName }}</div>
                                                <div class="badge bg-light text-dark border px-2 py-1 shadow-sm">
                                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $item->room_venue }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            @if(!$hasClasses)
                <div class="col-12 text-center py-5">
                    <div class="text-muted mb-3"><i class="bi bi-calendar-x fs-1 opacity-50"></i></div>
                    <h5 class="text-muted fw-bold">No Schedule Found</h5>
                    <p class="text-muted mb-0">There are no classes scheduled for this semester.</p>
                </div>
            @endif
        </div>
    </div>
</div>
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
    
    // Initialize styles
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('btnGrid').nextElementSibling.classList.remove('bg-white');
        document.getElementById('btnGrid').nextElementSibling.classList.add('btn-primary', 'text-white');
    });
</script>
<style>
    /* Styling for the radio button labels */
    .btn-check:checked + .btn-outline-primary {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
</style>
@endpush
