<?php

    namespace si\outros;

    use si\APIIntegrada;

    class Menu {

	private static $menu = [];
	private static $submenu = [];
	private static $index = [];

	public static function getMenu($posicao = 0) {
	    if (!self::$menu) {
		$busca = APIIntegrada::exec('menu', ['posicao' => $posicao], 15);
		self::indexarMenus($busca);
		foreach ($busca as $i => $v) {
		    self::$menu[$i] = $v->menu;
		    self::$submenu[$v->menu->urlamigavel] = $v->submenu;
		}
	    }
	    return self::$menu;
	}

	private static function indexarMenus($menus) {
	    if ($menus) {
		foreach ($menus as $i => $v) {
		    $menu = isset($v->menu) ? $v->menu : $v;
		    $submenu = isset($v->submenu) ? $v->submenu : [];
		    self::$menu[$i] = $menu;
		    self::$submenu[$menu->urlamigavel] = $menu;
		    self::$index[$menu->urlamigavel] = $menu;
		    self::indexarMenus($submenu);
		}
	    }
	}

	public static function getSubMenu($urlMenu) {
	    self::getMenu();
	    if (isset(self::$submenu[$urlMenu])) {
		return self::$submenu[$urlMenu];
	    } else {
		return null;
	    }
	}

	public static function detalhes($url) {
	    self::getMenu();
	    $index = isset(self::$index[$url]) ? self::$index[$url] : null;
	    return $index;
	}

    }
    