<?php

    namespace si\servicos;

    use si\abs\Options;

    class Categorias extends Options {

        public function __construct()
        {
            $this->ref = 'servicos';
            $this->refid = 0;
        }

        public function getCategorias()
        {
            return $this->getOptions();
        }

    }
    