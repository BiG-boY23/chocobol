@extends('layouts.app')

@section('title', 'System Settings')
@section('subtitle', 'Configure global application settings.')

@section('content')
<div class="table-container">
    <div class="section-header">
        <h3>General Configuration</h3>
        <button class="btn btn-primary"><i class="ph ph-floppy-disk"></i> Save Changes</button>
    </div>
    
    <div style="max-width: 600px;">
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Allowed Visitor Duration (Hours)</label>
            <input type="number" value="4" style="width: 100%; padding: 0.8rem; background: var(--bg-input); border: 1px solid #e2e8f0; border-radius: 8px;">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Gate Operation Mode</label>
            <select style="width: 100%; padding: 0.8rem; background: var(--bg-input); border: 1px solid #e2e8f0; border-radius: 8px;">
                <option value="auto">Automatic (RFID)</option>
                <option value="manual">Manual Override</option>
            </select>
        </div>
    </div>
</div>
@endsection
