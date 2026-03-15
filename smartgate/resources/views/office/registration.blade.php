@extends('layouts.app')

@section('title', 'Vehicle Owner Registration')
@section('subtitle', 'Register new vehicle owners and assign RFID tags for system access.')

@section('content')
<div class="table-container">
    <!-- Success toast -->
    <div id="registrationSuccessToast" class="toast hidden">
        <div class="toast-icon">
            <i class="ph ph-check-circle"></i>
        </div>
        <div class="toast-text">
            <div class="toast-title">Registration Saved</div>
            <div class="toast-message" id="registrationSuccessMessage"></div>
        </div>
        <button type="button" id="toastCloseBtn" class="toast-close">&times;</button>
    </div>

    <form id="registerForm" class="registration-form">
        @csrf
        <!-- ================= APPLICANT INFORMATION ================= -->
        <section class="form-section">
            <h2 class="section-title">
                <i class="ph ph-user"></i> Applicant Information
            </h2>

            <div class="form-group mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <div class="role-options">
                    <label class="role-option">
                        <input type="radio" name="role" value="student">
                        <span>Student</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="faculty">
                        <span>Faculty</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="staff">
                        <span>Non-Teaching</span>
                    </label>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <input type="text" name="firstName" placeholder="First Name" required>
                </div>
                <div class="form-field">
                    <input type="text" name="lastName" placeholder="Last Name" required>
                </div>
                <div class="form-field">
                    <input type="text" name="middleName" placeholder="Middle Name (Optional)">
                </div>
                <div class="form-field" id="field-universityId">
                    <label id="label-universityId" class="block text-xs text-gray-500 mb-1">University ID</label>
                    <input type="text" name="universityId" id="input-universityId" placeholder="University ID" required>
                </div>
                <div class="form-field" id="field-collegeDept">
                    <input type="text" name="collegeDept" placeholder="College / Department" required>
                </div>
                <div class="form-field">
                    <input type="text" name="contactNumber" placeholder="Contact Number" required>
                </div>
                <div class="form-field md-col-2" id="field-emailAddress">
                    <input type="email" name="emailAddress" placeholder="Email Address" required>
                </div>
            </div>

            <!-- Role-Specific Fields -->
            <div id="studentFields" class="form-grid hidden mt-4">
                <div class="form-field">
                    <input type="text" name="course" placeholder="Course">
                </div>
                <div class="form-field">
                    <select name="yearLevel">
                        <option value="">Year Level</option>
                        <option>1st Year</option>
                        <option>2nd Year</option>
                        <option>3rd Year</option>
                        <option>4th Year</option>
                    </select>
                </div>
            </div>

            <div id="facultyFields" class="form-grid hidden mt-4">
                <div class="form-field" id="field-rank">
                    <input type="text" name="rank" placeholder="Rank / Position">
                </div>
                <div class="form-field" id="field-office">
                    <input type="text" name="office" placeholder="Office / Room">
                </div>
            </div>

            <div id="staffFields" class="form-grid hidden mt-4">
                <div class="form-field" id="field-businessStallName">
                    <input type="text" name="businessStallName" placeholder="Business / Stall Name">
                </div>
                <div class="form-field" id="field-vendorAddress">
                    <input type="text" name="vendorAddress" placeholder="Business Address">
                </div>
            </div>
        </section>

        <hr class="section-divider">

        <!-- ================= VEHICLE INFORMATION ================= -->
        <section class="form-section">
            <h2 class="section-title">
                <i class="ph ph-car"></i> Vehicle Information
            </h2>

            <div class="form-group mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                <div class="role-options">
                    <label class="role-option">
                        <input type="radio" name="vehicleType" value="car" required>
                        <span>Car</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="vehicleType" value="suv">
                        <span>SUV</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="vehicleType" value="van">
                        <span>Van</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="vehicleType" value="motorcycle">
                        <span>Motorcycle</span>
                    </label>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field" id="field-registeredOwner">
                    <input type="text" name="registeredOwner" placeholder="Registered Owner" required>
                </div>
                <div class="form-field">
                    <input type="text" name="makeBrand" placeholder="Make / Brand" required>
                </div>
                <div class="form-field" id="field-modelYear">
                    <input type="text" name="modelYear" placeholder="Model / Year" required>
                </div>
                <div class="form-field" id="field-color">
                    <input type="text" name="color" placeholder="Color" required>
                </div>
                <div class="form-field">
                    <input type="text" name="plateNumber" placeholder="Plate Number" required>
                </div>
                <div class="form-field" id="field-engineNumber">
                    <input type="text" name="engineNumber" placeholder="Engine Number" required>
                </div>
            </div>
        </section>

        <hr class="section-divider">

        <!-- ================= TAG CLASSIFICATION ================= -->
        <section class="form-section">
            <h2 class="section-title">
                <i class="ph ph-tag"></i> Access Classification
            </h2>

            <div class="checkbox-grid">
                <label id="stickerStudent" class="checkbox-option hidden">
                    <input type="checkbox" name="stickerClassification[]" value="student">
                    <span>Student Access</span>
                </label>
                <label id="stickerFaculty" class="checkbox-option hidden">
                    <input type="checkbox" name="stickerClassification[]" value="faculty">
                    <span>Faculty Access</span>
                </label>
                <label id="stickerStaff" class="checkbox-option hidden">
                    <input type="checkbox" name="stickerClassification[]" value="staff">
                    <span>Non-Teaching Access</span>
                </label>
                <label id="stickerService" class="checkbox-option hidden">
                    <input type="checkbox" name="stickerClassification[]" value="service">
                    <span>Service Vehicle</span>
                </label>
            </div>
        </section>

        <hr class="section-divider">

        <!-- ================= REQUIREMENTS SUBMITTED ================= -->
        <section class="form-section">
            <h2 class="section-title">
                <i class="ph ph-file-check"></i> Requirements & Document Scan
            </h2>

            <div class="checkbox-grid mb-6">
                <label class="checkbox-option">
                    <input type="checkbox" name="requirements[]" value="certificateOfRegistration">
                    <span>Certificate of Registration</span>
                </label>
                <label class="checkbox-option">
                    <input type="checkbox" name="requirements[]" value="officialReceipt">
                    <span>Official Receipt (Renewal)</span>
                </label>
                <label class="checkbox-option">
                    <input type="checkbox" name="requirements[]" value="certificateOfEnrollment">
                    <span>Enrollment / Employment Cert</span>
                </label>
                <label class="checkbox-option">
                    <input type="checkbox" name="requirements[]" value="driversLicense">
                    <span>Driver's License</span>
                </label>
                <label id="businessPermitRequirement" class="checkbox-option hidden md-col-2">
                    <input type="checkbox" name="requirements[]" value="businessPermit">
                    <span>Business Permit</span>
                </label>
            </div>

            <div class="document-upload-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <div class="form-group">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Scan Vehicle CR</label>
                    <input type="file" class="doc-input" data-type="cr_file" style="font-size: 0.8rem;">
                    <div class="validation-error" style="color: #ef4444; font-size: 0.75rem; margin-top: 4px; display: none;"></div>
                </div>
                <div class="form-group">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Scan Vehicle OR</label>
                    <input type="file" class="doc-input" data-type="or_file" style="font-size: 0.8rem;">
                    <div class="validation-error" style="color: #ef4444; font-size: 0.75rem; margin-top: 4px; display: none;"></div>
                </div>
                <div class="form-group">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Scan Driver's License</label>
                    <input type="file" class="doc-input" data-type="license_file" style="font-size: 0.8rem;">
                    <div class="validation-error" style="color: #ef4444; font-size: 0.75rem; margin-top: 4px; display: none;"></div>
                </div>
            </div>
        </section>

        <hr class="section-divider">

        <!-- ================= VALIDITY ================= -->
        <section class="form-section">
            <h2 class="section-title">
                <i class="ph ph-calendar"></i> Validity Period
            </h2>
            <div class="form-grid">
                <div class="form-field">
                    <label class="block text-xs text-gray-500 mb-1">Valid From</label>
                    <input type="date" name="validityFrom" required>
                </div>
                <div class="form-field">
                    <label class="block text-xs text-gray-500 mb-1">Valid To</label>
                    <input type="date" name="validityTo" required>
                </div>
            </div>
        </section>

        <hr class="section-divider">

        <!-- ================= RFID TAG ASSIGNMENT ================= -->
        <section class="form-section">
            <h2 class="section-title">
                <i class="ph ph-identification-card"></i> RFID Tag Assignment
            </h2>
            <div class="tag-scanner-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px dashed #e2e8f0;">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Registration Mode</span>
                        <div class="mode-toggle" style="display: flex; background: #f1f5f9; padding: 4px; border-radius: 8px; gap: 4px;">
                            <button type="button" id="modeAuto" class="btn btn-mode active" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Automatic</button>
                            <button type="button" id="modeManual" class="btn btn-mode" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Manual</button>
                        </div>
                    </div>
                    <button type="button" id="btnConnectBridge" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; gap: 0.4rem;">
                        <i class="ph ph-broadcast"></i> <span>Connect Bridge</span>
                    </button>
                </div>

                <!-- Automatic Mode View -->
                <div id="autoModeContainer">
                    <div class="form-field">
                        <label class="block text-sm font-medium text-gray-700 mb-2">RFID Tag ID (Double Scan to Verify)</label>
                        <div style="display: flex; gap: 10px;">
                            <div style="flex-grow: 1; position: relative;">
                                <input type="text" name="rfidTagId" id="rfidTagId" placeholder="Waiting for scan..." readonly required 
                                    style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; background: #f1f5f9; cursor: not-allowed;">
                                <div id="scanVerificationBadge" class="hidden" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 4px;"></div>
                            </div>
                            <button type="button" id="scanBtn" class="btn btn-primary" style="padding: 0 1.5rem;">
                                <i class="ph ph-scan"></i> <span id="scanBtnText">Scan Tag</span>
                            </button>
                            <button type="button" id="resetScanBtn" class="btn btn-outline hidden" style="padding: 0 1rem;" title="Reset Scan">
                                <i class="ph ph-arrows-clockwise"></i>
                            </button>
                        </div>
                        <div id="scannerStatusBox" class="mt-3 p-2 rounded bg-gray-50 flex items-center gap-2 border border-gray-100">
                            <span id="statusIcon"><i class="ph ph-circle text-gray-400"></i></span> 
                            <span id="statusText" class="text-xs text-gray-600">Hardware scanner ready for assignment.</span>
                        </div>
                    </div>
                </div>

                <!-- Manual Mode View -->
                <div id="manualModeContainer" class="hidden">
                    <div class="form-grid">
                        <div class="form-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">RFID Tag ID</label>
                            <input type="text" id="manualRfidTagId" placeholder="Enter Tag ID manually" 
                                style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                        </div>
                        <div class="form-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Tag ID</label>
                            <input type="text" id="confirmRfidTagId" placeholder="Re-enter Tag ID to verify" 
                                style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                        </div>
                    </div>
                    <div id="manualStatusBox" class="mt-3 p-2 rounded bg-gray-50 flex items-center gap-2 border border-gray-100">
                        <i class="ph ph-info text-blue-400"></i>
                        <span class="text-xs text-gray-600">Please ensure both entries match to prevent registration errors.</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="form-actions mt-8">
            <button type="submit" class="btn btn-primary w-full justify-center" style="height: 50px; font-size: 1.1rem;">
                <i class="ph ph-user-plus"></i> Complete Registration
            </button>
        </div>
    </form>
