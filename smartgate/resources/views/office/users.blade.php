@extends('layouts.app')

@section('title', 'Registered Users')
@section('subtitle', 'SmartGate – Registered vehicle owners and applicants.')

@section('content')
<div class="table-container">
    <div class="section-header">
        <div style="display: flex; gap: 1rem; align-items: center; flex: 1;">
            <div style="position: relative; flex: 1; max-width: 400px;">
                <i class="ph ph-magnifying-glass" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" id="searchInput" placeholder="Search by name..." 
                       style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid #e2e8f0; border-radius: 8px; outline: none;">
            </div>
            
            <select id="roleFilter" style="padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; background: white;">
                <option value="all">All Roles</option>
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
                <option value="staff">Non-Teaching</option>
            </select>
        </div>
        
        <a href="{{ route('office.registration') }}" class="btn btn-primary">
            <i class="ph ph-plus"></i> New User
        </a>
    </div>

    <div class="table-wrapper">
        <table class="users-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>University ID</th>
                    <th>College / Dept</th>
                    <th>Vehicle</th>
                    <th>Plate No.</th>
                    <th>RFID Tag</th>
                    <th>Validity</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="usersTable">
                @forelse($registrations as $reg)
                @php
                    $initials = collect(explode(' ', $reg->full_name))->filter()->map(fn($p) => strtoupper(substr($p,0,1)))->take(2)->implode('');
                    $roleLabel = $reg->role === 'staff' ? 'Non-Teaching' : ucfirst($reg->role);
                    
                    $badgeClass = 'badge-normal';
                    $badgeText = ucfirst($reg->status);
                    
                    if ($reg->status === 'rejected') {
                        $badgeClass = 'badge-danger';
                    } elseif ($reg->status === 'pending') {
                        $badgeClass = 'badge-warning';
                    } elseif ($reg->status === 'verified') {
                        $badgeClass = 'badge-info';
                    } elseif ($reg->status === 'approved') {
                        $badgeClass = 'badge-success';
                        $badgeText = 'Active';
                    }
                @endphp
                <tr class="user-row" data-role="{{ $reg->role }}"
                    data-id="{{ $reg->id }}"
                    data-name="{{ $reg->full_name }}"
                    data-email="{{ $reg->email_address }}"
                    data-status="{{ $reg->status }}"
                    data-role-label="{{ $roleLabel }}"
                    data-cr="{{ $reg->cr_path ? asset('storage/' . $reg->cr_path) : '' }}"
                    data-or="{{ $reg->or_path ? asset('storage/' . $reg->or_path) : '' }}"
                    data-license="{{ $reg->license_path ? asset('storage/' . $reg->license_path) : '' }}"
                    data-com="{{ $reg->com_path ? asset('storage/' . $reg->com_path) : '' }}"
                    data-sid="{{ $reg->student_id_path ? asset('storage/' . $reg->student_id_path) : '' }}"
                    data-eid="{{ $reg->employee_id_path ? asset('storage/' . $reg->employee_id_path) : '' }}"
                >
                    <td><div style="font-weight: 600;">{{ $reg->full_name }}</div></td>
                    <td>{{ $roleLabel }}</td>
                    <td>{{ $reg->university_id }}</td>
                    <td>{{ $reg->college_dept }}</td>
                    <td>{{ ucfirst($reg->vehicle_type) }}</td>
                    <td>{{ $reg->plate_number }}</td>
                    <td>{{ $reg->rfid_tag_id ?? '—' }}</td>
                    <td>
                        @if($reg->validity_from && $reg->validity_to)
                            {{ \Carbon\Carbon::parse($reg->validity_from)->format('M d, Y') }}
                            –
                            {{ \Carbon\Carbon::parse($reg->validity_to)->format('M d, Y') }}
                        @else
                            —
                        @endif
                    </td>
                    <td><span class="badge {{ $badgeClass }}">{{ $badgeText }}</span></td>
                    <td>
                        <div class="action-group">
                            @if($reg->status === 'pending')
                            <button type="button" class="btn btn-primary btn-verify" title="Verify Online Registration" data-id="{{ $reg->id }}" style="padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.8rem;">
                                 Verify
                            </button>
                            @endif
                            <a href="{{ route('office.registration') }}?id={{ $reg->id }}" class="btn btn-outline" title="Edit/Assign RFID" style="padding: 0.4rem; border-radius: 6px;">
                                <i class="ph ph-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline btn-delete" title="Delete" data-id="{{ $reg->id }}" style="padding: 0.4rem; border-radius: 6px;">
                                <i class="ph ph-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding: 1rem;">No registrations yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- VERIFICATION MODAL -->
