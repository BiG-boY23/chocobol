@extends('layouts.app')

@section('title', 'Visitor Exit')
@section('subtitle', 'Process exits for manual visitors and search active entries.')

@section('content')

<div class="table-container">
    <div class="section-header" style="margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem;">
        <h3 style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="ph ph-sign-out" style="color: #ef4444;"></i>
            Active Visitors Inside
        </h3>
        <div style="width: 350px; position: relative;">
            <i class="ph ph-magnifying-glass" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
            <input type="text" id="visitorSearch" placeholder="Search name or plate..." 
                style="width: 100%; padding: 0.8rem 1rem 0.8rem 2.8rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.95rem; outline: none;">
        </div>
    </div>

    <div class="table-wrapper">
        <table id="visitorsTable">
            <thead>
                <tr>
                    <th>Entry ID</th>
                    <th>Visitor Name</th>
                    <th>Vehicle Details</th>
                    <th>Time In</th>
                    <th>Duration</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($visitors as $visitor)
                <tr>
                    <td><span style="font-family: monospace; color: #64748b; font-weight: 600;">V-{{ str_pad($visitor->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;">{{ $visitor->name }}</div>
                        <div style="font-size: 0.75rem; color: #64748b;">{{ $visitor->purpose }}</div>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="badge" style="background: #f1f5f9; color: #1e293b; border: 1px solid #e2e8f0;">{{ $visitor->plate ?? 'N/A' }}</span>
                            <span style="font-size: 0.75rem; color: #64748b;">{{ $visitor->vehicle_type }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="color: #1e293b;">{{ $visitor->time_in->format('h:i A') }}</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $visitor->time_in->format('M d, Y') }}</div>
                    </td>
                    <td>
                        @php
                            $duration = $visitor->time_in->diffInMinutes(now());
                            $hours = floor($duration / 60);
                            $mins = $duration % 60;
                        @endphp
                        <span style="font-weight: 600; color: {{ $hours >= 4 ? '#ef4444' : '#f59e0b' }};">
                            {{ $hours }}h {{ $mins }}m
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('guard.visitor.exit.process', $visitor->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="background: #ef4444; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; transition: transform 0.1s;" onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='scale(1)'">
                                <i class="ph ph-sign-out"></i> Confirm Exit
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 4rem; color: #94a3b8;">
                        <i class="ph ph-users" style="font-size: 3rem; opacity: 0.2; display: block; margin: 0 auto 1rem;"></i>
                        No visitors currently recorded inside the campus.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('visitorSearch').addEventListener('keyup', function() {
        let searchValue = this.value.toLowerCase();
        let rows = document.querySelectorAll('#visitorsTable tbody tr');
        
        rows.forEach(row => {
            if (row.cells.length < 2) return; // Skip empty row
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
</script>

<style>
    .badge { padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
    #visitorSearch:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
</style>

@endsection
