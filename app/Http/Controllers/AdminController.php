<?php

namespace App\Http\Controllers;

use App\Models\user_account;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = user_account::with('course')->where('role', 'student');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $users = $query->withCount(['history as borrowed_books_count' => function ($q) {
            $q->where('status', 'borrowed');
        }])->orderBy('user_id', 'asc')->get();

        foreach ($users as $user) {
            $user->name = $user->first_name . ' ' . $user->last_name;
        }

        // Stats
        $totalUsers = user_account::where('role', 'student')->count();
        $activeUsers = user_account::where('role', 'student')->where('status', 'active')->count();
        $suspendedUsers = user_account::where('role', 'student')->where('status', 'suspended')->count();
        $newUsers = user_account::where('role', 'student')
            ->whereMonth('date_joined', Carbon::now()->month)
            ->whereYear('date_joined', Carbon::now()->year)
            ->count();

        // View user
        $viewUser = null;
        if ($request->filled('view_user')) {
            $viewUser = user_account::with('course')
                ->where('user_id', $request->view_user)
                ->where('role', 'student')
                ->withCount(['history as borrowed_books_count' => function ($q) {
                    $q->where('status', 'borrowed');
                }])
                ->first();
        }

        // Edit user
        $editUser = null;
        if ($request->filled('edit_user')) {
            $editUser = user_account::where('user_id', $request->edit_user)
                ->where('role', 'student')
                ->first();
        }

        return view('dashboard.librarian.monitor_users', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'suspendedUsers',
            'newUsers',
            'viewUser',
            'editUser'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = user_account::where('user_id', $id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user_accounts,email,' . $user->user_id . ',user_id',
            'status' => 'required|in:active,suspended,pending',
            'password' => 'nullable|min:6'
        ]);

        $nameParts = explode(' ', $request->name, 2);
        $user->first_name = $nameParts[0];
        $user->last_name = $nameParts[1] ?? '';
        $user->email = $request->email;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Student updated successfully');
    }

    public function suspend($id)
    {
        $user = user_account::where('user_id', $id)->firstOrFail();
        $user->status = 'suspended';
        $user->save();

       

        return redirect()->route('admin.users.index')->with('success', 'Student suspended successfully');
    }

    public function activate($id)
    {
        $user = user_account::where('user_id', $id)->firstOrFail();
        $user->status = 'active';
        $user->save();
       
        return redirect()->route('admin.users.index')->with('success', 'Student activated successfully');
    }
}
