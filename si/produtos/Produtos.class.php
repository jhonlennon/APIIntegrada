<?php

    namespace si\produtos;

    use si\APIIntegrada;
    use si\helpers\Cache;
    use stdClass;

    class Produtos {

	private static $cache;
	private static $produtos = [];

	static function init() {
	    if (!self::$cache) {
		self::$cache = new Cache('produtos.all', 15);
		self::$produtos = self::$cache->getContent();
	    }
	}

	/**
	 * Faz uma busca por produtos do site
	 * @param array $parans
	 * @param int $page
	 * @param int $forPage
	 * @return stdClass
	 */
	function busca(array $parans = null, $page = 1, $forPage = 20) {
	    $produtos = APIIntegrada::exec('produtos', APIIntegrada::extend($parans, [
				'page' => (int) $page,
				'forpage' => $forPage,
	    ]));

	    foreach ($produtos->data as $produto) {
		self::$produtos[$produto->urlamigavel] = $produto;
	    }

	    return $produtos;
	}

	function detalhes($url) {
	    if (isset(self::$produtos[$url])) {
		return self::$produtos[$url];
	    } else {
		$produto = APIIntegrada::exec('produtos/detalhes', ['urlamigavel' => $url]);
		self::$produtos[$produto->urlamigavel] = $produto;
		return $produto;
	    }
	}

	function __destruct() {
	    self::$cache->setContent(self::$produtos);
	}

    }

    Produtos::init();
    