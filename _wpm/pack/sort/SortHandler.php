<?php namespace WpmPack\Sort;

use WpmPack\Sort\src\SortPostType;

class SortHandler {
    public function handle()
    {
        return $this;
    }
    
    
    public function add($args)
    {
        return (new SortPostType())->add($args);
    }
}