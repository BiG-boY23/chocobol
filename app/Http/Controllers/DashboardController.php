<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        // Mock Data for UI demonstration
        $stats = [
            'entries_today' => 142,
            'exits_today' => 118,
            'visitors_inside' => 8,
            'overstaying' => 2,
        ];

        $visitors_inside = [
            [
                'id' => 'V-1001',
                'name' => 'John Doe',
                'plate' => 'ABC-1234',
                'purpose' => 'Meeting with Principal',
                'time_in' => '09:15 AM',
                'duration' => '3h 15m',
                'status' => 'WARNING', // purely visual mock
            ],
            [
                'id' => 'V-1004',
                'name' => 'Elena Cruz',
                'plate' => 'XYZ-9876',
                'purpose' => 'Delivery',
                'time_in' => '08:00 AM',
                'duration' => '4h 30m',
                'status' => 'OVERSTAY',
            ],
            [
                'id' => 'V-1005',
                'name' => 'Michael Tan',
                'plate' => 'N/A',
                'purpose' => 'Inquiry',
                'time_in' => '11:45 AM',
                'duration' => '0h 45m',
                'status' => 'NORMAL',
            ],
        ];

        return view('dashboard', compact('stats', 'visitors_inside'));
    }

}
