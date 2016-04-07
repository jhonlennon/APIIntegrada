<?php

    function si_autoload($class) {
	$file = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, __DIR__ . '/../' . $class) . '.class.php';
	if (file_exists($file)) {
	    include $file;
	}
    }

    spl_autoload_register('si_autoload');

    