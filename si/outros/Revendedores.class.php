<?php

    namespace si\outros;

    use si\APIIntegrada;
    use si\helpers\Registros;
    use stdClass;

    class Revendedores {

        /**
         * 
         * @param array $parans
         * @param type $page
         * @param type $forpage
         * @return stdClass
         */
        static function busca(array $parans = null, $page = 1, $forpage = 10)
        {
            return new Registros(APIIntegrada::exec('revendedores', ['page' => $page, 'forpage' => $forpage] + (array) $parans, 15));
        }

        /**
         * Retorna o detalhes do Revendedor
         * @param string $login
         * @return stdClass
         */
        public static function detalhes($login)
        {
            return APIIntegrada::exec('revendedores/detalhes', ['login' => $login], 15);
        }

    }
    