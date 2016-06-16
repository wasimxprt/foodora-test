<?php

    define('BASE_PATH', realpath(dirname(__FILE__)));

    // Simple loader to work with namespaces
    function my_autoloader($class) {
        $filename = BASE_PATH . '/../' . str_replace('\\', '/', $class) . '.php';
        include($filename);
    }

    spl_autoload_register('my_autoloader');

    // Connecting to the Database through PDO
    // Using an ORM would be a bit too much for this project but and I don't really like mysqli native functions
    $provider = function () {
        $instance = new PDO("mysql:host=localhost;dbname=foodora-test", 'root', '');
        $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $instance;
    };