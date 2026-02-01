@extends('layouts.app')

@section('title', 'Admin Overview')
@section('subtitle', 'Manage system settings and users.')

@section('content')
<!-- Summary Cards -->
<div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
    <!-- Total RFID Tags -->
    <div class="stat-card" style="border-left: 4px solid var(--bg-sidebar);">
        <div class="stat-label" style="color: var(--bg-sidebar);">Total RFID Tags</div>
        <div class="stat-value">{{ number_format($stats['total_rfid']) }}</div>
        <i class="ph ph-identification-card stat-icon" style="color: var(--bg-sidebar); opacity: 0.15;"></i>
    </div>

    <!-- Active Tags -->
    <div class="stat-card" style="border-left: 4px solid var(--color-success);">
        <div class="stat-label" style="color: var(--color-success);">Active Tags</div>
        <div class="stat-value">{{ number_format($stats['active_rfid']) }}</div>
        <i class="ph ph-check-circle stat-icon" style="color: var(--color-success); opacity: 0.15;"></i>
    </div>

    <!-- Deactivated/Blacklisted -->
    <div class="stat-card" style="border-left: 4px solid var(--color-danger);">
        <div class="stat-label" style="color: var(--color-danger);">Blacklisted / Inactive</div>
        <div class="stat-value">{{ $stats['blacklisted_rfid'] }}</div>
        <i class="ph ph-prohibit stat-icon" style="color: var(--color-danger); opacity: 0.15;"></i>
    </div>

    <!-- Today's Entries -->
    <div class="stat-card" style="border-left: 4px solid var(--color-evsu-gold);">
        <div class="stat-label" style="color: var(--color-primary-hover);">Entries Today</div>
        <div class="stat-value">{{ number_format($stats['entries_today']) }}</div>
        <i class="ph ph-car stat-icon" style="color: var(--color-evsu-gold); opacity: 0.2;"></i>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 2fr 1fr; margin-top: 2rem;">
    <!-- Recent Activity / Audit Log -->
    <div class="table-container">
        <div class="section-header">
            <h3><i class="ph ph-files" style="color: var(--color-evsu-gold); margin-right: 8px;"></i> Recent Audit Logs</h3>
            <a href="#" class="btn btn-outline" style="font-size: 0.85rem;">View All</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td style="color: var(--text-muted); font-size: 0.85rem;">{{ $log['time'] }}</td>
                        <td style="font-weight: 500; color: var(--bg-sidebar);">{{ $log['user'] }}</td>
                        <td>
                            <span class="badge badge-neutral">{{ $log['action'] }}</span>
                        </td>
                        <td style="color: var(--text-muted);">{{ $log['details'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions / System Status -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- System Status -->
        <div class="stat-card">
            <div class="stat-label">System Status</div>
            <div class="stat-value" style="font-size: 1.25rem; color: var(--color-success); display: flex; align-items: center; gap: 0.5rem;">
                <span style="height: 10px; width: 10px; background: var(--color-success); border-radius: 50%;"></span>
                Operational
            </div>
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                 <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem;">
                    <span>Gate Mode</span>
                    <span style="font-weight: 600;">Automatic</span>
                 </div>
                 <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span>Database</span>
                    <span style="color: var(--color-success);">Connected</span>
                 </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="table-container" style="padding: 1.5rem;">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Quick Actions</h3>
            <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                <a href="#" class="btn btn-primary" style="justify-content: center;">
                    <i class="ph ph-user-plus"></i> Add New User
                </a>
                <a href="#" class="btn btn-outline" style="justify-content: center;">
                    <i class="ph ph-gear"></i> System Settings
                </a>
                <a href="#" class="btn btn-outline" style="justify-content: center; border-color: var(--color-danger); color: var(--color-danger);">
                    <i class="ph ph-lock-key"></i> Emergency Lockdown
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
