@extends('layouts.app')

@section('title', 'Visitor Exit')
@section('subtitle', 'Log a visitor exit.')

@section('content')

<div class="table-container">
    <div class="section-header">
        <h3>Processing Exit</h3>
        <div style="width: 300px;">
            <input type="text" placeholder="Scan Entry ID or Search Name..." style="width: 100%; padding: 0.8rem; background: var(--bg-input); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius); color: white;">
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Entry ID</th>
                    <th>Name</th>
                    <th>Plate Number</th>
                    <th>Time In</th>
                    <th>Time Now</th>
                    <th>Total Duration</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Mock Row for Exit Processing -->
                <tr>
                    <td><span style="font-family: monospace; color: var(--text-muted);">V-1001</span></td>
                    <td style="font-weight: 500;">John Doe</td>
                    <td>ABC-1234</td>
                    <td>09:15 AM</td>
                    <td>{{ date('h:i A') }}</td>
                    <td style="color: var(--color-warning); font-weight: bold;">3h 30m</td>
                    <td>
                        <button class="btn btn-primary" style="background: var(--color-danger); padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                            <i class="ph ph-sign-out"></i> Confirm Exit
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
