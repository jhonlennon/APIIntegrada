<?php

    namespace si\portfolio;

    use si\abs\Options;

    class Categorias extends Options {

	public function __construct() {
	    $this->ref = 'portfolio';
	    $this->refid = 0;
	}

	public function getCategorias() {
	    return $this->getOptions();
	}

    }
    