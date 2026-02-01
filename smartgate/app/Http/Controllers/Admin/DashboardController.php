<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleRegistration;
use App\Models\RegistrationReview;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\VehicleLog;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get real statistics from database
        $stats = [
            'total_rfid' => VehicleRegistration::count(),
            'active_rfid' => VehicleRegistration::where('status', 'approved')->count(),
            'blacklisted_rfid' => VehicleRegistration::where('status', 'rejected')->count(),
            'pending_registrations' => VehicleRegistration::where('status', 'pending')->count(),
            // Placeholder: use total approved registrations as total entries until a gate log table exists
            'total_entries' => VehicleRegistration::where('status', 'approved')->count(),
            // Entries created (registrations) today
            'entries_today' => VehicleRegistration::whereDate('created_at', Carbon::today())->count(),
        ];

        // Get recent registrations for activity logs (load latest review + admin)
        $recentRegistrations = VehicleRegistration::with(['latestReview.admin'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $logs = $recentRegistrations->map(function ($registration) {
            return [
                'time' => $registration->created_at->format('h:i A'),
                'user' => 'Office Staff',
                'action' => 'Registration ' . ucfirst($registration->status),
                'details' => $registration->full_name . ' - ' . $registration->plate_number,
            ];
        })->toArray();

        return view('admin.dashboard', compact('stats', 'logs'));
    }

    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'nullable|email|unique:users',
            'role' => 'required|in:admin,office,guard',
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        $this->recordActivity('USER_CREATED', "Created user account for {$request->first_name} {$request->last_name} ({$request->role})");

        return redirect()->back()->with('success', 'User added successfully.');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'username' => 'required|string|unique:users,username,'.$id,
            'email' => 'nullable|email|unique:users,email,'.$id,
            'role' => 'required|in:admin,office,guard',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => [
                    'confirmed',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&]/',
                ],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $this->recordActivity('USER_UPDATED', "Updated user account for {$user->username}");

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }
        $username = $user->username;
        $user->delete();
        $this->recordActivity('USER_DELETED', "Deleted user account: {$username}");
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function rfid(Request $request)
    {
        $query = VehicleRegistration::with(['officeUser']);

        // Quick Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('plate_number', 'like', "%$search%")
                  ->orWhere('rfid_tag_id', 'like', "%$search%");
            });
        }

        // Status Filter
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'total' => VehicleRegistration::count(),
            'active' => VehicleRegistration::where('status', 'approved')->count(),
            'blacklisted' => VehicleRegistration::where('status', 'rejected')->count(),
        ];

        return view('admin.rfid', compact('registrations', 'stats'));
    }

    public function toggleStatus(Request $request, $id)
    {
        $registration = VehicleRegistration::findOrFail($id);
        $newStatus = $registration->status === 'approved' ? 'rejected' : 'approved';
        
        $registration->update([
            'status' => $newStatus
        ]);

        $this->recordActivity('TAG_STATUS_CHANGE', ($newStatus === 'approved' ? 'Activated' : 'Blacklisted') . " tag ID: {$registration->rfid_tag_id} for {$registration->full_name}");

        return response()->json([
            'success' => true, 
            'message' => 'Tag status updated to ' . ($newStatus === 'approved' ? 'Active' : 'Blacklisted'),
            'new_status' => $newStatus
        ]);
    }

    public function showRegistration($id)
    {
        $registration = VehicleRegistration::with(['officeUser'])->findOrFail($id);
        return response()->json($registration);
    }

    public function reports()
    {
        // Fetch Audit Logs
        $auditLogs = AuditLog::with('user')->orderByDesc('created_at')->paginate(20, ['*'], 'audit_page');

        // Fetch Gate Traffic (Manual + RFID)
        $rfidLogs = VehicleLog::with('vehicleRegistration')->orderByDesc('timestamp')->limit(100)->get()->map(function($log) {
            return [
                'time' => $log->timestamp,
                'category' => 'RFID',
                'type' => $log->type,
                'detail' => $log->vehicleRegistration->full_name . " (" . $log->vehicleRegistration->plate_number . ")",
                'plate' => $log->vehicleRegistration->plate_number
            ];
        });

        $vLogs = Visitor::orderByDesc('time_in')->limit(100)->get()->flatMap(function($v) {
            $logs = [];
            $logs[] = [
                'time' => $v->time_in,
                'category' => 'Visitor',
                'type' => 'entry',
                'detail' => $v->name . " (Visitor)",
                'plate' => $v->plate ?? 'N/A'
            ];
            if ($v->time_out) {
                $logs[] = [
                    'time' => $v->time_out,
                    'category' => 'Visitor',
                    'type' => 'exit',
                    'detail' => $v->name . " (Visitor)",
                    'plate' => $v->plate ?? 'N/A'
                ];
            }
            return $logs;
        });

        $trafficLogs = $rfidLogs->concat($vLogs)->sortByDesc('time')->values();

        return view('admin.reports', compact('auditLogs', 'trafficLogs'));
    }

    private function recordActivity($action, $details)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip()
        ]);
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
