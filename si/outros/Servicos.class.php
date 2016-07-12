<?php

    namespace si\outros;

    use si\APIIntegrada;

    class Servicos {

	private static $servicos = [];
	private static $index = [];

	static function getServicos($page = 1, $forpage = 500) {
	    if (!self::$servicos) {
		$busca = APIIntegrada::exec('servicos', ['page' => 1, 'forpage' => 500], 15)->data;
		foreach ($busca as $i => $v) {
		    self::$servicos[$i] = $v;
		    self::$index[$v->urlamigavel] = $i;
		}
	    }
	    return self::$servicos;
	}

	public static function detalhes($url) {
	    $servicos = self::getServicos();
	    $index = isset(self::$index[$url]) ? self::$index[$url] : null;
	    return $index !== null ? $servicos[$index] : null;
	}

    }
    