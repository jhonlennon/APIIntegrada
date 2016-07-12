<?php

namespace si\outros;

use si\APIIntegrada;

class Publicidade {

    private static $posicao;

    public function __construct($posicao = NULL) {
            self::setPosicao($posicao);
    }

    public static function busca($forPage = 1, $order = 'desc') {
        return APIIntegrada::exec('publicidade', ['posicao' => self::$posicao,'forpage' => $forPage, 'orderby' => $order]);
    }

    public static function setPosicao($posicao) {
        return self::$posicao = $posicao;
    }

}
