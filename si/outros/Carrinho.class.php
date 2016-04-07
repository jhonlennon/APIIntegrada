<?php

    namespace si\outros;

    use Exception;
    use si\APIIntegrada;

    class Carrinho {

	/**
	 * Cria o carrinho e retorna a URL para redirecionamento
	 * @param array $produtos
	 * @param int $revendedor ID do revendor responsável pela venda
	 * @return string URL do carrinho
	 * @throws Exception
	 */
	public static function criar(array $produtos, $revendedor = 0) {

	    # Sem produtos no carrinho
	    if (!$produtos) {
		throw new Exception('Carrinho vazio.');
	    }

	    # Checando formatação
	    foreach ($produtos as $v) {
		if (!is_array($v)) {
		    throw new Exception('Não é um Array de produtos [produto/quantidade].');
		} else if (empty($v['produto']) or ! is_int($v['produto'])) {
		    throw new Exception("{$v['produto']}: Produto inválido.");
		} else if (empty($v['quantidade']) or ! is_int($v['quantidade'])) {
		    throw new Exception('Quantidade inválida.');
		}
	    }

	    $curl = APIIntegrada::exec('carrinho/novo', [
			'produtos' => $produtos,
			'revendedor' => $revendedor,
			    ], 0);

	    if ($curl->result == 1) {
		return $curl->url;
	    } else {
		throw new Exception($curl->message);
	    }
	}

    }
    