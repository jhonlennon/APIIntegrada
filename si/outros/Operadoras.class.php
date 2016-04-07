<?php

    namespace si\outros;

    use si\abs\Options;

    class Operadoras extends Options {

	private static $instance;

	public function __construct() {
	    $this->ref = 'operadoras';
	    $this->refid = 0;
	    $this->geral = 1;
	}

	public static function getOperadoras() {
	    return self::_instance()->getOptions();
	}

	private static function _instance() {
	    return self::$instance ? : self::$instance = new self;
	}

    }
    