<?php

    namespace si\produtos;

    use si\abs\ValueObject;

    class ProdutoVO extends ValueObject {

        protected $id;
        protected $tipo;
        protected $codigo;
        protected $varia;
        protected $variacoes;
        protected $categoria;
        protected $marca;
        protected $title;
        protected $subtitle;
        protected $texto;
        protected $urlamigavel;
        protected $descricao;
        protected $keywords;
        protected $valor;
        protected $imagem;
        protected $imagens;
        protected $renovar;

        public function getId()
        {
            return $this->id;
        }

        public function getTipo()
        {
            return $this->tipo;
        }

        public function getCodigo()
        {
            return $this->codigo;
        }

        public function getVaria()
        {
            return $this->varia;
        }

        public function getVariacoes()
        {
            return $this->variacoes;
        }

        public function getCategoria()
        {
            return $this->categoria;
        }

        public function getMarca()
        {
            return $this->marca;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function getSubtitle()
        {
            return $this->subtitle;
        }

        public function getTexto()
        {
            return $this->texto;
        }

        public function getUrlamigavel()
        {
            return $this->urlamigavel;
        }

        public function getDescricao()
        {
            return $this->descricao;
        }

        public function getKeywords()
        {
            return $this->keywords;
        }

        public function getValor()
        {
            return $this->valor;
        }

        public function getImagem()
        {
            return $this->imagem;
        }

        public function getImagens()
        {
            return $this->imagens;
        }

        public function getRenovar()
        {
            return $this->renovar;
        }

    }
    