<div id="verifyModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Verify Registration</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div id="verifyDetails">
                <p><strong>Name:</strong> <span id="v-name"></span></p>
                <p><strong>Role:</strong> <span id="v-role"></span></p>
                <div class="document-previews" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                    <div id="v-cr-sect">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <label>Vehicle CR</label>
                            <button type="button" class="btn-ai-check" data-type="cr_file" data-img-id="v-cr" style="font-size:0.7rem; padding:2px 6px; border-radius:4px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; cursor:pointer;">
                                <i class="ph ph-sparkle"></i> AI Scan
                            </button>
                        </div>
                        <div style="position:relative;">
                            <img id="v-cr" src="" style="width: 100%; border-radius: 8px; border: 1px solid #ddd;">
                            <div class="ai-result-badge" id="v-cr-result" style="display:none; position:absolute; bottom:8px; right:8px; padding:4px 8px; border-radius:4px; font-size:0.7rem; font-weight:700;"></div>
                        </div>
                    </div>
                    <div id="v-or-sect">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <label>Vehicle OR</label>
                            <button type="button" class="btn-ai-check" data-type="or_file" data-img-id="v-or" style="font-size:0.7rem; padding:2px 6px; border-radius:4px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; cursor:pointer;">
                                <i class="ph ph-sparkle"></i> AI Scan
                            </button>
                        </div>
                        <div style="position:relative;">
                            <img id="v-or" src="" style="width: 100%; border-radius: 8px; border: 1px solid #ddd;">
                            <div class="ai-result-badge" id="v-or-result" style="display:none; position:absolute; bottom:8px; right:8px; padding:4px 8px; border-radius:4px; font-size:0.7rem; font-weight:700;"></div>
                        </div>
                    </div>
                    <div id="v-license-sect">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <label>Driver's License</label>
                            <button type="button" class="btn-ai-check" data-type="license_file" data-img-id="v-license" style="font-size:0.7rem; padding:2px 6px; border-radius:4px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; cursor:pointer;">
                                <i class="ph ph-sparkle"></i> AI Scan
                            </button>
                        </div>
                        <div style="position:relative;">
                            <img id="v-license" src="" style="width: 100%; border-radius: 8px; border: 1px solid #ddd;">
                            <div class="ai-result-badge" id="v-license-result" style="display:none; position:absolute; bottom:8px; right:8px; padding:4px 8px; border-radius:4px; font-size:0.7rem; font-weight:700;"></div>
                        </div>
                    </div>
                    <div id="v-extra-sect">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <label id="v-extra-label">Other Document</label>
                            <button type="button" class="btn-ai-check" data-type="extra" data-img-id="v-extra" style="font-size:0.7rem; padding:2px 6px; border-radius:4px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; cursor:pointer;">
                                <i class="ph ph-sparkle"></i> AI Scan
                            </button>
                        </div>
                        <div style="position:relative;">
                            <img id="v-extra" src="" style="width: 100%; border-radius: 8px; border: 1px solid #ddd;">
                            <div class="ai-result-badge" id="v-extra-result" style="display:none; position:absolute; bottom:8px; right:8px; padding:4px 8px; border-radius:4px; font-size:0.7rem; font-weight:700;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
            <div id="rejectionForm" style="display:none; flex: 1; margin-right: auto;">
                <input type="text" id="rejectionReason" placeholder="Reason for rejection (e.g. Blurred documents)" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <button id="showReject" class="btn btn-outline" style="color: #b91c1c;">Reject</button>
            <button id="confirmReject" class="btn btn-danger" style="display:none; background: #b91c1c; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px;">Confirm Reject</button>
            <button class="btn btn-outline close-modal">Cancel</button>
            <button id="confirmVerify" class="btn btn-primary">Verify & Send Email</button>
        </div>
    </div>
