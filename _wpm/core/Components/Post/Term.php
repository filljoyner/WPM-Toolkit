<?php
namespace Wpm\Components\Post;

class Term
{
    protected $id = null;
    protected $taxonomy = null;
    protected $postType = null;

    
    public function __construct($id, $postType)
    {
        $this->id = $id;
        $this->postType = $postType;
    }
    
    
    public function taxonomy($taxonomy)
    {
        $this->taxonomy = $taxonomy;
        return $this;
    }


    public function all()
    {
        $taxonomies = get_object_taxonomies($this->postType);

        $results = [];

        if($taxonomies) {
            foreach($taxonomies as $tax)
            {
                $this->taxonomy = $tax;
                $results[$tax] = $this->get();
            }
            
            $this->taxonomy = null;
        }

        return $results;
    }
    
    
    public function get()
    {
        return get_the_terms($this->id, $this->taxonomy);
    }
    
    
    public function first()
    {
        $terms = $this->get();
        if(isset($terms[0])) return $terms[0];
        
        return false;
    }


    public function lists($before='', $sep='', $after='')
    {
        return get_the_term_list($this->id, $this->taxonomy, $before, $sep, $after);
    }
}