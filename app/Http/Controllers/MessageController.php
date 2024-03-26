<?php

namespace App\Http\Controllers;

use App\Events\MessageBroadcast;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $user = $request->user();
        event(new MessageBroadcast($user));
//        return response()->json(['success' => true, 'message' => 'MessageBroadcast sent successfully!'], 200);
    }
}
