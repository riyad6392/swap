<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Mockery\Matcher\Not;

class NotificationController extends Controller
{
    const PER_PAGE = 10;

    /**
     * Notifications List.
     *
     * @OA\Get(
     *     path="/api/notifications",
     *     tags={"Notifications"},
     *     security={{ "apiAuth": {} }},
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
        $notification = Notification::query();

        if ($request->get('get_all')) {

            return response()->json(['success' => true, 'data' => $notification->get()]);
        }

        $notification = $notification->paginate(10);

        return response()->json(['success' => true, 'data' => $notification], 200);
    }

    /**
     * Mark As Read.
     *
     * @OA\Get(
     *     path="/api/mark-as-read",
     *     tags={"Notifications"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\Parameter(
     *     in="query",
     *     name="id",
     *     required=false,
     *
     *     @OA\Schema(type="number"),
     *     example="1"
     *    ),
     *
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="true", type="json", example={"message": {"All notifications marked as read."}}),
     *           ),
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
    public function markAllAsRead(Request $request): \Illuminate\Http\JsonResponse
    {
        $notification = Notification::query();

        if ($request->has('id')) {
            $notification->where('id', $request->id);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Notifications update successfully'], 200);
    }

    /**
     * Mark As Un Read.
     *
     * @OA\Get(
     *     path="/api/mark-as-unread",
     *     tags={"Notifications"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\Parameter(
     *     in="query",
     *     name="id",
     *     required=false,
     *
     *     @OA\Schema(type="number"),
     *     example="1"
     *    ),
     *
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="true", type="json", example={"message": {"All notifications marked as read."}}),
     *           ),
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

    public function markAllAsUnRead(Request $request): \Illuminate\Http\JsonResponse
    {
        $notification = Notification::query();

        if($request->has('id')){
            $notification->where('id', $request->id);
        }

        $notification->update(['read_at' => null]);

        return response()->json(['success' => true, 'message' => 'Notifications update successfully'], 200);
    }
}
