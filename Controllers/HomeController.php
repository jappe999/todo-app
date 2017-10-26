<?php

namespace Controllers;

use Models\User as User;
use Core\Database as DB;
use Models\CSRF as CSRF;

/**
 * HomeController
 */
class HomeController extends Controller
{
    /**
     * Get the home page view.
     *
     * @return string
     */
    public function get()
    {
        if (User::authenticate())
            return view('home.view.php');

        return redirect('/login');
    }

    /**
     * Test purposes.
     */
    function test()
    {
        var_dump(User::authenticate());
    }
}