</div>

<style>
    .form-section {
        margin-bottom: 2rem;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--bg-sidebar);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .role-options, .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }
    .role-option, .checkbox-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .role-option:hover, .checkbox-option:hover {
        background: #f1f5f9;
        border-color: var(--bg-sidebar);
    }
    .role-option input, .checkbox-option input {
        accent-color: var(--bg-sidebar);
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    .form-field input, .form-field select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.2s;
    }
    .form-field input:focus, .form-field select:focus {
        border-color: var(--bg-sidebar);
    }
    .md-col-2 {
        grid-column: span 2;
    }
    .section-divider {
        border: 0;
        border-top: 1px solid #f1f5f9;
        margin: 2rem 0;
    }
    .hidden {
        display: none !important;
    }
    .form-group.scanning {
        position: relative;
        opacity: 0.7;
        pointer-events: none;
    }
    .form-group.scanning::after {
        content: "SCANNING...";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(30, 41, 59, 0.9);
        color: white;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1px;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }
    .validation-error {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .toast {
        position: fixed;
        top: 88px;
        right: 32px;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 1px solid #6ee7b7;
        color: #065f46;
        padding: 0.85rem 1.2rem;
        border-radius: 16px;
        box-shadow: 0 18px 45px rgba(4, 120, 87, 0.3);
        display: flex;
        align-items: center;
        gap: 0.85rem;
        z-index: 60;
        font-size: 0.85rem;
        opacity: 0;
        transform: translateY(-12px);
        transition: opacity 0.22s ease, transform 0.22s ease;
        backdrop-filter: blur(6px);
    }
    .toast.visible {
        opacity: 1;
        transform: translateY(0);
    }
    .toast-icon {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #10b981;
        color: #ecfdf5;
        flex-shrink: 0;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
    }
    .toast-icon i {
        font-size: 18px;
    }
    .toast-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
        max-width: 260px;
    }
    .toast-title {
        font-weight: 700;
        font-size: 0.82rem;
        letter-spacing: .02em;
        text-transform: uppercase;
    }
    .toast-message {
        font-size: 0.8rem;
        color: #047857;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .toast-close {
        background: transparent;
        border: none;
        color: #065f46;
        font-size: 1rem;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        margin-left: 0.25rem;
    }
    .btn-mode {
        background: transparent;
        border: none;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 6px;
        font-weight: 600;
    }
    .btn-mode.active {
        background: white;
        color: var(--bg-sidebar);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editMode = @json(isset($registration));
        const registrationData = @json($registration ?? null);
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const studentFields = document.getElementById('studentFields');
        const facultyFields = document.getElementById('facultyFields');
        const staffFields = document.getElementById('staffFields');

        const stickerStudent = document.getElementById('stickerStudent');
        const stickerFaculty = document.getElementById('stickerFaculty');
        const stickerStaff = document.getElementById('stickerStaff');
        const stickerService = document.getElementById('stickerService');
        const businessPermitRequirement = document.getElementById('businessPermitRequirement');

        function hideAll() {
            [studentFields, facultyFields, staffFields, stickerStudent, stickerFaculty, stickerStaff, stickerService, businessPermitRequirement]
            .forEach(el => el.classList.add('hidden'));
        }

        const fieldUniversityId = document.getElementById('field-universityId');
        const inputUniversityId = document.getElementById('input-universityId');
        const labelUniversityId = document.getElementById('label-universityId');
        const fieldCollegeDept = document.getElementById('field-collegeDept');
        const fieldEmailAddress = document.getElementById('field-emailAddress');
        
        const fieldRank = document.getElementById('field-rank');
        const fieldOffice = document.getElementById('field-office');
        const fieldBusinessStallName = document.getElementById('field-businessStallName');
        const fieldVendorAddress = document.getElementById('field-vendorAddress');

        // Vehicle extra fields (these are common to ALL roles now)
        const vOwner = document.getElementById('field-registeredOwner');
        const vModel = document.getElementById('field-modelYear');
        const vColor = document.getElementById('field-color');
        const vEngine = document.getElementById('field-engineNumber');

        // Helper to toggle visibility and required status
        function toggleField(el, show) {
            if (!el) return;
            if (show) {
                el.classList.remove('hidden');
                const input = el.querySelector('input');
                // middleName is optional
                if (input && input.name !== 'middleName' && input.name !== 'rank' && input.name !== 'office' && input.name !== 'businessStallName' && input.name !== 'vendorAddress') {
                    input.required = true;
                }
            } else {
                el.classList.add('hidden');
                const input = el.querySelector('input');
                if (input) input.required = false;
            }
        }

        roleInputs.forEach(radio => {
            radio.addEventListener('change', () => {
                hideAll();
                
                // --- Reset visibility to default (Visible) ---
                toggleField(fieldUniversityId, true);
                toggleField(fieldCollegeDept, true);
                toggleField(fieldEmailAddress, true);
                
                // --- Global Vehicle Information Rule ---
                // "it should be Make/Brand, Plate Number and Vehicle Type only"
                toggleField(vOwner, false);
                toggleField(vModel, false);
                toggleField(vColor, false);
                toggleField(vEngine, false);

                if (radio.value === 'student') {
                    studentFields.classList.remove('hidden');
                    stickerStudent.classList.remove('hidden');
                    
                    labelUniversityId.innerText = "Student ID";
                    inputUniversityId.placeholder = "Student ID";

                } else if (radio.value === 'faculty') {
                    facultyFields.classList.remove('hidden'); // container
                    stickerFaculty.classList.remove('hidden');
                    stickerService.classList.remove('hidden');
                    
                    labelUniversityId.innerText = "Faculty ID";
                    inputUniversityId.placeholder = "Faculty ID";
                    
                    // Faculty Custom: Remove Email, Rank, Office
                    toggleField(fieldEmailAddress, false);
                    toggleField(fieldRank, false);
                    toggleField(fieldOffice, false);

                } else if (radio.value === 'staff') {
                    staffFields.classList.remove('hidden'); // container
                    stickerStaff.classList.remove('hidden');
                    stickerService.classList.remove('hidden');
                    businessPermitRequirement.classList.remove('hidden');

                    // Staff Custom: Remove ID, College, Email, Business Address
                    toggleField(fieldUniversityId, false);
                    toggleField(fieldCollegeDept, false);
                    toggleField(fieldEmailAddress, false);
                    toggleField(fieldVendorAddress, false);
                }
            });
        });

        // Tag Scanner Logic (Bridge, Verification & Manual Entry)
        const scanBtn = document.getElementById('scanBtn');
        const scanBtnText = document.getElementById('scanBtnText');
        const resetScanBtn = document.getElementById('resetScanBtn');
        const rfidInput = document.getElementById('rfidTagId');
        const scanBadge = document.getElementById('scanVerificationBadge');
        const statusText = document.getElementById('statusText');
        const statusIcon = document.getElementById('statusIcon');
        const btnConnectBridge = document.getElementById('btnConnectBridge');
        
        const modeAuto = document.getElementById('modeAuto');
        const modeManual = document.getElementById('modeManual');
        const autoContainer = document.getElementById('autoModeContainer');
        const manualContainer = document.getElementById('manualModeContainer');
        const manualTagInput = document.getElementById('manualRfidTagId');
        const confirmTagInput = document.getElementById('confirmRfidTagId');

        let registrationMode = 'auto';
        let scanStep = 0; // 0: none, 1: first scan captured, 2: verified
        let firstScanId = '';

        // Mode Switching
        modeAuto.addEventListener('click', () => {
            registrationMode = 'auto';
            modeAuto.classList.add('active');
            modeManual.classList.remove('active');
            autoContainer.classList.remove('hidden');
            manualContainer.classList.add('hidden');
            resetScanner();
        });

        modeManual.addEventListener('click', () => {
            registrationMode = 'manual';
            modeManual.classList.add('active');
            modeAuto.classList.remove('active');
            manualContainer.classList.remove('hidden');
            autoContainer.classList.add('hidden');
            resetScanner();
        });

        function resetScanner() {
            scanStep = 0;
            firstScanId = '';
            rfidInput.value = '';
            rfidInput.style.background = '#f1f5f9';
            rfidInput.style.borderColor = '#e2e8f0';
            scanBadge.classList.add('hidden');
            resetScanBtn.classList.add('hidden');
            
            manualTagInput.value = '';
            confirmTagInput.value = '';
            
            if (registrationMode === 'auto') {
                updateStatus('Hardware scanner ready for assignment.', 'info');
                scanBtnText.innerText = isBridgeMode ? 'Listening...' : 'Scan Tag';
            } else {
                updateStatus('Manual entry mode active.', 'info');
            }
        }

        resetScanBtn.addEventListener('click', resetScanner);

        // Manual Input Validation
        const validateManualTags = () => {
            if (registrationMode !== 'manual') return;
            
            const tag1 = manualTagInput.value.trim();
            const tag2 = confirmTagInput.value.trim();
            
            if (tag1 && tag2) {
                if (tag1 === tag2) {
                    rfidInput.value = tag1;
                    updateStatus('Manual Tag Verified!', 'success');
                    manualTagInput.style.borderColor = '#10b981';
                    confirmTagInput.style.borderColor = '#10b981';
                } else {
                    rfidInput.value = '';
                    updateStatus('Tags do not match. Please check your entry.', 'danger');
                    manualTagInput.style.borderColor = '#ef4444';
                    confirmTagInput.style.borderColor = '#ef4444';
                }
            } else {
                rfidInput.value = '';
                manualTagInput.style.borderColor = '#e2e8f0';
                confirmTagInput.style.borderColor = '#e2e8f0';
            }
        };

        manualTagInput.addEventListener('input', validateManualTags);
        confirmTagInput.addEventListener('input', validateManualTags);

        const toast = document.getElementById('registrationSuccessToast');
        const toastCloseBtn = document.getElementById('toastCloseBtn');

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        function showToast(msg, icon = 'success') {
            Toast.fire({
                icon: icon,
                title: msg
            });
        }

        if (toastCloseBtn) {
            toastCloseBtn.addEventListener('click', function () {
                toast.classList.add('hidden');
                toast.classList.remove('visible');
            });
        }
        
        let bridgeSocket = null;
        let isBridgeMode = false;

        function updateStatus(text, type = 'info') {
            statusText.innerText = text;
            if (type === 'success') {
                statusIcon.innerHTML = '<i class="ph ph-check-circle text-success" style="color: #10b981"></i>';
                statusText.className = 'text-xs text-success font-semibold';
            } else if (type === 'warning') {
                statusIcon.innerHTML = '<i class="ph ph-broadcast text-warning" style="color: #f59e0b"></i>';
                statusText.className = 'text-xs text-warning font-semibold';
            } else if (type === 'danger') {
                statusIcon.innerHTML = '<i class="ph ph-warning-circle text-danger" style="color: #ef4444"></i>';
                statusText.className = 'text-xs text-danger font-semibold';
            } else {
                statusIcon.innerHTML = '<i class="ph ph-circle text-gray-400"></i>';
                statusText.className = 'text-xs text-gray-600';
            }
        }

        // Bridge Connection Code
        btnConnectBridge.addEventListener('click', function() {
            if (bridgeSocket && bridgeSocket.readyState === WebSocket.OPEN) {
                bridgeSocket.close();
                return;
            }

            btnConnectBridge.innerHTML = '<i class="ph ph-spinner-gap animate-spin"></i> Connecting...';
            bridgeSocket = new WebSocket('ws://127.0.0.1:8080');

            bridgeSocket.onopen = function() {
                isBridgeMode = true;
                btnConnectBridge.innerHTML = '<i class="ph ph-plugs-connected"></i> Connected';
                btnConnectBridge.classList.replace('btn-outline', 'btn-primary');
                updateStatus('Hardware Bridge Connected.', 'success');
                if (registrationMode === 'auto') {
                    scanBtnText.innerText = 'Listening...';
                    scanBtn.disabled = true;
                }
            };

            bridgeSocket.onmessage = async function(event) {
                if (registrationMode !== 'auto') return;
                
                try {
                    const data = JSON.parse(event.data);
                    if (data.tagId) {
                        const scannedId = data.tagId;

                        if (scanStep === 0) {
                            // First Scan
                            firstScanId = scannedId;
                            scanStep = 1;
                            
                            rfidInput.value = ''; // Don't fill yet
                            rfidInput.placeholder = "FIRST SCAN CAPTURED...";
                            rfidInput.style.background = '#fffbeb';
                            rfidInput.style.borderColor = '#fbbf24';
                            
                            scanBadge.classList.remove('hidden');
                            scanBadge.innerText = "STEP 1/2";
                            scanBadge.style.background = '#fbbf24';
                            scanBadge.style.color = '#78350f';
                            
                            resetScanBtn.classList.remove('hidden');
                            updateStatus('First scan successful. Please scan the same tag AGAIN to verify.', 'warning');
                            
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'info',
                                title: 'Step 1 Captured',
                                text: 'Scan again to verify',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else if (scanStep === 1) {
                            // Verification Scan
                            if (scannedId === firstScanId) {
                                // MATCH!
                                // Final step: Check if already registered
                                const checkResponse = await fetch(`{{ url('office/check-tag') }}?tagId=${scannedId}`);
                                const checkResult = await checkResponse.json();

                                if (checkResult.exists) {
                                    updateStatus('Verification Conflict: Tag already in use.', 'danger');
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Tag Already Registered',
                                        text: checkResult.message,
                                        footer: '<b>Owner:</b> ' + checkResult.owner,
                                        confirmButtonColor: '#ef4444'
                                    });
                                    resetScanner();
                                } else {
                                    rfidInput.value = scannedId;
                                    rfidInput.style.background = '#ecfdf5';
                                    rfidInput.style.borderColor = '#10b981';
                                    
                                    scanBadge.innerText = "VERIFIED";
                                    scanBadge.style.background = '#10b981';
                                    scanBadge.style.color = 'white';
                                    
                                    scanStep = 2;
                                    updateStatus('Tag Double-Verified and Ready.', 'success');
                                    
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Double Verification Successful',
                                        text: 'Tag ID ' + scannedId + ' has been verified.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            } else {
                                // MISMATCH
                                updateStatus('MISMATCH! Verification failed. Try again.', 'danger');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Verification Failed',
                                    text: 'The second scan did not match the first. Please scan the tag again carefully.',
                                    confirmButtonColor: '#ef4444'
                                });
                                // Keep Step 1 but maybe they want to reset? Let's just let them try again for Step 2 or reset.
                                // Actually, it's safer to reset to step 0.
                                resetScanner();
                            }
                        }
                    }
                } catch (e) {
                    console.error('Bridge parse error', e);
                }
            };

            bridgeSocket.onclose = function() {
                isBridgeMode = false;
                btnConnectBridge.innerHTML = '<i class="ph ph-broadcast"></i> Connect Bridge';
                btnConnectBridge.classList.replace('btn-primary', 'btn-outline');
                updateStatus('Hardware Bridge disconnected.', 'info');
                scanBtnText.innerText = 'Scan Tag';
                scanBtn.disabled = false;
            };

            bridgeSocket.onerror = function() {
                updateStatus('Bridge connection failed.', 'danger');
                btnConnectBridge.innerHTML = '<i class="ph ph-broadcast"></i> Connect Bridge';
            };
        });

        scanBtn.addEventListener('click', function() {
            if (isBridgeMode) return;
            btnConnectBridge.click();
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Comprehensive Tag Validation
            if (registrationMode === 'auto' && scanStep < 2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Verification Required',
                    text: 'Please complete the double-scan verification before submitting.',
                    confirmButtonColor: '#1e293b'
                });
                return;
            }
            
            if (registrationMode === 'manual' && (!rfidInput.value || manualTagInput.value !== confirmTagInput.value)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Manual Entry Incomplete',
                    text: 'Please ensure the Tag IDs match and are correctly entered.',
                    confirmButtonColor: '#1e293b'
                });
                return;
            }

            if (!rfidInput.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Tag ID',
                    text: 'Please assign an RFID tag before submitting.',
                    confirmButtonColor: '#1e293b'
                });
                return;
            }

            // Collect form data
            const formData = new FormData(this);
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            const isEdit = editMode && registrationData;
            const submitUrl = isEdit
                ? `{{ url('office/registration') }}/${registrationData?.id}`
                : `{{ route('office.registration.store') }}`;
            
            if (isEdit) {
                formData.append('_method', 'PUT');
            }
            
            // Submit via AJAX
            fetch(submitUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to submit registration');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const fname = document.getElementsByName('firstName')[0]?.value || '';
                    const lname = document.getElementsByName('lastName')[0]?.value || '';
                    const nameVal = (fname + ' ' + lname).trim() || 'User';
                    
                    const successMsg = isEdit
                        ? ('Registration for ' + nameVal + ' updated successfully.')
                        : ('Registration for ' + nameVal + ' completed successfully and is now active.');
                    
                    Swal.fire({
                        icon: 'success',
                        title: isEdit ? 'Update Successful' : 'Registration Complete',
                        text: successMsg,
                        confirmButtonColor: '#10b981'
                    });

                    if (!isEdit) {
                        this.reset();
                        resetScanner();
                    }
                    showToast(successMsg);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Error',
                        text: (data.message || 'Failed to submit registration'),
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Error submitting registration: ' + error.message,
                    confirmButtonColor: '#ef4444'
                });
            });
        });


        // Prefill when editing
        function prefillForm() {
            if (!editMode || !registrationData) return;
            // role
            const roleRadio = document.querySelector(`input[name="role"][value="${registrationData.role}"]`);
            if (roleRadio) {
                roleRadio.checked = true;
                roleRadio.dispatchEvent(new Event('change'));
            }

            // Handle name splitting if prefilling
            if (registrationData.full_name) {
                const parts = registrationData.full_name.split(' ');
                if (parts.length >= 2) {
                    const fNameInput = document.getElementsByName('firstName')[0];
                    const lNameInput = document.getElementsByName('lastName')[0];
                    const mNameInput = document.getElementsByName('middleName')[0];
                    
                    if (fNameInput) fNameInput.value = parts[0];
                    if (lNameInput) lNameInput.value = parts[parts.length - 1];
                    if (mNameInput && parts.length > 2) {
                        mNameInput.value = parts.slice(1, -1).join(' ');
                    }
                } else {
                    const fNameInput = document.getElementsByName('firstName')[0];
                    if (fNameInput) fNameInput.value = registrationData.full_name;
                }
            }

            const map = {
                universityId: 'university_id',
                collegeDept: 'college_dept',
                contactNumber: 'contact_number',
                emailAddress: 'email_address',
                course: 'course',
                yearLevel: 'year_level',
                rank: 'rank',
                office: 'office',
                businessStallName: 'business_stall_name',
                vendorAddress: 'vendor_address',
                vehicleType: 'vehicle_type',
                registeredOwner: 'registered_owner',
                makeBrand: 'make_brand',
                modelYear: 'model_year',
                color: 'color',
                plateNumber: 'plate_number',
                engineNumber: 'engine_number',
                validityFrom: 'validity_from',
                validityTo: 'validity_to',
                rfidTagId: 'rfid_tag_id',
            };
            Object.keys(map).forEach(name => {
                const field = document.querySelector(`[name="${name}"]`);
                if (field && registrationData[map[name]] !== null) {
                    field.value = registrationData[map[name]];
                }
            });
            // vehicle type radios
            const vRadio = document.querySelector(`input[name="vehicleType"][value="${registrationData.vehicle_type}"]`);
            if (vRadio) vRadio.checked = true;

            // sticker_classification
            if (Array.isArray(registrationData.sticker_classification)) {
                registrationData.sticker_classification.forEach(val => {
                    const cb = document.querySelector(`input[name="stickerClassification[]"][value="${val}"]`);
                    if (cb) cb.checked = true;
                });
            }
            // requirements
            if (Array.isArray(registrationData.requirements)) {
                registrationData.requirements.forEach(val => {
                    const cb = document.querySelector(`input[name="requirements[]"][value="${val}"]`);
                    if (cb) cb.checked = true;
                });
            }
            // UI tweaks
            rfidInput.style.background = '#ecfdf5';
            rfidInput.style.borderColor = '#10b981';
            submitButton.innerHTML = '<i class="ph ph-pencil"></i> Update Registration';
            updateStatus('Edit mode: loaded existing registration.', 'info');
        }

        prefillForm();

        // Document Validation for Office
        document.querySelectorAll('.doc-input').forEach(input => {
            input.addEventListener('change', async function() {
                const file = this.files[0];
                if (!file) return;

                const type = this.dataset.type;
                const formGroup = this.closest('.form-group');
                const errorDiv = formGroup.querySelector('.validation-error');
                
                formGroup.classList.add('scanning');
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
                this.style.borderColor = '#e2e8f0';

                const formData = new FormData();
                formData.append('file', file);
                formData.append('type', type);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const response = await fetch('{{ route("online-registration.validate") }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        this.style.borderColor = '#10b981';
                        showToast('Document Validated!', 'success');
                        // Auto-check the corresponding requirement if scan matches
                        const reqVal = type === 'cr_file' ? 'certificateOfRegistration' : 
                                     type === 'or_file' ? 'officialReceipt' : 
                                     type === 'license_file' ? 'driversLicense' : '';
                        if (reqVal) {
                            const cb = document.querySelector(`input[name="requirements[]"][value="${reqVal}"]`);
                            if (cb) cb.checked = true;
                        }
                    } else {
                        errorDiv.textContent = data.message;
                        errorDiv.style.display = 'block';
                        this.value = ''; 
                        this.style.borderColor = '#ef4444';
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Document',
                            text: data.message,
                            confirmButtonColor: '#741b1b'
                        });
                    }
                } catch (err) {
                    console.error('Validation error:', err);
                } finally {
                    formGroup.classList.remove('scanning');
                }
            });
        });
    });
</script>

<style>
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .tag-scanner-box {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 12px;
        border: 2px dashed #e2e8f0;
    }
    .text-success { color: #10b981; }
    .text-warning { color: #f59e0b; }
</style>
@endsection
@endsection
