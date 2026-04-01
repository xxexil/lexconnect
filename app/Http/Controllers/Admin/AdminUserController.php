<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $totalClients  = User::where('role', 'client')->count();
        $totalLawyers  = User::where('role', 'lawyer')->count();
        $totalFirms    = User::where('role', 'law_firm')->count();

        return view('admin.users', compact('users', 'totalClients', 'totalLawyers', 'totalFirms'));
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->withErrors(['error' => 'Cannot delete admin accounts.']);
        }
        $user->delete();
        return back()->with('success', "User \"{$user->name}\" has been deleted.");
    }
}
