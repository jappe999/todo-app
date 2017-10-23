<?php

namespace Controllers;

use Models\User as User;

/**
 * HomeController
 */
class UsersController extends Controller
{

    /**
     * Get the user's id, name and e-mail.
     *
     * @param int|string $userId
     * @return string
     */
    public function getUser($userId): string
    {
        if ($userId === 'me')
            $userId = $_SESSION['id'];

        if (!empty($userId)) {
            $status   = 'success';
            $data     = User::byId($userId)->getAll();
            $response = compact('status', 'data');
        } else {
            $status   = "error";
            $error    = "User id cannot be empty.";
            $response = compact('status', 'error');
        }

        return json_encode($response);
    }
}
