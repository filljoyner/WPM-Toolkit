<?php

return [
    'packs' => [
        // WPM Core Packages
		'wp'       => \Wpm\Wp::class,
        'store'    => \Wpm\Wp::class,
        'q'        => \Wpm\Q::class,

		// All Other Packs
        'img'      => \WpmPack\Img\ImgHandler::class,
        'sort'     => \WpmPack\Sort\SortHandler::class,
        'test'     => \WpmPack\Test\TestHandler::class,
		'acf'      => \WpmPack\Acf\AcfHandler::class
    ]
];
