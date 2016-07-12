<?php

    namespace si\anuncios;

    use si\APIIntegrada;
    use si\helpers\Cache;

    class Dicionario {

	private static $words;

	/**
	 * Retorna um array com todos os títulos de anúncios ativos
	 * @return array
	 */
	static function getDicionario() {
	    return APIIntegrada::exec('anuncios/dicionario', null, 15);
	}

	/**
	 * Retorna a lista de títulos dos anúncios
	 * @return array
	 */
	static function getTitulos() {
	    $titles = self::getDicionario();
	    foreach ($titles as $key => $value) {
		$titles[$key] = $value->title;
	    }
	    return $titles;
	}

	/**
	 * Retorna a lista de palavras chaves
	 * @return array
	 */
	static function getKeyWods() {
	    if (!self::$words) {
		$cache = new Cache('anuncios.words');
		if (!self::$words = $cache->getContent()) {
		    self::$words = [];
		    foreach (self::getDicionario() as $v) {
			if ($v->keywords) {
			    $words = self::explodeWords($v->keywords);
			    foreach ($words as $word) {
				if (!in_array($word, self::$words)) {
				    self::$words[] = $word;
				}
			    }
			}
		    }
		}
		$cache->setContent(self::$words);
	    }
	    return self::$words;
	}

	private static function explodeWords($string) {
	    $words = explode(',', $string);
	    foreach ($words as $key => $word) {
		$word = trim($word);
		if ($word) {
		    $words[$key] = $word;
		} else {
		    unset($words[$key]);
		}
	    }
	    return $words;
	}

    }
    