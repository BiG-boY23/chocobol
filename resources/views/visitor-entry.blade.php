@extends('layouts.app')

@section('title', 'Visitor Entry')
@section('subtitle', 'Log a new one-time visitor.')

@section('content')

<div style="max-width: 600px; margin: 0 auto;">
    <div class="table-container">
        <form action="#" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Visitor Name</label>
                <input type="text" placeholder="Enter full name" style="width: 100%; padding: 0.8rem; background: var(--bg-input); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius); color: white;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Plate Number</label>
                    <input type="text" placeholder="ABC-1234" style="width: 100%; padding: 0.8rem; background: var(--bg-input); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius); color: white;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Vehicle Type</label>
                    <select style="width: 100%; padding: 0.8rem; background: var(--bg-input); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius); color: white;">
                        <option>Car</option>
                        <option>Motorcycle</option>
                        <option>Truck</option>
                        <option>Walk-in</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Purpose of Visit</label>
                <input type="text" placeholder="e.g. Delivery, Meeting" style="width: 100%; padding: 0.8rem; background: var(--bg-input); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius); color: white;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Destination Office / Person</label>
                <input type="text" placeholder="e.g. Registrar, Principal" style="width: 100%; padding: 0.8rem; background: var(--bg-input); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius); color: white;">
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                    <i class="ph ph-check-circle"></i> Log Entry
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline" style="flex: 1; justify-content: center;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
