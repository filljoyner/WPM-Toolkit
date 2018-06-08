<?php
namespace Wpm\Components;

/*
 * An instance of PostType will respond to all wpm('wp.postType') calls
 */
class PostType
{
    public $data;
    
    
    /**
     * Store data as a property and get on with it
     * 
     * @param $data
     */
    public function __construct($data=[])
    {
        $this->data = $data;
    }
    
    
    /**
     * Create a post type from the provided arguments.
     *
     * @param $args
     */
    public function create($args)
    {
        $singular = $args['name'];
        $plural = issetOrDefault($args, 'plural_name', $singular . 's');
        $slug = issetOrDefault($args, 'slug', strtolower($singular));
        $like = issetOrDefault($args, 'like', 'post');
        $icon = issetOrDefault($args, 'icon');
        
        if ($icon) $args['menu_icon'] = $icon;
        
        foreach (['name', 'plural_name', 'slug', 'like', 'icon'] as $key) unset($args[ $key ]);
        
        if (isset($args['labels'])) {
            $labels = $args['labels'];
        } else {
            $labels = [
                'name'               => _x($plural, 'post type general name', 'wpm'),
                'singular_name'      => _x($singular, 'post type singular name', 'wpm'),
                'menu_name'          => _x($plural, 'admin menu', 'wpm'),
                'name_admin_bar'     => _x($singular, 'add new on admin bar', 'wpm'),
                'add_new'            => _x('Add New', $singular, 'wpm'),
                'add_new_item'       => __('Add New ' . $singular, 'wpm'),
                'new_item'           => __('New ' . $singular, 'wpm'),
                'edit_item'          => __('Edit ' . $singular, 'wpm'),
                'view_item'          => __('View ' . $singular, 'wpm'),
                'all_items'          => __('All ' . $plural, 'wpm'),
                'search_items'       => __('Search ' . $plural, 'wpm'),
                'parent_item_colon'  => __('Parent ' . $plural . ':', 'wpm'),
                'not_found'          => __('No ' . strtolower($plural) . ' found.', 'wpm'),
                'not_found_in_trash' => __('No ' . strtolower($plural) . ' found in Trash.', 'wpm')
            ];
        }
        
        $buildArgs = array_merge([
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => ['slug' => $slug],
            'capability_type'     => $like,
        ], $args);
        
        if ($like == 'page') {
            $orderBy = 'menu_order';
            $order = 'asc';
            
            $typeArgs = array_merge([
					'has_archive' => false,
					'hierarchical' => true,
					'supports' => array('title', 'editor', 'author', 'excerpt', 'custom-fields', 'page-attributes')
            ], $buildArgs);
        }
        
        if ($like == 'post') {
            $orderBy = 'post_date';
            $order = 'desc';
            
            $typeArgs = array_merge([
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor', 'author', 'excerpt', 'custom-fields', 'comments')
            ], $buildArgs);
        }
        
        register_post_type($slug, $typeArgs);
        
        wpm('store.var')->set($slug . 'PostType', [
            'capability' => $like,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }
}
