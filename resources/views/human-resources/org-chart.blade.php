@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Organizational Chart</h2>
            <p class="text-muted mb-0">View and manage the organization's reporting structure.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('human-resources.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to HR Dashboard
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Staff</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_staff'] }}</h3>
                        </div>
                        <div class="p-3 bg-primary rounded text-white fs-4">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Departments</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['departments'] }}</h3>
                        </div>
                        <div class="p-3 bg-info rounded text-white fs-4">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Managers</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['managers'] }}</h3>
                        </div>
                        <div class="p-3 bg-success rounded text-white fs-4">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Vacancies</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['vacancies'] }}</h3>
                        </div>
                        <div class="p-3 bg-warning rounded text-white fs-4">
                            <i class="bi bi-person-x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="orgSearch" class="form-control border-start-0" placeholder="Search employees, departments...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterDepartment">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterStatus">
                        <option value="">All Statuses</option>
                        <option value="Active">Active</option>
                        <option value="On Leave">On Leave</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-outline-secondary me-2" id="zoomOut" title="Zoom Out"><i class="bi bi-zoom-out"></i></button>
                    <button class="btn btn-outline-secondary me-2" id="resetZoom" title="Reset"><i class="bi bi-arrows-move"></i></button>
                    <button class="btn btn-outline-secondary" id="zoomIn" title="Zoom In"><i class="bi bi-zoom-in"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Org Chart Workspace -->
    <div class="card border-0 shadow-sm" style="min-height: 600px;">
        <div class="card-body p-0 position-relative overflow-hidden" id="orgChartContainer" style="background-color: #f8f9fa;">
            @if(empty($allEmployees))
                <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5">
                    <div class="text-muted mb-3"><i class="bi bi-diagram-2" style="font-size: 4rem;"></i></div>
                    <h5 class="text-muted">No organizational records yet</h5>
                    <p class="text-muted text-center mb-4">Your organizational chart will appear here once employees<br>and reporting relationships have been added.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('human-resources.staff.index') }}" class="btn btn-primary">Add Employee</a>
                    </div>
                </div>
            @else
                <!-- The actual chart element -->
                <div id="chartDataContainer" class="d-none" data-employees="{{ json_encode($allEmployees) }}"></div>
                <div id="orgChartCanvas" style="width: 100%; height: 100%; min-height: 600px; cursor: grab;"></div>
            @endif
        </div>
    </div>
</div>

<!-- Employee Profile Side Panel / Modal -->
<div class="modal fade" id="employeeProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <img src="" id="empModalPhoto" class="rounded-circle mb-3 border border-3 border-light shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                <h4 class="mb-1 fw-bold" id="empModalName">Name</h4>
                <p class="text-primary fw-semibold mb-2" id="empModalTitle">Title</p>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <span class="badge bg-light text-dark border" id="empModalDept">Department</span>
                    <span class="badge bg-success" id="empModalStatus">Active</span>
                </div>

                <div class="row text-start g-3 mb-4">
                    <div class="col-6">
                        <label class="text-muted small fw-bold">Employee ID</label>
                        <div id="empModalId">EMP-000</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small fw-bold">Reports To</label>
                        <div id="empModalManager">None</div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small fw-bold">Contact</label>
                        <div><i class="bi bi-envelope me-2 text-muted"></i><span id="empModalEmail">email@example.com</span></div>
                        <div><i class="bi bi-telephone me-2 text-muted"></i><span id="empModalPhone">N/A</span></div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="#" id="empModalFullProfileBtn" class="btn btn-primary">View Full Employee Profile</a>
                    <div class="btn-group w-100">
                        <button class="btn btn-outline-secondary" type="button" id="empModalChangeManagerBtn">Change Manager</button>
                        <button class="btn btn-outline-secondary" type="button" id="empModalUpdateRoleBtn">Update Role</button>
                    </div>
                    <button class="btn btn-outline-success" type="button" id="empModalAddReportBtn"><i class="bi bi-plus-circle me-1"></i> Add Direct Report / Branch</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Manager Modal -->