</div>

<!-- ZOOM MODAL -->
<div id="zoomModal" class="modal-overlay" style="display:none; z-index: 2000; background: rgba(0,0,0,0.9);">
    <div style="position: absolute; top: 1rem; right: 2rem; display: flex; gap: 1rem; align-items: center;">
        <div style="background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 99px; color: white; display: flex; gap: 1rem; border: 1px solid rgba(255,255,255,0.2);">
            <button id="zoomOut" style="background:none; border:none; color:white; cursor:pointer;"><i class="ph-bold ph-minus"></i></button>
            <span id="zoomLevel">100%</span>
            <button id="zoomIn" style="background:none; border:none; color:white; cursor:pointer;"><i class="ph-bold ph-plus"></i></button>
        </div>
        <button class="close-zoom" style="background: white; border: none; width: 40px; height: 40px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
    </div>
    <div id="zoomContainer" style="width: 90vw; height: 85vh; display: flex; align-items: center; justify-content: center; overflow: hidden; cursor: grab;">
        <img id="zoomedImg" src="" style="max-width: 90%; max-height: 90%; transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1); transform-origin: center;">
    </div>
</div>

<style>
    /* Zoom Modal Helper Styles */
    .document-previews img {
        cursor: zoom-in;
        transition: transform 0.2s;
    }
    .document-previews img:hover {
        transform: scale(1.02);
        border-color: #3b82f6 !important;
    }
    #zoomContainer:active {
        cursor: grabbing;
    }

    .table-container {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
    }
    .users-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border: none; /* remove outer lines */
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }
    .users-table thead {
        background: #f8fafc;
    }
    .users-table th,
    .users-table td {
        padding: 12px 14px;
        text-align: left;
        border: none; /* remove column lines */
        border-bottom: 1px solid #f1f5f9; /* keep only row separators */
        font-size: 0.92rem;
    }
    .users-table th {
        font-weight: 700;
        color: #334155;
        letter-spacing: 0.01em;
        white-space: nowrap;
    }
    .users-table tbody tr:hover {
        background: #f8fafc;
    }
    .users-table .badge {
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .users-table .badge-normal, 
    .users-table .badge-info {
        background: #ecfdf3;
        color: #065f46;
    }
    .users-table .badge-warning {
        background: #fffbeb;
        color: #92400e;
    }
    .users-table .badge-info {
        background: #eff6ff;
        color: #1e40af;
    }
    .users-table .badge-success {
        background: #ecfdf3;
        color: #065f46;
    }
    .users-table .badge-danger {
        background: #fef2f2;
        color: #b91c1c;
    }
    .users-table .btn.btn-outline {
        border: 1px solid #e2e8f0;
        padding: 6px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #334155;
        background: #fff;
        transition: all 0.15s ease;
    }
    .users-table .btn.btn-outline:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .users-table .action-group {
        display: inline-flex;
        gap: 6px;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
    .document-previews label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #64748b;
    }
</style>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById("searchInput");
        const roleFilter = document.getElementById("roleFilter");
        const rows = document.querySelectorAll(".user-row");
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        function toast(msg, icon = 'success') {
            Toast.fire({
                icon: icon,
                title: msg
            });
        }

        const verifyModal = document.getElementById('verifyModal');
        const confirmVerifyBtn = document.getElementById('confirmVerify');
        const showRejectBtn = document.getElementById('showReject');
        const confirmRejectBtn = document.getElementById('confirmReject');
        const rejectionForm = document.getElementById('rejectionForm');
        const rejectionReason = document.getElementById('rejectionReason');
        let currentVerifyId = null;

        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', () => verifyModal.style.display = 'none');
        });

        function attachActions() {
            document.querySelectorAll('.btn-verify').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const row = btn.closest('.user-row');
                    currentVerifyId = row.dataset.id;
                    
                    document.getElementById('v-name').textContent = row.dataset.name;
                    document.getElementById('v-role').textContent = row.dataset.roleLabel;
                    
                    document.getElementById('v-cr').src = row.dataset.cr;
                    document.getElementById('v-or').src = row.dataset.or;
                    document.getElementById('v-license').src = row.dataset.license;
                    
                    const extra = row.dataset.com || row.dataset.eid || row.dataset.sid;
                    if (extra) {
                        document.getElementById('v-extra-sect').style.display = 'block';
                        document.getElementById('v-extra').src = extra;
                        document.getElementById('v-extra-label').textContent = row.dataset.role === 'student' ? 'COM / Student ID' : 'Employee ID';
                    } else {
                        document.getElementById('v-extra-sect').style.display = 'none';
                    }

                    // Reset badges
                    document.querySelectorAll('.ai-result-badge').forEach(b => {
                        b.style.display = 'none';
                        b.textContent = '';
                    });

                    // Reset modal state
                    rejectionForm.style.display = 'none';
                    confirmRejectBtn.style.display = 'none';
                    confirmVerifyBtn.style.display = 'block';
                    showRejectBtn.style.display = 'block';

                    verifyModal.style.display = 'flex';
                });
            });

            // AI Check Integration
            document.querySelectorAll('.btn-ai-check').forEach(btn => {
                btn.addEventListener('click', async function() {
                    if (!currentVerifyId) return;
                    
                    const type = this.dataset.type;
                    const resultBadge = document.getElementById(this.dataset.imgId + '-result');
                    const originalHtml = this.innerHTML;

                    this.disabled = true;
                    this.innerHTML = '<i class="ph ph-spinner-gap animate-spin"></i> Scanning...';
                    
                    resultBadge.style.display = 'block';
                    resultBadge.textContent = 'Scanning...';
                    resultBadge.style.background = '#f1f5f9';
                    resultBadge.style.color = '#64748b';

                    try {
                        const response = await fetch(`/office/registration/validate-stored/${currentVerifyId}/${type}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();

                        if (data.success) {
                            resultBadge.textContent = 'MATCHED';
                            resultBadge.style.background = '#dcfce7';
                            resultBadge.style.color = '#166534';
                        } else {
                            resultBadge.textContent = 'INVALID: ' + (data.message.includes('look like') || data.message.includes('appear') ? 'Mismatch' : 'Failed');
                            resultBadge.style.background = '#fee2e2';
                            resultBadge.style.color = '#991b1b';
                        }
                    } catch (err) {
                        console.error(err);
                        resultBadge.textContent = 'ERROR';
                    } finally {
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                    }
                });
            });

            showRejectBtn.addEventListener('click', () => {
                rejectionForm.style.display = 'block';
                confirmRejectBtn.style.display = 'block';
                confirmVerifyBtn.style.display = 'none';
                showRejectBtn.style.display = 'none';
            });

            confirmRejectBtn.addEventListener('click', () => {
                const reason = rejectionReason.value.trim();
                if (!reason) {
                    toast('Please provide a reason for rejection.');
                    return;
                }

                confirmRejectBtn.disabled = true;
                confirmRejectBtn.textContent = 'Rejecting...';

                fetch(`/office/registration/${currentVerifyId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ reason: reason })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Rejection failed');
                    toast(data.message);
                    location.reload();
                })
                .catch(err => {
                    console.error(err);
                    toast(err.message || 'Error rejecting registration.');
                    confirmRejectBtn.disabled = false;
                    confirmRejectBtn.textContent = 'Confirm Reject';
                });
            });

            confirmVerifyBtn.addEventListener('click', () => {
                if (!currentVerifyId) return;
                
                confirmVerifyBtn.disabled = true;
                confirmVerifyBtn.textContent = 'Verifying...';

                fetch(`/office/registration/${currentVerifyId}/verify`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Verification failed');
                    toast(data.message);
                    location.reload();
                })
                .catch(err => {
                    console.error(err);
                    toast(err.message || 'Error verifying registration.');
                    confirmVerifyBtn.disabled = false;
                    confirmVerifyBtn.textContent = 'Verify & Send Email';
                });
            });

            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const row = btn.closest('.user-row');
                    const name = row.dataset.name;
                    const id = row.dataset.id;
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `You are about to delete the registration for ${name}. This cannot be undone!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#b91c1c',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/office/registration/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (!data.success) throw new Error(data.message || 'Delete failed');
                                row.remove();
                                toast('Registration deleted.');
                            })
                            .catch(err => {
                                console.error(err);
                                toast('Error deleting registration.', 'error');
                            });
                        }
                    });
                });
            });

            // ZOOM LOGIC
            const zoomModal = document.getElementById('zoomModal');
            const zoomedImg = document.getElementById('zoomedImg');
            const zoomInBtn = document.getElementById('zoomIn');
            const zoomOutBtn = document.getElementById('zoomOut');
            const zoomLevelText = document.getElementById('zoomLevel');
            const zoomContainer = document.getElementById('zoomContainer');
            
            let scale = 1;
            let isDragging = false;
            let startX, startY, translateX = 0, translateY = 0;

            document.querySelectorAll('.document-previews img').forEach(img => {
                img.addEventListener('click', function() {
                    if (!this.src || this.src.includes('undefined')) return;
                    zoomedImg.src = this.src;
                    scale = 1;
                    translateX = 0;
                    translateY = 0;
                    updateZoomTransform();
                    zoomModal.style.display = 'flex';
                });
            });

            function updateZoomTransform() {
                zoomedImg.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
                zoomLevelText.textContent = `${Math.round(scale * 100)}%`;
            }

            zoomInBtn.addEventListener('click', () => { scale += 0.2; updateZoomTransform(); });
            zoomOutBtn.addEventListener('click', () => { if (scale > 0.4) scale -= 0.2; updateZoomTransform(); });

            document.querySelector('.close-zoom').addEventListener('click', () => {
                zoomModal.style.display = 'none';
            });

            // Dragging Logic
            zoomContainer.addEventListener('mousedown', (e) => {
                if (scale <= 1) return;
                isDragging = true;
                startX = e.clientX - translateX;
                startY = e.clientY - translateY;
            });

            window.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                translateX = e.clientX - startX;
                translateY = e.clientY - startY;
                updateZoomTransform();
            });

            window.addEventListener('mouseup', () => isDragging = false);

            // Scroll to zoom
            zoomContainer.addEventListener('wheel', (e) => {
                e.preventDefault();
                const delta = e.deltaY > 0 ? -0.1 : 0.1;
                scale = Math.min(Math.max(0.4, scale + delta), 4);
                updateZoomTransform();
            });
        }

        attachActions();

        function filterUsers() {
            const searchText = searchInput.value.toLowerCase();
            const role = roleFilter.value;

            rows.forEach(row => {
                const nameContent = row.querySelector('div[style*="font-weight: 600"]').textContent.toLowerCase();
                const rowRole = row.getAttribute("data-role");

                const matchesSearch = nameContent.includes(searchText);
                const matchesRole = role === "all" || role === rowRole;

                row.style.display = (matchesSearch && matchesRole) ? "" : "none";
            });
        }

        searchInput.addEventListener("input", filterUsers);
        roleFilter.addEventListener("change", filterUsers);
    });
</script>
@endsection
@endsection
