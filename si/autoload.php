<?php

    function si_autoload($class)
    {
        $path = dirname(__DIR__);
        $file = $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.class.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    spl_autoload_register('si_autoload');

    