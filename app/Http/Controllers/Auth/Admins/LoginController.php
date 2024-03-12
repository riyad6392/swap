<?php

namespace App\Http\Controllers\Auth\Admins;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Support\Auth\Facade\PassportService;
use Laravel\Passport\Passport;


class LoginController extends Controller
{
    /**
     * Login Admin
     *
     * @OA\Info(
     *      title="SWAP API Documantation",
     *      version="latest",
     *  )
     * @OA\Post (
     *     path="/api/admin/login",
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
    public function login(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required',
            'remember' => 'boolean'
        ]);

        if ($validateData->fails()) {
            return response()->json(['success' => false, 'errors' => $validateData->errors()], 422);
        }

        if (auth()->guard('admins')->attempt($request->only('email', 'password'), (bool)$request->remember)) {
            config(['auth.guards.api.provider' => 'admins']);

//            return response()->json(['success' => true, 'message' => 'rEQUST'], 200);

            $user = auth()->guard('admins')->user();
            $strToken = $user->createToken('API Token',['user'])->accessToken;;
            $token = Passport::
//            $expiration = Carbon::parse(Carbon::now()->addDays($this->expiresInDays))->diffInSeconds(Carbon::now()) ;

//            $refresh_token = $this->getTokenAndRefreshToken( $request->email, $request->password, 'user');
            return response()->json([
                'success' => true,
                'user' => $user,
                'token_type' => 'Bearer',
//                'expires_in' => $expiration,
                'access_token' => $strToken,
                //                'refresh_token' => $refresh_token,
            ], 200);
        }
        return response()->json(['success' => false, 'errors' => ['message' => 'Authentication failed']], 422);
//        https://www.webappfix.com/post/laravel-9-multi-authentication-guard-passport-api-example.html
    }
}
