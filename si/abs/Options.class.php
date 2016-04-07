<?php

    namespace si\abs;

    use si\APIIntegrada;

    abstract class Options {

	protected $geral = 0;
	protected $ref = '';
	protected $refid = 0;
	private $options = [];
	private $index = [];

	public function getOptions() {
	    if (!$this->options) {
		$busca = APIIntegrada::exec('options', [
			    'ref' => $this->ref,
			    'refid' => $this->refid,
			    'geral' => $this->geral,
		]);

		foreach ($busca as $i => $v) {
		    $this->options[$i] = $v;
		    $this->index[$v->urlamigavel] = $i;
		}
	    }
	    return $this->options;
	}

	public function selectList() {
	    $html = '';
	    foreach ($this->getOptions() as $opt) {
		$html .= '<option value="' . htmlspecialchars($opt->urlamigavel) . '" >' . $opt->titulo . '</option>';
	    }
	    return $html;
	}

	public function detalhes($url) {
	    $options = $this->getOptions();
	    $index = isset($this->index[$url]) ? $this->index[$url] : null;
	    return $index !== null ? $options[$index] : null;
	}

    }
    