<?php

namespace Controllers;

use Models\User as User;
use Core\Request as Request;

/**
 * Controller class
 */
class Controller
{
    public function __construct()
    {
        if (!User::authenticate() &&
            Request::getPath() !== '/login' &&
            Request::getPath() !== '/register')
            return redirect('/login');
    }
}
