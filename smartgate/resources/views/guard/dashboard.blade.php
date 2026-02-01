@extends('layouts.app')

@section('title', 'Guard Dashboard')
@section('subtitle', 'Real-time monitoring of entries and exits.')

@section('content')

<!-- Hardware Connection Header -->
<div class="tag-scanner-box mb-8" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div id="statusIcon" style="font-size: 1.5rem;"><i class="ph ph-broadcast text-gray-400"></i></div>
        <div>
            <div id="statusText" class="text-sm font-semibold text-gray-700">Scanner Offline</div>
            <div id="scannerSubtext" class="text-xs text-gray-500">Connect to hardware bridge to begin monitoring.</div>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" id="btnConnectHardware" class="btn btn-outline" style="gap: 0.5rem;">
            <i class="ph ph-plugs"></i> <span>Connect Reader</span>
        </button>
        <button type="button" id="btnSimulateScan" class="btn btn-outline" style="gap: 0.5rem; border-style: dashed;">
            <i class="ph ph-monitor"></i> <span>Simulate</span>
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-label">Total Entries Today</div>
        <div class="stat-value" id="countEntries">{{ $stats['entries_today'] }}</div>
        <i class="ph ph-arrow-circle-down-left stat-icon"></i>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Total Exits Today</div>
        <div class="stat-value" id="countExits">{{ $stats['exits_today'] }}</div>
        <i class="ph ph-arrow-circle-up-right stat-icon"></i>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Active Users</div>
        <div class="stat-value">{{ $stats['visitors_inside'] }}</div>
        <i class="ph ph-users-three stat-icon"></i>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">System Status</div>
        <div class="stat-value text-success" style="font-size: 1.5rem;">ACTIVE</div>
        <i class="ph ph-check-circle stat-icon text-success" style="opacity: 0.2"></i>
    </div>
</div>

<!-- Real-time Activity Logs -->
<div class="table-container">
    <div class="section-header">
        <h3>Recent Vehicle Activity</h3>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('guard.entry') }}" class="btn btn-primary">
                <i class="ph ph-plus"></i> Manual Entry
            </a>
        </div>
    </div>

    <div class="table-wrapper">
        <table id="logsTable">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Vehicle Details</th>
                    <th>Owner</th>
                    <th>Plate Number</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentLogs as $log)
                <tr>
                    <td>{{ $log->timestamp->format('h:i:s A') }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="ph ph-car" style="font-size: 1.25rem;"></i>
                            <div>
                                <div style="font-weight: 600;">{{ $log->vehicleRegistration->make_brand ?? 'Unknown' }}</div>
                                <div style="font-size: 0.75rem; color: #64748b;">{{ ucfirst($log->vehicleRegistration->vehicle_type ?? 'N/A') }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $log->vehicleRegistration->full_name ?? 'N/A' }}</td>
                    <td><span class="badge" style="background: #f1f5f9; color: #1e293b; border: 1px solid #e2e8f0;">{{ $log->vehicleRegistration->plate_number ?? 'N/A' }}</span></td>
                    <td>
                        @if($log->type == 'entry')
                            <span class="badge" style="background: #ecfdf5; color: #059669;">ENTRY</span>
                        @else
                            <span class="badge" style="background: #fef2f2; color: #dc2626;">EXIT</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">View Details</button>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="6" style="text-align: center; padding: 2rem; color: #94a3b8;">No recent activity detected.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Manual Visitors Inside -->
