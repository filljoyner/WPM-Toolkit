<?php
namespace Wpm\Components;

/*
 * An instance of Nav will respond to all wpm('wp.nav') calls
 */
class Nav
{
    /**
     * If a string is not provided return the previous url to the current pagination
     * If a string is provided, return a link using the string as the link's text.
     *
     * @param null $text
     *
     * @return string|void
     */
    public function prev($text=null)
    {
        if($text) return get_previous_posts_link($text);
        return get_previous_posts_page_link();
    }
    
    
    /**
     * If a string is not provided return the next url to the current pagination
     * If a string is provided, return a link using the string as the link's text.@param null $text
     *
     * @return string|void
     */
    public function next($text=null)
    {
        if($text) return get_next_posts_link($text);
        return get_next_posts_page_link();
    }
    
    
    /**
     * Return page numbers for paginated results
     *
     * @param array $args
     *
     * @return string
     */
    public function paginate($args=[])
    {
        return get_the_posts_pagination($args);
    }
}