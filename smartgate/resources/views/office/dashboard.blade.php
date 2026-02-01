@extends('layouts.app')

@section('title', 'Office Dashboard')
@section('subtitle', 'SmartGate – EVSU Vehicle Owner Management Summary')

@section('content')
<div class="dashboard-grid">
    <!-- TOTAL USERS -->
    <div class="stat-card">
        <div class="stat-label">Total Users</div>
        <div style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $totalUsers ?? 0 }}</div>
        <i class="ph ph-users stat-icon"></i>
        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem;">
            All registered users in the system
        </p>
    </div>

    <!-- REGISTERED TODAY -->
    <div class="stat-card">
        <div class="stat-label">Registered Today</div>
        <div style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $registeredToday ?? 0 }}</div>
        <i class="ph ph-user-plus stat-icon"></i>
        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem;">
            New owner registration applications today
        </p>
    </div>

    <!-- ACTIVE VEHICLES -->
    <div class="stat-card">
        <div class="stat-label">Active Vehicles</div>
        <div style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $activeVehicles ?? 0 }}</div>
        <i class="ph ph-car stat-icon"></i>
        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem;">
            Vehicles currently registered and valid
        </p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <!-- QUICK SUMMARY -->
    <div class="table-container">
        <div class="section-header">
            <h3 style="font-weight: 600; color: var(--bg-sidebar); display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph ph-chart-pie"></i> Quick Summary
            </h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @php
                $summaryItems = $summary ?? [];
                $renderItem = function($item) {
                    return [
                        'label' => $item['label'] ?? '',
                        'percent' => $item['percent'] ?? 0,
                        'count' => $item['count'] ?? 0,
                    ];
                };
            @endphp
            @foreach($summaryItems as $item)
            @php $it = $renderItem($item); @endphp
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                <span>{{ $it['label'] }}</span>
                <span style="font-weight: 700; color: var(--bg-sidebar);">
                    {{ $it['percent'] }}%
                    <span style="color: #94a3b8; font-weight: 600;">({{ $it['count'] }})</span>
                </span>
            </div>
            @endforeach
            @if(empty($summaryItems))
            <div style="text-align:center; color:#94a3b8; padding:0.5rem;">No data yet.</div>
            @endif
        </div>
    </div>

    <!-- SYSTEM STATUS -->
    <div class="table-container">
        <div class="section-header">
            <h3 style="font-weight: 600; color: var(--bg-sidebar); display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph ph-shield-check"></i> System Status
            </h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                <span>Database</span>
                <span class="text-success" style="font-weight: 600;">Online</span>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                <span>RFID Reader</span>
                <span class="text-success" style="font-weight: 600;">Connected</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem;">
                <span>Gate Controller</span>
                <span class="text-warning" style="font-weight: 600;">Standby</span>
            </div>
        </div>
    </div>
</div>
@endsection
