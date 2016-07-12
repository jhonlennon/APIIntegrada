<?php

    namespace si\anuncios;

    use si\APIIntegrada;

    class Funcionalidades {

	private static $funcionalidades = [];
	private static $index = [];

	static function getFuncionalidades() {
	    if (!self::$funcionalidades) {
		$busca = APIIntegrada::exec('anuncios/funcionalidades');
		foreach ($busca as $i => $v) {
		    self::$funcionalidades[$i] = $v;
		    self::$index[$v->urlamigavel] = $i;
		}
	    }
	    return self::$funcionalidades;
	}

	static function detalhes($url) {
	    $funcionalidades = self::getFuncionalidades();
	    $index = isset(self::$index[$url]) ? self::$index[$url] : null;
	    return $index !== null ? $funcionalidades[$index] : null;
	}

    }
    