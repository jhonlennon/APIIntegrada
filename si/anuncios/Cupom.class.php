<?php

    namespace si\anuncios;

    use Exception;
    use si\APIIntegrada;

    class Cupom {

	private $anuncio;
	private $nome;
	private $cpf;
	private $email;
	private $telefone;

	/**
	 * 
	 * @param string $url URLAmigável do Anúncio
	 * @throws Exception
	 */
	public function __construct($url) {
	    $this->anuncio = (new Anuncios)->detalhes($url);

	    if (!$this->anuncio) {
		throw new Exception('Cupom inválido.');
	    }
	}

	function setNome($nome) {
	    $this->nome = $nome;
	    return $this;
	}

	function setCpf($cpf) {
	    $this->cpf = $cpf;
	    return $this;
	}

	function setEmail($email) {
	    $this->email = $email;
	    return $this;
	}

	function setTelefone($telefone) {
	    $this->telefone = $telefone;
	    return $this;
	}

	/**
	 * Imprimir o HTML do cupom
	 * @param boolean $return Se true irá retorna o html do Cupom
	 * @return html|null
	 */
	public function imprimir($return = false) {
	    $html = APIIntegrada::exec('anuncios/cupom', [
			'urlamigavel' => $this->anuncio->urlamigavel,
			'nome' => $this->nome,
			'cpf' => $this->cpf,
			'email' => $this->email,
			'telefone' => $this->telefone,
			    ], 0, null, false);

	    if ($return) {
		return $html;
	    } else {
		echo $html;
	    }
	}

    }
    