<?php

    namespace si\outros;

    use si\APIIntegrada;

    class Dados {

	private static $dados;

	public static function getDados() {
	    return self::$dados ? : self::$dados = APIIntegrada::exec('dados', null, 15);
	}

    }
    