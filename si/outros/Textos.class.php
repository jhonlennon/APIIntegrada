<?php

    namespace si\outros;

    use si\APIIntegrada;

    class Textos {

	private static $textos = [];
	private static $index = [];

	public static function getTextos() {
	    if (!self::$textos) {
		$busca = APIIntegrada::exec('textos', ['page' => 1, 'forpage' => 500], 15);
		foreach ($busca as $i => $v) {
		    self::$textos[$i] = $v;
		    self::$index[$v->urlamigavel] = $i;
		}
	    }
	    return self::$textos;
	}

	public static function detalhes($url) {
	    $textos = self::getTextos();
	    $index = isset(self::$index[$url]) ? self::$index[$url] : null;
	    return $index !== null ? $textos[$index] : null;
	}

    }
    