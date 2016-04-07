<?php

    namespace si\ecommerce;

    use si\APIIntegrada;
    use si\helpers\Cache;
    use si\outros\Usuario;

    class Pedidos {

	/** @var Usuario */
	private $user;

	/** Array contendo todos os pedidos */
	private $pedidos;

	/** @var Cache */
	private $cache;

	function __construct(Usuario $user) {
	    $this->user = $user;
	    $this->cache = new Cache(self::class);
	    $this->pedidos = isset($_SESSION[self::class]) ? $_SESSION[self::class] : [];
	}

	function busca($page = 1, $forPage = 10) {
	    $busca = APIIntegrada::exec('pedidos', [
			'loginToken' => $this->user->getToken(),
			'page' => $page,
			'forpage' => $forPage,
	    ]);

	    foreach ($busca->data as $pedido) {
		$this->pedidos[$pedido->token] = $pedido;
	    }

	    return $busca;
	}

	function detalhes($token) {
	    if (isset($this->pedidos[$token])) {
		return $this->pedidos[$token];
	    } else {

		$pedido = APIIntegrada::exec('pedidos/detalhes', [
			    'loginToken' => $this->user->getToken(),
			    'token' => $token,
		]);

		$this->pedidos[$token] = $pedido;

		return $pedido;
	    }
	}

	public function __destruct() {
	    $_SESSION[self::class] = $this->pedidos;
	}

    }
    