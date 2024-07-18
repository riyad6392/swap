<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notification\UpdateReadAndUnreadNotificationRequest;
use App\Models\Notification;
use App\Models\Swap;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    /**
     *
     * Use scope to get the notifications of the authenticated user
     * Scope name is UserNotificationScope
     *
     */

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
        $notificationQuery = Notification::query()->with('notifiable');

        $unreadNotificationsCount = Notification::getReadNotification()->count();
        $readNotificationsCount = Notification::getUnreadNotification()->count();

        $notificationQuery->orderByDesc('created_at');

        if ($request->get('get_all')) {
            $notificationList = $notificationQuery->get();
        } else {
            $notificationList = $notificationQuery->paginate(10);
        }

        return apiResponseWithSuccess('Notifications Retrieved Successfully', [
            'notifications' => $notificationList,
            'unreadNotifications' => $unreadNotificationsCount,
            'readNotifications' => $readNotificationsCount
        ]);
    }

    /**
     * A Single Notification show.
     *
     * @OA\Get(
     *     path="/api/notification-show/{id}",
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

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $notification = Notification::findOrFail($id);

        $notification->update(['read_at' => now()]);

        return apiResponseWithSuccess('Notification updated successfully', $notification);
    }

    /**
     * Mark As Read.
     *
     * @OA\Post(
     *     path="/api/mark-as-read",
     *     tags={"Notifications"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\Parameter(
     *          in="query",
     *         name="id[]",
     *         required=true,
     *         description="Notification id",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 required={"path"},
     *                 @OA\Property(property="path", type="string", example="[1,2,3]"),
     *             ),
     *         ),
     *     ),
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

    public function markAllAsRead(UpdateReadAndUnreadNotificationRequest $readAndUnreadNotificationRequest): \Illuminate\Http\JsonResponse
    {
        $notification = Notification::query();

        $notification->whereIn('id', $readAndUnreadNotificationRequest->id);

        $notification->update(['read_at' => now()]);

        return apiResponseWithSuccess('Notification marked as read');
    }

    /**
     * Mark As Unread.
     *
     * @OA\Post(
     *     path="/api/mark-as-unread",
     *     tags={"Notifications"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\Parameter(
     *          in="query",
     *         name="id[]",
     *         required=true,
     *         description="Notification id",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 required={"path"},
     *                 @OA\Property(property="path", type="string", example="[1,2,3]"),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="true", type="json", example={"message": {"All notifications marked as unread."}}),
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

    public function markAllAsUnRead(UpdateReadAndUnreadNotificationRequest $readAndUnreadNotificationRequest): \Illuminate\Http\JsonResponse
    {
        $notification = Notification::query();

        $notification->whereIn('id', $readAndUnreadNotificationRequest->id);

        $notification->update(['read_at' => null]);

        return apiResponseWithSuccess('Notification marked as read');
    }

    public function deleteNotification(Request $request, String $id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $notification = $user->notifications()->where('notifications.id', $id)->first();

            if (!$notification) {
                return apiResponseWithError('Notification not found', Response::HTTP_NOT_FOUND);
            }

            $user->notifications()->detach($notification->id);
            $notification->delete();

            DB::commit();
            return apiResponseWithSuccess('Notification deleted successfully');
        } catch (\Error $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
