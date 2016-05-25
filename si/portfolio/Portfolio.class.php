<?php

    namespace si\portfolio;

    use si\APIIntegrada;
    use si\helpers\Cache;
    use si\helpers\Registros;

    class Portfolio {

        private static $portfolio;

        /** @var Cache */
        private static $cache;

        static function __init()
        {
            if (!self::$portfolio) {
                self::$cache = new Cache('portfolio.registros', 15);
                self::$portfolio = self::$cache->getContent() ? : [];
            }
        }

        /**
         * 
         * @param array $parans
         * @param int $page
         * @param int $forPage
         * @return Registros
         */
        function busca(array $parans = null, $page = 1, $forPage = 20)
        {
            $portfolio = APIIntegrada::exec('portfolio', APIIntegrada::extend($parans, [
                                'page' => (int) $page,
                                'forpage' => (int) $forPage,
            ]));

            foreach ($portfolio->data as $port) {
                self::$portfolio[$port->urlamigavel] = $port;
            }

            return new Registros($portfolio);
        }

        function detalhes($url)
        {

            # Verificando no cache
            if (!empty(self::$portfolio[$url])) {
                return self::$portfolio[$url];
            }

            # Buscando o portfólio
            $port = APIIntegrada::exec('portfolio/detalhes', [
                        'urlamigavel' => $url,
            ]);

            # Portfólio encontrada
            if ($port) {
                self::$portfolio[$port->urlamigavel] = $port;
                return $port;
            }
            # Portfólio inválida
            else {
                return false;
            }
        }

        function __destruct()
        {
            self::$cache->setContent(self::$portfolio);
        }

    }

    Portfolio::__init();
    