<?php

namespace Controllers;

use Models\User as User;

/**
 * HomeController
 */
class UsersController extends Controller
{

    public function getUser($userId)
    {
        if ($userId === 'me')
            $userId = $_SESSION['id'];

        if (!empty($userId))
            $data = User::byId($userId)->getAll();
        else
            $data = [];

        $status = 'success';
        return json_encode(compact('status', 'data'));
    }
}
