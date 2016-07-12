<?php

    namespace si\pesquisa;

    use Exception;
    use si\APIIntegrada;
    use si\helpers\Registros;
    use si\outros\Usuario;
    use stdClass;

    class Pesquisa {

        /**
         * Busca de pesquisas
         * @param array $parans
         * @param int $pagina
         * @param int $porPagina
         * @return Registros
         */
        public static function getPesquisas(array $parans = null, $pagina = 1, $porPagina = 20)
        {
            $busca = APIIntegrada::exec('pesquisa', (array) $parans + ['page' => $pagina, 'forpage' => $porPagina]);
            return new Registros($busca);
        }

        /**
         * @param string $token
         * @return stdClass
         */
        public static function detalhes($token)
        {
            foreach (self::getPesquisas(null, 1, 999)->getRegistros() as $pesquisa) {
                if ($pesquisa->token == $token) {
                    return $pesquisa;
                }
            }
            return null;
        }

        /**
         * Respondendo a uma pesquisa
         * @param stdClass $pesquisa
         * @param stdClass $pergunta
         * @param string|int $resposta
         * @param Usuario
         */
        public static function responder(stdClass $pesquisa, stdClass $pergunta, $resposta, stdClass $usuario = null)
        {

            if (!$pergunta->discursiva) {
                if (!is_int($resposta)) {
                    throw new Exception('Deve ser informado o ID da respota de 1 à 6');
                }
            } else if (empty($resposta)) {
                throw new Exception('Preencha a respota da pergunta `' . $pergunta->pergunta . '`');
            }
            
            $result = APIIntegrada::exec('pesquisa/responder', [
                        'pesquisa' => $pesquisa->id,
                        'pergunta' => $pergunta->id,
                        ('resposta' . ($pergunta->discursiva ? 'discursiva' : null)) => $resposta,
                        'cadastro' => $usuario ? $usuario->idcadastro : null,
            ]);

            return $result;
        }

    }
    