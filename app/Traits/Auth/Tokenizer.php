<?php

namespace App\Traits\Auth;

use App\Transformers\ArrayTransformer;
use App\User;
use Exception;
use Illuminate\Http\Request;
use JWT;
use Restable;

trait Tokenizer
{
    final protected function createToken(User $user, $expiration = 1209600 /*14 days*/)
    {
        $payload = [
            'sub'       => $user->id,
            'iat'       => $now = time(),
            'exp'       => $now + $expiration,
            'data'      => $user->toArray(),
        ];

        return JWT::encode($payload, $this->secretKey());
    }

    final protected function readToken(Request $request)
    {
        $payload = empty($token = $request->header('x-token-id'))
            ? null
            : (array) JWT::decode($token, $this->secretKey(), ['HS256']);

        return $this->validateToken($payload);
    }

    /**
     * @param array $payload
     * @return array
     * @throws Exception
     */
    protected function validateToken($payload)
    {
        if ($payload['exp'] < time()) {
            throw new \Exception("Expired");
        }

        return $payload;
    }

    /**
     * @return mixed
     */
    protected function secretKey()
    {
        return config('key');
    }
}


