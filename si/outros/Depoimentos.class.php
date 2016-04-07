<?php

    namespace si\outros;

    use si\APIIntegrada;

    class Depoimentos {

	private static $depoimentos = [];
	private static $index = [];

	public static function getDepoimentos() {
	    if (!self::$depoimentos) {
		$busca = APIIntegrada::exec('depoimentos', ['page' => 1, 'forpage' => 500], 15)->data;
		foreach ($busca as $i => $v) {
		    self::$depoimentos[$i] = $v;
		    self::$index[$v->id] = $i;
		}
	    }
	    return self::$depoimentos;
	}

	public static function detalhes($id) {
	    $depoimentos = self::getDepoimentos();
	    $index = isset(self::$index[$id]) ? self::$index[$id] : null;
	    return $index !== null ? $depoimentos[$index] : null;
	}

    }
    