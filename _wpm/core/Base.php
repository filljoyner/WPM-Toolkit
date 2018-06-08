<?php
namespace Wpm;


/*
 * When the wpm function is passed a string, a handler is selected from the
 * classMap and used to route request to the associated class. This is the
 * BaseHandler that all other handlers must extend.
 */
abstract class Base
{
    
    /**
     * Passes the call to the proper component to continue
     *
     * @param $args
     * @return mixed
     */
    public function handle($args)
    {
        $class = $this->resolveClass($args[0]);

        if(isset($args[1])) return (new $class($args[1]));

        return (new $class());
    }
    
    
    private function resolveClass($classAlias)
    {
        return $this->classMap[$classAlias];
    }
}