<?php namespace WpmPack\Acf;

use Wpm\Base;
use WpmPack\Acf\src\Acf;

class AcfHandler extends Base {

    public function handle($args)
    {
        return new Acf();
    }
}
