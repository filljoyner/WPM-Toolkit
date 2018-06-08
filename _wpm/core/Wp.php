<?php
namespace Wpm;

use Wpm\Components\Action;
use Wpm\Components\Filter;
use Wpm\Components\Nav;
use Wpm\Components\PostType;
use Wpm\Components\StoreDb;
use Wpm\Components\StoreVar;
use Wpm\Components\StoreCookie;
use Wpm\Components\Taxonomy;

class Wp extends Base
{
    protected $classMap = [
        'post_type' => PostType::class,
        'taxonomy'  => Taxonomy::class,
        'var'       => StoreVar::class,
        'db'        => StoreDb::class,
        'cookie'    => StoreCookie::class,
        'action'    => Action::class,
        'filter'    => Filter::class,
        'nav'       => Nav::class,
    ];
}