<?php
namespace Wpm\Components\Post;

class Ancestor
{
    protected $id;
    protected $parentId;
    protected $postType;

    
    public function __construct($id, $parentId, $postType)
    {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->postType = $postType;
    }
    
    
    public function ancestor()
    {
        return $this;
    }


    public function get()
    {
        if($this->parentId) {
            return get_ancestors($this->id, $this->postType, 'post_type');
        }
        return [];
    }
    
    
    public function first()
    {
        return wpm('q.' . $this->postType)->find($this->parentId);
    }
}