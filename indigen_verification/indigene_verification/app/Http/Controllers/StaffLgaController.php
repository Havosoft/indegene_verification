<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffLgaController extends Controller
{
    public function index()
    {
        $staff = User::where('role', 'staff')
            ->orderBy('name')
            ->get();

        // Get all available LGAs from the JSON file
        $lgas = $this->getAvailableLgas();

        return view('admin.staff-lga', compact('staff', 'lgas'));
    }

    public function update(Request $request, User $staff)
    {
        // Ensure the user is a staff member
        if ($staff->role !== 'staff') {
            return redirect()->back()->withErrors(['error' => 'Selected user is not a staff member.']);
        }

        $request->validate([
            'assigned_lga' => 'nullable|string|max:255',
        ]);

        $staff->assigned_lga = $request->assigned_lga;
        $staff->save();

        // Log the activity
        Log::info("Admin assigned LGA '{$request->assigned_lga}' to staff {$staff->name} (ID: {$staff->id})");

        return redirect()->back()->with('status', "LGA assignment updated for {$staff->name}.");
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:users,id',
            'assigned_lga' => 'nullable|string|max:255',
        ]);

        $staffIds = $request->staff_ids;
        $assignedLga = $request->assigned_lga;

        $updatedCount = User::whereIn('id', $staffIds)
            ->where('role', 'staff')
            ->update(['assigned_lga' => $assignedLga]);

        // Log the activity
        Log::info("Admin bulk updated LGA assignment to '{$assignedLga}' for {$updatedCount} staff members");

        return redirect()->back()->with('status', "LGA assignment updated for {$updatedCount} staff members.");
    }

    private function getAvailableLgas()
    {
        // Taraba State LGAs as used in registration forms
        return [
            'Ardo Kola',
            'Bali',
            'Donga',
            'Gashaka',
            'Gassol',
            'Ibi',
            'Jalingo',
            'Karim Lamido',
            'Kurmi',
            'Lau',
            'Sardauna',
            'Takum',
            'Ussa',
            'Wukari',
            'Yorro',
            'Zing'
        ];
    }

    public function reassignUsers()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->withErrors(['error' => 'Unauthorized access.']);
        }

        try {
            // Reassign users to staff members
            User::reassignUsersToStaff();
            
            return redirect()->back()->with('status', 'Users have been successfully reassigned to staff members.');
        } catch (\Exception $e) {
            \Log::error('User reassignment error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to reassign users. Please try again.']);
        }
    }

    public function getAssignedUsersCount(User $staff)
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        // Ensure the user is a staff member
        if ($staff->role !== 'staff') {
            return response()->json(['error' => 'Selected user is not a staff member.'], 400);
        }

        $assignedUsersCount = User::where('assigned_staff_id', $staff->id)->count();

        return response()->json([
            'count' => $assignedUsersCount,
            'staff_name' => $staff->name,
            'lga' => $staff->assigned_lga
        ]);
    }
}
