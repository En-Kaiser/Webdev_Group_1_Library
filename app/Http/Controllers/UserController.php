<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Empty data just for UI testing
        $users = [];
        $totalUsers = 0;
        $activeUsers = 0;
        $suspendedUsers = 0;
        $newUsers = 0;

        return view('dashboard.librarian.monitor_users', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'suspendedUsers',
            'newUsers'
        ));
    }

    public function show($id)
    {
        // Dummy data for testing- remove niyo nalang
        return response()->json([
            'id' => $id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'student',
            'status' => 'Active',
            'profile_image' => null,
            'borrowed_books_count' => 0,
            'created_at' => '18/01/2026',
            'last_active' => 'Never',
        ]);
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function update(Request $request, $id)
    {
        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    public function suspend($id)
    {
        return response()->json(['success' => true, 'message' => 'User suspended successfully']);
    }

    public function activate($id)
    {
        return response()->json(['success' => true, 'message' => 'User activated successfully']);
    }
}
