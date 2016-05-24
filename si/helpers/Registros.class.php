<?php

    namespace si\helpers;

    use stdClass;

    class Registros {

        /**
         * Dados da busca
         * @var stdClass
         */
        private $dados;

        function __construct($dados)
        {
            $this->dados = $dados;
        }

        /**
         * 
         * @return int
         */
        function getTotalPaginas()
        {
            return $this->dados->pages;
        }

        /**
         * 
         * @return int
         */
        function getPaginaAtual()
        {
            return $this->dados->page;
        }

        /**
         * 
         * @return int
         */
        function getTotalRegistros()
        {
            return $this->dados->count;
        }

        /**
         * Retorna a lista de registros
         * @return stdClass[]
         */
        function getRegistros()
        {
            return $this->dados->data;
        }

    }
    