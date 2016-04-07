<?php

    namespace si\noticias;

    use si\abs\Options;

    class Categorias extends Options {

	public function __construct() {
	    $this->ref = 'noticias';
	    $this->refid = 0;
	}

	public function getCategorias() {
	    return $this->getOptions();
	}

    }
    