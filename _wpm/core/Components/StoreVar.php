<?php
namespace Wpm\Components;

use Wpm\Components\Interfaces\StoreInterface;

/*
 * An instance of StoreVar will respond to all wpm('store.var') calls
 */
class StoreVar implements StoreInterface
{
    protected $varStoreKey = 'WpmContainerVarStore';
    
    /**
     * Checks if key exists in store variables and returns boolean result.
     * 
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        if ($this->get($key) !== null) return true;
        
        return false;
    }
    
    
    /**
     * Returns all variables stored by wpm
     *
     * @return array
     */
    public function all()
    {
        return $this->getVarStore();
    }
    
    
    /**
     * Returns the value of a variable stored for a given key.
     * 
     * @param $key
     * @return null
     */
    public function get($key)
    {
        $vars = $this->getVarStore();
        
        return (isset($vars[$key]) ? $vars[$key] : null);
    }
    
    
    /**
     * Stores a value to a given key in the variable store.
     * 
     * @param $key
     * @param null $value
     */
    public function set($key, $value = null)
    {
        $vars = $this->getVarStore();
        
        $vars[$key] = $value;
        
        $this->setVarStore($vars);
    }
    
    
    /**
     * Removes the key from the variable store.
     * 
     * @param $key
     * @return bool
     */
    public function remove($key)
    {
        if ($this->has($key)) {
            $vars = $this->getVarStore();
            
            unset($vars[$key]);
            
            $this->setVarStore($vars);
            
            return true;
        }
        
        return false;
    }
    
    
    private function getVarStore()
    {
        return wpmContainer()->getStore($this->varStoreKey);
    }
    
    
    private function setVarStore($vars)
    {
        return wpmContainer()->setStore($this->varStoreKey, $vars);
    }
}
