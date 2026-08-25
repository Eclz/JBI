@extends('layouts.app')
@section('title', 'Departments')

@push('styles')
<style>
.dept-stat{border:0;box-shadow:0 .25rem 1rem rgba(15,43,82,.08)}.dept-icon{width:46px;height:46px;display:grid;place-items:center;border-radius:14px;font-size:1.25rem}.dept-row{border-left:4px solid #d9e2f1}.dept-row:hover{border-left-color:var(--jbi-primary);background:#f8fbff}.dept-code{background:#eaf1ff;color:#123b79;border:1px solid #b9cef5;border-radius:7px;padding:.3rem .55rem;font-weight:700}.faculty-label{background:#f0f4fa!important;color:#253858!important;border:1px solid #cfdaea;white-space:normal}.hod-avatar{width:38px;height:38px;flex:0 0 38px;display:grid;place-items:center;border-radius:50%;color:#fff;background:linear-gradient(135deg,#0b2f63,#2673da);font-weight:700}.metric{min-width:52px;padding:.35rem .45rem;border-radius:9px;background:#f5f7fa;text-align:center}.metric strong{display:block;color:#14213d;line-height:1}.metric small{color:#6c757d;font-size:.67rem}
</style>
@endpush

@section('content')
<div class="container-fluid">
 <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h3 mb-1">Departments</h1><p class="text-muted mb-0">Schools, leadership, programmes and activity in one view</p></div><a href="{{ route('admin.departments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Department</a></div>
 @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
 @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
 @if($stats['without_faculty']>0)<div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ $stats['without_faculty'] }} unassigned {{ Str::plural('department',$stats['without_faculty']) }}.</strong> <a href="{{ route('admin.departments.index',['faculty'=>'none']) }}" class="alert-link">Review now</a></div>@endif

 <div class="row g-3 mb-4">
 @foreach([['Total departments',$stats['total'],'bi-diagram-3','primary',[]],['Active',$stats['active'],'bi-check-circle','success',['status'=>'active']],['With a head',$stats['with_head'],'bi-person-badge','info',['head'=>'assigned']],['Needs a head',$stats['total']-$stats['with_head'],'bi-person-exclamation','warning',['head'=>'missing']]] as [$label,$value,$icon,$colour,$params])
  <div class="col-6 col-xl-3"><a href="{{ route('admin.departments.index',$params) }}" class="text-decoration-none"><div class="card dept-stat h-100"><div class="card-body d-flex align-items-center gap-3"><div class="dept-icon bg-{{ $colour }} text-white"><i class="bi {{ $icon }}"></i></div><div><div class="h4 mb-0 text-dark">{{ $value }}</div><small class="text-muted">{{ $label }}</small></div></div></div></a></div>
 @endforeach
 </div>

 <div class="card mb-4"><div class="card-header bg-white d-flex justify-content-between"><h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter departments</h5>@if(request()->hasAny(['search','faculty','status','head','sort_by','order']))<span class="badge bg-primary">Filters active</span>@endif</div><div class="card-body">
  <form method="GET" action="{{ route('admin.departments.index') }}" class="row g-3">
   <div class="col-lg-4"><label class="form-label">Search</label><div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Name, code, faculty or description"></div></div>
   <div class="col-md-6 col-lg-3"><label class="form-label">Faculty / School</label><select class="form-select" name="faculty"><option value="">All faculties</option><option value="none" @selected(request('faculty')==='none')>No faculty assigned</option>@foreach($faculties as $faculty)<option value="{{ $faculty->id }}" @selected(request('faculty')==$faculty->id)>{{ $faculty->name }}</option>@endforeach</select></div>
   <div class="col-md-3 col-lg-2"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">All statuses</option><option value="active" @selected(request('status')==='active')>Active</option><option value="inactive" @selected(request('status')==='inactive')>Inactive</option></select></div>
   <div class="col-md-3 col-lg-3"><label class="form-label">Department head</label><select class="form-select" name="head"><option value="">Assigned or missing</option><option value="assigned" @selected(request('head')==='assigned')>Head assigned</option><option value="missing" @selected(request('head')==='missing')>Head not assigned</option></select></div>
   <div class="col-md-5 col-lg-3"><label class="form-label">Sort by</label><select class="form-select" name="sort_by"><option value="name" @selected(request('sort_by','name')==='name')>Department name</option><option value="code" @selected(request('sort_by')==='code')>Code</option><option value="faculty" @selected(request('sort_by')==='faculty')>Faculty</option><option value="created_at" @selected(request('sort_by')==='created_at')>Date created</option></select></div>
   <div class="col-md-3 col-lg-2"><label class="form-label">Order</label><select class="form-select" name="order"><option value="asc" @selected(request('order')!=='desc')>A–Z / Oldest</option><option value="desc" @selected(request('order')==='desc')>Z–A / Newest</option></select></div>
   <div class="col-md-4 col-lg-3 d-flex align-items-end gap-2"><button class="btn btn-primary flex-grow-1"><i class="bi bi-funnel-fill me-1"></i>Apply filters</button><a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a></div>
  </form>
 </div></div>

 <div class="card"><div class="card-header bg-white d-flex justify-content-between"><h5 class="mb-0">Department directory</h5><span class="badge bg-secondary">{{ $departments->total() }} results</span></div><div class="card-body p-0">
 @forelse($departments as $department)
  <div class="dept-row px-3 py-3 border-bottom"><div class="row g-3 align-items-center">
   <div class="col-lg-4"><div class="d-flex align-items-start gap-3"><span class="dept-code">{{ $department->code }}</span><div><a href="{{ route('admin.departments.show',$department) }}" class="fw-bold text-dark text-decoration-none">{{ $department->name }}</a><div class="mt-1">@if($department->faculty)<span class="badge faculty-label"><i class="bi bi-bank me-1"></i>{{ $department->faculty->name }}</span>@else<span class="badge bg-danger">No faculty assigned</span>@endif</div></div></div></div>
   <div class="col-md-6 col-lg-3">@if($department->headOfDepartment)<div class="d-flex align-items-center gap-2"><div class="hod-avatar">{{ strtoupper(substr($department->headOfDepartment->first_name??$department->headOfDepartment->name,0,1)) }}{{ strtoupper(substr($department->headOfDepartment->last_name??'',0,1)) }}</div><div class="text-truncate"><div class="fw-semibold text-truncate">{{ $department->headOfDepartment->name }}</div><small class="text-muted text-truncate d-block">{{ $department->headOfDepartment->email }}</small></div></div>@else<span class="badge bg-warning text-dark"><i class="bi bi-person-exclamation me-1"></i>Head not assigned</span>@endif</div>
   <div class="col-md-6 col-lg-3"><div class="d-flex gap-2">@foreach([['Programs',$department->programs_count],['Courses',$department->courses_count],['Staff',$department->faculty_members_count],['Students',$department->students_count]] as [$label,$count])<div class="metric"><strong>{{ $count }}</strong><small>{{ $label }}</small></div>@endforeach</div></div>
   <div class="col-lg-2 text-lg-end"><div class="mb-2"><span class="badge {{ $department->is_active?'bg-success':'bg-secondary' }}">{{ $department->is_active?'Active':'Inactive' }}</span></div><div class="btn-group"><a href="{{ route('admin.departments.show',$department) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a><a href="{{ route('admin.departments.edit',$department) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a><button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $department->id }}" data-name="{{ $department->name }}" title="Delete"><i class="bi bi-trash"></i></button></div></div>
  </div></div>
 @empty<div class="text-center py-5"><i class="bi bi-diagram-3 display-3 text-muted"></i><h5 class="mt-3">No departments match these filters</h5><p class="text-muted">Clear the filters or create a department.</p><a href="{{ route('admin.departments.index') }}" class="btn btn-outline-primary">Clear filters</a></div>@endforelse
 </div>@if($departments->hasPages())<div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center"><small class="text-muted">Showing {{ $departments->firstItem() }}–{{ $departments->lastItem() }} of {{ $departments->total() }}</small>{{ $departments->links() }}</div>@endif</div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Delete department?</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">Delete <strong id="departmentName"></strong>? This is only allowed when it has no staff, students or courses.</div><div class="modal-footer"><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><form id="deleteForm" method="POST">@csrf @method('DELETE')<button class="btn btn-danger">Delete Department</button></form></div></div></div></div>
@endsection

@push('scripts')
<script>document.querySelectorAll('.delete-btn').forEach(button=>button.addEventListener('click',()=>{document.getElementById('departmentName').textContent=button.dataset.name;document.getElementById('deleteForm').action=`{{ url('/admin/departments') }}/${button.dataset.id}`;bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).show()}));</script>
@endpush
