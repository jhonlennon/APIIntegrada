<?php

    namespace si\abs;

    use si\helpers\Cache;

    abstract class Categorias {

	protected $ref = '';
	protected $refid = 0;
	protected $categorias;

	/** @var Cache */
	static $cache;

	static function __init() {
	    if (!self::$cache) {
		self::$cache = new Cache;
	    }
	}

	/**
	 * Retorna os detalhes da categoria pela urlAmigável
	 * @param string $url
	 * @return array|false
	 */
	function detalhes($url = null) {
	    foreach (self::$categorias as $categoria) {
		if ($categoria['categoria']['urlamigavel'] == $url) {
		    return $categoria;
		}
		if (!empty($categoria['subcategorias'])) {
		    foreach ($categoria['subcategorias'] as $categoria) {
			if ($categoria['urlamigavel'] == $url) {
			    return $categoria;
			}
		    }
		}
	    }
	    return false;
	}

	public function __destruct() {
	    self::$cache->setKey("categorias.{$this->ref}-{$this->refid}")->setContent(self::$categorias);
	}

    }

    Categorias::__init();
    