<div class="modal fade" id="changeManagerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="changeManagerForm" action="{{ route('human-resources.org-chart.update-manager') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Reporting Manager</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="employee_id" id="editManagerEmployeeId">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <input type="text" class="form-control bg-light" id="editManagerEmployeeName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Manager</label>
                        <select name="manager_id" class="form-select" required id="managerSelectDropdown">
                            <option value="">-- No Manager (Top Level) --</option>
                            @foreach($allEmployees ?? [] as $emp)
                                <option value="{{ $emp['id'] }}">{{ $emp['name'] }} ({{ $emp['title'] }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Select the person this employee reports to.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Role Modal -->
<div class="modal fade" id="updateRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="updateRoleForm" action="{{ route('human-resources.org-chart.update-role') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Promote / Demote / Update Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="employee_id" id="updateRoleEmployeeId">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <input type="text" class="form-control bg-light" id="updateRoleEmployeeName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Job Title</label>
                        <input type="text" name="job_title" class="form-control" id="updateRoleJobTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control" id="updateRoleDepartment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Direct Report Modal -->
<div class="modal fade" id="addReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addReportForm" action="{{ route('human-resources.org-chart.add-report') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Direct Report / Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="manager_id" id="addReportManagerId">
                    <div class="mb-3">
                        <label class="form-label">Reporting To</label>
                        <input type="text" class="form-control bg-light" id="addReportManagerName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Staff Member</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">-- Select Staff --</option>
                            @foreach($unassignedStaff ?? [] as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->full_name }} ({{ $staff->email }})</option>
                            @endforeach
                            @foreach($allEmployees ?? [] as $emp)
                                <option value="{{ $emp['id'] }}">{{ $emp['name'] }} ({{ $emp['title'] }})</option>
                            @endforeach
                        </select>
                        <div class="form-text d-flex justify-content-between">
                            <span>Choose an existing staff member to place under this manager.</span>
                            <a href="{{ route('human-resources.staff.create') }}" class="text-decoration-none"><i class="bi bi-person-plus"></i> Create New Staff</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Job Title</label>
                        <input type="text" name="job_title" class="form-control" required placeholder="e.g. Lecturer">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control" placeholder="e.g. Computing">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Org Chart Node Styles */
    .org-node {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
        min-width: 220px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: absolute; /* absolute for d3/canvas placement */
    }
    .org-node:hover {
        border-color: #0d6efd;
        box-shadow: 0 6px 12px rgba(13,110,253,0.15);
        transform: translateY(-2px);
    }
    .org-node img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
    }
    .org-node-info {
        flex: 1;
        overflow: hidden;
    }
    .org-node-name {
        font-weight: 600;
        font-size: 0.95rem;
        margin: 0 0 2px 0;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        color: #212529;
    }
    .org-node-title {
        font-size: 0.8rem;
        color: #0d6efd;
        margin: 0 0 4px 0;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }
    .org-node-dept {
        font-size: 0.75rem;
        color: #6c757d;
        margin: 0;
    }
    
    /* Connectors */
    .org-line {
        position: absolute;
        background-color: #cbd5e1;
        z-index: 0;
    }
</style>
@endpush

@push('scripts')
<script src="https://d3js.org/d3.v7.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/d3-org-chart@3.1.0"></script>
<script src="https://cdn.jsdelivr.net/npm/d3-flextree@2.1.2/build/d3-flextree.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataContainer = document.getElementById('chartDataContainer');
        if (!dataContainer) return;
        
        const rawData = JSON.parse(dataContainer.getAttribute('data-employees'));
        const canvas = document.getElementById('orgChartCanvas');
        
        let data = Object.values(rawData).map(emp => {
            return {
                id: emp.id.toString(),
                parentId: emp.manager_id ? emp.manager_id.toString() : 'root',
                name: emp.name,
                title: emp.title,
                department: emp.department,
                photo: emp.photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(emp.name) + '&background=e0e7ff&color=1e3a8a',
                status: emp.status,
                employee_id: emp.employee_id,
                email: emp.email,
                phone: emp.phone,
                _raw: emp // keep raw reference for modals
            };
        });

        // Ensure all parentIds exist in the dataset, fallback to 'root' if missing
        const allIds = new Set(data.map(d => d.id));
        allIds.add('root');
        allIds.add('');
        
        data.forEach(d => {
            if (!allIds.has(d.parentId)) {
                console.warn(`Parent ID ${d.parentId} not found for node ${d.id}. Defaulting to root.`);
                d.parentId = 'root';
            }
        });

        // Break circular dependencies to prevent D3 stratify crash
        let parentMap = {};
        data.forEach(d => parentMap[d.id] = d.parentId);

        data.forEach(d => {
            let current = d.id;
            let visited = new Set([current]);
            let hasCycle = false;
            
            while (parentMap[current] && parentMap[current] !== 'root' && parentMap[current] !== '') {
                current = parentMap[current];
                if (visited.has(current)) {
                    hasCycle = true;
                    break;
                }
                visited.add(current);
            }
            
            if (hasCycle) {
                console.warn(`Cycle detected for node ${d.id}. Breaking cycle by moving to root.`);
                d.parentId = 'root';
                parentMap[d.id] = 'root';
            }
        });

        // Add a virtual root if there are multiple root nodes (or if we fallback to root)
        const rootNodesCount = data.filter(d => d.parentId === 'root').length;
        if (rootNodesCount > 0) {
            data.push({
                id: 'root',
                parentId: '',
                name: 'University Board',
                title: 'Governing Body',
                department: 'Administration',
                photo: 'https://ui-avatars.com/api/?name=UB&background=0d6efd&color=fff',
                status: 'Active',
                _isVirtual: true,
                _raw: {
                    id: 'root',
                    name: 'University Board',
                    title: 'Governing Body',
                    department: 'Administration',
                    status: 'Active',
                    manager_id: null
                }
            });
        }
        
        let chart = new d3.OrgChart()
            .container(canvas)
            .data(data)
            .nodeWidth(d => 250)
            .nodeHeight(d => 120)
            .childrenMargin(d => 50)
            .compactMarginBetween(d => 25)
            .compactMarginPair(d => 50)
            .nodeContent(function(d, i, arr, state) {
                const colors = {
                    'Active': '#198754',
                    'On Leave': '#ffc107',
                    'Inactive': '#6c757d'
                };
                const statusColor = colors[d.data.status] || '#198754';
                return `
                <div style="font-family: 'Inter', sans-serif; background-color: white; border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); padding: 15px; width: ${d.width}px; height: ${d.height}px; position: relative;">
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background-color: ${statusColor}; border-top-left-radius: 8px; border-top-right-radius: 8px;"></div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-top: 5px;">
                        <img src="${d.data.photo}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #f8f9fa;">
                        <div style="overflow: hidden;">
                            <div style="font-weight: bold; font-size: 15px; color: #212529; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">${d.data.name}</div>
                            <div style="font-size: 12px; color: #0d6efd; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; margin-bottom: 3px;">${d.data.title}</div>
                            <div style="font-size: 11px; color: #6c757d;">${d.data.department || ''}</div>
                        </div>
                    </div>
                </div>
                `;
            })
            .onNodeClick(d => {
                showEmployeeModal(d.data._raw);
            })
            .render();

        // Custom Zoom Controls
        document.getElementById('zoomIn').addEventListener('click', () => chart.zoomIn());
        document.getElementById('zoomOut').addEventListener('click', () => chart.zoomOut());
        document.getElementById('resetZoom').addEventListener('click', () => {
            chart.fit();
        });

        // Search functionality
        document.getElementById('orgSearch').addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            if(!val) {
                chart.clearHighlighting();
            } else {
                chart.clearHighlighting();
                const matched = data.filter(d => 
                    d.name.toLowerCase().includes(val) || 
                    d.title.toLowerCase().includes(val) || 
                    (d.department && d.department.toLowerCase().includes(val))
                );
                matched.forEach(m => chart.setHighlighted(m.id));
                chart.render();
            }
        });

        // Modals logic
        const empModal = new bootstrap.Modal(document.getElementById('employeeProfileModal'));
        const changeManagerModal = new bootstrap.Modal(document.getElementById('changeManagerModal'));
        
        function showEmployeeModal(emp) {
            document.getElementById('empModalPhoto').src = emp.photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(emp.name) + '&background=e0e7ff&color=1e3a8a';
            document.getElementById('empModalName').textContent = emp.name;
            document.getElementById('empModalTitle').textContent = emp.title;
            document.getElementById('empModalDept').textContent = emp.department || 'General';
            
            let statusBadge = document.getElementById('empModalStatus');
            statusBadge.textContent = emp.status || 'Active';
            statusBadge.className = 'badge ' + ((emp.status === 'Active' || !emp.status) ? 'bg-success' : (emp.status === 'On Leave' ? 'bg-warning' : 'bg-secondary'));
            
            document.getElementById('empModalId').textContent = emp.employee_id || 'N/A';
            document.getElementById('empModalEmail').textContent = emp.email || 'N/A';
            document.getElementById('empModalPhone').textContent = emp.phone || 'N/A';
            
            let managerName = 'University Board (Top Level)';
            if (emp.manager_id && rawData[emp.manager_id]) {
                managerName = rawData[emp.manager_id].name;
            }
            document.getElementById('empModalManager').textContent = managerName;
            
            if (emp.id === 'root') {
                document.getElementById('empModalFullProfileBtn').classList.add('d-none');
                document.getElementById('empModalChangeManagerBtn').classList.add('d-none');
                document.getElementById('empModalUpdateRoleBtn').classList.add('d-none');
            } else {
                document.getElementById('empModalFullProfileBtn').classList.remove('d-none');
                document.getElementById('empModalChangeManagerBtn').classList.remove('d-none');
                document.getElementById('empModalUpdateRoleBtn').classList.remove('d-none');
                document.getElementById('empModalFullProfileBtn').href = `/human-resources/staff/${emp.id}`;
            }
            
            document.getElementById('empModalChangeManagerBtn').onclick = () => {
                empModal.hide();
                document.getElementById('editManagerEmployeeId').value = emp.id;
                document.getElementById('editManagerEmployeeName').value = emp.name;
                document.getElementById('managerSelectDropdown').value = emp.manager_id || '';
                
                // disable selecting self as manager
                Array.from(document.getElementById('managerSelectDropdown').options).forEach(opt => {
                    if (opt.value == emp.id) opt.disabled = true;
                    else opt.disabled = false;
                });
                
                changeManagerModal.show();
            };
            
            document.getElementById('empModalUpdateRoleBtn').onclick = () => {
                empModal.hide();
                document.getElementById('updateRoleEmployeeId').value = emp.id;
                document.getElementById('updateRoleEmployeeName').value = emp.name;
                document.getElementById('updateRoleJobTitle').value = emp.title;
                document.getElementById('updateRoleDepartment').value = emp.department;
                new bootstrap.Modal(document.getElementById('updateRoleModal')).show();
            };

            document.getElementById('empModalAddReportBtn').onclick = () => {
                empModal.hide();
                document.getElementById('addReportManagerId').value = emp.id === 'root' ? '' : emp.id;
                document.getElementById('addReportManagerName').value = emp.name;
                new bootstrap.Modal(document.getElementById('addReportModal')).show();
            };
            
            empModal.show();
        }
    });
</script>
@endpush
@endsection
