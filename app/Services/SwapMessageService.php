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
    public $message_files = '';

    public function prepareData($sender_id, $receiver_id, $conversation_type, $message_type, $message, $message_files, $swap = null): static
    {
//        dd($sender_id, $receiver_id, $conversation_type, $message_type, $message, $message_files, $swap);
        $this->sender_id = $sender_id;
        $this->receiver_id = (int) $receiver_id;
        $this->conversation_type = $conversation_type;
        $this->message_type = $message_type;
        $this->message = $message;
        $this->message_files = $message_files;
        $this->swap = $swap;

        return $this;
    }


    public function messageGenerate()
    {
        $this->conversation = $this->findOrCreateConversation(
            $this->sender_id, $this->receiver_id, $this->conversation_type,
        );

        if($this->message){
            $this->message = Message::create([
                'message' => $this->message,
                'receiver_id' => $this->receiver_id,
                'swap_id' => $this->swap->id ?? null,
                'sender_id' => auth()->id(),
                'conversation_id' => $this->conversation->id,
                'message_type' => $this->message_type,
            ]);
        }

        if ($this->message_files && count($this->message_files) > 0) {
            foreach ($this->message_files as $key => $file) {
                foreach ($file as $singleFile) {
                    $this->message = Message::create(
                        [
                            'conversation_id' => $this->conversation->id,
                            'receiver_id' => $this->receiver_id,
                            'sender_id' => $this->sender_id,
                            'swap_id' => null,
                            'message_type' => 'file',
                            'message' => null,
                            'data' => null,
                            'file_path' => FileUploadService::uploadFile($singleFile, new Message()),
                        ]
                    );
                }
            }
        }
        
        $this->conversation->update([
            'last_message_id' => $this->message->id,
            'last_message' => $this->message->message,
        ]);

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

            return $conversation;

        } else if ($conversation_type == 'group') {
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

    public function doMessageBroadcast()
    {
        event(new MessageBroadcast(
            $this->conversation,
            $this->message
        ));
        return $this;
    }

    public function doConversationBroadcast()
    {
        info('conversation broadcast');
        event(new ConversationBroadcast(
            $this->conversation
        ));
        return $this;
    }

}
