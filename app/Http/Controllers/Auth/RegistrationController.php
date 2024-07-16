<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Mail\RegistrationSuccess;
use App\Models\User;
use App\Traits\TokenTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Client as OClient;


class RegistrationController extends Controller
{
    use TokenTrait;
    /**
     * Register a new User.
     *
     * @OA\Post (
     *     path="/api/register",
     *     tags={"Authentication"},
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
     *
     *
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
    public function register(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        if ($validateData->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors', 'errors' => $validateData->errors()], 422);
        } else {

            try {
                DB::beginTransaction();
                User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'is_approved_by_admin' => 0, // This is for admin approval, if you want to approve user by admin then set 0 otherwise set 1
                    'password' => bcrypt($request->password),
                    'subscription_is_active' => 0
                ]);

                DB::commit();

                $data = $request->validate([
                    'email' => 'required|email',
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                ]);

                Mail::to($data['email'])->send((new RegistrationSuccess($data))->afterCommit());

                if (auth()->attempt($request->only('email', 'password'), $request->remember)) {
                    $user = auth()->user();
                    $token = $this->getTokenAndRefreshToken($request->email, $request->password, 'user', 'users');

                    if (!$token) {
                        return response()->json(['success' => false, 'message' => 'Invalid token.'], 422);
                    }


//            Cache::store('redis')->remember('active_users_'.auth()->id(), 60, function () {
//                return auth()->user()->update(['active_at' => Carbon::now(),'is_active'=> 1]);
//            });

                    return response()->json([
                        'success' => true,
                        'message' => 'User Registration Successfully!',
                        'user' => new UserResource($user),
                        'token' => $token,
                    ], 200);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Something went wrong', 'errors' => $e->getMessage()], 500);
            }
        }
    }
}
