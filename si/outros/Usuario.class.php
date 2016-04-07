<?php

    namespace si\outros;

    use si\APIIntegrada;
    use si\helpers\Cache;

    class Usuario {

	private $user;

	public function __construct() {
	    $this->user = isset($_SESSION[self::class]) ? $_SESSION[self::class] : null;
	}

	function logIn($login, $senha) {
	    $this->user = APIIntegrada::exec('usuarios/login', [
			'login' => $login,
			'senha' => $senha,
			    ], 15);
	    return $this;
	}

	function logOut() {
	    $_SESSION[self::class] = $this->user = null;
	    return $this;
	}

	function estaLogado() {
	    return $this->user ? true : false;
	}

	function getDados() {
	    return $this->user;
	}

	/**
	 * Token de login na aplicação
	 * @return string
	 */
	function getToken() {
	    if ($this->estaLogado()) {
		return $this->getDados()->token;
	    } else {
		return null;
	    }
	}

	/**
	 * Salva o login na sessão
	 */
	function __destruct() {
	    $_SESSION[self::class] = $this->user;
	}

    }
    