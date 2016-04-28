<?php

    namespace si\anuncios;

    use si\APIIntegrada;
    use si\helpers\Cache;

    class Anuncios {

        private static $anuncios;
        protected $extraParans = [];

        /** [] */
        const PARAN_ORDERBY = 'orderby';
        const PARAN_SEARCH = 'search';
        const PARAN_KEYWORDS = 'keywords';
        const PARAN_DESCONTO = 'desconto';
        const PARAN_PROMOCAO = 'promocao';

        /**
         * Array com a lista de funcionalidades
         */
        const PARAN_FUNCIONALIDADES = 'funcionalidade';
        const PARAN_CATEGORIAS = 'categoria';

        /** @var Cache */
        private static $cache;

        static function __init()
        {
            if (!self::$cache) {
                self::$cache = new Cache('anuncios.registros', 15);
                self::$anuncios = self::$cache->getContent() ? : [];
            }
        }

        function busca(array $parans = null, $page = 1, $forPage = 20)
        {
            $anuncios = APIIntegrada::exec('anuncios', APIIntegrada::extend($parans, [
                                'page' => (int) $page,
                                'forpage' => $forPage,
                                    ], $this->extraParans), 15);

            foreach ($anuncios->data as $anuncio) {
                self::$anuncios[$anuncio->urlamigavel] = $anuncio;
            }

            return $anuncios;
        }

        function detalhes($url)
        {

            # Verificando no cache
            if (!empty(self::$anuncios[$url])) {
                return self::$anuncios[$url];
            }

            # Buscando a notícia
            $anuncio = APIIntegrada::exec('anuncios/detalhes', [
                        'urlamigavel' => $url,
            ]);

            # Notícia encontrada
            if ($anuncio) {
                self::$anuncios[$anuncio->urlamigavel] = $anuncio;
                return $anuncio;
            }
            # Notícia inválida
            else {
                return false;
            }
        }

        static function incAcesso(\stdClass $anuncio)
        {
            $spot = $anuncio->spot;
            APIIntegrada::exec('anuncios/incAcesso', ['spot' => $spot], 0);
        }

        function __destruct()
        {
            self::$cache->setContent(self::$anuncios);
        }

    }

    Anuncios::__init();
    