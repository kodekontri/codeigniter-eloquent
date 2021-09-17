<?php

namespace App\Controllers\Api;

use App\Models\User;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;

class Users extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $users = User::all();
        return $this->respond($users);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $user = User::find($id);
        if (!$user) return $this->failNotFound('User not found');
        return $this->respond($user);
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $validated = $this->validate([
            'fullname' => 'if_exist|required',
            'username' => 'if_exist|required|is_unique[users.username]',
        ]);

        if (!$validated){
            return $this->failValidationErrors($this->validator->getErrors());
        }

        try {
            if (!$user = User::find($id)) return $this->failNotFound('User not found');
            $data = $this->request->getVar(['fullname', 'username'], FILTER_SANITIZE_STRING);

            if (in_array('', $data)){
                return $this->failValidationErrors('Empty fields not accepted');
            }

            $user->update($data);
            return $this->respondUpdated($user, 'information updated');

        }catch (\Throwable $e){
            return $this->failServerError($e);
        }
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
    }
}
