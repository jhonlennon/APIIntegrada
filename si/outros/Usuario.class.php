<?php

    namespace si\outros;

    use Exception;
    use si\APIIntegrada;
    use si\helpers\Session;

    class Usuario {

        private $user;

        public function __construct()
        {
            $this->user = self::getSession()->get('usuarios', null);
        }

        function logIn($login, $senha)
        {
            $this->user = APIIntegrada::exec('usuarios/login', [
                        'login' => $login,
                        'senha' => $senha,
                            ], 15);
            return $this;
        }

        function logOut()
        {
            $this->getSession()->set('usuarios', $this->user = null);
            //$_SESSION[self::class] = $this->user = null;
            return $this;
        }

        function estaLogado()
        {
            return $this->user ? true : false;
        }

        function getDados()
        {
            return $this->user;
        }

        function redirecionar()
        {
            if ($this->getToken()) {
                $url = APIIntegrada::getURI(false) . '/acesso/auth/' . APIIntegrada::getToken() . '/' . $this->getToken();
                header('Location: ' . $url);
            } else {
                throw new Exception('O usuário deve estar logado para ser redirecionado para o sistema integrado.');
            }
        }

        /**
         * Token de login na aplicação
         * @return string
         */
        function getToken()
        {
            if ($this->estaLogado()) {
                return $this->getDados()->token;
            } else {
                return null;
            }
        }

        /**
         * Salva o login na sessão
         */
        function __destruct()
        {
            $this->getSession()->set('usuarios', $this->user);
            // $_SESSION[self::class] = $this->user;
        }

        /**
         * 
         * @staticvar Session $_session
         * @return Session
         */
        private static function getSession()
        {
            static $_session;
            if (!$_session) {
                $_session = new Session(__CLASS__);
            }
            return $_session;
        }

    }
    