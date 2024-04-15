<?php

namespace App\Http\Controllers;

use App\Events\MessageBroadcast;
use App\Http\Requests\Message\StoreMessageRequest;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(StoreMessageRequest $messageRequest)
    {
        dd($messageRequest->all());
        $user = $request->user();
        event(new MessageBroadcast($user));
//        return response()->json(['success' => true, 'message' => 'MessageBroadcast sent successfully!'], 200);
    }
}
