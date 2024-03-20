<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Client as OClient;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Http;



class LoginController extends Controller
{
    public int $expiresInDays = 7;

    /**
     * Login User
     *
     * @OA\Info(
     *      title="SWAP API Documantation",
     *      version="latest",
     *  )
     * @OA\Post (
     *     path="/api/login",
     *     tags={"Authentication"},
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="email",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="k.r.imtiaz@gmail.com"
     *     ),
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="password",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="password"
     *     ),
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="remember",
     *         description="This value must be boolean 0 or 1 not true/false.",
     *
     *         @OA\Schema(type="boolean"),
     *         example="false"
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="user", type="json", example={"id": 2,"name": "Imtiaz Ur Rahman Khan","email": "k.r.imtiaz@gmail.com","email_verified_at": null,"created_at": "2022-11-02T12:25:16.000000Z","updated_at": "2022-11-02T12:25:16.000000Z"},),
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="token_type", type="string", example="Bearer"),
     *              @OA\Property(property="expires_in", type="number", example="86400"),
     *              @OA\Property(property="access_token", type="token", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOTAwNDU1YTJmZGY4YzQ3YmQ1ODQxODMwZDQzN2JhNWM4NDJhZDdlNTAwMDNiODBlNjJhZjFlYTJhZDhiZTAxY2FkMjdiOWIxZjkxZjkwZjMiLCJpYXQiOjE2Njc0MzQ1NTYuMTQ0MTk5LCJuYmYiOjE2Njc0MzQ1NTYuMTQ0MjAyLCJleHAiOjE2OTg5NzA1NTYuMDI2NDg3LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.LzRYUmOk7uCScSwi_RTR4nuKQ3tOeKiGAjM7PxvQUW9Fs93K3HqIxvIIBAnniTc8ee1plfy7qikxEsfOsJLaMjm7c2hmFyy4hhRxYJAxc0lAnA6G7i0Ue-kFeivs7cSzwEzvGRhnLgtv4J7vYbvJA8yh9KrvqvsB19_Y-RPzng6d4ZKR-Ij6nF5GfN-QCNP4nyqRfiPsuQVMJuP9KiWyacvpr7XUX2wjcY8xQtxQBfvaSNehujhX8B-0f0CwGpOBTi3fDdpvZbeqa9s7ZBDb9bSWahZ90XIvpls5bCnt7zuhXGte9HKeFulrISeCj-onCnGvqzFsxDESdCDVU0MC2hbXwHHLgQbBWhG0EM2u86VgdAqPktNsAK-4l7_zCRHuGMnT_qpY4He1e-MvECDQ8wfGtunKfizwTzxJ2VrsFPkWl90fldcmfTt0Mbd_HJfPCw4XViUYBQAPgSUKxKsuPN5RNott4zzCTtWJKA8Ot9L05H4zQQB6yWrj8juPUA3qBVO5jDC12SM32mrVwUTWXEfZa8EDUQ2MQytPR-uflfkTdYVPYvoRQPoEAAmD1oz4_kas0F23xSza6K8mwA3pT1Znqjbz4fDDrcoxQDENsPwTqfuZBWFfz5OXOh6f_5NO41g_qOWEdzwGLaST9p-8ALUC8oExDIfQq2EvM3Cqh-M"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid user",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Credientials does not match"}}),
     *          )
     *      )
     * )
     *
     * @throws ValidationException
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validateData = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required',
            'remember' => 'required|boolean'
        ]);

        if ($validateData->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors', 'errors' => $validateData->errors()], 422);
        }

        if (auth()->attempt($request->only('email', 'password'), (bool)$request->remember)) {
            $user = auth()->user();
            $token = $this->getTokenAndRefreshToken( $request->email, $request->password, 'user');

            return response()->json([
                'success' => true,
                'message' => 'User successfully login!',
                'user' => $user,
                'token' => $token,
            ], 200);
        }
        return response()->json(['success' => false, 'message' => 'Authentication failed'], 422);
    }

    public function getTokenAndRefreshToken($email, $password, $scope = 'user')
    {
        $oClient = OClient::where('password_client', 1)->where('provider', 'users')->first();;

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

        return json_decode((string) $result->getContent(), true);

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

    public function refreshToken(Request $request): \Illuminate\Http\JsonResponse
    {
        $validateData = Validator::make($request->all(), [
            'refresh_token' => 'required'
        ]);

        if ($validateData->fails()) {
            return response()->json(['success' => false, 'errors' => $validateData->errors()], 422);
        }

        $oClient = OClient::where('password_client', 1)->first();

        $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'scope' => 'user'
        ]);

        return response()->json($response->json());
    }
}
