<?php
/**
 * This file contains the CSRF class.
 */

namespace Models;

use Core\Database as DB;
use Models\User as User;

/**
 * Generate, retrieve and verify CSRF tokens with this class.
 */
class CSRF
{
    /**
     * The length of a CSRF token generated within this class.
     *
     * @var int
     */
    const TOKENLENGTH = 24;

    /**
     * Get the token from the session global.
     */
    public static function getToken()
    {
        return $_SESSION['csrf_token'];
    }

    /**
     * Returns a generated token with the length defined in TOKENLENGTH.
     *
     * @return string
     */
    public static function generate(): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $newToken = '';

        // Generate random string.
        for ($i = 0; $i < self::TOKENLENGTH; $i++) {
            $randomIndex = rand(0, strlen($chars) - 1);
            $newToken .= $chars[$randomIndex];
        }

        return $newToken;
    }

    /**
     * Verifies the token in the session with the token in the database.
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
