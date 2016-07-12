<?php

namespace si\outros;

class Videos {
    private static $videos = [];
    private static $index = [];
    
    static function getVideos($page = 1, $forpage = 500) {
	    if (!self::$videos) {
		$busca = \si\APIIntegrada::exec('videos', ['page' => 1, 'forpage' => $forpage], 15)->data;
		foreach ($busca as $i => $v) {
		    self::$videos[$i] = $v;
		    self::$index[$v->urlamigavel] = $i;
		}
	    }
	    return self::$videos;
    }
    
    public static function detalhes($url) {
	$videos = self::getVideos();
	$index = isset(self::$index[$url]) ? self::$index[$url] : null;
	return $index !== null ? $videos[$index] : null;
    }
}
