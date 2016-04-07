<?php

    namespace si;

    use Exception;
    use si\helpers\Cache;

    class APIIntegrada {

	const ROOT = __DIR__;

	/** @var string URL da API do Sistema Integrado */
	const URI = 'www.sistemaintegrado.com.br/si/api';

	/** @var string Pasta de arquivos temporários */
	private static $_CACHE_PATH;

	/** @var int CHMODE que será aplicado aos arquivos criados */
	private static $_CHMODE = 0644;

	/** @var int time() */
	private static $_TIMER;

	/** @var string Timer padrão da aplicação */
	private static $_CACHE_TIMER = '+0 minutes';

	/** @var boolean Se `true` irá retorna um Object ao invés de um array */
	private static $_RETURN_OBJECT = true;

	/** @var boolean Token da aplicação no Sistema Integrado */
	private static $_TOKEN;

	/** @var boolean */
	private static $_CACHE_ENABLE = true;

	/** @var array Cache em tempo de execução */
	private static $_CACHE = array();

	# Data/Hoje
	private $dtHoje;

	/** @var boolean Define se está funcionando em um servidor de testes com a base de dados rodando */
	private static $IS_TEST = false;

	/** @var APIIntegrada Instância única */
	private static $Object;

	/** @var Cache */
	private static $Cache;

	static function __init() {
	    if (!self::$_TIMER) {
		self::$_TIMER = time();
		self::$Cache = new Cache;
	    }
	}

	/**
	 * Método construtor
	 */
	public function __construct() {

	    # Data do dia
	    $this->dtHoje = date('Y-m-d');

	    # Limpando o cache
	    $this->cache_clean();

	    # Instância única
	    if (!self::$Object) {
		self::$Object = $this;
	    }
	}

	/**
	 * Define se está rodando em servidor de testes
	 * @param boolean $boolean
	 */
	public static function isTest($boolean = false) {
	    self::$IS_TEST = $boolean;
	}

	/**
	 * Define o token da aplicação
	 * @param string $token
	 * @throws Exception
	 */
	public static function setToken($token) {
	    self::$_TOKEN = $token;
	}

	/**
	 * Retorna o TOKEN da aplicação
	 * @return string
	 */
	public static function getToken() {
	    if (!self::$_TOKEN) {
		throw new Exception('Token da aplicação não foi definido.');
	    } else {
		return self::$_TOKEN;
	    }
	}

	/**
	 * Define a permissão de pastas/arquivos temporários
	 * @param int $value
	 */
	public static function setCHMode($value = 0644) {
	    self::$_CHMODE = $value;
	}

	/**
	 * Define o tempo padrão de cache
	 * @param string|int $time
	 */
	public static function setDefaultTimer($time = '+7minutes') {
	    self::$_CACHE_TIMER = $time;
	}

	/**
	 * Define o padrão de retorno
	 * @param boolean $value
	 */
	public static function setReturnObject($value = false) {
	    self::$_RETURN_OBJECT = $value ? true : false;
	}

	/** Desativa o cache */
	static function disableCache() {
	    self::$_CACHE_ENABLE = false;
	}

	/** Ativa o cache */
	static function enableCache() {
	    self::$_CACHE_ENABLE = true;
	}

	/**
	 * Retorna o status do cache Enable/Disabled
	 * @return boolean
	 */
	static function getCacheStatus() {
	    return self::$_CACHE_ENABLE;
	}

	/**
	 * Recuperar informações do site no sistema integrado
	 * @param type $action
	 * @param array $values
	 * @param int $cacheTime minutos
	 * @param boolean $toObject true => object | false => array
	 * @param boolean $decodeJson
	 * @return array|object
	 * @throws Exception
	 */
	public function execute($action = 'index', array $values = null, $cacheTime = 'self::$_CACHE_TIMER', $toObject = 'self::$_RETURN_OBJECT', $decodeJson = true) {

	    # Configuração padrão $_CACHE_TIMER
	    if ($cacheTime === null or $cacheTime === 'self::$_CACHE_TIMER') {
		$cacheTime = self::$_CACHE_TIMER;
	    }

	    # Configuração padrão $_RETURN_OBJECT
	    if ($toObject === null or $toObject === 'self::$_RETURN_OBJECT') {
		$toObject = self::$_RETURN_OBJECT;
	    }

	    # Chave do Cache
	    $cacheKey = $this->getCacheKey($action, $values);

	    # Retornando o Cache
	    if ($cacheTime !== 0 and $content = $this->getCache($cacheKey)) {
		return $content;
	    }

	    # Requesitando dados
	    $dados = $this->curl($action, $values, $toObject, $decodeJson);

	    # Salvando em cache e retornando dados
	    return $this->setCache($cacheKey, $dados, $cacheTime);
	}

	/**
	 * Retorna a URI de integração
	 * @return string
	 */
	private static function getURI() {
	    if (self::$IS_TEST and strpos($_SERVER['HTTP_HOST'], 'localhost:8090') !== false) {
		return 'localhost:8090/sistemaintegrado.com.br/api';
	    } else {
		return self::URI;
	    }
	}

	/**
	 * 
	 * @param string $action
	 * @param array $values
	 * @param boolean $toObject
	 * @param boolean $decodeJson
	 * @return array
	 * @throws Exception
	 */
	private function curl($action, $values = null, $toObject = true, $decodeJson = true) {

	    # CURL
	    $ch = curl_init(self::getProtocol() . "://" . self::getURI() . "/{$action}");

	    # Valores
	    $postValues = array(
		'si_token' => self::getToken(),
		'si_user_ip' => self::getUserIp(),
		'si_user_agent' => getenv('HTTP_USER_AGENT'),
		'si_post' => serialize($values),
	    );

	    # Codificando arrays
	    foreach ($postValues as $key => $value) {
		if (is_array($value)) {
		    $postValues[$key] = json_encode($value);
		}
	    }

	    # Configurando conexão
	    curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $postValues,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_CONNECTTIMEOUT => 30,
		CURLOPT_AUTOREFERER => true,
	    ));

	    # Executando busca
	    $result = curl_exec($ch);
	    curl_close($ch);

	    # Resultado da busca
	    if ($result == '[]') {
		return array();
	    }

	    # Decodificando o resultado
	    if ($decodeJson) {
		$dados = json_decode($result, !$toObject);

		# Não foi possível decodificar
		if (!$dados) {
		    $this->si_error_log("{$action}: Não foi possível decodificar o resultado.");
		    throw new Exception($result);
		}

		# Retornando
		return $dados;
	    } else {
		return $result;
	    }
	}

	/**
	 * Retorna o protocolo da página acessada
	 * @return http|https
	 */
	static function getProtocol() {
	    if (isset($_SERVER['HTTPS'])) {
		if ($_SERVER['HTTPS'] == "on") {
		    return 'https';
		}
	    }
	    return 'http';
	}

	/**
	 * IP do usuário
	 * @return string
	 */
	static function getUserIp() {

	    $ipaddress = null;

	    if (getenv('HTTP_CLIENT_IP')) {
		$ipaddress = getenv('HTTP_CLIENT_IP');
	    } else if (getenv('HTTP_X_FORWARDED_FOR')) {
		$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    } else if (getenv('HTTP_X_FORWARDED')) {
		$ipaddress = getenv('HTTP_X_FORWARDED');
	    } else if (getenv('HTTP_FORWARDED_FOR')) {
		$ipaddress = getenv('HTTP_FORWARDED_FOR');
	    } else if (getenv('HTTP_FORWARDED')) {
		$ipaddress = getenv('HTTP_FORWARDED');
	    } else if (getenv('REMOTE_ADDR')) {
		$ipaddress = getenv('REMOTE_ADDR');
	    } else {
		$ipaddress = null;
	    }

	    # IP
	    return $ipaddress;
	}

	/**
	 * SiErrorLog
	 * @param string $mensagem
	 */
	private function si_error_log($mensagem) {
	    $file = self::$Cache->getPath() . 'si_error.txt';

	    $time = date("H:i:s d/m/Y");

	    if (!file_exists($file) and ! is_writable(dirname($file))) {
		throw new Exception('Não foi possível gerar o si_error.log');
	    } else if (file_exists($file) and ! is_writable($file)) {
		throw new Exception('Não é possível atualizar o arquivo si_error.log');
	    }

	    if (!file_exists($file)) {
		file_put_contents($file, "{$time}: {$mensagem}\n");
	    } else {
		file_put_contents($file, file_get_contents($file) . "{$time} {$mensagem}\n");
	    }
	}

	/**
	 * Gera a chave do cache
	 * @param string $action
	 * @param array $values
	 * @return string
	 */
	private static function getCacheKey($action, array $values = null) {
	    $key = str_replace('/', '.', $action);
	    if ($values) {
		$key .= '.' . hash('sha512', serialize($values));
	    }
	    return $key;
	}

	/**
	 * Recuperando conteúdo em cache
	 * @param string $key
	 * @return array|null
	 */
	private static function getCache($key) {
	    $content = self::$Cache->setKey($key)->getContent();

	    # Buscando o conteúdo
	    if ($content = self::$Cache->getContent()) {
		return $content;
	    } else {
		return null;
	    }
	}

	/**
	 * Salvando conteúdo em cache
	 * @param string $key
	 * @param array $content
	 * @param int|string $time
	 * @return content Conteúdo do cache
	 * @throws Exception
	 */
	private static function setCache($key, $content, $time) {

	    # Sem cache
	    if ($time === 0) {
		return $content;
	    }

	    # Granvando o cache
	    self::$Cache
		    ->setKey($key)
		    ->setContent($content, $time);

	    return $content;
	}

	/**
	 * array_merge inteligente
	 * @param array $initial
	 * @param array $merge
	 * @return array
	 */
	static function extend(array $initial = null, array $merge = null) {
	    foreach ((array) $merge as $key => $value) {
		if (is_int($key)) {
		    $initial[] = $value;
		} else if (is_array($value) and isset($initial[$key])) {
		    $initial[$key] = self::extend($initial[$key], $value);
		} else {
		    $initial[$key] = $value;
		}
	    }
	    return $initial;
	}

	/**
	 * Limpa o cache dos últimos {x} dias
	 * @param int $dias
	 */
	public static function cache_clean($dias = 2) {

	    $key = self::getCacheKey('cache');
	    $cache = self::getCache($key);
	    $hoje = date('Y-m-d');

	    if (!$cache or $hoje != $cache) {

		self::setCache($key, $hoje, $hoje . ' 23:59:59');
		$time = strtotime("{$hoje} 23:59:59 -{$dias}days");

		$files = self::cache_clean_files(self::$Cache->getPath());

		# Excluíndo aruivos
		foreach ($files as $file) {
		    if (filemtime($file) < $time) {
			unlink($file);
		    }
		}
	    }
	}

	/**
	 * Lista todos os arquivos temporários
	 * @param string $path
	 */
	private static function cache_clean_files($path) {
	    $dir = dir($path);
	    $arquivos = [];

	    while ($file = $dir->read()) {
		if ($file == '.' or $file == '..') {
		    continue;
		} else if (is_dir($path . $file)) {
		    $arquivos = array_merge($arquivos, self::cache_clean_files($path . $file . DIRECTORY_SEPARATOR));
		} else if (is_file($path . $file)) {
		    $arquivos[] = $path . $file;
		}
	    }

	    return $arquivos;
	}

	/**
	 * Alias para <b>APIIntegrada::execute</b>
	 * @param string $action
	 * @param array $values
	 * @param string|int $cacheTime
	 * @param boolean $toObject
	 * @param boolean $decodeJson
	 * @return array|object
	 */
	public static function exec($action = 'index', array $values = null, $cacheTime = 'self::$_CACHE_TIMER', $toObject = 'self::$_RETURN_OBJECT', $decodeJson = true) {

	    # $this
	    if (!$o = self::$Object) {
		$o = self::$Object = new self;
	    }

	    # Executando pesquisa
	    return $o->execute($action, $values, $cacheTime, $toObject, $decodeJson);
	}

	/**
	 * Arquivo de visualização
	 * @param string $view
	 * @param array $vars
	 * @return string
	 * @throws Exception
	 */
	public static function loadView($_view, array $_vars = null) {

	    # Diretório do arquivo
	    $_file_load_view = $_view . '.phtml';

	    # Verificando arquivo
	    if (!file_exists($_file_load_view)) {
		throw new Exception('Template inexistente.');
	    }

	    # Extraindo valores
	    if ($_vars) {
		extract($_vars);
	    }

	    # Retornando o HTML
	    ob_start();
	    include $_file_load_view;
	    return ob_get_clean();
	}

    }

    APIIntegrada::__init();
    