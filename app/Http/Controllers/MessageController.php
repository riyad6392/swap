<?php

namespace App\Http\Controllers;

use App\Events\MessageBroadcast;
use App\Facades\MessageFacade;
use App\Http\Requests\Conversation\StoreConversationRequest;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\ConversationResources;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\SwapMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


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
        MessageFacade::prepareData(
            auth()->id(),
            $messageRequest->receiver_id,
            'private',
            'message',
            $messageRequest->message,
            null
        )->messageGenerate()->doBroadcast();

        return response()->json(['success' => true, 'message' => 'Message sent successfully']);
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

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $conversation = Conversation::query();

        $conversation = $conversation->whereHas('participants', function ($query) {
            $query->where('user_id', auth()->id());
        })->with('participants.user');

        if (request()->get_all) {

            $conversation = $conversation->get();

            return response()->json(['success' => true, 'data' => $conversation]);
        }

        $conversation = ConversationResources::collection($conversation->paginate($request->pagination ?? self::PER_PAGE));

        return response()->json(['success' => true, 'data' => $conversation]);
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

    public function deleteMessage(Request $request)
    {
        $message = Message::where('sender_id', auth()->id())->where('id', $request->id)->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message not found'], 404);
        }

        $message->delete();

        return response()->json(['success' => true, 'message' => 'Message deleted successfully']);
    }

}
