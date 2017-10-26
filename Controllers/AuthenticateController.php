<?php

namespace Controllers;

use Models\User as User;

/**
 * AuthenticateController
 */
class AuthenticateController extends Controller
{
    /**
     * Get the login page.
     *
     * @return object|string
     */
    public function getLogin()
    {
        if (User::authenticate())
            return redirect('/');

        return view('login.view.php');
    }

    /**
     * Get the register page.
     *
     * @return object|string
     */
    public function getRegister()
    {
        if (User::authenticate())
            return redirect('/');

        return view('register.view.php');
    }

    /**
     * Login to the application
     *
     * @return object
     */
    public function login()
    {
        if (User::login())
            return redirect('/');

        return redirect('/login');
    }

    /**
     * Register a user in the application.
     *
     * @return object
     */
    public function register()
    {
        if (User::register()) {
            User::login();
            return redirect('/');
        }

        return redirect('/register');
    }

    /**
     * Logout of the application.
     *
     * @return object
     */
    public function logout()
    {
        User::logout();
        redirect('/');
    }
}
