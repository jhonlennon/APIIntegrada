<?php

    namespace si\servicos;

    use si\produtos\Produtos;

    class Servicos extends Produtos {

        public function __construct()
        {
            $this->_type = 'servico';
        }

    }
    