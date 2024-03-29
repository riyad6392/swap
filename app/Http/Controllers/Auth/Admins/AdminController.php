<?php

namespace App\Http\Controllers\Auth\Admins;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    /**
     * User Approved by Admin.
     * @OA\Post (
     *     path="/api/admin/approve-user/{user}",
     *     tags={"Admin Authentication"},
     *     security={{ "apiAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="User approved by admin!"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid user",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *          )
     *      )
     * )
     */
    public function approveUser(User $user)
    {
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        $user->update(['is_approved_by_admin' => true]);
        return response()->json(['success' => true, 'message' => 'User approved by admin'], 200);
    }
}
