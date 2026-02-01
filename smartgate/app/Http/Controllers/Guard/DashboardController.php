<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use App\Models\VehicleRegistration;
use App\Models\VehicleLog;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Aggregate Database Stats
        $stats = [
            'entries_today' => VehicleLog::where('type', 'entry')->whereDate('timestamp', $today)->count() + Visitor::whereDate('time_in', $today)->count(),
            'exits_today' => VehicleLog::where('type', 'exit')->whereDate('timestamp', $today)->count() + Visitor::where('status', 'left')->whereDate('time_out', $today)->count(),
            'visitors_inside' => Visitor::where('status', 'inside')->count(),
            'overstaying' => 0,
        ];

        // Fetch recent logs (Combined)
        $recentLogs = VehicleLog::with('vehicleRegistration')
            ->orderByDesc('timestamp')
            ->limit(10)
            ->get();

        $visitors_inside = Visitor::where('status', 'inside')->orderByDesc('time_in')->get();

        return view('guard.dashboard', compact('stats', 'recentLogs', 'visitors_inside'));
    }

    public function lookupTag(Request $request) {
        $tagId = $request->tagId;
        $registration = VehicleRegistration::where('rfid_tag_id', $tagId)->first();
        
        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Tag not found in database']);
        }

        // Check last log status
        $lastLog = VehicleLog::where('rfid_tag_id', $tagId)
            ->orderByDesc('timestamp')
            ->first();

        $status = 'out'; // Default
        if ($lastLog) {
            $status = ($lastLog->type === 'entry') ? 'in' : 'out';
        }

        return response()->json([
            'success' => true,
            'data' => $registration,
            'current_status' => $status,
            'suggested_action' => ($status === 'in') ? 'exit' : 'entry'
        ]);
    }

    public function logVehicle(Request $request) {
        $request->validate([
            'tagId' => 'required',
            'type' => 'required|in:entry,exit'
        ]);

        $registration = VehicleRegistration::where('rfid_tag_id', $request->tagId)->first();
        
        $log = VehicleLog::create([
            'vehicle_registration_id' => $registration ? $registration->id : null,
            'rfid_tag_id' => $request->tagId,
            'type' => $request->type,
            'timestamp' => now()
        ]);

        $info = $registration ? ($registration->full_name . " [{$request->tagId}]") : "Unregistered tag [{$request->tagId}]";
        $this->recordActivity('RFID_SCAN', "Processed {$request->type} for {$info}");

        return response()->json([
            'success' => true,
            'message' => 'Vehicle logged as ' . $request->type,
            'log' => $log->load('vehicleRegistration')
        ]);
    }

    public function entry()
    {
        return view('guard.visitor-entry');
    }

    public function storeVisitor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'plate' => 'nullable|string|max:20',
            'vehicle_type' => 'nullable|string',
            'purpose' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Visitor::create([
            'name' => $request->name,
            'plate' => $request->plate,
            'vehicle_type' => $request->vehicle_type,
            'purpose' => $request->purpose,
            'destination' => $request->destination,
            'time_in' => now(),
            'status' => 'inside',
        ]);

        $this->recordActivity('VISITOR_ENTRY', "Logged manual entry for visitor: {$request->name}");

        return redirect()->route('guard.dashboard')->with('success', 'Visitor entry logged successfully.');
    }

    public function exit()
    {
        $visitors = Visitor::where('status', 'inside')->orderByDesc('time_in')->get();
        return view('guard.visitor-exit', compact('visitors'));
    }

    public function exitVisitor($id)
    {
        $visitor = Visitor::findOrFail($id);
        $visitor->update([
            'status' => 'left',
            'time_out' => now(),
        ]);

        $this->recordActivity('VISITOR_EXIT', "Logged manual exit for visitor: {$visitor->name}");

        return redirect()->back()->with('success', 'Visitor exit logged successfully.');
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
}
