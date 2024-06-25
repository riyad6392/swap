<?php

namespace App\Services;

use App\Events\ConversationBroadcast;
use App\Events\MessageBroadcast;
use App\Jobs\SwapJob;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Swap;
use Illuminate\Support\Facades\DB;

class SwapMessageService
{
    public $sender_id = null;
    public $receiver_id = null;
    public $conversation_type = null;
    public $message_type = null;
    public $message = null;
    public $swap = null;
    public $conversation = null;

    public function prepareData($sender_id, $receiver_id, $conversation_type, $message_type, $message, $swap = null): static
    {
        $this->sender_id = $sender_id;
        $this->receiver_id = $receiver_id;
        $this->conversation_type = $conversation_type;
        $this->message_type = $message_type;
        $this->message = $message;
        $this->swap = $swap;

        return $this;
    }


    public function messageGenerate()
    {
        $this->conversation = $this->findOrCreateConversation(
            $this->sender_id, $this->receiver_id, $this->conversation_type,
        );

        $this->message = Message::create([
            'message' => $this->message,
            'receiver_id' => $this->receiver_id,
            'swap_id' => $this->swap->id ?? null,
            'sender_id' => auth()->id(),
            'conversation_id' => $this->conversation->id,
            'message_type' => $this->message_type,
        ]);

        $this->conversation->last_message_id = $this->message->id;
        $this->conversation->last_message = $this->message->message;
        $this->conversation->save();

//        $this->message = $message->load('conversation');

        return $this;
    }


    public function findOrCreateConversation(
        $sender_id,
        $receiver_id,
        $conversation_type,
    )
    {
        $sender_id = (int)$sender_id;
        $receiver_id = (int)$receiver_id;

        if ($conversation_type == 'private') {
            try {

                DB::beginTransaction();

                $conversation = Conversation::where('composite_id', $sender_id . ':' . $receiver_id)
                    ->orWhere('composite_id', $receiver_id . ':' . $sender_id)
                    ->orWhere('reverse_composite_id', $sender_id . ':' . $receiver_id)
                    ->orWhere('reverse_composite_id', $receiver_id . ':' . $sender_id)
                    ->first();

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'name' => 'Private',
                        'channel_name' => 'channel-' . rand(10000, 99999999) . '-' . time(),
                        'user_id' => $sender_id,
                        'conversation_type' => 'private',
                        'composite_id' => $sender_id . ':' . $receiver_id,
                        'reverse_composite_id' => $receiver_id . ':' . $sender_id,
                        // 'last_message_id' => '',
                        // 'last_message' => '',
                    ]);

                    $this->insertParticipant(
                        $conversation,
                        [$sender_id, $receiver_id]
                    );
                }

                DB::commit();

                return $conversation;

            } catch (\Exception $e) {

                DB::rollBack();
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }else if ($conversation_type == 'group') {
            //
        }
        return null;
    }

    public function insertParticipant($messageRequest, array $participants): void
    {
        $insertDataForParticipant = [];

        foreach ($participants as $participant) {
            $insertDataForParticipant[] = [
                'conversation_id' => $messageRequest->id,
                'user_id' => $participant,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Participant::insert($insertDataForParticipant);
    }

    public function withNotify()
    {
        SwapNotificationService::sendNotification(
            $this->swap,
            [$this->swap->exchanged_user_id],
            $this->message
        );
        return $this;
    }

    public function doBroadcast()
    {
        event(new MessageBroadcast(
            $this->conversation,
            $this->message
        ));
        return $this;
    }

}
