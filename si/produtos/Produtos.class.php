<?php

    namespace si\produtos;

    use si\abs\ValueObject;
    use si\APIIntegrada;
    use si\helpers\Cache;
    use si\helpers\Registros;

    class Produtos {

        protected $_type = 'produto';

        /**
         * 
         * @staticvar Cache $_cache
         * @return Cache
         */
        protected static function _cache()
        {
            static $_cache = null;
            if (!$_cache) {
                $_cache = new Cache(str_replace(['\\', '/'], '.', get_called_class()) . '.all', 15);
            }
            return $_cache;
        }

        /**
         * 
         * @staticvar array $_array_cache
         * @param string $key
         * @param \stdClass $value
         * @return array
         */
        protected static function _array_cache($key = null, $value = null)
        {
            static $_array_cache = null;
            if ($_array_cache === null) {
                $_array_cache = self::_cache()->getContent();
            }
            if ($key !== null) {
                $_array_cache[$key] = $value;
            }
            return $_array_cache;
        }

        /**
         * Faz uma busca por produtos do site
         * @param array $parans
         * @param int $page
         * @param int $forPage
         * @return Registros
         */
        function busca(array $parans = null, $page = 1, $forPage = 20)
        {
            $busca = APIIntegrada::exec('produtos', APIIntegrada::extend($parans, [
                                'type' => $this->_type,
                                'page' => (int) $page,
                                'forpage' => $forPage,
            ]));

            $busca->data = ValueObject::converter($busca->data, ProdutoVO::class);

            foreach ($busca->data as $produto) {
                self::_array_cache($produto->getUrlamigavel(), $produto);
            }

            return new Registros($busca);
        }

        /**
         * Retorna as variações do produto
         * @param int $idProduto
         * @return Registros
         */
        function variacoes($idProduto)
        {
            return new Registros(APIIntegrada::exec('produtos', ['produtoref' => $idProduto]));
        }

        /**
         * Retorna os detalhes
         * @param string $url_id URLAmigavel ou id do produto
         * @return array
         */
        static function detalhes($url_id)
        {

            $value = $url_id;
            $field = 'id';

            if (!is_numeric($value)) {
                $field = 'urlamigavel';
            }

            return APIIntegrada::exec('produtos/detalhes', [$field => $value], 15);
        }

        /**
         * Gravando resultados no cache
         */
        function __destruct()
        {
            self::_cache()->setContent(self::_array_cache());
        }

    }
    