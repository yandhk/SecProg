<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function suspend(User $user)
    {
        $user->is_suspended = true;
        $user->save();

        return back()->with('success', 'User suspended.');
    }

    public function unsuspend(User $user)
    {
        $user->is_suspended = false;
        $user->save();

        return back()->with('success', 'User unsuspended.');
    }
}
