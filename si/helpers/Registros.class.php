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
         * Retorna a lista de registros
         * @return stdClass[]
         */
        function getRegistros()
        {
            return $this->dados->data;
        }

        /**
         * Monta a páginação
         * 
         * @return Pagination
         */
        function getPagination()
        {
            $paginacao = new Pagination($this->dados->page, $this->dados->forPage, $this->dados->count);
            return $paginacao;
        }

    }
    