<?php
namespace Wpm\Components;

use Wpm\Components\Interfaces\StoreInterface;

/*
 * An instance of StoreVar will respond to all wpm('store.var') calls
 */
class StoreCookie implements StoreInterface
{
    protected $cookie_name = 'WpmCookieStore';
    protected $expires_in = null;
    protected $path = "/";
    protected $domain = "";
    protected $secure = false;
    protected $http_only = false;


    public function __construct()
    {
    	$this->expires_in = time() + ( 86400 * 30 );
    }


	/**
	 * Change the key name for the cookie
	 *
	 * @param $cookie_name
	 *
	 * @return $this
	 */
	public function name($cookie_name)
    {
    	$this->cookie_name = $cookie_name;
    	return $this;
    }


    /**
	 * Set the cookie expiration
	 *
	 * @param $time
	 */
	public function expires($time)
    {
    	$this->expires_in = $time;
    }


	/**
	 * Change the cookie domain
	 *
	 * @param $domain
	 *
	 * @return $this
	 */
	public function domain($domain)
    {
    	$this->domain = $domain;
    	return $this;
    }


	/**
	 * Change the cookie path
	 *
	 * @param $path
	 *
	 * @return $this
	 */
	public function path($path)
    {
    	$this->path = $path;
    	return $this;
    }


	/**
	 * Change the cookie secure flag
	 *
	 * @param $secure
	 *
	 * @return $this
	 */
	public function secure($secure)
    {
    	$this->secure = $secure;
    	return $this;
    }


	/**
	 * Change the cookie http_only flag
	 *
	 * @param $http_only
	 *
	 * @return $this
	 */
	public function httpOnly($http_only)
    {
    	$this->http_only = $http_only;
    	return $this;
    }


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
        return $this->getCookieStore();
    }
    
    
    /**
     * Returns the value of a variable stored for a given key.
     * 
     * @param $key
     * @return null
     */
    public function get($key)
    {
        $vars = $this->getCookieStore();
        
        return (isset($vars[$key]) ? $vars[$key] : null);
    }


    /**
     * Create a cookie with all settings
     *
     * @param $key
     * @param null $value
     */
    public function create()
    {
        $this->setCookie('');
    }
    
    
    /**
     * Stores a value to a given key in the variable store.
     * 
     * @param $key
     * @param null $value
     */
    public function set($key, $value = null)
    {
        $vars = $this->getCookieStore();
        
        $vars[$key] = $value;

        $this->setCookieStore($vars);
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
            $vars = $this->getCookieStore();
            
            unset($vars[$key]);
            
            $this->setCookieStore($vars);
            
            return true;
        }
        
        return false;
    }


	/**
	 * Remove all stored cookie data
	 *
	 * @return bool
	 */
	public function removeAll()
    {
    	return $this->setCookie([]);
    }


	/**
	 * Get decoded cookie array
	 *
	 * @return array|mixed|object
	 */
	private function getCookieStore()
    {
    	if(empty($_COOKIE[$this->cookie_name])) {
    		return [];
	    }

    	$data = stripslashes($_COOKIE[$this->cookie_name]);

    	if($data == '') {
    		return [];
	    }

    	return json_decode($data, true);
    }


	/**
	 * Store encoded array
	 *
	 * @param $vars
	 *
	 * @return bool
	 */
	private function setCookieStore($vars)
    {
    	return $this->setCookie($vars);
    }


	/**
	 * Set a cookie
	 *
	 * @param $string
	 *
	 * @return bool
	 */
	private function setCookie($vars=[])
    {
    	$string = '';
    	if($vars) {
    		$string = json_encode($vars);
	    }
	    $_COOKIE[$this->cookie_name] = $string;
    	return setcookie($this->cookie_name, $string, $this->expires_in, $this->path, $this->domain, $this->secure, $this->http_only);
    }
}
