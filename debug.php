<?php

    include './si/autoload.php';

    si\APIIntegrada::disableCache();
    si\APIIntegrada::setToken('215785241B6E93AWDA5UK6BQJFUC65TYQ70GL1ERXT61IDQ8MX');

    $produtos = (new \si\produtos\Produtos)->busca(null, 1, 5);

    foreach ($produtos->getRegistros() as $v) {
        if ($v instanceof si\produtos\ProdutoVO) {
            var_dump($v->getUrlamigavel());
        }
    }