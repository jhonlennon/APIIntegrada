<?php

    namespace si\noticias;

    use si\APIIntegrada;
    use si\helpers\Cache;

    class Noticias {

	private static $noticias;

	/** @var Cache */
	private static $cache;

	static function __init() {
	    if (!self::$noticias) {
		self::$cache = new Cache('noticias.registros', 15);
		self::$noticias = self::$cache->getContent() ? : [];
	    }
	}

	function busca(array $parans = null, $page = 1, $forPage = 20) {
	    $noticias = APIIntegrada::exec('noticias', APIIntegrada::extend($parans, [
				'page' => (int) $page,
				'forpage' => $forPage,
	    ]));

	    foreach ($noticias->data as $noticia) {
		self::$noticias[$noticia->urlamigavel] = $noticia;
	    }

	    return $noticias;
	}

	function detalhes($url) {

	    # Verificando no cache
	    if (!empty(self::$noticias[$url])) {
		return self::$noticias[$url];
	    }

	    # Buscando a notícia
	    $noticia = APIIntegrada::exec('noticias/detalhes', [
			'urlamigavel' => $url,
	    ]);

	    # Notícia encontrada
	    if ($noticia) {
		self::$noticias[$noticia->urlamigavel] = $noticia;
		return $noticia;
	    }
	    # Notícia inválida
	    else {
		return false;
	    }
	}

	function __destruct() {
	    self::$cache->setContent(self::$noticias);
	}

    }

    Noticias::__init();
    