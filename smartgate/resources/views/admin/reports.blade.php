@extends('layouts.app')

@section('title', 'Reports & System Logs')
@section('subtitle', 'Track gate traffic, monitor staff actions, and generate audit trails.')

@section('content')

<div class="reports-container">
    <!-- Tab Navigation -->
    <div style="display: flex; gap: 2rem; border-bottom: 2px solid #e2e8f0; margin-bottom: 2rem; padding-bottom: 0.5rem;">
        <button class="tab-btn active" onclick="switchTab('traffic')">
            <i class="ph ph-intersect"></i> Gate Traffic Logs
        </button>
        <button class="tab-btn" onclick="switchTab('audit')">
            <i class="ph ph-shield-check"></i> System Audit Trail
        </button>
        <button class="tab-btn" onclick="switchTab('analytics')">
            <i class="ph ph-chart-bar"></i> Traffic Analytics
        </button>
    </div>

    <!-- Tab 1: Gate Traffic Logs -->
    <div id="traffic-tab" class="tab-content active">
        <div class="table-container">
            <div class="section-header">
                <h3>Unified Traffic History</h3>
                <div style="display: flex; gap: 10px;">
                    <button class="btn btn-outline" onclick="window.print()"><i class="ph ph-printer"></i> Print Report</button>
                    <button class="btn btn-primary"><i class="ph ph-download-simple"></i> Export CSV</button>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>DateTime</th>
                            <th>Category</th>
                            <th>Identity / Vehicle Detail</th>
                            <th>Plate Number</th>
                            <th>Log Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trafficLogs as $log)
                        <tr>
                            <td style="color: #64748b;">{{ \Carbon\Carbon::parse($log['time'])->format('M d, h:i A') }}</td>
                            <td>
                                <span class="badge" style="background: {{ $log['category'] === 'RFID' ? '#e0e7ff' : '#fef3c7' }}; color: {{ $log['category'] === 'RFID' ? '#4338ca' : '#d97706' }};">
                                    {{ $log['category'] }}
                                </span>
                            </td>
                            <td style="font-weight: 600;">{{ $log['detail'] }}</td>
                            <td><span style="font-family: monospace; font-weight: 700;">{{ $log['plate'] }}</span></td>
                            <td>
                                @if($log['type'] === 'entry')
                                    <span style="color: #10b981; font-weight: 600;"><i class="ph ph-arrow-circle-down-left"></i> ENTRY</span>
                                @else
                                    <span style="color: #ef4444; font-weight: 600;"><i class="ph ph-arrow-circle-up-right"></i> EXIT</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center; padding: 2rem;">No movement logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 2: System Audit Trail -->
    <div id="audit-tab" class="tab-content">
        <div class="table-container">
            <div class="section-header">
                <h3>Admin & Staff Actions</h3>
                <p style="font-size: 0.8rem; color: #64748b;">A record of sensitive changes made within the management portal.</p>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Staff Member</th>
                            <th>Action Type</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditLogs as $log)
                        <tr>
                            <td style="font-size: 0.85rem;">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <div style="font-weight: 600;">{{ $log->user->name ?? 'System' }}</div>
                                <div style="font-size: 0.7rem; color: #94a3b8;">{{ ucfirst($log->user->role ?? 'N/A') }}</div>
                            </td>
                            <td>
                                <span class="badge" style="background: #f1f5f9; color: #475569; font-family: monospace; font-size: 0.7rem;">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td style="max-width: 300px; font-size: 0.85rem;">{{ $log->details }}</td>
                            <td style="font-family: monospace; font-size: 0.75rem;">{{ $log->ip_address }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center; padding: 2rem;">No audit logs recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem;">
                {{ $auditLogs->links() }}
            </div>
        </div>
    </div>

    <!-- Tab 3: Analytics (Summary Cards for now) -->
    <div id="analytics-tab" class="tab-content">
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-label">Busiest Hour</div>
                <div class="stat-value">07:00 AM</div>
                <div style="font-size: 0.7rem; color: #64748b;">Based on historical entry data.</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Visitor Avg Stay</div>
                <div class="stat-value">42m</div>
                <div style="font-size: 0.7rem; color: #64748b;">Average duration of visitor entries.</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Traffic (24h)</div>
                <div class="stat-value">{{ count($trafficLogs) }}</div>
                <div style="font-size: 0.7rem; color: #64748b;">Total entry/exit events today.</div>
            </div>
        </div>
        
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 3rem; text-align: center; margin-top: 2rem; color: #94a3b8;">
            <i class="ph ph-chart-line" style="font-size: 4rem; opacity: 0.1; display: block; margin: 0 auto 1rem;"></i>
            <h4>Advanced Graphical Analytics</h4>
            <p>Integration with Chart.js is ready for development to visualize seasonal traffic trends.</p>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId) {
        // Remove active class from all buttons and contents
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        // Add active class to selected
        event.currentTarget.classList.add('active');
        document.getElementById(tabId + '-tab').classList.add('active');
    }
</script>

<style>
    .tab-btn {
        background: none;
        border: none;
        padding: 0.75rem 0;
        font-weight: 600;
        color: #94a3b8;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        border-bottom: 3px solid transparent;
        margin-bottom: -0.5rem;
    }
    .tab-btn:hover { color: #475569; }
    .tab-btn.active {
        color: #1e293b;
        border-bottom-color: #1e293b;
    }
    .tab-content { display: none; transition: opacity 0.3s ease; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .badge { 
        padding: 0.25rem 0.5rem; 
        border-radius: 6px; 
        font-size: 0.75rem; 
        font-weight: 600; 
    }

    @media print {
        .sidebar, .tab-btn, .btn, .section-header div { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .table-container { border: none !important; box-shadow: none !important; }
        .tab-content.active { display: block !important; }
    }
</style>

@endsection
