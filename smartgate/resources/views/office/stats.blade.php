@extends('layouts.app')

@section('title', 'System Statistics')
@section('subtitle', 'Detailed analytics and reporting for the SmartGate system.')

@section('content')
<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-label">Total Entries</div>
        <div style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ number_format($totalEntries) }}</div>
        <i class="ph ph-sign-in stat-icon"></i>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Exits</div>
        <div style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ number_format($totalExits) }}</div>
        <i class="ph ph-sign-out stat-icon"></i>
    </div>
    <div class="stat-card">
        <div class="stat-label">Peak Hour</div>
        <div style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $peakHour }}</div>
        <i class="ph ph-clock-afternoon stat-icon"></i>
    </div>
</div>

<div class="table-container">
    <div class="section-header">
        <h3 style="font-weight: 600; color: var(--bg-sidebar);">Monthly Registration Trends</h3>
    </div>
    <div style="height: 350px; padding: 1rem; background: white; border-radius: 8px; border: 1px solid #e2e8f0;">
        <canvas id="registrationTrendsChart"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('registrationTrendsChart').getContext('2d');
        
        // Define high-end color palette
        const primaryColor = '#800000'; // Maroon
        const primaryColorLight = '#A52A2A'; 

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(128, 0, 0, 0.2)');
        gradient.addColorStop(1, 'rgba(128, 0, 0, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [{
                    label: 'New Registrations',
                    data: {!! json_encode($counts) !!},
                    borderColor: primaryColor,
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'white',
                    pointBorderColor: primaryColor,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            stepSize: 1,
                            color: '#64748b',
                            font: { family: 'Outfit, sans-serif' }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Outfit, sans-serif' }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
