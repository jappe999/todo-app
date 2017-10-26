<?php
/**
 * This file contains the user class.
 */

namespace Models;

use Core\Database as DB;
use Core\Request as Request;
use Models\CSRF;
use Exception;

/**
 * Create, read and delete users with this class.
 * Updating users is not supported.
 *
 * Note: This class is called statically.
 *
 */
class User
{
    /**
     * This is used to store database content.
     *
     * @var array
     */
    private static $user;

    /**
     * Retrieves database row with id.
     *
     * @param int $userId
     *
     * @return self
     */
    public static function byId(int $userId): self
    {
        $query = "SELECT * FROM users WHERE id=:user_id";

        $stmt = DB::prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        self::$user = $stmt->fetch();

        return new self;
    }

    /**
     * Retrieves database row with name.
     *
     * @param string $name
     *
     * @return self
     */
    public static function byName(string $name): self
    {
        $query = "SELECT id, name, email, password FROM users WHERE name=:name";

        $stmt = DB::prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        self::$user = $stmt->fetch();

        return new self;
    }

    /**
     * Retrieves database row with email.
     *
     * @param string $email
     *
     * @return self
     */
    public static function byEmail(string $email): self
    {
        $query = "SELECT id, name, email, password FROM users WHERE email=:email";

        $stmt = DB::prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        self::$user = $stmt->fetch();

        return new self;
    }

    /**
     * Retrieves database row with CSRF token.
     *
     * @param string $token
     *
     * @return self
     */
    public static function byToken(string $token): self
    {
        $query = "SELECT id, name, email, password FROM users WHERE csrf_token=:csrf_token";

        $stmt = DB::prepare($query);
        $stmt->bindParam(':csrf_token', $token);
        $stmt->execute();
        self::$user = $stmt->fetch();

        return new self;
    }

    public static function getAll(): array
    {
        return array(
            'id' => self::$user['id'],
            'name' => self::$user['name'],
            'email' => self::$user['email'],
        );
    }

    public static function getId(): int
    {
        return self::$user['id'] ?? 0;
    }

    public static function getName(): string
    {
        return self::$user['name'] ?? '';
    }

    public static function getEmail(): string
    {
        return self::$user['email'] ?? '';
    }

    public static function getPassword(): string
    {
        return self::$user['password'] ?? '';
    }

    public static function getToken(): string
    {
        return self::$user['csrf_token'] ?? '';
    }

    /**
     * Registers the user with the POST variables that he sent.
     *
     * @return bool
     */
    public static function register(): bool
    {
        $params   = Request::getParams();
        $id       = randomId();
        $name     = $params->get('name');
        $email    = $params->get('email');
        $password = encrypt($params->get('password'));

        $query    = "INSERT INTO users (id, name, email, password)
                     VALUES (:id, :name, :email, :password)";

        try {
            $stmt = DB::prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            return $stmt->execute() ? true : false;
        } catch (\PDOException $e) {
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * Authenticates the user with the id and CSRF token defined in $_SESSION.
     *
     * @return bool
     */
    public static function authenticate(): bool
    {
        if (!isset($_SESSION['id']) || !isset($_SESSION['csrf_token']))
            return false;

        $userId = $_SESSION['id'];

        if (empty($userId))
            return false;

        self::byId($userId);

        return (!empty(self::$user) && CSRF::verify());

    }

    /**
     * Logs the user in to the application.
     *
     * User logs in with their username and password
     */
    public static function login(): bool
    {
        $params = Request::getParams();

        if (Request::isPost() &&
            $params->has('name') &&
            $params->has('password')) {

            $name     = $params->get('name');
            $password = $params->get('password');
            $user     = self::byName($name);
            $userId   = $user->getId();

            if ($name === $user->getName() &&
                verifyHash($user->getPassword(), $password)) {

                $newToken = CSRF::generate();
                $query    = "UPDATE users SET csrf_token = :new_token WHERE id=:id";

                $stmt = DB::prepare($query);
                $stmt->bindParam(':new_token', $newToken);
                $stmt->bindParam(':id', $userId);

                if ($stmt->execute()) {
                    $_SESSION['id'] = $userId;
                    $_SESSION['csrf_token'] = $newToken;
                    return true;
                }
            }
        } else {
            throw new Exception('Login can only be requested with correct POST headers');
        }

        // If all else fails
        return false;
    }

    /**
     * Logs the user out of the application.
     *
     * Logs the user out of the application by unsetting id and csrf_token
     * in the array $_SESSION.
     */
    public static function logout()
    {
        unset($_SESSION['id']);
        unset($_SESSION['csrf_token']);
    }

    /**
     * Removes a row from the users table corresponding to the given user id.
     *
     * @return bool
     */
    public static function delete(): bool
    {
        $query = "DELETE FROM users WHERE id=:id";

        $stmt = DB::prepare($query);
        $stmt->bindParam(':id', self::$user['id']);

        return $stmt->execute() ? true : false;
    }
}
