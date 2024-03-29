<?php

namespace App\Http\Controllers\Auth\Admins;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function approveUser(User $user)
    {
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        $user->update(['is_approved_by_admin' => true]);
        return response()->json(['success' => true, 'message' => 'User approved by admin'], 200);
    }
}
