<?php

    namespace si\noticias;

    use si\APIIntegrada;
    use si\helpers\Cache;
    use si\helpers\Registros;

    class Noticias {

        private static $noticias;
        private static $autores;

        /** @var Cache */
        private static $cache;

        static function __init()
        {
            if (!self::$noticias) {
                self::$cache = new Cache('noticias.registros', 15);
                self::$noticias = self::$cache->getContent() ? : [];
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
            $noticias = APIIntegrada::exec('noticias', APIIntegrada::extend($parans, [
                                'page' => (int) $page,
                                'forpage' => $forPage,
            ]));
            
            foreach ($noticias->data as $noticia) {
                self::$noticias[$noticia->urlamigavel] = $noticia;
            }

            return new Registros($noticias);
        }
        function autores(array $parans = null, $page = 1, $forPage = 20)
        {
            $autores = APIIntegrada::exec('noticias/autores', APIIntegrada::extend($parans, [
                                'page' => (int) $page,
                                'forpage' => $forPage,
            ]));

            foreach ($autores as $autor) {
                self::$autores[$autor->urlamigavel] = $autor;
            }

            return $autores;
        }

        function detalhesAutor($url)
        {

            # Verificando no cache
            if (!empty(self::$autores[$url])) {
                return self::$autores[$url];
            }

            # Buscando o $autor
            $autor = APIIntegrada::exec('noticias/autores', [
                        'urlamigavel' => $url,
            ])[0];
            
            # $autor encontrado
            if ($autor) {
                self::$autores[$autor->urlamigavel] = $autor;
                return $autor;
            }
            # $autor inválido
            else {
                return false;
            }
        }

        function detalhes($url)
        {

            # Verificando no cache
            if (!empty(self::$noticias[$url])) {
                return self::$noticias[$url];
            }

            # Buscando a notícia
            $noticia = APIIntegrada::exec('noticias/detalhes', [
                        'urlamigavel' => $url,
            ]);

            # Notícia encontrada
            if ($noticia) {
                self::$noticias[$noticia->urlamigavel] = $noticia;
                return $noticia;
            }
            # Notícia inválida
            else {
                return false;
            }
        }

        function __destruct()
        {
            self::$cache->setContent(self::$noticias);
        }

    }

    Noticias::__init();
    