<?php

namespace App\Http\Controllers;

use App\Events\MessageBroadcast;
use App\Facades\MessageFacade;
use App\Http\Requests\Conversation\StoreConversationRequest;
use App\Http\Requests\Message\ConversationListRequest;
use App\Http\Requests\Message\ConversationLitRequest;
use App\Http\Requests\Message\MessageListRequest;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\ConversationResources;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Participant;
use App\Models\User;
use App\Services\SwapMessageService;
use App\Services\SwapNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class MessageController extends Controller
{
    const PER_PAGE = 10;

    /**
     * Create a new Conversation.
     *
     *
     * @OA\Post (path="/api/prepare-conversation",
     *     tags={"Message"},
     *     security={{ "apiAuth": {} }},
     *
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="reciver_id",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="Doel Rana",
     *     ),
     *          @OA\Parameter(
     *          in="query",
     *          name="sender_id",
     *          required=true,
     *
     *          @OA\Schema(type="integer"),
     *          example="1",
     *      ),
     *          @OA\Parameter(
     *          in="query",
     *          name="swap_id",
     *          required=true,
     *
     *          @OA\Schema(type="integer"),
     *          example="2",
     *      ),
     *          @OA\Parameter(
     *          in="query",
     *          name="conversation_type",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="private",
     *      ),
     *           @OA\Parameter(
     *           in="query",
     *           name="conversation_id",
     *           required=true,
     *
     *           @OA\Schema(type="integer"),
     *           example="2",
     *       ),
     *           @OA\Parameter(
     *            in="query",
     *            name="message",
     *            required=true,
     *
     *            @OA\Schema(type="text"),
     *            example="2",
     *        ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Conversation created successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      )
     * )
     */
    public function prepareConversation(StoreConversationRequest $conversationRequest): JsonResponse
    {

        MessageFacade::prepareData(
            auth()->id(),
            $conversationRequest->receiver_id,
            'private',
            'message',
            'You have a new swap request ' . $swap->uid,
            $swap
        )->messageGenerate()->withNotify();

        $conversation = SwapMessageService::createPrivateConversation(
            $conversationRequest->sender_id,
            $conversationRequest->receiver_id,
            $conversationRequest->conversation_type
        );
        return response()->json(['success' => true, 'data' => $conversation, 'message' => 'Conversation started successfully']);
    }

    /**
     * Send Message.
     *
     *
     * @OA\Post (path="/api/send-messages",
     *     tags={"Message"},
     *     security={{ "apiAuth": {} }},
     *
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="reciver_id",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="Doel Rana",
     *     ),
     *          @OA\Parameter(
     *          in="query",
     *          name="sender_id",
     *          required=true,
     *
     *          @OA\Schema(type="integer"),
     *          example="1",
     *      ),
     *          @OA\Parameter(
     *          in="query",
     *          name="swap_id",
     *          required=true,
     *
     *          @OA\Schema(type="integer"),
     *          example="2",
     *      ),
     *          @OA\Parameter(
     *          in="query",
     *          name="conversation_type",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="private",
     *      ),
     *           @OA\Parameter(
     *           in="query",
     *           name="conversation_id",
     *           required=true,
     *
     *           @OA\Schema(type="integer"),
     *           example="2",
     *       ),
     *           @OA\Parameter(
     *            in="query",
     *            name="message",
     *            required=true,
     *
     *            @OA\Schema(type="text"),
     *            example="2",
     *        ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Message Sent successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      )
     * )
     */
    public function sendMessages(StoreMessageRequest $messageRequest): JsonResponse
    {
        try {
            DB::beginTransaction();
            $response = MessageFacade::prepareData(
                auth()->id(),
                $messageRequest->receiver_id,
                'private',
                'message',
                $messageRequest->message,
                $messageRequest->files,
                null
            )
                ->messageGenerate()
                ->doMessageBroadcast()
                ->doConversationBroadcast();


//            dd($response->conversation);
            $data = [
                'messages'     => $response->insert_message,
                'conversation' => $response->conversation,
            ];

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Message sent successfully', 'data' => $data]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

    }

    /**
     * Message List.
     *
     * @OA\Get(
     *     path="/api/message",
     *     tags={"Message"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="pagination",
     *          required=true,
     *
     *          @OA\Schema(type="number"),
     *          example="10"
     *      ),
     *
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *
     *          @OA\Schema(type="boolean")
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */


    // Deprecated method
    public function index(ConversationListRequest $conversationListRequest): \Illuminate\Http\JsonResponse
    {
        $conversation = Conversation::query();

        $conversation = $conversation->whereHas(
            'participants', function ($query) use ($conversationListRequest) {
            $query->where('user_id', auth()->id());
        }
        )->whereHas(
            'participants.user', function ($query) use ($conversationListRequest) {
            if ($conversationListRequest->search) {
                $query->where(
                    function ($query) use ($conversationListRequest) {
                        $query->where('first_name', 'like', '%' . $conversationListRequest->search . '%')
                            ->orWhere('last_name', 'like', '%' . $conversationListRequest->search . '%');
                    }
                );
            }
        }
        )->with('participants.user');


        $conversation = $conversation->orderBy('updated_at', 'desc');

        $conversation = ConversationResources::collection(
            $conversation->paginate($request->pagination ?? self::PER_PAGE)
        )->resource;

        return response()->json(['success' => true, 'data' => $conversation]);
    }

    public function messageList(MessageListRequest $messageListRequest, $id)
    {

        $message = Message::whereHas(
            'conversation', function ($query) use ($id) {
            $query->whereHas(
                'participants', function ($query) {
                $query->where('user_id', auth()->id());
            }
            )->where('id', $id);
        }
        );

        MessageFacade::updateUserLastSeen($id, auth()->id());

        $latestMessage = $message->latest('id')->first();
        if ($latestMessage) {
            auth()->user()->participants()->where('conversation_id', $id)->update(['message_id' => $latestMessage->id]);
        }

        $operator = '<';
        $order = 'desc';

        if ($messageListRequest->sort == 'newest') {
            $operator = '>';
            $order = 'asc';
        }

        if ($messageListRequest->paginate_message_id) {
            $message = $message->where('id', $operator, $messageListRequest->paginate_message_id);
        }

        $message = $message->orderBy('id', $order);

        $message = $message->take(10)->get();

        $message = $message->load('sender.image');


        // $participants = Participant::where('conversation_id', $id)->get();
        // $participants->each(function ($participant) use($message) {
        //     $message_ins = $message->where('id',$participant->message_id)->first();
        //     if (!isset($message_ins->last_seen_users)) {
        //         $message_ins->last_seen_users = collect(); // Initialize the array if it doesn't exist
        //     }

        //     // Check if $participant->user is not already in $message_ins->last_seen_users
        //     $message_ins->last_seen_users->push($participant->user);  // Add $participant->user to the array
        // });

        return response()->json(['success' => true, 'data' => MessageResource::collection($message)->resource]);
    }

    /**
     * Update Message.
     *
     *
     * @OA\Put  (path="/api/update-message/{id}",
     *     tags={"Message"},
     *     security={{ "apiAuth": {} }},
     *          @OA\Parameter(
     *          in="query",
     *          name="sender_id",
     *          required=true,
     *
     *          @OA\Schema(type="integer"),
     *          example="1",
     *      ),
     *          @OA\Parameter(
     *          in="query",
     *          name="id",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="1",
     *      ),
     *           @OA\Parameter(
     *            in="query",
     *            name="message",
     *            required=true,
     *
     *            @OA\Schema(type="text"),
     *            example="This is a message.",
     *        ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Message Sent successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      )
     * )
     */

    public function updateMessage(Request $request)
    {
        $message = Message::where('sender_id', auth()->id())->where('id', $request->id)->first();
        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message not found'], 404);
        }

        $message->update($request->only('message'));

        return response()->json(['success' => true, 'message' => 'Message updated successfully', 'data' => $message]);
    }

    /**
     * Delete Message.
     *
     *
     * @OA\Delete (path="/api/delete-message/{id}",
     *     tags={"Message"},
     *     security={{ "apiAuth": {} }},
     *          @OA\Parameter(
     *          in="query",
     *          name="sender_id",
     *          required=true,
     *
     *          @OA\Schema(type="integer"),
     *          example="1",
     *      ),
     *          @OA\Parameter(
     *          in="query",
     *          name="id",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="1",
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Message Delete successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      )
     * )
     */

    public function deleteMessage(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $message = Message::where('sender_id', auth()->id())
                ->where('id', $request->id)
                ->with('conversation')
                ->firstOrFail();

            $conversation = $message->conversation;

            if ($conversation->last_message_id == $message->id) {
                $previousMessage = $conversation->messages()
                    ->where('id', '<', $message->id)
                    ->latest()
                    ->first();

                $conversation->last_message_id = $previousMessage ? $previousMessage->id : null;
                $conversation->last_message = $previousMessage ? $previousMessage->message : null;
                $conversation->save();
            }

            $message->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Message deleted successfully'], 200);
        } catch (\Error $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e]);
        }

    }

}
