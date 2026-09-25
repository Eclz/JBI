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
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-primary fs-4">
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
                        <div class="p-3 bg-info bg-opacity-10 rounded text-info fs-4">
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
                        <div class="p-3 bg-success bg-opacity-10 rounded text-success fs-4">
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
                        <div class="p-3 bg-warning bg-opacity-10 rounded text-warning fs-4">
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
                    <button class="btn btn-outline-secondary" type="button" id="empModalChangeManagerBtn">Change Manager</button>
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
<script>
    // A simple Vanilla JS interactive Org Chart implementation using positioned divs and lines.
    // D3.js or OrgChart.js would be ideal, but building a custom robust CSS/JS one ensures no external heavy deps.
    document.addEventListener('DOMContentLoaded', function() {
        const dataContainer = document.getElementById('chartDataContainer');
        if (!dataContainer) return;
        
        const rawData = JSON.parse(dataContainer.getAttribute('data-employees'));
        const canvas = document.getElementById('orgChartCanvas');
        
        let zoomLevel = 1;
        let panX = 0;
        let panY = 0;
        let isDragging = false;
        let startX, startY;
        
        // Build hierarchy tree
        const buildTree = (data) => {
            let tree = [];
            let mappedArr = {};
            
            // First pass: create map
            Object.values(data).forEach(emp => {
                mappedArr[emp.id] = { ...emp, children: [] };
            });
            
            // Second pass: wire children
            Object.values(mappedArr).forEach(emp => {
                if (emp.manager_id && mappedArr[emp.manager_id]) {
                    mappedArr[emp.manager_id].children.push(emp);
                } else {
                    tree.push(emp);
                }
            });
            return tree;
        };
        
        const treeData = buildTree(rawData);
        
        // We'll create a single wrapper that gets transformed (panned/zoomed)
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        wrapper.style.transformOrigin = '0 0';
        wrapper.style.width = '10000px'; // large canvas
        wrapper.style.height = '10000px';
        wrapper.style.transition = 'transform 0.1s ease-out';
        canvas.appendChild(wrapper);
        
        // Layout constants
        const NODE_WIDTH = 250;
        const NODE_HEIGHT = 85;
        const H_SPACING = 40;
        const V_SPACING = 80;
        
        let nodes = [];
        let edges = [];
        
        // Recursive layout function
        const layoutTree = (node, depth, offset) => {
            if (!node) return 0;
            
            let childrenWidth = 0;
            let childXOffsets = [];
            
            if (node.children && node.children.length > 0) {
                node.children.forEach(child => {
                    let w = layoutTree(child, depth + 1, offset + childrenWidth);
                    childXOffsets.push(offset + childrenWidth + (w / 2));
                    childrenWidth += w + H_SPACING;
                });
                childrenWidth -= H_SPACING; // remove last spacing
            }
            
            const width = Math.max(NODE_WIDTH, childrenWidth);
            const x = offset + (width / 2);
            const y = depth * (NODE_HEIGHT + V_SPACING) + 50;
            
            nodes.push({ ...node, x, y });
            
            if (node.children && node.children.length > 0) {
                node.children.forEach((child, idx) => {
                    edges.push({
                        startX: x,
                        startY: y + NODE_HEIGHT,
                        endX: childXOffsets[idx],
                        endY: y + NODE_HEIGHT + V_SPACING
                    });
                });
            }
            
            return width;
        };
        
        // Calculate layouts for all roots
        let totalOffset = 500; // Start with some padding
        treeData.forEach(rootNode => {
            let w = layoutTree(rootNode, 0, totalOffset);
            totalOffset += w + H_SPACING * 2;
        });
        
        // Render edges (lines)
        edges.forEach(edge => {
            // vertical line down from parent
            const vLine1 = document.createElement('div');
            vLine1.className = 'org-line';
            vLine1.style.width = '2px';
            vLine1.style.height = (V_SPACING / 2) + 'px';
            vLine1.style.left = edge.startX + 'px';
            vLine1.style.top = edge.startY + 'px';
            wrapper.appendChild(vLine1);
            
            // horizontal line to child x
            const hLine = document.createElement('div');
            hLine.className = 'org-line';
            hLine.style.height = '2px';
            const hStart = Math.min(edge.startX, edge.endX);
            const hWidth = Math.abs(edge.startX - edge.endX) + 2;
            hLine.style.left = hStart + 'px';
            hLine.style.width = hWidth + 'px';
            hLine.style.top = (edge.startY + V_SPACING / 2) + 'px';
            wrapper.appendChild(hLine);
            
            // vertical line down to child
            const vLine2 = document.createElement('div');
            vLine2.className = 'org-line';
            vLine2.style.width = '2px';
            vLine2.style.height = (V_SPACING / 2) + 'px';
            vLine2.style.left = edge.endX + 'px';
            vLine2.style.top = (edge.startY + V_SPACING / 2) + 'px';
            wrapper.appendChild(vLine2);
        });
        
        // Render nodes
        nodes.forEach(node => {
            const el = document.createElement('div');
            el.className = 'org-node';
            el.style.left = (node.x - NODE_WIDTH / 2) + 'px';
            el.style.top = node.y + 'px';
            el.dataset.id = node.id;
            
            let badgeColor = node.status === 'Active' ? 'bg-success' : (node.status === 'On Leave' ? 'bg-warning' : 'bg-secondary');
            
            el.innerHTML = `
                <img src="${node.photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(node.name) + '&background=e0e7ff&color=1e3a8a'}" alt="${node.name}">
                <div class="org-node-info">
                    <h6 class="org-node-name">${node.name}</h6>
                    <div class="org-node-title">${node.title}</div>
                    <div class="org-node-dept">${node.department}</div>
                </div>
            `;
            
            el.addEventListener('click', () => {
                showEmployeeModal(node);
            });
            
            wrapper.appendChild(el);
        });
        
        // Default Pan to first root node
        if (nodes.length > 0) {
            panX = -nodes[0].x + canvas.clientWidth / 2;
            panY = 0;
            updateTransform();
        }

        // Panning Logic
        canvas.addEventListener('mousedown', e => {
            if (e.target.closest('.org-node')) return; // don't pan if clicking node
            isDragging = true;
            startX = e.clientX - panX;
            startY = e.clientY - panY;
            canvas.style.cursor = 'grabbing';
        });
        
        window.addEventListener('mouseup', () => {
            isDragging = false;
            canvas.style.cursor = 'grab';
        });
        
        window.addEventListener('mousemove', e => {
            if (!isDragging) return;
            panX = e.clientX - startX;
            panY = e.clientY - startY;
            updateTransform();
        });
        
        // Zooming Logic
        document.getElementById('zoomIn').addEventListener('click', () => {
            zoomLevel = Math.min(zoomLevel + 0.1, 2);
            updateTransform();
        });
        document.getElementById('zoomOut').addEventListener('click', () => {
            zoomLevel = Math.max(zoomLevel - 0.1, 0.4);
            updateTransform();
        });
        document.getElementById('resetZoom').addEventListener('click', () => {
            zoomLevel = 1;
            if (nodes.length > 0) {
                panX = -nodes[0].x + canvas.clientWidth / 2;
                panY = 0;
            }
            updateTransform();
        });
        
        function updateTransform() {
            wrapper.style.transform = `translate(${panX}px, ${panY}px) scale(${zoomLevel})`;
        }
        
        // Modal Logic
        const empModal = new bootstrap.Modal(document.getElementById('employeeProfileModal'));
        const changeManagerModal = new bootstrap.Modal(document.getElementById('changeManagerModal'));
        
        function showEmployeeModal(emp) {
            document.getElementById('empModalPhoto').src = emp.photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(emp.name) + '&background=e0e7ff&color=1e3a8a';
            document.getElementById('empModalName').textContent = emp.name;
            document.getElementById('empModalTitle').textContent = emp.title;
            document.getElementById('empModalDept').textContent = emp.department;
            
            let statusBadge = document.getElementById('empModalStatus');
            statusBadge.textContent = emp.status;
            statusBadge.className = 'badge ' + (emp.status === 'Active' ? 'bg-success' : (emp.status === 'On Leave' ? 'bg-warning' : 'bg-secondary'));
            
            document.getElementById('empModalId').textContent = emp.employee_id || 'N/A';
            document.getElementById('empModalEmail').textContent = emp.email;
            document.getElementById('empModalPhone').textContent = emp.phone || 'N/A';
            
            let managerName = 'None (Top Level)';
            if (emp.manager_id && rawData[emp.manager_id]) {
                managerName = rawData[emp.manager_id].name;
            }
            document.getElementById('empModalManager').textContent = managerName;
            
            document.getElementById('empModalFullProfileBtn').href = `/human-resources/staff/${emp.id}`;
            
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
            
            empModal.show();
        }
    });
</script>
@endpush
@endsection
