<?php

namespace si\outros;

use si\APIIntegrada;

class Menu {

    private $menus;
    private $posicao;
    private static $indexId = [];

    public function __construct($setPosicao = null) {
        if(isset($setPosicao)){ $this->posicao = $setPosicao; }
        $posicao = isset($this->posicao) ? $this->posicao : 0;
        $this->menus = APIIntegrada::exec('menu', ['posicao' => $posicao], 15);
    }
    
    public function setPosicao($posicao = 0){
        $this->posicao = $posicao;
    }

    public static function getMenu() {
        self::indexarMenus($this->menus);
        return $this->menus;
    }

    private static function indexarMenus($menus) {
        if ($menus) {
            foreach ($menus as $i => $v) {
                $menu = isset($v->menu) ? $v->menu : $v;
                $submenu = isset($v->submenu) ? $v->submenu : [];
                self::$menu[$i] = $menu;
                self::$submenu[$menu->urlamigavel] = $menu;
                self::$index[$menu->urlamigavel] = $menu;
                self::$indexId[$menu->id] = $menu;
                self::indexarMenus($submenu);
            }
        }
    }

    public function getSubMenu($urlMenu) {
        foreach ($this->menus as $menu) {
            if ($menu->menu->urlamigavel == $urlMenu) {
                return $menu->submenu;
            }
        }
        return null;
    }

    public function geraMenu($params = []) {
        $i = $o = 0;

        foreach ($this->menus as $index => $MenuList) {

            $i++;
            $forPage = isset($params['forpage']) ? : 6;

            if ($MenuList->menu->root == 0) {
                if ($i <= $forPage) {

                    $SubMenu = self::getSubMenu($MenuList->menu->urlamigavel);

                    $MenuAtual[$i] = [
                        'id' => $MenuList->menu->id,
                        'titulo' => $MenuList->menu->title,
                        'urlamigavel' => $MenuList->menu->urlamigavel,
                        'link' => (($SubMenu) ? 1 : 0) ? "#" : url($MenuList->menu->urlamigavel),
                        'target' => $MenuList->menu->hreftarget,
                    ];

                    if (isset($SubMenu)) {
                        foreach ($SubMenu as $k => $v) {
                            if (isset($v->root)) {
                                $MenuAtual[$i]["submenu"][$k] = [
                                    'id' => $v->id,
                                    'titulo' => $v->title,
                                    'urlamigavel' => $v->urlamigavel,
                                    'link' => (($v->tipo->urlamigavel == "link") ? true : false) ? $v->href : url($v->urlamigavel),
                                    'tipo' => $v->tipo->urlamigavel,
                                    'target' => $v->hreftarget,
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $MenuAtual;
    }

    /**
     * Retorna os detalhes do menu
     * @param string $url
     * @return \stdClass
     */
    public static function detalhes($url) {
        if(is_numeric($url)){
	    self::getMenu();
	    $index = isset(self::$indexId[$url]) ? self::$indexId[$url] : null;
	    return $index;
        }else{
            return APIIntegrada::exec('menu/detalhes', ['urlamigavel' => $url], 15);
        }
    }

}
