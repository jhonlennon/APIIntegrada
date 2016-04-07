<?php

    namespace si\noticias;

    use si\APIIntegrada;

    class Autores {

	static $autores = [];

	/**
	 * Retorna a lista de Autores do Site
	 * @return array
	 */
	static function getAutores() {
	    if (!self::$autores) {
		$busca = APIIntegrada::exec('noticias/autores', ['page' => 1, 'forpage' => 500]);
		foreach ($busca as $autor) {
		    self::$autores[$autor->urlamigavel] = $autor;
		}
	    }
	    return self::$autores;
	}

	/**
	 * Retorna o Autor filtrado pela UrlAmigavel
	 * @param string $url
	 * @return array|false
	 */
	static function detalhes($url) {
	    $autores = self::getAutores();
	    return !empty($autores[$url]) ? $autores[$url] : null;
	}

    }
    