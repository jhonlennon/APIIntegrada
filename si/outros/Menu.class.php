<?php

    namespace si\outros;

    use si\APIIntegrada;

    class Menu {

        private static $index = [];

        public static function getMenu($posicao = 0)
        {
            $menus = APIIntegrada::exec('menu', ['posicao' => $posicao], 15);
            self::indexarMenus($menus);
            return $menus;
        }

        private static function indexarMenus($menus)
        {
            if ($menus) {
                foreach ($menus as $i => $v) {
                    $menu = isset($v->menu) ? $v->menu : $v;
                    $submenu = isset($v->submenu) ? $v->submenu : [];
                    self::$index[$menu->urlamigavel] = $menu;
                    self::indexarMenus($submenu);
                }
            }
        }

        public static function detalhes($url)
        {
            self::getMenu();
            $menu = isset(self::$index[$url]) ? self::$index[$url] : null;
            return $menu;
        }

    }
    