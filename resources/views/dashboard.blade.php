@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h2>Welcome, {{ Auth::user()->name }}!</h2>
                    <p>This is the main dashboard of the JBI University Management System.</p>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">Quick Links</div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @if(Auth::user()->role == 'admin')
                                            <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">
                                                <i class="fa fa-users mr-2"></i> User Management
                                            </a>
                                            <a href="{{ route('admin.courses.index') }}" class="list-group-item list-group-item-action">
                                                <i class="fa fa-book mr-2"></i> Course Management
                                            </a>
                                            <a href="{{ route('admin.reports') }}" class="list-group-item list-group-item-action">
                                                <i class="fa fa-chart-bar mr-2"></i> Reports
                                            </a>
                                        @elseif(Auth::user()->role == 'faculty')
                                            <a href="{{ route('faculty.courses.index') }}" class="list-group-item list-group-item-action">
                                                <i class="fa fa-book mr-2"></i> My Courses
                                            </a>
                                            <a href="{{ route('faculty.attendance.index') }}" class="list-group-item list-group-item-action">
                                                <i class="fa fa-clipboard-check mr-2"></i> Attendance
                                            </a>
                                            <a href="{{ route('faculty.grading.index') }}" class="list-group-item list-group-item-action">
                                                <i class="fa fa-star mr-2"></i> Grading
                                            </a>
                                        @elseif(Auth::user()->role == 'student')
                                            <a href="{{ route('student.courses.index') }}" class="list-group-item list-group-item-action">
                                                <i class="fa fa-book mr-2"></i> My Courses
                                            </a>
                                            <a href="{{ route('student.assignments.index') }}" class="list-group-item list-group-item-action">
                                                <i class="fa fa-tasks mr-2"></i> Assignments
                                            </a>
                                            <a href="{{ route('student.grades.index') }}" class="list-group-item list-group-item-action">
                                                <i class="fa fa-star mr-2"></i> Grades
                                            </a>
                                        @endif
                                        <a href="{{ route('profile') }}" class="list-group-item list-group-item-action">
                                            <i class="fa fa-user mr-2"></i> My Profile
                                        </a>
                                        <a href="{{ route('settings') }}" class="list-group-item list-group-item-action">
                                            <i class="fa fa-cog mr-2"></i> Settings
                                        </a>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">Recent Announcements</div>
                                <div class="card-body">
                                    @if(isset($announcements) && $announcements->count() > 0)
                                        <div class="list-group">
                                            @foreach($announcements as $announcement)
                                                <div class="list-group-item">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h5 class="mb-1">{{ $announcement->title }}</h5>
                                                        <small>{{ $announcement->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <p class="mb-1">{{ Str::limit($announcement->content, 100) }}</p>
                                                    <small>Posted by: {{ $announcement->user->name }}</small>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-3">
                                            <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-primary">View All Announcements</a>
                                        </div>
                                    @else
                                        <p>No recent announcements.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">System Information</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Current Academic Year:</strong>
                                                @if(isset($currentAcademicYear))
                                                    {{ $currentAcademicYear->name }}
                                                @else
                                                    Not set
                                                @endif
                                            </p>
                                            <p><strong>Current Semester:</strong>
                                                @if(isset($currentSemester))
                                                    {{ $currentSemester->name }}
                                                @else
                                                    Not set
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>User Role:</strong> {{ ucfirst(Auth::user()->role) }}</p>
                                            <p><strong>Last Login:</strong> {{ Auth::user()->last_login ? Auth::user()->last_login->diffForHumans() : 'First login' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
