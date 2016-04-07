<?php

    namespace si\outros;

    use si\APIIntegrada;

    class Botoes {

	private static $botoes = [];
	private static $index = [];

	public static function getBotoes() {
	    if (!self::$botoes) {
		$busca = APIIntegrada::exec('botoes', null, 15);
		foreach ($busca as $i => $v) {
		    self::$botoes[$i] = $v;
		    self::$index[$v->ref] = $i;
		}
	    }
	    return self::$botoes;
	}

	public static function detalhes($ref) {
	    $botoes = self::getBotoes();
	    $index = isset(self::$index[$ref]) ? self::$index[$ref] : null;
	    return $index !== null ? $botoes[$index] : null;
	}

    }
    