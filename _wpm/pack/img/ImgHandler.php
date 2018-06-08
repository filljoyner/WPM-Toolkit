<?php namespace WpmPack\Img;

require __DIR__ . '/vendor/autoload.php';

use Wpm\Base;
use WpmPack\Img\src\Image;

class ImgHandler extends Base {

    public function handle($args)
    {
        return new Image(
        	wpmContainer()->getStore('imageCacheDir'),
        	wpmContainer()->getStore('imageCacheUrl')
        );
    }
}
