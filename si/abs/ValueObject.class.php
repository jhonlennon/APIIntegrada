<?php

    namespace si\abs;

    abstract class ValueObject {

        public function __construct(\stdClass $values)
        {
            foreach ($values as $key => $value) {
                $this->{$key} = $value;
            }
        }

        /**
         * @param array $registros
         * @param string $class
         * @return array
         */
        public static function converter(array $registros = null, $class)
        {
            foreach ((array) $registros as &$r) {
                $r = new $class($r);
            }

            return $registros;
        }

    }
    