<?php
namespace Wpm\Components\Post;

class Children
{
    protected $q;
    protected $id;
    protected $wpm;

    public function __construct($id, $postType)
    {
        $this->id = $id;
        $this->postType = $postType;
    }
    
    public function children()
    {
        $this->wpm = wpm('q.' . $this->postType)->parent($this->id);
        return $this;
    }
    
    
    public function get()
    {
        return $this->wpm->get();
    }
    
    
    public function first()
    {
        return $this->wpm->first();
    }
}