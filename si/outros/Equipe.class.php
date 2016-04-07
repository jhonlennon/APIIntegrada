<?php

    namespace si\outros;

    use si\APIIntegrada;

    class Equipe {

	private static $integrantes = [];
	private static $index = [];

	static function getIntegrantes() {
	    if (!self::$integrantes) {
		$busca = APIIntegrada::exec('equipe', ['page' => 1, 'forpage' => 500], 15)->data;
		foreach ($busca as $i => $v) {
		    self::$integrantes[$i] = $v;
		    self::$index[$v->urlamigavel] = $i;
		}
	    }
	    return self::$integrantes;
	}

	public static function detalhes($url) {
	    $integrantes = self::getIntegrantes();
	    $index = isset(self::$index[$url]) ? self::$index[$url] : null;
	    return $index !== null ? $integrantes[$index] : null;
	}

    }
    