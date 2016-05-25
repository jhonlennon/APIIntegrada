<?php

    namespace si\helpers;

    use si\helpers\pagination\Google;
    use si\helpers\pagination\Jumping;
    use si\helpers\pagination\Simple;
    use si\helpers\pagination\Yahoo;

    class Pagination {

        /**
         * valor padrão para a página atual
         *
         * protected - somente poderá ser acessado dentro da própria classe em que foram declarados e a 
         * partir de classes descendentes, mas não poderão ser acessados a partir do programa que faz uso dessa classe 
         *
         * static -  atributos dinâmicos como as propriedades de um objeto, mas estão relacionados à classe, são compartilhadas
         * entre todos os objetos de uma mesma classe
         *
         * @ var int
         */
        protected $_page = 1;

        /**
         * valor padrão para o total de registros por página
         *
         * @ var int
         */
        protected $_recordsPage = 10;

        /**
         * valor padrão para o retorno do início para cláusulas de banco de dados
         * essa classe trabalha com arrays e inteiros, o valor retornado em questão serve apenas para organização
         * e prevenção de erros, ou seja dentro de um método é feita a conta do início para cláusulas sql, evitando
         * que os resultados da sua paginação não sejam iguais aos resultados retornados pelo banco.
         * A paginação e o banco trabalharão com o mesmo valor inicial evitando erros.
         * Você consegue montar a paginação sem ele, esse valor é apenas de retorno
         *
         * @ var int
         */
        protected $_start = 0;

        /**
         * total de registros retornados pelo banco ou array de resultados
         *
         * @ var int
         */
        protected $_totalRecords = null;

        /**
         * valor padrão para os compomentes próximos e anteriores
         * valor opcional você pode fazer a paginação sem ele
         *
         * @ var int
         */
        protected $_nextPreviousValue = 15;

        /**
         * retorna os índices com as páginas geradas pelo total de registros, ou tamanho do array
         * é um valor de retorno (array) e também é opcional você pode trabalhar sem ele
         *
         * @ var array
         */
        protected $_indexes = array();

        /**
         * define o total de índices das páginas que serão mostrados, valor padrão de 1 à 10.
         *
         * @ var int
         */
        protected $_perPage = 10;

        /**
         * define as configurações extras para paginações do tipo Delicious ou Google que usam respactivamente 
         * esse parâmetro como índices extras, e total de índices inicial
         *
         * @ var int
         */
        protected $_extraSettings = 4;

        /**
         * retorna o total de páginas
         *
         * @ var int
         */
        protected $_totalPages = null;

        /**
         * retorna a próxima página
         *
         * @ var int
         */
        protected $_nextPage = 1;

        /**
         * retorna a página anterior
         *
         * @ var int
         */
        protected $_previousPage = 1;

        /**
         * retorna um array com todos os índices das páginas existentes
         *
         * @ var array
         */
        protected $_arrayPages = array();

        /**
         * Construtor da classe, nele você informa a página atual e o total de registros por página
         * Ele retorna para você as informações de início em cláusulas sql
         */
        public function __construct($page, $recordsPage, $totalRecords)
        {

            /*
             * se a página atual não for um valor numérico ou for igual a zero
             * então a página atual recebe o valor definido em $_page
             */
            if (is_numeric($page) and $page > 0) {
                $this->_page = $page;
            }

            /*
             * se o valor de resultados por página não for numérico
             * então o valor de resultados por página recebe o valor definido em $_recordsPage
             */
            if (is_numeric($recordsPage) and $recordsPage > 0) {
                $this->_recordsPage = $recordsPage;
            }

            /*
             * Se o total de registros ou dimensão do array não for um número ou for menor ou igual a zero
             * o método retorna falso
             */
            if (is_numeric($totalRecords) and $totalRecords > 0) {
                $this->_totalRecords = $totalRecords;
            }

            /*
             * criando o valor de início para cláusulas sql
             * esse valor é apenas um valor de retorno, você não é obrigado a trabalhar com ele
             * a função desse valor é garantir que a sua cláusula limit tenha um valor de início igual
             * ao valor calculado pela paginação
             */
            $this->_start = ($this->_page - 1) * $this->_recordsPage;

            /*
             * Total de páginas é igual ao total de registros dividido pelo total de registros por página com o valor arredondado para cima
             */
            $this->_totalPages = ceil($this->_totalRecords / $this->_recordsPage);
        }

        /**
         * Usando o método mágico __get para retornar os valores das propriedades e métodos dessa classe
         */
        public function __get($property)
        {
            return $this->$property;
        }

        /**
         * Retornando a próxima página, página anterior, total de páginas, primeira página, e índices de páginas
         */
        public function CreatePages($pager = null, $marcadores = 10, $extraSettings = null)
        {

            /*
             * definindo a primeira página sempre no valor 1
             */
            $this->_firstPage = 1;

            /*
             * Calculando a próxima página
             */
            $nextPage = $this->_page + 1;

            if ($nextPage >= $this->_totalPages) {
                $nextPage = $this->_totalPages;
            }

            $this->_nextPage = $nextPage;

            /*
             * Calculando a página anterior
             */
            $previousPage = $this->_page - 1;
            if ($previousPage <= 1) {
                $previousPage = 1;
            }

            $this->_previousPage = $previousPage;

            /*
             * Retornando um array com todas as páginas
             */
            $this->_arrayPages = range(1, $this->_totalPages);


            /*
             * Verificando se o total de índices por página foi informado
             */
            if (!is_numeric($this->_perPage)) {
                $this->_perPage = $this->_perPage;
            }

            /*
             * Verificando se as informações extras foram informadas
             */
            if (!is_numeric($extraSettings)) {
                $extraSettings = $this->_extraSettings;
            }

            /*
             * Chamando a classe de acordo com o tipo informado
             */
            switch ($pager) {
                case "yahoo":
                    $indexes = new Yahoo;
                    $indexes = $indexes->ReturnIndexes($this->_page, $this->_totalPages, $marcadores, $extraSettings, $this->_arrayPages);

                    $this->_indexes = $indexes['index'];
                    $this->_initialIndex = $indexes['initialIndex'];
                    $this->_finalIndex = $indexes['finalIndex'];
                    break;

                case "google":
                    $indexes = new Google();
                    $indexes = $indexes->ReturnIndexes($this->_page, $this->_totalPages, $marcadores, $extraSettings, $this->_arrayPages);

                    $this->_indexes = $indexes['index'];
                    break;

                case "jumping":
                    $indexes = new Jumping;
                    $indexes = $indexes->ReturnIndexes($this->_page, $this->_totalPages, $marcadores, $this->_arrayPages);

                    $this->_indexes = $indexes['index'];
                    break;

                default:
                    $indexes = new Simple;
                    $indexes = $indexes->ReturnIndexes($this->_page, $this->_totalPages, $marcadores, $this->_arrayPages);

                    $this->_indexes = $indexes['index'];
                    break;
            }
        }

        /**
         * Retornando a página atual + o número de páginas passadas no parâmetro do método
         */
        public function Go($parameter)
        {
            $go = (int) $this->_page + $parameter;
            if ($go >= $this->_totalPages)
                $go = $this->_totalPages;

            return $go;
        }

        /**
         * Retornando a página atual - o número de páginas passadas no parâmetro do método
         */
        public function Back($parameter)
        {
            $back = (int) $this->_page - $parameter;
            if ($back <= 1)
                $back = 1;

            return $back;
        }

        function getIndexes()
        {
            return $this->_indexes;
        }

        function getPerPagina()
        {
            return $this->_perPage;
        }

        function getTotalPages()
        {
            return $this->_totalPages;
        }

    }
    