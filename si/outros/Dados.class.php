<?php

    namespace si\outros;

    use si\APIIntegrada;

    class Dados {

        private static $dados;
        private static $redes = [];

        public function __construct()
        {
            if (!self::$dados) {
                self::$dados = APIIntegrada::exec('dados', null, 15);
            }
        }

        public static function getDados()
        {
            return self::$dados;
        }

        public function getModelo()
        {
            return self::$dados->modelo;
        }

        public function getUrl()
        {
            return self::$dados->url;
        }

        public function getTitle()
        {
            return self::$dados->title;
        }

        public function getDescricao()
        {
            return self::$dados->descricao;
        }

        public function getKeywords()
        {
            return self::$dados->keywords;
        }

        public function getSubject()
        {
            return self::$dados->subject;
        }

        public static function getTelefone()
        {
            return self::$dados->telefone;
        }

        public function getTelefoneoperadora()
        {
            return self::$dados->telefoneoperadora;
        }
        
        public static function getTelefonePrincipal() {
	    if (self::getTelefone()) {
		return self::getTelefone();
	    } else if (self::getCelular1()) {
		return self::getCelular1();
	    } else if (self::getCelular2()) {
		return self::getCelular2();
	    }
	}

        public static function getRedes($redesocial = false) {
            
            if($redesocial == false){
                if(self::$dados->facebook){
                    self::$redes["facebook"] = (object) [
                        'titulo' => 'Facebook',
                        'icone' => 'fa fa-facebook',
                        'url' => self::$dados->facebook,
                    ];
                }
                if(self::$dados->twitter){
                    self::$redes["twitter"] = (object) [
                        'titulo' => 'Twitter',
                        'icone' => 'fa fa-twitter',
                        'url' => self::$dados->twitter,
                    ];
                }
                if(self::$dados->googleplus){
                    self::$redes["googleplus"] = (object) [
                        'titulo' => 'Google Plus+',
                        'icone' => 'fa fa-google-plus',
                        'url' => self::$dados->googleplus,
                    ];
                }
                if(self::$dados->instagram){
                    self::$redes["instagram"] = (object) [
                        'titulo' => 'Instagram',
                        'icone' => 'fa fa-instagram',
                        'url' => self::$dados->instagram,
                    ];
                }
                if(self::$dados->youtube){
                    self::$redes["youtube"] = (object) [
                        'titulo' => 'Youtube',
                        'icone' => 'fa fa-youtube',
                        'url' => self::$dados->youtube,
                    ];
                }
                if(self::$dados->linkedin){
                    self::$redes["linkedin"] = (object) [
                        'titulo' => 'Linkedin',
                        'icone' => 'fa fa-linkedin',
                        'url' => self::$dados->linkedin,
                    ];
                }
                
                return self::$redes;
            }else{
                    return (isset(self::$redes[$redesocial])) ? self::$redes[$redesocial] : null;
            }
        }
        
        public function getCelular1()
        {
            return self::$dados->celular1;
        }

        public function getCelular1operadora()
        {
            return self::$dados->celular1operadora;
        }

        public function getCelular2()
        {
            return self::$dados->celular2;
        }

        public function getCelular2operadora()
        {
            return self::$dados->celular2operadora;
        }

        public function getEmail()
        {
            return self::$dados->email;
        }

        public function getEmaildestinatario()
        {
            return self::$dados->emaildestinatario;
        }

        public function getFacebook()
        {
            return self::$dados->facebook;
        }

        public function getFacebooklike()
        {
            return self::$dados->facebooklike;
        }

        public function getTwitter()
        {
            return self::$dados->twitter;
        }

        public function getGoogleplus()
        {
            return self::$dados->googleplus;
        }

        public function getLinkedin()
        {
            return self::$dados->linkedin;
        }

        public function getYoutube()
        {
            return self::$dados->youtube;
        }

        public function getInstagram()
        {
            return self::$dados->instagram;
        }

        public function getCodigowebmasters()
        {
            return self::$dados->codigowebmasters;
        }

        public function getCodigoanalytics()
        {
            return self::$dados->codigoanalytics;
        }

        public function getCaptchaprivate()
        {
            return self::$dados->captchaprivate;
        }

        public function getCaptchapublic()
        {
            return self::$dados->captchapublic;
        }

        public function getStatustitle()
        {
            return self::$dados->statustitle;
        }

        public function getFormaspagamento()
        {
            return self::$dados->formaspagamento;
        }

        public function getId()
        {
            return self::$dados->id;
        }

        public function getInsert()
        {
            return self::$dados->insert;
        }

        public function getUpdate()
        {
            return self::$dados->update;
        }

        public function getStatus()
        {
            return self::$dados->status;
        }

        public function getImagereference()
        {
            return self::$dados->imagereference;
        }

        public function getToken()
        {
            return self::$dados->token;
        }

        public static function getCep()
        {
            return self::$dados->cep;
        }

        public static function getLogradouro()
        {
            return self::$dados->logradouro;
        }

        public static function getNumero()
        {
            return self::$dados->numero;
        }

        public static function getBairro()
        {
            return self::$dados->bairro;
        }

        public static function getComplemento()
        {
            return self::$dados->complemento;
        }

        public static function getCidade()
        {
            return self::$dados->cidade;
        }

        public static function getUf()
        {
            return self::$dados->uf;
        }

        public static function getEstado()
        {
            return self::$dados->estado;
        }
        
        public static function getEndereco() {
            return self::getLogradouro(). ", n°" . self::getNumero() . ", "  . (self::getComplemento() ? self::getComplemento() . ', ' : '') . self::getBairro() . ", " . self::getCidade() . "-" . self::getUf() . ", CEP: " . self::getCep();
        }

        public function getLatitude()
        {
            return self::$dados->latitude;
        }

        public function getLongitude()
        {
            return self::$dados->longitude;
        }

        public function getZoom()
        {
            return self::$dados->zoom;
        }

        public function getLogotipo()
        {
            return self::$dados->logotipo;
        }

    }
    