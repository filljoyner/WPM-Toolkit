<?php
namespace Wpm\Components\Interfaces;

interface StoreInterface
{
    // check if key has been stored
    public function has($key);

    // return all values
    public function all();
    
    // get value by key
    public function get($key);
    
    // set a value by key
    public function set($key, $value=null);
    
    // remove a value
    public function remove($key);
}