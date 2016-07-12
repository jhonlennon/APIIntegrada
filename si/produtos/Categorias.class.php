<?php

    namespace si\produtos;

    use si\abs\Options;

    class Categorias extends Options {

        public function __construct()
        {
            $this->ref = 'produtos';
            $this->refid = 0;
        }

        public function getCategorias()
        {
            return $this->getOptions();
        }

    }
    