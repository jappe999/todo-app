<?php

namespace Models;

use Core\Database as DB;
use Models\User as User;

class CSRF
{
    private static $tokenLength = 24;

    public static function getToken()
    {
        return $_SESSION['csrf_token'];
    }

    /**
     * Returns a generated token of $tokenLength length.
     *
     * @return string
     */
    public static function generate(): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $newToken = '';

        // Generate random string.
        for ($i = 0; $i < self::$tokenLength; $i++) {
            $randomIndex = rand(0, strlen($chars) - 1);
            $newToken .= $chars[$randomIndex];
        }

        return $newToken;
    }

    /**
     * Verifies the token of the session with the token in the database.
     *
     * @return bool
     */
    public static function verify(): bool
    {
        $token         = self::getToken();
        $userId        = $_SESSION['id'];
        $databaseToken = User::byId($userId)->getToken();

        return ($databaseToken === $token);
    }
}
