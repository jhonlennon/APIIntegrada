<?php

    namespace si\anuncios;

    use si\APIIntegrada;
    use stdClass;

    class Categorias {

	private static $categorias = [];
	private static $index = [];

	static function getCategorias() {
	    if (!self::$categorias) {
		$busca = APIIntegrada::exec('anuncios/categorias', null, 15);
		$i = 0;
		foreach ($busca as $v) {
		    self::$categorias[$i] = new stdClass;
		    self::$categorias[$i]->categoria = $v->categoria;
		    self::$categorias[$i]->subcategorias = !empty($v->subcategorias) ? $v->subcategorias : null;
		    self::$index[$v->categoria->urlamigavel] = $i;
		    if (self::$categorias[$i]->subcategorias) {
			$i ++;
			foreach ($v->subcategorias as $v) {
			    self::$categorias[$i] = $v;
			    self::$index[$v->urlamigavel] = $i;
			    $i ++;
			}
		    } else {
			$i ++;
		    }
		}
	    }
	    return self::$categorias;
	}

	static function detalhes($url) {
	    $categorias = self::getCategorias();
	    $index = isset(self::$index[$url]) ? self::$index[$url] : null;
	    return $index !== null ? $categorias[$index] : null;
	}

    }
    