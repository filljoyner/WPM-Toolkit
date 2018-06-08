<?php
namespace Wpm\Components\Post;

/**
 * wpms post object
 */
class Post
{
    protected $authorComponent = null;
    protected $childrenComponent = null;
    protected $metaComponent = null;
    protected $ancestorComponent = null;
    protected $tagComponent = null;
    protected $termComponent = null;
    
    
    public function __construct ($post)
    {
        $this->post = $post;
        
        if(!empty($post->ID)) $this->createEndpoints();
    }
    
    
    public function meta($key=null)
    {
        return $this->metaComponent->key($key);
    }
    
    
    public function term($tax = null)
    {
        return $this->termComponent->taxonomy($tax);
    }
    
    
    public function tag()
    {
        return $this->tagComponent->tag();
    }
    
    
    public function children()
    {
        return $this->childrenComponent->children();
    }
    
    
    public function ancestor()
    {
        return $this->ancestorComponent->ancestor();
    }
    
    
    public function author($field=null)
    {
        return $this->authorComponent->author($field);
    }
    
    
    private function createEndpoints()
    {
        $this->metaComponent       = new Meta($this->post->ID);
        $this->termComponent       = new Term($this->post->ID, $this->post->post_type);
        $this->tagComponent        = new Tag($this->post->ID);
        $this->authorComponent     = new Author($this->post->post_author);
        $this->childrenComponent   = new Children($this->post->ID, $this->post->post_type);
        $this->ancestorComponent   = new Ancestor($this->post->ID, $this->post->post_parent, $this->post->post_type);
    }
    
    
    public function __get($name)
    {
        return $this->post->$name;
    }
}