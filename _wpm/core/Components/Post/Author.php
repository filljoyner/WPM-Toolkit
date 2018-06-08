<?php
namespace Wpm\Components\Post;

class Author
{
    protected $authorId;
    protected $meta;
    protected $field;

    public function __construct($authorId)
    {
        $this->authorId = $authorId;
    }
    
    public function author($field)
    {
        $this->field = $field;
        return $this;
    }

    public function all()
    {
        return get_user_meta($this->authorId);
    }
    
    
    public function first()
    {
        return $this->get($this->field);
    }


    public function get($field='')
    {
        return get_the_author_meta($field, $this->authorId);
    }


    public function id()
    {
        return $this->get('ID');
    }


    public function name()
    {
        return $this->get('display_name');
    }


    public function email()
    {
        return $this->get('user_email');
    }


    public function url()
    {
        return $this->get('user_url');
    }


    public function slug()
    {
        return $this->get('user_nicename');
    }


    public function firstName()
    {
        return $this->get('first_name');
    }


    public function lastName()
    {
        return $this->get('last_name');
    }


    public function description()
    {
        return $this->get('user_description');
    }


    public function level()
    {
        return $this->get('user_level');
    }
    
    
    public function feedLink()
    {
        return get_author_feed_link($this->authorId);
    }
    
    
    public function postsUrl()
    {
        return get_author_posts_url($this->authorId);
    }
}

    