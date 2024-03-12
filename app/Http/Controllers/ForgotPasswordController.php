<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function getTokenAndRefreshTokenByRefreshToken(Request $request) {
        
        $validateData = Validator::make($request->all(), [
            'refresh_token' => 'required'
        ]);

        if ($validateData->fails()) {
            return response()->json(['success' => false, 'errors' => $validateData->errors()], 422);
        }

        $oClient = OClient::where('password_client', 1)->first();

        $response = request()->create('/oauth/token', 'post', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'scope' => 'user',
        ]);

        $result = app()->handle($response);

        return json_decode((string) $result->getContent(), true);
    }
}
