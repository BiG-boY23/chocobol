@extends('layouts.app')

@section('title', 'Visitor Entry')
@section('subtitle', 'Log a new manual visitor entry (for non-tagged vehicles).')

@section('content')

<div style="max-width: 700px; margin: 0 auto;">
    <div class="table-container" style="padding: 2.5rem; border-radius: 16px;">
        <div class="section-header" style="margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
            <h3 style="font-size: 1.25rem; color: #1e293b; display: flex; align-items: center; gap: 0.75rem;">
                <i class="ph ph-user-plus" style="color: #6366f1;"></i>
                Manual Visitor Registration
            </h3>
        </div>

        <form action="{{ route('guard.visitor.store') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569; font-size: 0.9rem;">Visitor Full Name</label>
                <input type="text" name="name" required placeholder="Enter visitor's full name" 
                    style="width: 100%; padding: 0.8rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 1rem; outline: none; transition: border-color 0.2s;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569; font-size: 0.9rem;">Plate Number</label>
                    <input type="text" name="plate" placeholder="ABC-1234 (Optional)" 
                        style="width: 100%; padding: 0.8rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 1rem;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569; font-size: 0.9rem;">Vehicle Type</label>
                    <select name="vehicle_type" style="width: 100%; padding: 0.8rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 1rem; cursor: pointer;">
                        <option value="Car">Car</option>
                        <option value="Motorcycle">Motorcycle</option>
                        <option value="Van">Van</option>
                        <option value="SUV">SUV</option>
                        <option value="Truck">Truck</option>
                        <option value="Delivery">Delivery Vehicle</option>
                        <option value="Walk-in">Walk-in</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569; font-size: 0.9rem;">Purpose of Visit</label>
                <input type="text" name="purpose" placeholder="e.g. Delivery, Meeting, Official Business" 
                    style="width: 100%; padding: 0.8rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569; font-size: 0.9rem;">Destination Office / Person</label>
                <input type="text" name="destination" placeholder="e.g. Registrar's Office, Admin Bldg" 
                    style="width: 100%; padding: 0.8rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 1rem;">
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex: 2; justify-content: center; height: 50px; font-weight: 600; font-size: 1rem; border-radius: 10px;">
                    <i class="ph ph-check-circle" style="font-size: 1.25rem;"></i> Log Visitor Entry
                </button>
                <a href="{{ route('guard.dashboard') }}" class="btn btn-outline" style="flex: 1; justify-content: center; height: 50px; border-radius: 10px;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    input:focus, select:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
</style>

@endsection
