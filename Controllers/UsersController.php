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
        if ($userId === 'me') {
            $userId = $_SESSION['id'];
        }
        $user = User::byId($userId)->getAll();
        return json_encode($user);
    }
}
