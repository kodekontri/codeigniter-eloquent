<?php

namespace App\Controllers\Api;

use App\Models\User;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Auth extends ResourceController
{
    use ResponseTrait;

    public function register()
    {
        $validated = $this->validate([
            'fullname' => 'required',
            'username' => 'required|is_unique[users.username]',
            'phone' => 'is_unique[users.phone]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required'
        ]);

        if (!$validated){
            return $this->failValidationErrors($this->validator->getErrors());
        }

        try {
            $user = User::create($this->request->getVar([
                'fullname','username', 'email', 'password'
            ]), FILTER_SANITIZE_STRING);
            $user->password = password_hash($user->password, PASSWORD_BCRYPT);
            $user->save();
            return $this->respondCreated([
                'message' => "Account Created",
                'user' => $user
            ]);
        }catch (\Throwable $e){
            return $this->failServerError($e);
        }
    }

    public function login()
    {
        $validated = $this->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if (!$validated){
            return $this->failValidationErrors($this->validator->getErrors());
        }

        try {
            $email = $this->request->getVar('email', FILTER_SANITIZE_STRING);
            $password = $this->request->getVar('password', FILTER_SANITIZE_STRING);

            if (!$user = User::orWhere(['email'=>$email, 'username'=>$email])->first()){
                return $this->failNotFound('email or username not registered yet');
            }

            if (!password_verify($password, $user->password)){
                return $this->fail([
                    'message' => 'Incorrect password provided',
                    'error' => 'password is not correct'
                ], 403);
            }

            return $this->respond([
                'token' => User::createAccessToken($user),
                'user' => $user
            ]);
        }catch (\Throwable $e){
            return $this->failServerError($e);
        }
    }
}
