<?php
namespace Wpm\Components\Post;

class Meta
{
    protected $id = null;
    protected $key = null;

    
    public function __construct($id)
    {
        $this->id = $id;
    }
    
    
    public function key($key)
    {
        $this->key = $key;
        return $this;
    }
    
    
    public function all()
    {
        return get_post_custom($this->id);
    }
    
    
    public function first()
    {
        return get_post_meta($this->id, $this->key, true);
    }


    public function get()
    {
        return get_post_meta($this->id, $this->key, false);
    }
}