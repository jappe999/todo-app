<?php

namespace Controllers;

use Core\Database as DB;
use Models\User as User;

/**
 * UserController
 */
class UsersController extends Controller
{
    /**
     * Get the ids, names and e-mail addresses of all users.
     *
     * @return string
     */
    public function getAll(): string
    {
        $query = 'SELECT id, name, email FROM users';
        $stmt  = DB::prepare($query);

        try {
            $stmt->execute();

            $status = 'success';
            $data   = $stmt->fetchAll();

            return json_encode(compact('status', 'data'));
        } catch (PDOException $e) {
            $status = 'error';
            $error  = 'Could not select users.';

            return json_encode(compact('status', 'error'));
        }

    }

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
