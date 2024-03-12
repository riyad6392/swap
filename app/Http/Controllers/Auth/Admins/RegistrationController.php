<?php

namespace App\Http\Controllers\Auth\Admins;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validateData = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        if ($validateData->fails()) {
            return response()->json(['success' => false, 'errors' => $validateData->errors()], 422);
        }else{
            $request->password = bcrypt($request->password);
            Admin::create($request->only('name', 'email', 'password'));

            return response()->json(['success' => true, 'message' => 'Your registration successfully done'], 200);
        }
    }
}
