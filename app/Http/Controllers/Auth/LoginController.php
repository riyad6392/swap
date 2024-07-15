<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Http;


class LoginController extends Controller
{
    public int $expiresInDays = 7;

    /**
     * Login User
     *
     * This method supports authenticating a user by their email and password.
     * Optionally, a 'remember' flag can be set to maintain the session persistence.
     *
     * @OA\Info(
     *      title="SWAP API Documentation",
     *      version="latest",
     * )
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Authenticate a user",
     *     operationId="loginUser",
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User email address",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="email"
     *         ),
     *         example="k.r.imtiaz@gmail.com"
     *     ),
     *
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User password",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="password"
     *         ),
     *         example="password"
     *     ),
     *
     *    @OA\Parameter(
     *          name="remember",
     *          in="query",
     *          description="remember option",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *          ),
     *          example="1"
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 example={"id": 2, "name": "Imtiaz Ur Rahman Khan", "email": "k.r.imtiaz@gmail.com", "email_verified_at": null, "created_at": "2022-11-02T12:25:16.000000Z", "updated_at": "2022-11-02T12:25:16.000000Z"}
     *             ),
     *             @OA\Property(property="success", type="boolean", example="1"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=86400),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiw...")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid user input",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="0"),
     *             @OA\Property(property="errors", type="object",
     *                 example={"message": "Credentials do not match"}
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Authentication failed"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     )
     * )
     *
     * @throws ValidationException If any required fields are missing or validation fails
     */

    public function login(Request $request)
    {

        //Log::info('Remember field:', ['remember' => $request->remember]);


        $validateData = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required',
            'remember' => 'required'
        ]);


        if ($validateData->fails()) {
            return response()->json(['success' => false, 'message' => config('constants.validation_error'), 'errors' => $validateData->errors()], 422);
        }


        if (auth()->attempt($request->only('email', 'password'), $request->remember)) {
            $user = auth()->user();
            $token = $this->getTokenAndRefreshToken($request->email, $request->password, 'user');

            if (!$token) {
                return response()->json(['success' => false, 'message' => 'Invalid token.'], 422);
            }


//            Cache::store('redis')->remember('active_users_'.auth()->id(), 60, function () {
//                return auth()->user()->update(['active_at' => Carbon::now(),'is_active'=> 1]);
//            });

            return response()->json([
                'success' => true,
                'message' => config('constants.login_success'),
                'user' => new UserResource($user),
                'token' => $token,
            ], 200);

        }
        return response()->json(['success' => false, 'message' => 'Authentication failed'], 422);
    }

    public function getTokenAndRefreshToken($email, $password, $scope = 'user')
    {
        $oClient = OClient::where('password_client', 1)->where('provider', 'users')->first();

        if (!$oClient) {
            return false;
        }

        $response = request()->create('/oauth/token', 'post', [
            'grant_type' => 'password',
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'response_type' => 'token',
            'username' => $email,
            'password' => $password,
            'scope' => $scope,
        ]);

        $result = app()->handle($response);

        return json_decode((string)$result->getContent(), true);


    }

    /**
     * User logout.
     *
     * @OA\Post (
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     security={{ "apiAuth": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="User successfully logout!"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Invalid user",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *          )
     *      )
     * )
     */

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json(['success' => true, 'message' => 'User logged out successfully'], 200);
    }

    /**
     * Refresh token
     * @OA\Post (
     *     path="/api/refresh-token",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         in="query",
     *         name="refresh_token",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     security={{ "apiAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="token_type", type="string", example="Bearer"),
     *              @OA\Property(property="expires_in", type="number", example="86400"),
     *              @OA\Property(property="access_token", type="token", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOTAwNDU1YTJmZGY4YzQ3YmQ1ODQxODMwZDQzN2JhNWM4NDJhZDdlNTAwMDNiODBlNjJhZjFlYTJhZDhiZTAxY2FkMjdiOWIxZjkxZjkwZjMiLCJpYXQiOjE2Njc0MzQ1NTYuMTQ0MTk5LCJuYmYiOjE2Njc0MzQ1NTYuMTQ0MjAyLCJleHAiOjE2OTg5NzA1NTYuMDI2NDg3LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.LzRYUmOk7uCScSwi_RTR4nuKQ3tOeKiGAjM7PxvQUW9Fs93K3HqIxvIIBAnniTc8ee1plfy7qikxEsfOsJLaMjm7c2hmFyy4hhRxYJAxc0lAnA6G7i0Ue-kFeivs7cSzwEzvGRhnLgtv4J7vYbvJA8yh9KrvqvsB19_Y-RPzng6d4ZKR-Ij6nF5GfN-QCNP4nyqRfiPsuQVMJuP9KiWyacvpr7XUX2wjcY8xQtxQBfvaSNehujhX8B-0f0CwGpOBTi3fDdpvZbeqa9s7ZBDb9bSWahZ90XIvpls5bCnt7zuhXGte9HKeFulrISeCj-onCnGvqzFsxDESdCDVU0MC2hbXwHHLgQbBWhG0EM2u86VgdAqPktNsAK-4l7_zCRHuGMnT_qpY4He1e-MvECDQ8wfGtunKfizwTzxJ2VrsFPkWl90fldcmfTt0Mbd_HJfPCw4XViUYBQAPgSUKxKsuPN5RNott4zzCTtWJKA8Ot9L05H4zQQB6yWrj8juPUA3qBVO5jDC12SM32mrVwUTWXEfZa8EDUQ2MQytPR-uflfkTdYVPYvoRQPoEAAmD1oz4_kas0F23xSza6K8mwA3pT1Znqjbz4fDDrcoxQDENsPwTqfuZBWFfz5OXOh6f_5NO41g_qOWEdzwGLaST9p-8ALUC8oExDIfQq2EvM3Cqh-M"),
     *              @OA\Property(property="refresh_token", type="token", example="def502000a6adbde3b00f2f16b60fe587d0fb88c2b1736cf26d562c433728e5c451cee8ddb8eb262e088c328f1748907301b94301afdaa4ff6dc714998a29d794ba65048c13a7859d9a9cb527c329e134ff7ab98b144f663b36c4f70866725dee75e3099ade9103b8c880f21010aa01f9b8ab2c0eada7561edb4da28c4c322e8a60ce3119776749a83ccd7560a995e1ea2614da33991403dd7ac1131fa5447fbed021b309176a02bece09b06a8bf8888a4bb84aef1ca9a23efaf4a06b57f33233d83aae99b1ee734f40830be2d9d57e5432c1ecaa4bd98fa31f30b57bbcd91dd79378c74f54616bdb0d0cfe73d996dbd0bf8602fa2056f223c5d93fe42806f327dc68af4f1a300fac0293eb5df83e969defb75a5204004a0d54008e07c94586d5c74f6773c94d0986ac151dd7f0f97ff8a35df5b044d59c8710e28e44f905639686698265a11003f471299c97e94a7efea30a4cc064f908c8ad147f5ad2a19f6c0e317e5")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid token",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Invalid token."}}),
     *          )
     *      )
     * )
     * @throws ValidationException
     */

    public function getRefreshToken(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'refresh_token' => 'required'
        ]);

        if ($validateData->fails()) {
            return response()->json(['success' => false, 'errors' => $validateData->errors()], 422);
        }

        $oClient = OClient::where('password_client', 1)->where('provider', 'users')->first();

        $response = request()->create('/oauth/token', 'post', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'scope' => 'user',
        ]);

        $result = app()->handle($response);

        return json_decode((string)$result->getContent(), true);
    }
}
