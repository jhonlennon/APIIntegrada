<?php

    namespace si\outros;

    class Slide {

	private static $slide = [];
	private static $index = [];

	public static function getSlide(array $parans = null, $page = 1, $forPage = 20) {
	    if (!self::$slide) {
		$busca = \si\APIIntegrada::exec('slide', \si\APIIntegrada::extend($parans, [
				'page' => (int) $page,
				'forpage' => $forPage,
	    ]), 15);
		foreach ($busca as $i => $v) {
		    self::$slide[$i] = $v;
		}
	    }
	    return self::$slide;
	}

    }
    