@extends('layouts.app')

@section('title', 'Overview')
@section('subtitle', 'Real-time monitoring of entries and exits.')

@section('content')

<!-- Summary Cards -->
<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-label">Total Entries Today</div>
        <div class="stat-value">{{ $stats['entries_today'] }}</div>
        <i class="ph ph-arrow-circle-down-left stat-icon"></i>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Total Exits Today</div>
        <div class="stat-value">{{ $stats['exits_today'] }}</div>
        <i class="ph ph-arrow-circle-up-right stat-icon"></i>
    </div>
    
    <div class="stat-card" style="border-color: rgba(14, 165, 233, 0.3);">
        <div class="stat-label" style="color: var(--color-primary);">Visitors Inside</div>
        <div class="stat-value">{{ $stats['visitors_inside'] }}</div>
        <i class="ph ph-users-three stat-icon" style="color: var(--color-primary); opacity: 0.2;"></i>
    </div>
    
    <div class="stat-card" style="{{ $stats['overstaying'] > 0 ? 'border-color: rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.05);' : '' }}">
        <div class="stat-label" style="{{ $stats['overstaying'] > 0 ? 'color: var(--color-danger);' : '' }}">Overstaying</div>
        <div class="stat-value" style="{{ $stats['overstaying'] > 0 ? 'color: var(--color-danger);' : '' }}">{{ $stats['overstaying'] }}</div>
        <i class="ph ph-warning-circle stat-icon" style="{{ $stats['overstaying'] > 0 ? 'color: var(--color-danger); opacity: 0.2;' : '' }}"></i>
    </div>
</div>

<!-- Active Visitor Table -->
<div class="table-container">
    <div class="section-header">
        <h3>Visitors Currently Inside</h3>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('visitor.entry') }}" class="btn btn-primary">
                <i class="ph ph-plus"></i> New Visitor Entry
            </a>
            <a href="#" class="btn btn-outline">
                <i class="ph ph-download-simple"></i> Export Log
            </a>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Entry ID</th>
                    <th>Name</th>
                    <th>Plate Number</th>
                    <th>Purpose</th>
                    <th>Time In</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($visitors_inside as $visitor)
                <tr>
                    <td><span style="font-family: monospace; color: var(--text-muted);">{{ $visitor['id'] }}</span></td>
                    <td style="font-weight: 500;">{{ $visitor['name'] }}</td>
                    <td>{{ $visitor['plate'] }}</td>
                    <td>{{ $visitor['purpose'] }}</td>
                    <td>{{ $visitor['time_in'] }}</td>
                    <td>{{ $visitor['duration'] }}</td>
                    <td>
                        @if($visitor['status'] == 'OVERSTAY')
                            <span class="badge badge-danger">Overstayed</span>
                        @elseif($visitor['status'] == 'WARNING')
                            <span class="badge badge-warning">Warning</span>
                        @else
                            <span class="badge badge-normal">Active</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                            Log Exit
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


@section('scripts')
<script>
    // Auto-refresh the dashboard every 30 seconds
    setInterval(function() {
        window.location.reload();
    }, 30000);
</script>
@endsection

