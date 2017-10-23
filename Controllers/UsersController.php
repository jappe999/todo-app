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
            $user = User::byId($userId)->getAll();
        else
            $user = [];

        return json_encode($user);
    }
}
