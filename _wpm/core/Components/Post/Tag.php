<?php
namespace Wpm\Components\Post;

class Tag
{
    protected $id = null;

    public function __construct($id)
    {
        $this->id = $id;
    }
    
    
    public function tag()
    {
        return $this;
    }


    public function all()
    {
        return get_the_tags($this->id);
    }


    public function lists($before='', $sep='', $after='')
    {
        return get_the_tag_list($before, $sep, $after, $this->id);
    }
}