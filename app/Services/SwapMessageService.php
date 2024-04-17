<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Participant;
use App\Models\Swap;
use Illuminate\Support\Facades\DB;

class SwapMessageService
{
    public static function createPrivateConversation($sender_id, $receiver_id, $conversation_type, $last_message_id = null, $last_message = null)
    {
        $sender_id = (int)$sender_id;
        $receiver_id = (int)$receiver_id;

        if ($conversation_type == 'private') {
            try {

                DB::beginTransaction();

                $conversation = Conversation::where('composite_id', $sender_id . ':' . $receiver_id)
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
                        'last_message_id' => $last_message_id,
                        'last_message' => $last_message,
                    ]);

                    (new SwapMessageService)->insertParticipant(
                        $conversation,
                        [$sender_id, $receiver_id]
                    );
                }else{

                    $conversation->last_message_id = $last_message_id;
                    $conversation->last_message = $last_message;
                    $conversation->save();
                }

                DB::commit();

                return $conversation;
            } catch (\Exception $e) {

                DB::rollBack();

                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
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
            ];
        }

        Participant::insert($insertDataForParticipant);
    }

}
