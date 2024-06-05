<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RegistrationController extends Controller
{

    /**
     * Register a new User.
     *
     * @OA\Post (
     *     path="/api/admin/register",
     *     tags={"Admin Authentication"},
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="name",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="Doel Rana",
     *     ),
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="email",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="K.r.imtiaz@gmail.com",
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="passord",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="Ex123456@",
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Registration successfully done."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid user",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      )
     * )
     */
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validateData = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:admins',
            'password' => 'required',
//            'role_id' => 'required|exists:roles,id'
        ]);

        if ($validateData->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors', 'errors' => $validateData->errors()], 422);
        }else{
            $request->password = bcrypt($request->password);
            Admin::create($request->only('name', 'email', 'password'));

//            $role = Role::findById($request->role_id);
//
//            $admin->assignRole($role->name);

            return response()->json(['success' => true, 'message' => 'Your registration successfully done'], 200);
        }
    }
}
