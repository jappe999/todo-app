<?php

function registerClass ($class)
{
    $class = str_replace('\\', '/', $class);
    $path  = ROOT . "/$class.php";

    include_once $path;
}

spl_autoload_register("registerClass");
