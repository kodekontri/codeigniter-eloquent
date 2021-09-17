<?php

namespace App\Models;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $hidden = ['password'];
    public $guarded = [];

    public static function createAccessToken($user)
    {
        $payload = array(
            "iss" => base_url(),
            "iat" => time(),
            "exp" => time() + 60 * 60 * 24 * 7 * 4,
            "user" => $user
        );

        return JWT::encode($payload, env('JWT_ACCESS_TOKEN_SECRET'));
    }

    public static function verifyAccessToken($token)
    {
        try {
            return JWT::decode($token, env('JWT_ACCESS_TOKEN_SECRET'), array('HS256'));
        }catch (\Throwable $e){
            throw new \Exception('Invalid token');
        }
    }
}
