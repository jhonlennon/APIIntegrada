<?php

    namespace si\outros;

    class Parceiros {

	private static $parceiros = [];
	private static $index = [];

	static function getParceiros($page = 1, $forpage = 500) {
	    if (!self::$parceiros) {
		$busca = \si\APIIntegrada::exec('parceiros', ['page' => 1, 'forpage' => 500], 15)->data;
		foreach ($busca as $i => $v) {
		    self::$parceiros[$i] = $v;
		}
	    }
	    return self::$parceiros;
	}

    }
    