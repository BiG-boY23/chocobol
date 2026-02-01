<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleRegistration;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing');
    }

    public function showRegistrationForm()
    {
        return view('online-registration');
    }

    public function submitRegistration(Request $request)
    {
        // 1. Base Validation
        $rules = [
            'role' => 'required|in:student,faculty,staff',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'contact_number' => 'required|string|max:20',
            'email_address' => 'nullable|email|max:255',
            'vehicle_type' => 'required|in:car,suv,van,motorcycle',
            'make_brand' => 'required|string|max:255',
            'plate_number' => 'required|string|max:20',
            // File validation
            'cr_file' => 'required|image|max:5120',
            'or_file' => 'required|image|max:5120',
            'license_file' => 'required|image|max:5120',
        ];

        // 2. Role-specific Validation
        if ($request->role === 'student') {
            $rules['student_id'] = 'required|string|max:50';
            $rules['course'] = 'required|string|max:100';
            $rules['college_dept'] = 'required|string|max:100';
            $rules['year_level'] = 'required|string|max:10';
            $rules['access_classification'] = 'required|string';
            $rules['com_file'] = 'required|image|max:5120';
            $rules['student_id_file'] = 'required|image|max:5120';
        } elseif ($request->role === 'faculty') {
            $rules['faculty_id'] = 'required|string|max:50';
            $rules['college_dept_faculty'] = 'required|string|max:100';
            $rules['address'] = 'required|string|max:255';
            $rules['access_classification_faculty'] = 'required|string';
            $rules['employee_id_file'] = 'required|image|max:5120';
        } elseif ($request->role === 'staff') {
            $rules['business_stall_name'] = 'required|string|max:255';
            $rules['access_classification_staff'] = 'required|string';
            $rules['employee_id_file'] = 'required|image|max:5120';
        }

        $request->validate($rules);

        // 3. Handle File Uploads
        $paths = [];
        $files = [
            'cr_file' => 'cr_path',
            'or_file' => 'or_path',
            'license_file' => 'license_path',
            'com_file' => 'com_path',
            'student_id_file' => 'student_id_path',
            'employee_id_file' => 'employee_id_path',
            'payment_receipt_file' => 'payment_receipt_path',
        ];

        foreach ($files as $input => $column) {
            if ($request->hasFile($input)) {
                $file = $request->file($input);
                $filename = time() . '_' . $input . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('registrations', $filename, 'public');
                $paths[$column] = $path;
            }
        }

        // 4. Prepare Database Entry
        $fullName = trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name);
        
        $data = [
            'role' => $request->role,
            'full_name' => $fullName,
            'contact_number' => $request->contact_number,
            'email_address' => $request->email_address,
            'vehicle_type' => $request->vehicle_type,
            'make_brand' => $request->make_brand,
            'plate_number' => $request->plate_number,
            'status' => 'pending',
            'registered_owner' => $fullName,
        ];

        // Specific mappings
        if ($request->role === 'student') {
            $data['university_id'] = $request->student_id;
            $data['course'] = $request->course;
            $data['college_dept'] = $request->college_dept;
            $data['year_level'] = $request->year_level;
            $data['sticker_classification'] = [$request->access_classification];
        } elseif ($request->role === 'faculty') {
            $data['university_id'] = $request->faculty_id;
            $data['college_dept'] = $request->college_dept_faculty;
            $data['office'] = $request->address;
            $data['sticker_classification'] = [$request->access_classification_faculty];
        } elseif ($request->role === 'staff') {
            $data['university_id'] = 'N/A';
            $data['business_stall_name'] = $request->business_stall_name;
            $data['sticker_classification'] = [$request->access_classification_staff];
        }

        // Merge file paths
        $data = array_merge($data, $paths);
        
        VehicleRegistration::create($data);

        return redirect()->route('landing')->with('success', 'Application submitted! Please visit the office for document verification and RFID tag issuance.');
    }
}
