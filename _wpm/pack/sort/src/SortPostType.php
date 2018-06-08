<?php
namespace WpmPack\Sort\src;


/*
 * An instance of SortPostType will respond to all wpm('wp.sort') calls
 */
class SortPostType {
    
    protected $cssUrl;                              // url of the sort css directory
    protected $imgUrl;                              // url of the sort img directory
    protected $jsUrl;                               // url of the sort js directory
    protected $viewDir;                             // path to the directory of sort views
    protected $postTypes;
    
    protected $options = null;                      // options stored for sort
    protected $optionVar = 'wpmSortOptions';        // the option variable where options are stored
    
    protected $tax = null;                          // the taxonomy to filter by
    protected $term = null;                         // the taxonomy term to filter by
    protected $postType = null;                     // the post type to display
    
    
    /**
     * Fire it up, set default properties and register actions
     */
    public function __construct()
    {
        $resourcesUrl = wpmContainer()->getStore('packUrl') . '/sort/src/resources';
        
        $this->cssUrl = $resourcesUrl . '/css';
        $this->imgUrl = $resourcesUrl . '/img';
        $this->jsUrl = $resourcesUrl . '/js';
        $this->viewDir = __DIR__ . '/resources';
        
        if(is_admin()) {
            add_action('wp_ajax_wpm_reorder', [&$this, 'ajaxReorderList']);
            add_action('wp_ajax_wpm_reorder_toggle', [&$this, 'ajaxToggleId']);
            add_filter('wp_list_pages_excludes', array(&$this, 'excludeFromListPages'));
        }
    }
    
    
    /**
     * Add post types that can be sorted to display an "Order" link in their
     * cms tab
     *
     * @param array $postTypes
     */
    public function add($postTypes=[])
    {
        $this->postTypes = $postTypes;
        add_action('admin_menu', [&$this, 'addSubMenu']);
    }
    
    
    /**
     * Adds the "order" page into the post types tab and adds some
     * styles and scripts
     */
    public function addSubMenu()
    {
        if(function_exists('add_options_page')) {
            foreach($this->postTypes as $postType) {
                $page = add_submenu_page('edit.php?post_type='.$postType, "Order", "Order", 'edit_pages', $postType.'Reorder', [&$this, 'adminUi']);
                add_action("admin_print_scripts-$page", [&$this, 'adminScripts']);
                add_action("admin_print_styles-$page", [&$this, 'adminStyles']);
            }
        }
    }
    
    
    /**
     * Get options stored in the database for sort
     */
    public function getOptions()
    {
        $options = get_option($this->optionVar);
        if($options) {
            $this->options = unserialize($options);
        } else {
            $this->options = [];
        }
    }
    
    
    /**
     * Store options in the database for sort
     */
    public function setOptions()
    {
        update_option($this->optionVar, serialize($this->options));
    }
    
    
    /**
     * Load up the admin ui
     */
    public function adminUi()
    {
        if(empty($_GET['post_type'])) return;
        
        $this->postType = esc_attr($_GET['post_type']);
        $this->tax = (isset($_GET['reorder_tax']) ? esc_attr($_GET['reorder_tax']) : false);
        $this->term = (isset($_GET['reorder_term']) ? esc_attr($_GET['reorder_term']) : false);
        
        $typeObj = get_post_type_object($this->postType);
        $title = $typeObj->labels->name;
        
        $this->getOptions();
        
        require_once( $this->viewDir . '/list.php' );
    }
    
    
    /**
     * queue the scripts for use in the admin
     */
    public function adminScripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('wpm-sort_post_types-interface', $this->jsUrl . '/interface-1.2.js', array('jquery'));
        wp_enqueue_script('wpm-sort_post_types-nested', $this->jsUrl . '/inestedsortable.js', array('wpm-sort_post_types-interface'));
        wp_enqueue_script('wpm-sort_post_types', $this->jsUrl . '/sort-post-types.js', array('wpm-sort_post_types-nested'));
    }
    
    
    /**
     * queue the styles fo ruse in the admin
     */
    public function adminStyles()
    {
        wp_enqueue_style('wpm-sort_post_types', $this->cssUrl.'/sort-post-types.css');
    }
    
    
    /**
     * Build a list of posts to display for the current post type
     *
     * @param int $postParent
     */
    public function buildList($postParent=0)
    {
        $posts = $this->getItems($postParent);
        
        foreach($posts as $post):
            $status = ($post->post_status != 'publish' ? '<span>' . strtoupper($post->post_status) . '</span>' : "");
            $title = ($post->post_title ? $post->post_title : '(no title)') . $status;
            ?>
            
            <li id="listItem_<?php echo $post->ID; ?>" class="clear-element page-item <?php echo $post->post_status; ?>">
                <table class="reorder-inner">
                    <tr>
                        <td>
                            <strong><?php echo $title; ?></strong>
                            <span id="postDisplay-<?php echo $post->ID; ?>" class="post-display">
								<?php if(in_array($post->ID, $this->options)) echo 'HIDDEN'; ?>
							</span>
                        </td>
                        <td width="80" class="sort-action">
                            <a href="#" class="show-hide-toggle" id="<?php echo $post->ID; ?>">Toggle</a>
                        </td>
                    </tr>
                </table>
                
                <?php
                if(is_post_type_hierarchical($this->postType)) {
                    echo '<ul class="page-list">';
                    $this->buildList($post->ID);
                    echo '</ul>';
                }
                ?>
            
            </li>
            
            <?php
        endforeach;
    }
    
    
    /**
     * Use wpm to get results to display in the order list
     *
     * @param $postParent
     *
     * @return mixed
     */
    public function getItems($postParent)
    {
        $query = wpm('q.' . $this->postType)
            ->parent($postParent)
            ->status(['publish', 'password', 'draft', 'private'])
            ->order('menu_order', 'asc');
        
        if($this->tax and $this->term) {
            $query->tax($this->tax, $this->term);
        }
        
        return $query->get();
    }
    
    
    /**
     * Reorder the list via ajax
     */
    public function ajaxReorderList()
    {
        $this->saveReorder($_POST['sort']['order-posts-list-nested']);
    }
    
    
    /**
     * Save the sort order
     *
     * @param     $data
     * @param int $parentId
     */
    public function saveReorder($data, $parentId=0)
    {
        $menuOrder = 0;
        
        foreach($data as $post) {
            $id = (int) str_replace('listItem_', '', $post['id']);
            
            if(is_numeric($id)) {
                $args = [
                    'ID' => $id,
                    'menu_order' => $menuOrder,
                    'post_parent' => $parentId
                ];
                wp_update_post($args);
                
                if(!empty($post['children'])) {
                    $this->saveReorder($post['children'], $id);
                }
            } else {
                echo $id . ',';
            }
            
            $menuOrder++;
        }
    }
    
    
    /**
     * Toggle the "hidden" value of a given post id
     */
    public function ajaxToggleId() {
        if(!empty($_POST['id']) and is_numeric($_POST['id'])) {
            $id = (int) $_POST['id'];
            $this->getOptions();
            
            if(in_array($id, $this->options)) {
                $key = array_search($id, $this->options);
                if($key !== false) {
                    unset($this->options[$key]);
                    echo '';
                }
            } else {
                $this->options[] = $id;
                echo 'HIDDEN';
            }
            
            $this->setOptions();
        }
        die();
    }
    
    
    /**
     * Return an array of excluded post ids
     *
     * @param $excludeArray
     *
     * @return array
     */
    public function excludeFromListPages($excludeArray) {
        $this->getOptions();
        
        if($this->options) {
            $excludeArray = array_merge($this->options, $excludeArray);
            sort($excludeArray);
        }
        
        return $excludeArray;
    }
}