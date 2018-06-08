<?php
namespace Wpm\Components;

use Wpm\Components\Interfaces\StoreInterface;

/*
 * An instance of StoreDb will respond to all wpm('store.db') calls
 */
class StoreDb implements StoreInterface
{
    protected $wpmDbVarKey = 'wpmDbStoreOption';        // the option key where options are stored
    
    /**
     * Checks if key exists in db store and returns boolean result.
     *
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        if ($this->get($key)) return true;
        
        return false;
    }
    
    
    /**
     * Return all stored variables
     *
     * @return array
     */
    public function all()
    {
        return $this->getDbVars();
    }
    
    
    /**
     * Returns the value of a variable db stored for a given key.
     *
     * @param $key
     * @return null
     */
    public function get($key)
    {
        $wpmDbVars = $this->getDbVars();
        
        if (isset($wpmDbVars[ $key ])) {
            return $wpmDbVars[ $key ];
        }
        
        return null;
    }
    
    
    /**
     * Stores a value to a given key in the db store.
     *
     * @param $key
     * @param null $value
     */
    public function set($key, $value = null)
    {
        $wpmDbVars = $this->getDbVars();
        $wpmDbVars[ $key ] = $value;
        $this->saveDbVars($wpmDbVars);
    }
    
    
    /**
     * Removes the key from the db store.
     *
     * @param $key
     * @return bool
     */
    public function remove($key)
    {
        $wpmDbVars = $this->getDbVars();
        
        if (isset($wpmDbVars[ $key ])) {
            unset($wpmDbVars[ $key ]);
            $this->saveDbVars($wpmDbVars);
            
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Gets the data from the db and decodes it to return.
     * @return array
     */
    protected function getDbVars()
    {
        $vars = get_option($this->wpmDbVarKey);
        if ($vars) {
            return json_decode($vars, true);
        };
        
        return [];
    }
    
    
    /**
     * Take an array of vars, serialize them, and store to wpm option.
     *
     * @param array $vars
     */
    protected function saveDbVars($vars = [])
    {
        if (empty($vars)) {
            $vars = [];
        }
        if (is_array($vars)) {
            update_option($this->wpmDbVarKey, json_encode($vars), true);
        }
    }
}