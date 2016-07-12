<?php

    namespace si\outros;

    use si\APIIntegrada;

    class Imagens {

	private static $imagens = [];
	private static $index = [];

	public static function getImagens() {
	    if (!self::$imagens) {
		$busca = APIIntegrada::exec('imagens', null, 15);
		foreach ($busca as $i => $v) {
		    self::$imagens[$i] = $v;
		    self::$index[$v->urlamigavel] = $i;
		}
	    }
	    return self::$imagens;
	}

	public static function detalhes($url) {
	    $imagens = self::getImagens();
	    $index = isset(self::$index[$url]) ? self::$index[$url] : null;
	    return $index !== null ? $imagens[$index] : null;
	}

    }
    