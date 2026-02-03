<?php

namespace App\Http\Controllers\Office;

use App\Http\Controllers\Controller;
use App\Models\VehicleRegistration;
use App\Models\VehicleLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = VehicleRegistration::count();
        $registeredToday = VehicleRegistration::whereDate('created_at', Carbon::today())->count();
        $activeVehicles = VehicleRegistration::where('status', 'approved')->count();
        $pendingRegistrations = VehicleRegistration::where('status', 'pending')->count();

        // Quick summary by role (student, faculty, staff)
        $roleCounts = VehicleRegistration::selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $summary = [
            'student' => [
                'label' => 'Students',
                'count' => $roleCounts['student'] ?? 0,
            ],
            'faculty' => [
                'label' => 'Faculty',
                'count' => $roleCounts['faculty'] ?? 0,
            ],
            'staff' => [
                'label' => 'Non-Teaching',
                'count' => $roleCounts['staff'] ?? 0,
            ],
        ];

        // Compute percentages safely
        foreach ($summary as $key => $item) {
            $summary[$key]['percent'] = $totalUsers > 0
                ? round(($item['count'] / $totalUsers) * 100)
                : 0;
        }

        return view('office.dashboard', compact('totalUsers', 'registeredToday', 'activeVehicles', 'summary', 'pendingRegistrations'));
    }

    public function registration(Request $request)
    {
        $registration = null;
        if ($request->has('id')) {
            $registration = VehicleRegistration::findOrFail($request->id);
        }
        return view('office.registration', compact('registration'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:student,faculty,staff',
            'firstName' => 'nullable|string|max:100',
            'lastName' => 'nullable|string|max:100',
            'fullName' => 'nullable|string|max:255', // fallback
            'universityId' => 'nullable|string|max:255',
            'collegeDept' => 'nullable|string|max:255',
            'contactNumber' => 'required|string|max:20',
            'emailAddress' => 'nullable|email|max:255',
            'vehicleType' => 'required|in:car,suv,van,motorcycle',
            'makeBrand' => 'required|string|max:255',
            'plateNumber' => 'required|string|max:20',
            'validityFrom' => 'required|date',
            'validityTo' => 'required|date|after:validityFrom',
            'rfidTagId' => 'required|string|unique:vehicle_registrations,rfid_tag_id',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $fullName = $request->fullName;
        if ($request->has('firstName') && $request->has('lastName')) {
            $fullName = trim($request->firstName . ' ' . ($request->middleName ?? '') . ' ' . $request->lastName);
            $fullName = preg_replace('/\s+/', ' ', $fullName);
        }

        $data = [
            'role' => $request->role,
            'full_name' => $fullName ?? 'N/A',
            'university_id' => $request->universityId ?? 'N/A',
            'college_dept' => $request->collegeDept ?? 'N/A',
            'contact_number' => $request->contactNumber,
            'email_address' => $request->emailAddress ?? 'N/A',
            'course' => $request->course,
            'year_level' => $request->yearLevel,
            'rank' => $request->rank,
            'office' => $request->office,
            'business_stall_name' => $request->businessStallName,
            'vendor_address' => $request->vendorAddress,
            'vehicle_type' => $request->vehicleType,
            'registered_owner' => $request->registeredOwner ?? 'N/A',
            'make_brand' => $request->makeBrand,
            'model_year' => $request->modelYear ?? 'N/A',
            'color' => $request->color ?? 'N/A',
            'plate_number' => $request->plateNumber,
            'engine_number' => $request->engineNumber ?? 'N/A',
            'sticker_classification' => $request->stickerClassification ?? [],
            'requirements' => $request->requirements ?? [],
            'validity_from' => $request->validityFrom,
            'validity_to' => $request->validityTo,
            'rfid_tag_id' => $request->rfidTagId,
            'status' => 'approved', // directly active, no admin approval needed
            'office_user_id' => Auth::id(),
        ];

        VehicleRegistration::create($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Registration completed successfully and is now active.'
            ]);
        }

        return redirect()->route('office.registration')
            ->with('success', 'Registration completed successfully and is now active.');
    }

    public function users()
    {
        $registrations = VehicleRegistration::orderByDesc('created_at')->get();
        return view('office.users', compact('registrations'));
    }

    /**
     * Show a single registration (JSON).
     */
    public function show($id)
    {
        $registration = VehicleRegistration::findOrFail($id);
        return response()->json(['success' => true, 'data' => $registration]);
    }

    /**
     * Update a registration using the same fields as create.
     */
    public function update(Request $request, $id)
    {
        $registration = VehicleRegistration::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:student,faculty,staff',
            'firstName' => 'nullable|string|max:100',
            'lastName' => 'nullable|string|max:100',
            'fullName' => 'nullable|string|max:255',
            'universityId' => 'nullable|string|max:255',
            'collegeDept' => 'nullable|string|max:255',
            'contactNumber' => 'required|string|max:20',
            'emailAddress' => 'nullable|email|max:255',
            'vehicleType' => 'required|in:car,suv,van,motorcycle',
            'makeBrand' => 'required|string|max:255',
            'plateNumber' => 'required|string|max:20',
            'validityFrom' => 'required|date',
            'validityTo' => 'required|date|after:validityFrom',
            'rfidTagId' => 'required|string|unique:vehicle_registrations,rfid_tag_id,' . $registration->id,
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $fullName = $request->fullName;
        if ($request->has('firstName') && $request->has('lastName')) {
            $fullName = trim($request->firstName . ' ' . ($request->middleName ?? '') . ' ' . $request->lastName);
            $fullName = preg_replace('/\s+/', ' ', $fullName);
        }

        $data = [
            'role' => $request->role,
            'full_name' => $fullName ?? $registration->full_name,
            'university_id' => $request->universityId ?? 'N/A',
            'college_dept' => $request->collegeDept ?? 'N/A',
            'contact_number' => $request->contactNumber,
            'email_address' => $request->emailAddress ?? 'N/A',
            'course' => $request->course,
            'year_level' => $request->yearLevel,
            'rank' => $request->rank,
            'office' => $request->office,
            'business_stall_name' => $request->businessStallName,
            'vendor_address' => $request->vendorAddress,
            'vehicle_type' => $request->vehicleType,
            'registered_owner' => $request->registeredOwner ?? 'N/A',
            'make_brand' => $request->makeBrand,
            'model_year' => $request->modelYear ?? 'N/A',
            'color' => $request->color ?? 'N/A',
            'plate_number' => $request->plateNumber,
            'engine_number' => $request->engineNumber ?? 'N/A',
            'sticker_classification' => $request->stickerClassification ?? [],
            'requirements' => $request->requirements ?? [],
            'validity_from' => $request->validityFrom,
            'validity_to' => $request->validityTo,
            'rfid_tag_id' => $request->rfidTagId,
            'status' => 'approved',
            'office_user_id' => Auth::id(),
        ];

        $registration->update($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Registration updated successfully.',
                'data' => $registration,
            ]);
        }

        return redirect()->route('office.registration', ['id' => $registration->id])
            ->with('success', 'Registration updated successfully.');
    }

    /**
     * Delete a registration.
     */
    public function destroy($id)
    {
        $registration = VehicleRegistration::findOrFail($id);
        $registration->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registration deleted.',
        ]);
    }

    public function stats()
    {
        // 1. Total Entries and Exits
        $totalEntries = VehicleLog::where('type', 'entry')->count();
        $totalExits = VehicleLog::where('type', 'exit')->count();

        // 2. Peak Hour
        $driver = \DB::connection()->getDriverName();
        $hourExpr = $driver === 'sqlite' ? "strftime('%H', timestamp)" : "HOUR(timestamp)";

        $peakMatch = VehicleLog::selectRaw("$hourExpr as hour, COUNT(*) as count")
            ->groupBy('hour')
            ->orderByDesc('count')
            ->first();

        $peakHour = 'N/A';
        if ($peakMatch) {
            $peakHour = Carbon::createFromTime($peakMatch->hour, 0)->format('h:i A');
        }

        // 3. Monthly Registration Trends (Last 6 Months)
        $months = [];
        $counts = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $count = VehicleRegistration::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            
            $months[] = $monthName;
            $counts[] = $count;
        }

        return view('office.stats', compact('totalEntries', 'totalExits', 'peakHour', 'months', 'counts'));
    }

    public function checkTag(Request $request)
    {
        $tagId = $request->query('tagId');
        $registration = VehicleRegistration::where('rfid_tag_id', $tagId)->first();

        if ($registration) {
            return response()->json([
                'exists' => true,
                'message' => 'This tag is already registered to ' . $registration->full_name . '.',
                'owner' => $registration->full_name
            ]);
        }

        return response()->json(['exists' => false]);
    }
    public function verify($id)
    {
        $registration = VehicleRegistration::findOrFail($id);
        
        if ($registration->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This registration is already ' . $registration->status . '.'
            ], 400);
        }

        $registration->update(['status' => 'verified']);

        // Send Email
        if ($registration->email_address && filter_var($registration->email_address, FILTER_VALIDATE_EMAIL)) {
            \Illuminate\Support\Facades\Mail::to($registration->email_address)
                ->send(new \App\Mail\RegistrationVerified($registration));
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration verified! An email has been sent to the applicant.'
        ]);
    }

    public function reject(Request $request, $id)
    {
        $registration = VehicleRegistration::findOrFail($id);
        
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);

        // Send Email
        if ($registration->email_address && filter_var($registration->email_address, FILTER_VALIDATE_EMAIL)) {
            \Illuminate\Support\Facades\Mail::to($registration->email_address)
                ->send(new \App\Mail\RegistrationRejected($registration));
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration rejected and applicant notified.'
        ]);
    }
}
