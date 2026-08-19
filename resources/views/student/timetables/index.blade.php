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

    <!-- Year Filters -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <div class="btn-group btn-group-sm">
            @for($y = 1; $y <= 4; $y++)
                <a href="{{ route('student.timetables.' . $type, ['year' => $y]) }}" class="btn {{ $yearOfStudy == $y ? 'btn-primary text-white fw-bold' : 'btn-outline-secondary' }}">
                    YEAR {{ $y }}
                </a>
            @endfor
        </div>
    </div>

    <!-- Timetable Grid Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-bordered table-hover text-center align-middle mb-0 small">
                    <thead class="bg-light sticky-top" style="z-index: 10;">
                        <tr>
                            <th style="width: 120px;" class="bg-secondary bg-opacity-10 text-uppercase fw-bold py-2">TIME</th>
                            @foreach($days as $day)
                                <th class="text-uppercase fw-bold py-2">{{ $day }}</th>
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
                                    <td class="p-2" style="height: 75px;">
                                        @if($matching)
                                            @php
                                                $lecturerName = $matching->faculty?->name ?? $matching->course?->instructor?->name ?? 'TBA';
                                            @endphp
                                            <div class="p-2 rounded-3 shadow-sm text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); min-height: 70px;">
                                                <div class="fw-bold text-uppercase text-white" style="letter-spacing: 0.5px; font-size: 0.8rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                                    {{ $matching->course?->code }}
                                                </div>
                                                <div class="text-white text-truncate fw-medium" style="max-width: 140px; font-size: 0.75rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);" title="{{ $matching->course?->title }}">
                                                    {{ $matching->course?->title }}
                                                </div>
                                                <div class="text-white-50 text-truncate" style="max-width: 140px; font-size: 0.7rem;" title="Lecturer: {{ $lecturerName }}">
                                                    <i class="bi bi-person-badge me-1"></i>{{ $lecturerName }}
                                                </div>
                                                <div class="badge bg-white text-dark mt-1 px-2 py-1 shadow-sm" style="font-size: 0.68rem; font-weight: 700;">
                                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $matching->room_venue }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted opacity-50">--x--</span>
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
</div>
@endsection
