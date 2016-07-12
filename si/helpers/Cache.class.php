<?php

    namespace si\helpers;

    use Exception;
    use si\APIIntegrada;

    final class Cache {

        const CH_MODE = 0777;

        private $KeyPath = '';
        private $Key = null;
        private $KeyName = null;
        private $ExpireDate;
        private $Content = null;

        /** @var array Armazendo na memória caches carregados */
        private static $CacheLoads = [];

        /** @var string Tempo padrão de um Cache */
        private $Time = '+1minute';

        /** @var string Pasta onde os arquivos de cache serão gravados */
        private static $Path;

        static function __init()
        {
            if (!self::$Path) {
                self::$Path = '_temp' . DIRECTORY_SEPARATOR . 'si';
                if (!file_exists(self::$Path)) {
                    mkdir(self::$Path, 0777, true);
                }
            }
        }

        /**
         * Inicia a leitura do cache
         * @param string $Key
         * @param int $CacheTime Minutos
         * @param mixed $Content Quando informado o arquivo de cache é criado
         */
        function __construct($Key = null, $CacheTime = null)
        {
            if ($Key) {
                $this->setKey($Key, $CacheTime);
            }
        }

        function getExpireDate()
        {
            return $this->ExpireDate;
        }

        /**
         * Inicia o gerenciamento de uma nova chave
         * @param string $Key
         * @param mixed $Content
         * @return Cache
         */
        public function setKey($Key, $time = null)
        {
            # Tempo de cache
            $this->setTime($time);

            # Extraindo valores
            $this->extractInfos($Key);

            # Recuperando conteúdo atual
            $this->loadCache();

            return $this;
        }

        /**
         * Extrai informações da chave
         * @param string $Key
         */
        private function extractInfos($Key)
        {

            $Key = str_replace(['/', '\\'], '.', $Key);
            $this->KeyName = $Key;
            $infos = explode('.', $Key);
            $this->Key = end($infos);
            array_pop($infos);
            $infos = array_values($infos);

            if (count($infos)) {
                $this->KeyPath = implode(DIRECTORY_SEPARATOR, $infos);
                $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $this->getPath() . $this->KeyPath);
                if (!file_exists($path)) {
                    mkdir($path, self::CH_MODE, true);
                    chmod($path, self::CH_MODE);
                }
            } else {
                $this->KeyPath = '';
            }

            if ($this->KeyPath) {
                $this->KeyName = $this->KeyPath . '-' . $this->Key;
            } else {
                $this->KeyName = $this->Key;
            }
            $this->KeyPath .= DIRECTORY_SEPARATOR;

            return $this;
        }

        /**
         * Retorna o diretório completo até o arquivo
         * @return string
         */
        private function getPathFile()
        {
            return $this->getPath() . $this->KeyPath . (preg_match('/^[a-z0-9]+$/i', $this->Key) ? $this->Key : sha1($this->Key)) . '.tmp';
        }

        /**
         * Retorna o diretório
         * @return string
         */
        public static function getPath()
        {
            return self::$Path . DIRECTORY_SEPARATOR;
        }

        /**
         * Lê o conteúdo do Cache
         * @return Cache
         */
        private function loadCache()
        {

            $this->Content = null;

            if (APIIntegrada::getCacheStatus()) {
                # Verificando se o valor já foi carregado
                if (isset(self::$CacheLoads[$this->KeyName])) {
                    $this->Content = self::$CacheLoads[$this->KeyName]['content'];
                    $this->ExpireDate = self::$CacheLoads[$this->KeyName]['expire'];
                }
                # Verificando existencia do cache
                else if (file_exists($this->getPathFile())) {
                    # Extraindo valores do arquivo
                    $content = @unserialize(file_get_contents($this->getPathFile()));
                    if ($content and $content['key'] == $this->KeyName) {
                        # Verificando se expirou
                        if (time() <= $content['expire']) {
                            self::$CacheLoads[$this->KeyName] = [
                                'content' => $this->Content = $content['content'],
                                'expire' => $this->ExpireDate = date('Y-m-d H:i:s', $content['expire']),
                            ];
                        }
                    }
                }
            }
            # Retorna o próprio objeto
            return $this;
        }

        /**
         * Define o tempo de cache
         * @param string|int $time
         * @throws Exception
         */
        public function setTime($time = null)
        {
            if (!is_null($time)) {
                if (is_int($time)) {
                    $this->Time = '+' . $time . 'minutes';
                } else if (is_string($time)) {
                    $this->Time = $time;
                } else if ($time != null) {
                    throw new Exception('Tipo inválido.');
                }
            }
        }

        /**
         * Seta o conteúdo
         * @param mixed $Content
         * @return Cache
         */
        function setContent($Content = null, $Time = null)
        {
            $this->Content = $Content;
            $this->setTime($Time);
            $this->save();
            return $this;
        }

        /**
         * Retorna o conteúdo do cache
         * @return mixed
         */
        public function getContent()
        {
            return $this->Content;
        }

        /**
         * Limpa o cache
         * @return Cache
         */
        public function clear()
        {
            if (file_exists($this->getPathFile())) {
                unlink($this->getPathFile());
            }
            # Limpando da Array
            if (isset(self::$CacheLoads[$this->KeyName])) {
                unset(self::$CacheLoads[$this->KeyName]);
            }
            return $this;
        }

        public static function ClearAll($path = null)
        {
            $files = self::getAllFiles(preg_replace('/[\/\\\]$/', null, self::getPath() . $path));
            foreach ($files as $file) {
                if (is_dir($file)) {
                    @rmdir($file);
                } else {
                    @unlink($file);
                }
            }
            return true;
        }

        private static function getAllFiles($Path)
        {
            $files = [];
            foreach (glob("{$Path}/*", GLOB_BRACE) as $file) {
                $files[] = $file;
                if (is_dir($file)) {
                    $files = array_merge(self::getAllFiles($file), $files);
                }
            }
            return $files;
        }

        /**
         * Salva os dados no cache
         * @param int $Minutes Tempo de cache em minutos 
         * @return Cache
         */
        private function save()
        {
            if ($this->Content === null) {
                $this->clear();
            } else {

                $file = $this->getPathFile();
                file_put_contents($file, serialize([
                    'key' => $this->KeyName,
                    'create' => time(),
                    'expire' => $expire = strtotime($this->Time),
                    'content' => $this->Content,
                ]));
                chmod($file, self::CH_MODE);

                self::$CacheLoads[$this->KeyName] = [
                    'content' => $this->Content,
                    'expire' => $this->ExpireDate = date('Y-m-d H:i:s', $expire),
                ];
            }
            return $this;
        }

        /**
         * Liberando a memória
         */
        public function __destruct()
        {
            self::$CacheLoads = [];
        }

    }

    Cache::__init();
    