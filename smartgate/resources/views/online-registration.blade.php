<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Registration | SmartGate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #741b1b;
            --primary-light: #8b2d2d;
            --secondary: #fdb913;
            --bg-light: #fdfdfc;
            --text-dark: #1b1b18;
            --text-muted: #706f6c;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-left: 0 !important;
        }

        .container {
            max-width: 800px;
            margin: 4rem auto;
            padding: 0 2rem;
            width: 100%;
        }

        .registration-card {
            background: white;
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f1f0;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }

        .header p {
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        label {
            display: block;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        input, select, textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            border: 1px solid #e3e3e0;
            font-family: inherit;
            font-size: 1rem;
            transition: var(--transition);
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(116, 27, 27, 0.1);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: white;
            padding: 1rem;
            border: none;
            border-radius: var(--radius);
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(116, 27, 27, 0.2);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--primary);
        }

        .dynamic-field-section {
            margin-bottom: 2rem;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-divider {
            margin: 2.5rem 0 1.5rem;
            border-top: 1px solid #f1f1f0;
            padding-top: 1.5rem;
        }

        .section-title {
            margin-bottom: 1.5rem;
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .registration-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('landing') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>

        <div class="registration-card">
            <div class="header">
                <h1>Online Registration</h1>
                <p>Register your vehicle to apply for an RFID tag.</p>
            </div>

            @if ($errors->any())
                <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 1rem; margin-bottom: 2rem; border-radius: 8px; font-size: 0.9rem;">
                    <p style="font-weight: 700; margin-bottom: 0.5rem;">Please fix the following errors:</p>
                    <ul style="margin-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('online-registration.submit') }}" method="POST" enctype="multipart/form-data" id="registration-form">
                @csrf
                
                <div class="form-group">
                    <label>Registration Role</label>
                    <select name="role" id="role-selector" required>
                        <option value="" disabled selected>None</option>
                        <option value="student">Student</option>
                        <option value="faculty">Staff / EVSU Personnel (Teaching)</option>
                        <option value="staff">Non-Teaching / Vendor</option>
                    </select>
                </div>

                <!-- COMMON NAME FIELDS -->
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" placeholder="Juan" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" placeholder="Dela Cruz" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Middle Name (Optional)</label>
                    <input type="text" name="middle_name" placeholder="Protacio">
                </div>

                <!-- DYNAMIC SECTIONS -->
                <div id="student-fields" class="dynamic-field-section" style="display:none;">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" name="student_id" placeholder="2024-XXXXX">
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <input type="text" name="course" placeholder="BSIT / BSCE">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>College / Department</label>
                            <input type="text" name="college_dept" placeholder="COICT / COE">
                        </div>
                        <div class="form-group">
                            <label>Year Level</label>
                            <select name="year_level">
                                <option value="" disabled selected>None</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                                <option value="5">5th Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Access Classification</label>
                        <select name="access_classification">
                            <option value="student">Student Access</option>
                        </select>
                    </div>
                </div>

                <div id="faculty-fields" class="dynamic-field-section" style="display:none;">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Faculty ID</label>
                            <input type="text" name="faculty_id" placeholder="F-XXXXX">
                        </div>
                        <div class="form-group">
                            <label>College / Department</label>
                            <input type="text" name="college_dept_faculty" placeholder="COICT / COE">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" placeholder="Ormoc City">
                    </div>
                    <div class="form-group">
                        <label>Access Classification</label>
                        <select name="access_classification_faculty">
                            <option value="faculty">Faculty Access</option>
                            <option value="service">Service Vehicle</option>
                        </select>
                    </div>
                </div>

                <div id="staff-fields" class="dynamic-field-section" style="display:none;">
                    <div class="form-group">
                        <label>Business / Stall Name</label>
                        <input type="text" name="business_stall_name" placeholder="EVSU Canteen Stall 1">
                    </div>
                    <div class="form-group">
                        <label>Access Classification</label>
                        <select name="access_classification_staff">
                            <option value="staff">Non-Teaching Access</option>
                            <option value="service">Service Vehicle</option>
                        </select>
                    </div>
                </div>

                <!-- COMMON CONTACT FIELDS -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" placeholder="09XXXXXXXXX" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email_address" placeholder="juan@example.com">
                    </div>
                </div>

                <div class="section-divider">
                    <h3 class="section-title">Vehicle Information</h3>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Vehicle Type</label>
                        <select name="vehicle_type" required>
                            <option value="" disabled selected>None</option>
                            <option value="car">Car</option>
                            <option value="suv">SUV</option>
                            <option value="van">Van</option>
                            <option value="motorcycle">Motorcycle</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Plate Number</label>
                        <input type="text" name="plate_number" placeholder="ABC 1234" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Make / Brand</label>
                    <input type="text" name="make_brand" placeholder="e.g., Toyota Vios" required>
                </div>

                <div class="section-divider">
                    <h3 class="section-title">Required Documents</h3>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Vehicle CR</label>
                        <input type="file" name="cr_file" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label>Vehicle OR</label>
                        <input type="file" name="or_file" accept="image/*" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Valid Driver's License</label>
                    <input type="file" name="license_file" accept="image/*" required>
                </div>

                <div id="role-file-uploads">
                    <!-- Dynamic file uploads based on role -->
                </div>

                <button type="submit" class="btn-submit">Submit Registration</button>
            </form>

            <script>
                const roleSelector = document.getElementById('role-selector');
                const studentFields = document.getElementById('student-fields');
                const facultyFields = document.getElementById('faculty-fields');
                const staffFields = document.getElementById('staff-fields');
                const roleFileUploads = document.getElementById('role-file-uploads');

                function updateFields() {
                    const role = roleSelector.value;
                    studentFields.style.display = role === 'student' ? 'block' : 'none';
                    facultyFields.style.display = role === 'faculty' ? 'block' : 'none';
                    staffFields.style.display = role === 'staff' ? 'block' : 'none';

                    // Update required attributes
                    const studentInputs = studentFields.querySelectorAll('input, select');
                    const facultyInputs = facultyFields.querySelectorAll('input, select');
                    const staffInputs = staffFields.querySelectorAll('input, select');

                    studentInputs.forEach(i => i.required = (role === 'student'));
                    facultyInputs.forEach(i => i.required = (role === 'faculty'));
                    staffInputs.forEach(i => i.required = (role === 'staff'));

                    // Update shared inputs (Email might be required for some)
                    // (User said email is mentioned for student and non-teaching, but email_address is common now)

                    // Update Dynamic File Uploads
                    let fileHtml = '';
                    if (role === 'student') {
                        fileHtml = `
                            <div class="form-row">
                                <div class="form-group">
                                    <label>COM (Cert. of Matriculation)</label>
                                    <input type="file" name="com_file" accept="image/*" required>
                                </div>
                                <div class="form-group">
                                    <label>EVSU Student ID</label>
                                    <input type="file" name="student_id_file" accept="image/*" required>
                                </div>
                            </div>`;
                    } else if (role === 'faculty' || role === 'staff') {
                        fileHtml = `
                            <div class="form-group">
                                <label>EVSU Employee ID</label>
                                <input type="file" name="employee_id_file" accept="image/*" required>
                            </div>`;
                    }
                    roleFileUploads.innerHTML = fileHtml;
                }

                roleSelector.addEventListener('change', updateFields);
                updateFields(); // Initial call
            </script>
        </div>
    </div>
</body>
</html>
