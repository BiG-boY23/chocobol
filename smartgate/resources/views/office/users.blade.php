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
                    // Treat any non-rejected status as Active (no Pending shown)
                    $isRejected = $reg->status === 'rejected';
                    $badgeClass = $isRejected ? 'badge-danger' : 'badge-normal';
                    $badgeText = $isRejected ? 'Rejected' : 'Active';
                @endphp
                <tr class="user-row" data-role="{{ $reg->role }}"
                    data-id="{{ $reg->id }}"
                    data-name="{{ $reg->full_name }}"
                    data-email="{{ $reg->email_address }}"
                    data-status="{{ $reg->status }}"
                    data-role-label="{{ $roleLabel }}">
                    <td><div style="font-weight: 600;">{{ $reg->full_name }}</div></td>
                    <td>{{ $roleLabel }}</td>
                    <td>{{ $reg->university_id }}</td>
                    <td>{{ $reg->college_dept }}</td>
                    <td>{{ ucfirst($reg->vehicle_type) }}</td>
                    <td>{{ $reg->plate_number }}</td>
                    <td>{{ $reg->rfid_tag_id }}</td>
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
                            <a href="{{ route('office.registration') }}?id={{ $reg->id }}" class="btn btn-outline" title="Edit" style="padding: 0.4rem; border-radius: 6px;">
                                <i class="ph ph-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline btn-delete" title "Delete" data-id="{{ $reg->id }}" style="padding: 0.4rem; border-radius: 6px;">
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

<style>
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
    .users-table .badge-normal {
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
</style>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById("searchInput");
        const roleFilter = document.getElementById("roleFilter");
        const rows = document.querySelectorAll(".user-row");
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function toast(msg) {
            alert(msg);
        }

        function attachActions() {
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const row = btn.closest('.user-row');
                    const name = row.dataset.name;
                    const id = row.dataset.id;
                    if (confirm(`Delete registration for ${name}? (ID: ${id})`)) {
                        fetch(`{{ url('office/registration') }}/${id}`, {
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
                            toast('Error deleting registration.');
                        });
                    }
                });
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