<div class="table-container mt-8">
    <div class="section-header">
        <h3>Manual Visitors Inside</h3>
        <a href="{{ route('guard.exit') }}" class="btn btn-outline">
            View All Entries
        </a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Visitor Name</th>
                    <th>Plate Number</th>
                    <th>Time In</th>
                    <th>Purpose</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($visitors_inside as $visitor)
                <tr>
                    <td style="font-weight: 600;">{{ $visitor->name }}</td>
                    <td><span class="badge" style="background: #f1f5f9; color: #1e293b; border: 1px solid #e2e8f0;">{{ $visitor->plate ?? 'N/A' }}</span></td>
                    <td>{{ $visitor->time_in->format('h:i A') }}</td>
                    <td>{{ $visitor->purpose }}</td>
                    <td style="text-align: right;">
                        <form action="{{ route('guard.visitor.exit.process', $visitor->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="background: #ef4444; border: none; padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                Log Exit
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: #94a3b8;">No manual visitors currently inside.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnConnect = document.getElementById('btnConnectHardware');
        const btnSimulate = document.getElementById('btnSimulateScan');
        const statusText = document.getElementById('statusText');
        const statusIcon = document.getElementById('statusIcon');
        const scannerSubtext = document.getElementById('scannerSubtext');
        const logsTable = document.getElementById('logsTable').getElementsByTagName('tbody')[0];
        
        let bridgeSocket = null;
        let isConnected = false;

        function updateUIStatus(status) {
            if (status === 'connected') {
                isConnected = true;
                statusText.innerText = 'Scanner Online';
                statusIcon.innerHTML = '<i class="ph ph-check-circle text-success" style="color: #10b981"></i>';
                scannerSubtext.innerText = 'Live hardware monitoring active.';
                btnConnect.innerHTML = '<i class="ph ph-plugs-connected"></i> Disconnect';
                btnConnect.classList.replace('btn-outline', 'btn-primary');
            } else if (status === 'connecting') {
                statusText.innerText = 'Connecting...';
                statusIcon.innerHTML = '<i class="ph ph-spinner-gap animate-spin"></i>';
                btnConnect.disabled = true;
            } else {
                isConnected = false;
                statusText.innerText = 'Scanner Offline';
                statusIcon.innerHTML = '<i class="ph ph-broadcast text-gray-400"></i>';
                scannerSubtext.innerText = 'Connect to hardware bridge to begin monitoring.';
                btnConnect.innerHTML = '<i class="ph ph-plugs"></i> Connect Reader';
                btnConnect.classList.replace('btn-primary', 'btn-outline');
                btnConnect.disabled = false;
            }
        }

        btnConnect.addEventListener('click', function() {
            if (isConnected) {
                bridgeSocket.close();
                return;
            }

            updateUIStatus('connecting');
            bridgeSocket = new WebSocket('ws://127.0.0.1:8080');

            bridgeSocket.onopen = function() {
                updateUIStatus('connected');
            };

            bridgeSocket.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if (data.tagId) {
                        processTag(data.tagId);
                    }
                } catch (e) { console.error('Bridge error', e); }
            };

            bridgeSocket.onclose = function() {
                updateUIStatus('offline');
            };

            bridgeSocket.onerror = function() {
                updateUIStatus('offline');
                Swal.fire({
                    icon: 'error',
                    title: 'Bridge Not Found',
                    text: 'Hardware bridge service is not running. Please start bridge_service.py.',
                    confirmButtonColor: '#1e293b'
                });
            };
        });

        // Simulation for testing
        btnSimulate.addEventListener('click', function() {
            Swal.fire({
                title: 'Simulate Plate Scan',
                input: 'text',
                inputLabel: 'Enter RFID Tag ID or Plate Number',
                inputPlaceholder: 'e.g. 1234567890',
                showCancelButton: true,
                confirmButtonText: 'Scan',
                confirmButtonColor: '#1e293b'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    processTag(result.value);
                }
            });
        });

        let lastProcessedTag = null;
        let lastProcessedTime = 0;

        async function processTag(tagId) {
            // 1. Protection against modal spam: Don't trigger if a popup is already visible
            if (Swal.isVisible()) {
                 console.log("Ignoring scan - SweetAlert is currently visible");
                 return;
            }

            // 2. Cooldown: Don't process the same tag ID too quickly (within 5 seconds)
            const now = Date.now();
            if (tagId === lastProcessedTag && (now - lastProcessedTime) < 5000) {
                console.log("Ignoring repeat scan - Cooldown active for " + tagId);
                return;
            }

            lastProcessedTag = tagId;
            lastProcessedTime = now;
            scannerSubtext.innerHTML = `Last detected: <b style="color:var(--bg-sidebar)">${tagId}</b>`;

            // First lookup the tag
            try {
                const response = await fetch(`{{ url('guard/lookup-tag') }}?tagId=${tagId}`);
                const result = await response.json();

                if (result.success) {
                    const vehicle = result.data;
                    const action = result.suggested_action;
                    const config = action === 'entry' ? {
                        title: 'Entry Detected',
                        btn: '<i class="ph ph-sign-in"></i> LOG ENTRY',
                        color: '#059669'
                    } : {
                        title: 'Exit Detected',
                        btn: '<i class="ph ph-sign-out"></i> LOG EXIT',
                        color: '#dc2626'
                    };
                    
                    const direction = await Swal.fire({
                        title: config.title,
                        html: `<div style="text-align:left; margin: 1rem 0; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                                <b>Owner:</b> ${vehicle.full_name}<br>
                                <b>Plate:</b> ${vehicle.plate_number}<br>
                                <b>Vehicle:</b> ${vehicle.make_brand} (${vehicle.vehicle_type})
                               </div>
                               Confirm vehicle movement:`,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: config.btn,
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: config.color,
                        reverseButtons: true
                    });

                    if (direction.isConfirmed) {
                        logVehicle(tagId, action);
                    }

                } else {
                    // Only show unregistered modal if it hasn't been shown too recently
                    Swal.fire({
                        icon: 'warning',
                        title: 'Unregistered Tag',
                        text: `The tag ID ${tagId} is not registered in the system.`,
                        footer: '<span style="color: #64748b; font-size: 0.8rem;">If this tag belongs to a user, please register it first.</span>',
                        confirmButtonText: 'Manual Entry',
                        showCancelButton: true,
                        confirmButtonColor: '#1e293b'
                    }).then((r) => {
                        if (r.isConfirmed) {
                             window.location.href = "{{ route('guard.entry') }}?tagId=" + tagId;
                        }
                    });
                }
            } catch (e) {
                console.error('Processing error', e);
            }
        }

        async function logVehicle(tagId, type) {
            try {
                const response = await fetch("{{ route('guard.log.vehicle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ tagId, type })
                });
                
                const result = await response.json();
                if (result.success) {
                    addLogRow(result.log);
                    updateCounters(type);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `${type.toUpperCase()} Logged`,
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            } catch (e) { console.error('Logging error', e); }
        }

        function addLogRow(log) {
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) emptyRow.remove();

            const row = logsTable.insertRow(0); // Add at top
            const time = new Date(log.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            
            const badgeType = log.type === 'entry' ? 
                '<span class="badge" style="background: #ecfdf5; color: #059669;">ENTRY</span>' : 
                '<span class="badge" style="background: #fef2f2; color: #dc2626;">EXIT</span>';

            row.innerHTML = `
                <td>${time}</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="ph ph-car" style="font-size: 1.25rem;"></i>
                        <div>
                            <div style="font-weight: 600;">${log.vehicle_registration?.make_brand || 'Unknown'}</div>
                            <div style="font-size: 0.75rem; color: #64748b;">${log.vehicle_registration?.vehicle_type || 'N/A'}</div>
                        </div>
                    </div>
                </td>
                <td>${log.vehicle_registration?.full_name || 'N/A'}</td>
                <td><span class="badge" style="background: #f1f5f9; color: #1e293b; border: 1px solid #e2e8f0;">${log.vehicle_registration?.plate_number || 'N/A'}</span></td>
                <td>${badgeType}</td>
                <td><button class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">View</button></td>
            `;
            
            // Highlight animation
            row.style.animation = 'highlight 1s ease-out';
        }

        function updateCounters(type) {
            const counter = type === 'entry' ? document.getElementById('countEntries') : document.getElementById('countExits');
            if (counter) {
                counter.innerText = parseInt(counter.innerText) + 1;
            }
        }
    });
</script>

<style>
    .animate-spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    @keyframes highlight { from { background: #f0f9ff; } to { background: transparent; } }
    .badge { padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
</style>
@endsection
@endsection
