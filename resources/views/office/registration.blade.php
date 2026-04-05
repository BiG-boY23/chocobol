@extends('layouts.app')

@section('title', 'Vehicle Owner Registration')
@section('subtitle', 'Register new vehicle owners and assign RFID tags for system access.')

@section('content')
<div class="table-container">
    <form id="registerForm" class="registration-form">
        @csrf
        
        <!-- ================= INSTITUTIONAL ROLE SELECTION ================= -->
        <section class="form-section">
            <h2 class="section-title">
                <i class="ph ph-identification-card"></i> Institutional Role
            </h2>

            <div class="form-group mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select the applicant's role to proceed <span style="color:red">*</span></label>
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
        </section>

        <!-- Hidden container that appears only after role selection -->
        <div id="registration-details-container" style="display: none;">
            
            <hr class="section-divider">

            <!-- ================= APPLICANT INFORMATION ================= -->
            <section class="form-section">
                <h2 class="section-title">
                    <i class="ph ph-user"></i> Applicant Details
                </h2>

                <div class="form-grid">
                    <div class="form-field">
                        <label class="field-label">First Name</label>
                        <input type="text" name="first_name" placeholder="First Name" required>
                    </div>
                    <div class="form-field">
                        <label class="field-label">Last Name</label>
                        <input type="text" name="last_name" placeholder="Last Name" required>
                    </div>
                    <div class="form-field">
                        <label class="field-label">Middle Name (Optional)</label>
                        <input type="text" name="middle_name" placeholder="Middle Name">
                    </div>
                    <div class="form-field">
                        <label class="field-label">Contact Number</label>
                        <input type="text" name="contact_number" placeholder="09XXXXXXXXX" required>
                    </div>
                    <div class="form-field md-col-2">
                        <label class="field-label">Email Address</label>
                        <input type="email" name="email_address" placeholder="email@example.com" required>
                    </div>
                </div>

                <!-- Role-Specific Fields Area -->
                <div id="dynamic-applicant-fields" class="mt-4">
                    <!-- Injected via JavaScript -->
                </div>
            </section>

            <hr class="section-divider">

            <!-- ================= VEHICLE INFORMATION ================= -->
            <section class="form-section">
                <h2 class="section-title">
                    <i class="ph ph-car"></i> Vehicle Identity
                </h2>

                <div class="form-grid">
                    <div class="form-field">
                        <label class="field-label">Vehicle Category <span style="color:red">*</span></label>
                        <select name="vehicle_type" id="office-category-selector" required>
                            <option value="" disabled selected>Select Category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field">
                        <label class="field-label">Brand <span style="color:red">*</span></label>
                        <div style="position: relative;">
                            <select name="make_brand" id="office-brand-selector" required disabled>
                                <option value="" disabled selected>Select Category First...</option>
                            </select>
                            <div id="office-brand-loader" style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                <i class="ph ph-circle-notch animate-spin text-primary"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="field-label">Specific Model <span style="color:red">*</span></label>
                        <div style="position: relative;">
                            <select name="model_name" id="office-model-selector" required disabled>
                                <option value="" disabled selected>Select Brand First...</option>
                            </select>
                            <div id="office-model-loader" style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                <i class="ph ph-circle-notch animate-spin text-primary"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="field-label">Plate Number <span style="color:red">*</span></label>
                        <input type="text" name="plate_number" placeholder="ABC 1234" required style="text-transform: uppercase;">
                    </div>
                </div>
            </section>

            <hr class="section-divider">

            <!-- ================= DIGITAL VERIFICATION CHECKLIST ================= -->
            <section class="form-section">
                <h2 class="section-title">
                    <i class="ph ph-shield-check"></i> Physical Verification Checklist
                </h2>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: -10px; margin-bottom: 20px;">Staff must manually verify these documents. Scans are not stored to comply with Data Privacy regulations.</p>

                <div class="verification-grid">
                    <label class="v-card">
                        <input type="checkbox" name="verified_cr" required>
                        <div class="v-content">
                            <i class="ph ph-file-text"></i>
                            <span>Vehicle CR Verified</span>
                        </div>
                    </label>
                    <label class="v-card">
                        <input type="checkbox" name="verified_or" required>
                        <div class="v-content">
                            <i class="ph ph-receipt"></i>
                            <span>Vehicle OR Verified</span>
                        </div>
                    </label>
                    <label class="v-card">
                        <input type="checkbox" name="verified_license" required>
                        <div class="v-content">
                            <i class="ph ph-identification-card"></i>
                            <span>Driver's License Verified</span>
                        </div>
                    </label>
                    <label class="v-card" id="role-verification-item">
                        <input type="checkbox" name="verified_institutional" required>
                        <div class="v-content">
                            <i class="ph ph-student"></i>
                            <span id="role-v-label">Institutional ID Verified</span>
                        </div>
                    </label>
                </div>
            </section>

            <hr class="section-divider">

            <!-- ================= VALIDITY ================= -->
            <section class="form-section">
                <h2 class="section-title">
                    <i class="ph ph-calendar-check"></i> Validity Interval
                </h2>
                <div class="form-grid">
                    <div class="form-field">
                        <label class="field-label">Valid From</label>
                        <input type="date" name="validity_from" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-field">
                        <label class="field-label">Valid Until</label>
                        <input type="date" name="validity_to" value="{{ date('Y-m-d', strtotime('+1 year')) }}" required>
                    </div>
                </div>
            </section>

            <hr class="section-divider">

            <!-- ================= RFID TAG ASSIGNMENT ================= -->
            <section class="form-section">
                <h2 class="section-title">
                    <i class="ph ph-broadcast"></i> RFID Tag Assignment
                </h2>
                
                <div class="tag-assignment-container" style="background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem;">
                        <button type="button" id="modeAuto" class="btn btn-outline active" style="flex:1">Automatic Mode</button>
                        <button type="button" id="modeManual" class="btn btn-outline" style="flex:1">Manual Mode</button>
                    </div>

                    <div id="autoModeContainer">
                        <div class="form-field">
                            <label class="field-label">RFID Tag ID (Scanned)</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" name="rfid_tag_id" id="rfidTagId" placeholder="Waiting for tag scan..." readonly style="flex-grow:1; background:#f1f5f9;">
                                <button type="button" id="scanBtn" class="btn btn-primary" style="padding: 0 1.5rem;">
                                    <i class="ph ph-scan"></i> <span id="scanBtnText">Scan Tag</span>
                                </button>
                            </div>
                            <p id="scannerStatus" style="font-size: 0.75rem; color: #64748b; margin-top: 8px;">Hardware scanner ready.</p>
                        </div>
                    </div>

                    <div id="manualModeContainer" style="display:none">
                        <div class="form-grid">
                            <div class="form-field">
                                <label class="field-label">Manual Tag ID</label>
                                <input type="text" id="manualRfidTagId" placeholder="Enter ID">
                            </div>
                            <div class="form-field">
                                <label class="field-label">Confirm Tag ID</label>
                                <input type="text" id="confirmRfidTagId" placeholder="Repeat ID">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="form-actions mt-8">
                <button type="submit" class="btn btn-primary w-full justify-center" style="height: 54px; font-size: 1.1rem; font-weight: 700;">
                    <i class="ph ph-check-circle"></i> Complete Owner Registration
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .form-section { margin-bottom: 2rem; }
    .section-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; }
    .role-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .role-option { display: flex; align-items: center; gap: 0.75rem; padding: 1.2rem 1rem; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: 0.3s; font-weight: 700; color: #64748b; }
    .role-option:hover { border-color: #cbd5e1; background: #f1f5f9; }
    .role-option input { width: 18px; height: 18px; accent-color: #741b1b; }
    .role-option input:checked + span { color: #741b1b; }
    .role-option:has(input:checked) { border-color: #741b1b; background: #fffcfc; color: #741b1b; box-shadow: 0 4px 12px rgba(116, 27, 27, 0.05); }
    
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .form-field { margin-bottom: 0.5rem; }
    .field-label { display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; }
    .md-col-2 { grid-column: span 2; }
    .section-divider { border: 0; border-top: 2px solid #f1f5f9; margin: 2.5rem 0; }
    
    input, select { width: 100%; padding: 0.85rem; border-radius: 8px; border: 1px solid #e2e8f0; outline: none; font-weight: 600; transition: border-color 0.2s; }
    input:focus, select:focus { border-color: #741b1b; box-shadow: 0 0 0 3px rgba(116, 27, 27, 0.05); }

    /* Verification Cards */
    .verification-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    .v-card { position: relative; cursor: pointer; }
    .v-card input { position: absolute; opacity: 0; width: 0; height: 0; }
    .v-content { background: white; border: 2px solid #e2e8f0; border-radius: 12px; padding: 1.5rem 1rem; display: flex; flex-direction: column; align-items: center; gap: 10px; transition: 0.2s; text-align: center; }
    .v-content i { font-size: 1.5rem; color: #94a3b8; }
    .v-content span { font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; line-height: 1.2; }
    .v-card input:checked + .v-content { background: #f0fdf4; border-color: #10b981; }
    .v-card input:checked + .v-content i { color: #10b981; }
    .v-card input:checked + .v-content span { color: #10b981; }

    .btn-outline.active { background: #1e293b; color: white; border-color: #1e293b; }
    .animate-spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

@endsection

@section('scripts')
<script>
    const collegesData = @json($colleges);

    document.addEventListener('DOMContentLoaded', function() {
        const roleRadios = document.querySelectorAll('input[name="role"]');
        const dynamicFields = document.getElementById('dynamic-applicant-fields');
        const regContainer = document.getElementById('registration-details-container');
        const roleVLabel = document.getElementById('role-v-label');
        const roleVIcon = document.querySelector('#role-verification-item i');
        const mainForm = document.getElementById('registerForm');

        // Role Dynamics
        const populateForm = (data) => {
            // Sync Institutional Role (Radio buttons) FIRST to generate dynamic DOM structure
            if (data.role) {
                const roleInput = mainForm.querySelector(`input[name="role"][value="${data.role}"]`);
                if (roleInput) { 
                    roleInput.checked = true; 
                    roleInput.dispatchEvent(new Event('change')); 
                }
            }

            // After role change, populate dynamic and standard fields
            setTimeout(() => {
                // Formatting
                let fN = data.first_name || '';
                let lN = data.last_name || '';
                let mN = data.middle_name || '';
                
                // Fallback for older records stored before first_name/last_name columns
                if (!fN && data.full_name) {
                    const parts = data.full_name.trim().split(' ');
                    lN = parts.pop(); // Last element is last name
                    fN = parts.shift() || ''; // First element is first name
                    if(parts.length > 0) mN = parts.join(' ');
                }

                // Date field correction for <input type="date"> which requires strict YYYY-MM-DD
                let vFrom = data.validity_from ? data.validity_from.split('T')[0] : '';
                let vTo = data.validity_to ? data.validity_to.split('T')[0] : '';

                // Mapping standard fields including dynamically created university_id
                const fieldsMap = {
                    'first_name': fN,
                    'last_name': lN,
                    'middle_name': mN,
                    'contact_number': data.contact_number,
                    'email_address': data.email_address,
                    'university_id': data.university_id,
                    'plate_number': data.plate_number,
                    'validity_from': vFrom,
                    'validity_to': vTo,
                };

                for (const [name, val] of Object.entries(fieldsMap)) {
                    const input = mainForm.querySelector(`[name="${name}"]`);
                    if (input) input.value = val || '';
                }

                if (data.role === 'student') {
                    if (data.college_dept) {
                        const sel = document.getElementById('college-selector');
                        if (sel) { sel.value = data.college_dept; sel.dispatchEvent(new Event('change')); }
                    }
                    setTimeout(() => {
                        const cSel = document.getElementById('course-selector');
                        if (cSel) cSel.value = data.course || '';
                    }, 500); // Wait for courses to load based on college
                    const ySel = mainForm.querySelector('select[name="year_level"]');
                    if (ySel) ySel.value = data.year_level || '';
                } else if (data.role === 'faculty') {
                    const dSel = mainForm.querySelector('select[name="college_dept"]');
                    if (dSel) dSel.value = data.college_dept || '';
                } else if (data.role === 'staff') {
                    const biz = mainForm.querySelector('input[name="business_stall_name"]');
                    if (biz) biz.value = data.business_stall_name || '';
                    const loc = mainForm.querySelector('input[name="vendor_address"]');
                    if (loc) loc.value = data.vendor_address || '';
                }

                // Vehicle details population
                if (data.vehicle_type) {
                    const catSel = document.getElementById('office-category-selector');
                    if (catSel) {
                        catSel.value = data.vehicle_type;
                        catSel.dispatchEvent(new Event('change'));
                        
                        setTimeout(() => {
                            const brandSel = document.getElementById('office-brand-selector');
                            if (brandSel) {
                                brandSel.value = data.make_brand;
                                brandSel.dispatchEvent(new Event('change'));
                            }
                            
                            setTimeout(() => {
                                const modelSel = document.getElementById('office-model-selector');
                                if (modelSel) {
                                    modelSel.value = data.model_name;
                                }
                            }, 500);
                        }, 500);
                    }
                }

                // Populate RFID tag using manual mode layout
                if (data.rfid_tag_id) {
                    const manualModeBtn = document.getElementById('modeManual');
                    if (manualModeBtn) manualModeBtn.click();
                    
                    setTimeout(() => {
                        const mRfid = document.getElementById('manualRfidTagId');
                        const cRfid = document.getElementById('confirmRfidTagId');
                        if (mRfid && cRfid) {
                            mRfid.value = data.rfid_tag_id;
                            cRfid.value = data.rfid_tag_id;
                            mRfid.dispatchEvent(new Event('input'));
                        }
                    }, 100);
                }

                // Auto-check all document verification checkboxes 
                // Since this user was already previously evaluated, skip re-evaluating papers on edit
                if (data.id || data.rfid_tag_id) {
                    const verifyChecks = mainForm.querySelectorAll('.v-card input[type="checkbox"]');
                    verifyChecks.forEach(chk => {
                        chk.checked = true;
                    });
                }

            }, 200);
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Data Found & Populated!', showConfirmButton: false, timer: 1500 });
        };

        const fetchExistingData = async (uid) => {
            if (!uid) return;
            Swal.fire({ title: 'Searching...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            try {
                const res = await fetch(`/office/registration/fetch-user/${encodeURIComponent(uid)}`);
                const result = await res.json();
                if (result.success) { populateForm(result.data); }
                else { Swal.fire('No History', result.message, 'info'); }
            } catch (err) { Swal.fire('Error', 'Fetch failed.', 'error'); }
        };

        roleRadios.forEach(radio => {
            radio.onchange = () => {
                const role = radio.value;
                regContainer.style.display = 'block';
                regContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

                let html = '';
                if (role === 'student' || role === 'faculty') {
                    const idLabel = role === 'student' ? 'Student ID Number' : 'Faculty ID Number';
                    html = `<div class="form-grid">
                        <div class="form-field">
                            <label class="field-label">${idLabel.toUpperCase()}</label>
                            <div style="display: flex; gap: 5px;">
                                <input type="text" name="university_id" id="search-id-input" placeholder="Enter ID..." required style="flex-grow:1">
                                <button type="button" id="fetch-search-btn" class="btn btn-outline" style="padding: 0 0.75rem;"><i class="ph ph-magnifying-glass"></i></button>
                            </div>
                        </div>`;
                    
                    if (role === 'student') {
                        html += `<div class="form-field">
                             <label class="field-label">College / Department</label>
                             <select name="college_dept" id="college-selector" required>
                                <option value="" disabled selected>Select College...</option>
                                ${collegesData.map(c => `<option value="${c.name}">${c.name}</option>`).join('')}
                            </select>
                        </div>
                        <div class="form-field"><label class="field-label">Course</label><select name="course" id="course-selector" required><option value="" disabled selected>Select College First...</option></select></div>
                        <div class="form-field"><label class="field-label">Year Level</label><select name="year_level" required><option value="" disabled selected>Select Year...</option><option>1st Year</option><option>2nd Year</option><option>3rd Year</option><option>4th Year</option></select></div>`;
                        roleVLabel.innerText = "COR / ENROLLMENT VERIFIED";
                        roleVIcon.className = "ph ph-certificate";
                    } else {
                        html += `<div class="form-field md-col-2">
                             <label class="field-label">Academic Department</label>
                             <select name="college_dept" required>
                                 <option value="" disabled selected>Select Department...</option>
                                 ${collegesData.map(c => `<option value="${c.name}">${c.name}</option>`).join('')}
                             </select>
                        </div>`;
                        roleVLabel.innerText = "EMPLOYEE ID VERIFIED";
                        roleVIcon.className = "ph ph-briefcase";
                    }
                    html += `</div>`;
                } else {
                    html = `<div class="form-grid">
                        <div class="form-field"><label class="field-label">Business / Stall Name</label><input type="text" name="business_stall_name" required></div>
                        <div class="form-field"><label class="field-label">Stall Location</label><input type="text" name="vendor_address" required></div>
                    </div>`;
                    roleVLabel.innerText = "BUSINESS PERMIT VERIFIED";
                    roleVIcon.className = "ph ph-storefront";
                }

                dynamicFields.innerHTML = html;

                const searchBtn = document.getElementById('fetch-search-btn');
                const searchInput = document.getElementById('search-id-input');
                if (searchBtn && searchInput) {
                    searchBtn.onclick = (e) => { e.preventDefault(); e.stopPropagation(); fetchExistingData(searchInput.value.trim()); };
                    searchInput.onkeypress = (e) => { if (e.key === 'Enter') { e.preventDefault(); fetchExistingData(searchInput.value.trim()); } };
                }
            };
        });

        // Trigger auto-population if editing an existing registration
        @if(isset($registration) && $registration)
            const editData = @json($registration);
            setTimeout(() => {
                // Populate the form with the existing data
                populateForm(editData);
            }, 500); // Small delay to ensure DOM and listeners are ready
        @endif

        // College Chained Dropdown - Refactored
        document.body.addEventListener('change', (e) => {
            if (e.target.id === 'college-selector') {
                const collegeName = e.target.value;
                const college = collegesData.find(c => c.name === collegeName);
                const courses = college ? college.courses : [];
                
                const courseSelector = document.getElementById('course-selector');
                if (courseSelector) {
                    courseSelector.innerHTML = '<option value="" disabled selected>Select Course...</option>';
                    courses.forEach(c => {
                        const opt = document.createElement('option'); opt.value = c.name; opt.innerText = c.name;
                        courseSelector.appendChild(opt);
                    });
                }
            }
        });

        // Vehicle Chained Dropdowns
        const catSel = document.getElementById('office-category-selector');
        const brandSel = document.getElementById('office-brand-selector');
        const modelSel = document.getElementById('office-model-selector');
        
        catSel.onchange = async function() {
            const catId = this.selectedOptions[0].dataset.id;
            brandSel.innerHTML = '<option value="" disabled selected>Loading...</option>';
            brandSel.disabled = true;
            document.getElementById('office-brand-loader').style.display = 'block';
            try {
                const res = await fetch(`/api/brands/${catId}`);
                const brands = await res.json();
                brandSel.innerHTML = '<option value="" disabled selected>Select Brand...</option>';
                brands.forEach(b => { brandSel.innerHTML += `<option value="${b.name}" data-id="${b.id}">${b.name}</option>`; });
                brandSel.innerHTML += `<option value="Other">Other</option>`;
                brandSel.disabled = false;
            } finally { document.getElementById('office-brand-loader').style.display = 'none'; }
        };

        brandSel.onchange = async function() {
            const brandId = this.selectedOptions[0].dataset.id;
            modelSel.innerHTML = '<option value="" disabled selected>Loading...</option>';
            modelSel.disabled = true;
            document.getElementById('office-model-loader').style.display = 'block';
            if(!brandId) { modelSel.innerHTML = '<option value="Other">Other</option>'; modelSel.disabled = false; document.getElementById('office-model-loader').style.display = 'none'; return; }
            try {
                const res = await fetch(`/api/models/${brandId}`);
                const models = await res.json();
                modelSel.innerHTML = '<option value="" disabled selected>Select Model...</option>';
                models.forEach(m => { modelSel.innerHTML += `<option value="${m.name}">${m.name}</option>`; });
                modelSel.disabled = false;
            } finally { document.getElementById('office-model-loader').style.display = 'none'; }
        };

        // RFID Scanner Logic
        const modeAuto = document.getElementById('modeAuto');
        const modeManual = document.getElementById('modeManual');
        const scanBtn = document.getElementById('scanBtn');
        const rfidInput = document.getElementById('rfidTagId');
        let bridgeSocket = null;

        modeAuto.onclick = () => { modeAuto.classList.add('active'); modeManual.classList.remove('active'); document.getElementById('autoModeContainer').style.display='block'; document.getElementById('manualModeContainer').style.display='none'; };
        modeManual.onclick = () => { modeManual.classList.add('active'); modeAuto.classList.remove('active'); document.getElementById('manualModeContainer').style.display='block'; document.getElementById('autoModeContainer').style.display='none'; };

        scanBtn.onclick = () => {
            if(bridgeSocket) { bridgeSocket.close(); bridgeSocket = null; document.getElementById('scanBtnText').innerText = "Scan Tag"; return; }
            bridgeSocket = new WebSocket('ws://localhost:8765');
            document.getElementById('scanBtnText').innerText = "Listening...";
            bridgeSocket.onmessage = (e) => {
                const data = JSON.parse(e.data);
                if(data.tag_id) {
                    rfidInput.value = data.tag_id;
                    Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Tag Captured!', showConfirmButton:false, timer:1500 });
                    bridgeSocket.close();
                }
            };
            bridgeSocket.onerror = () => { Swal.fire('Error', 'RFID Bridge not found.', 'error'); bridgeSocket = null; document.getElementById('scanBtnText').innerText = "Scan Tag"; };
        };

        // Manual Mode Sync logic
        const manualInput = document.getElementById('manualRfidTagId');
        const confirmInput = document.getElementById('confirmRfidTagId');
        
        const syncManual = () => {
            if(modeManual.classList.contains('active')) {
                if(manualInput.value === confirmInput.value && manualInput.value !== '') {
                    rfidInput.value = manualInput.value;
                } else {
                    rfidInput.value = ''; // keep empty if mismatch
                }
            }
        };

        manualInput.addEventListener('input', syncManual);
        confirmInput.addEventListener('input', syncManual);

        // Complete Submission
        mainForm.onsubmit = async (e) => {
            if(modeManual.classList.contains('active')) {
                if(manualInput.value !== confirmInput.value) {
                    Swal.fire('RFID Mismatch', 'Manual Tag ID and Confirmation do not match.', 'warning');
                    return false;
                }
                if(manualInput.value === '') {
                    Swal.fire('Missing Tag', 'Please enter an RFID Tag ID.', 'warning');
                    return false;
                }
                rfidInput.value = manualInput.value;
            }
            
            e.preventDefault();
            Swal.fire({ title:'Saving Registration...', text:'Recording verification and assigning tag...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
            
            const formData = new FormData(mainForm);
            
            @if(isset($registration) && $registration)
                formData.append('_method', 'PUT');
                const actionUrl = '{{ route('office.registration.update', $registration->id) }}';
            @else
                const actionUrl = '{{ route('office.registration.store') }}';
            @endif

            try {
                const res = await fetch(actionUrl, { 
                    method: 'POST', // Use POST here, Laravel handles _method for PUT
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await res.json();
                if(data.success) {
                    Swal.fire({ icon:'success', title:'Registration Saved', text: data.message }).then(() => { window.location.href = '{{ route('office.registration') }}'; });
                } else {
                    Swal.fire('Validation/Server Error', data.message || 'Check form fields and try again.', 'error');
                }
            } catch(e) { 
                console.error(e);
                Swal.fire('Submission Failed', 'Could not reach the server or invalid response. Please check your data and try again.', 'error'); 
            }
        };
    });
</script>
@endsection
