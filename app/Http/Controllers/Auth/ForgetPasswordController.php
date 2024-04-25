<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
//use DB;

class ForgetPasswordController extends Controller
{
    /**
     * Forget Password.
     *
     * @OA\Post (
     *     path="/api/forget-password",
     *     tags={"Authentication"},
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="email",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="K.r.imtiaz@gmail.com",
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Password reset link sent to your email."}}),
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

    public function forgetPassword(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validateData->fails()) {
            return response()->json(['success' => false, 'errors' => $validateData->errors()], 422);
        } else {
            DB::beginTransaction();
            try {

                $token = Str::random(64);

                Mail::send('email.forgetPassword', ['token' => $token], function($message) use($request){
                    $message->to($request->email);
                    $message->subject('Reset Password');

                });
                DB::table('password_reset_tokens')
                    ->updateOrInsert(
                        ['email' => $request->email],
                        ['token' => $token, 'created_at' => now()]
                    );

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset link sent to your email.',
                ]);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json(["status" => 400, "message" => $e->getMessage(), "data" => array()]);
            }
        }
    }


    /**
     * Reset Password.
     *
     * @OA\Post (
     *     path="/api/reset-password",
     *     tags={"Authentication"},
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
     *      @OA\Parameter(
     *           in="query",
     *           name="password_confirmation",
     *           required=true,
     *
     *           @OA\Schema(type="string"),
     *           example="Ex123456@",
     *       ),
     *       @OA\Parameter(
     *          in="query",
     *          name="token",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="sBvotKzWHeXv2I0Mw4cdtm895xtAldugCgWwZxN45nOSK6uwANLcKa0Yz5shAnQx",
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
    public function resetPassword(Request $request){


        $validateData = Validator::make($request->all(), [
//            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|same:password',
            'token' => 'required'
        ]);


        if ($validateData->fails()) {
            return response()->json(['success' => false,
                'status' => 400,
                'message' => 'Validation errors',
                'errors' => $validateData->errors()],
                422
            );
        } else {
            DB::beginTransaction();

            try{

                $updatePassword = DB::table('password_reset_tokens')
//                    ->where('email', $request->email)
                    ->where('token', $request->token)
                    ->first();

                if(!$updatePassword){
                    return response()->json([
                        'success' => false,
                        'status' => 400,
                        'message' => 'Invalid token!'
                    ]);
                }

                User::where('email', $updatePassword->email)
                    ->update(['password' => Hash::make($request->password)]);

                DB::table('password_reset_tokens')->where(['email'=> $updatePassword->email])->delete();
                DB::commit();

                return response()->json(['success' => true, 'message' => 'Password reset successfully.']);
            }catch (Exception $e){
                DB::rollBack();
                return response()->json(['success' => false,"status" => 400, "message" => $e->getMessage(), "data" => array()]);
            }
        }
    }
}
