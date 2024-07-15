<?php

namespace App\Traits;

use Laravel\Passport\Client as OClient;

trait TokenTrait
{
    public static function getTokenAndRefreshToken($email, $password, $scope, $provider)
    {
        $oClient = OClient::where('password_client', 1)->where('provider', $provider)->first();

        if (!$oClient) {
            return false;
        }

        $response = request()->create('/oauth/token', 'post', [
                'grant_type'    => 'password',
                'client_id'     => $oClient->id,
                'client_secret' => $oClient->secret,
                'response_type' => 'token',
                'username'      => $email,
                'password'      => $password,
                'scope'         => $scope,
            ]
        );

        $result = app()->handle($response);

        return json_decode((string)$result->getContent(), true);
    }

}
