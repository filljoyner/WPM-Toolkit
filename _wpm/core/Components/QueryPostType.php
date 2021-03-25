<?php
namespace Wpm\Components;

/*
 * An instance of QueryPostType will respond to all wpm('q.post_type_here') calls
 */
class QueryPostType
{
    protected $postTypes;
    
    
    protected $args = [                         // default arguments for all requests
        'posts_per_page'   => -1,
        'post_type'        => 'post',
        'post_status'      => 'publish',
        'suppress_filters' => true
    ];
    
    
    /**
     * Store the post types
     *
     * PostTypeComponent constructor.
     * @param $postTypes
     */
    public function __construct($postTypes)
    {
        $this->args['post_type'] = $postTypes;
    }
    
    
    /**
     * Setter for the postType
     *
     * @param $postType
     *
     * @return $this
     */
    public function postType($postType)
    {
        $this->args['post_type'] = $postType;
        return $this;
    }
    
    
    /**
     * Allows for getting by author. Either id, name, array of ids, or with
     * a connector.
     *
     * @param $varA
     * @param null $varB
     * @return $this
     */
    public function author($varA, $varB = null)
    {
        // if only input a is provided
        if (!$varB) {
            if (is_array($varA)) {
                $this->args['author__in'] = $varA;
            }
            
            if (is_numeric($varA)) {
                $this->args['author'] = $varA;
            }
            
            if (is_string($varA)) {
                $this->args['author_name'] = $varA;
            }
            
            return $this;
        }
        
        
        // if input a and b are provided
        $connector = $varA;
        $ids = (is_array($varB) ? $varB : [$varB]);
        
        if ($connector == 'in') {
            $this->args['author__in'] = $ids;
        }
        
        if ($connector == 'not') {
            $this->args['author__not_in'] = $ids;
        }
        
        return $this;
    }
    
    
    /**
     * Creates a tax query when querying with a post's category
     * @param      $varA
     * @param null $varB
     *
     * @return $this
     */
    public function category($varA, $varB = null)
    {
        $this->tax('category', $varA, $varB);
        return $this;
    }
    
    
    /**
     * Parses tag arguments into the args array. See the docs for more.
     *
     * @param      $varA
     * @param null $varB
     *
     * @return $this
     */
    public function tag($varA, $varB = null)
    {
        // if no input is provided for varB
        if (!$varB) {
            if (is_numeric($varA)) {
                $this->args['tag_id'] = $varA;
            }
            
            if (is_string($varA)) {
                $this->args['tag'] = $varA;
            }
            
            if (is_array($varA)) {
                if (is_numeric(array_values($varA)[0])) {
                    $this->args['tag__and'] = $varA;
                }
                
                if (is_string(array_values($varA)[0])) {
                    $this->args['tag'] = implode('+', $varA);
                }
            }
            
            return $this;
        }
        
        // if input is provided for both varA and varB
        $connector = $varA;
        $ids = (is_array($varB) ? $varB : [$varB]);
        
        if (!is_array($ids)) $ids = [$ids];
        
        if ($connector == 'and') {
            if (is_numeric(array_values($ids)[0])) {
                $this->args['tag__and'] = $ids;
            }
            
            if (is_string(array_values($ids)[0])) {
                $this->args['tag'] = implode('+', $ids);
            }
        }
        
        if ($connector == 'in') {
            if (is_numeric(array_values($ids)[0])) {
                $this->args['tag__in'] = $ids;
            }
            
            if (is_string(array_values($ids)[0])) {
                $this->args['tag'] = implode(',', $ids);
            }
        }
        
        if ($connector == 'not') {
            $this->args['tag__not_in'] = $ids;
        }
        
        return $this;
    }
    
    
    /**
     * Create a taxonomy query from provided arguments. See docs for more details.
     *
     * @param      $taxonomy
     * @param null $inputA
     * @param null $inputB
     * @param bool $includeChildren
     *
     * @return $this
     */
    public function tax($taxonomy, $inputA=null, $inputB = null, $includeChildren = true)
    {
        $this->args['tax_query'][] = $this->taxQuery($taxonomy, $inputA, $inputB, $includeChildren);
        return $this;
    }
    
    
    /**
     * Returns an array to be added to a nested tax query by a closure. You'll want to
     * read up on this in the docs.
     *
     * @param      $taxonomy
     * @param null $inputA
     * @param null $inputB
     * @param bool $includeChildren
     *
     * @return array
     */
    public function taxQuery($taxonomy, $inputA=null, $inputB = null, $includeChildren = true)
    {
        if(is_callable($taxonomy)) return $taxonomy($this);
        
        if (is_array($taxonomy)) {
            
            $taxQuery = $taxonomy;

        } else {

            $field = 'term_id';
            $terms = $inputA;
            $operator = 'IN';

            if($inputB) {
                $operator = $inputA;
                $terms = $inputB;
            }

            if ($operator == 'not') $operator = 'not in';

            $terms = is_array($terms) ? $terms : [$terms];
            
            if (is_string(array_values($terms)[0])) $field = 'slug';

            $taxQuery = [
                'taxonomy'         => $taxonomy,
                'field'            => $field,
                'terms'            => $terms,
                'operator'         => $operator,
                'include_children' => $includeChildren
            ];

        }

        return $taxQuery;
    }
    
    
    /**
     * Adds an additional tax query and sets the relation between the queries as "or"
     * See docs for more
     *
     * @param      $taxonomy
     * @param null $inputA
     * @param null $inputB
     * @param bool $includeChildren
     *
     * @return QueryPostType
     */
    public function orTax($taxonomy, $inputA=null, $inputB = null, $includeChildren = true)
    {
        $this->setTaxQueryRelation('OR');
        return $this->tax($taxonomy, $inputA, $inputB, $includeChildren);
    }
    
    
    /**
     * Adds an additional tax query and set the relation between the queries to "and"
     * See docs for more
     *
     * @param      $taxonomy
     * @param null $inputA
     * @param null $inputB
     * @param bool $includeChildren
     *
     * @return QueryPostType
     */
    public function andTax($taxonomy, $inputA=null, $inputB = null, $includeChildren = true)
    {
        $this->setTaxQueryRelation('AND');
        return $this->tax($taxonomy, $inputA, $inputB, $includeChildren);
    }
    
    
    /**
     * Manually set the tax query relation when defining multiple tax query entries.
     *
     * @param      $relation
     * @param bool $countCheck
     */
    protected function setTaxQueryRelation($relation, $countCheck = false)
    {
        $pass = true;
        
        if ($countCheck) {
            if (count($this->args['tax_query']) <= 1 or isset($this->args['tax_query']['relation'])) {
                $pass = false;
            }
        }
        
        if ($pass) $this->args['tax_query']['relation'] = $relation;
    }
    
    
    /**
     * Setter for the meta_key argument.
     *
     * @param $key
     *
     * @return $this
     */
    public function metaKey($key)
    {
        $this->args['meta_key'] = $key;
        return $this;
    }
    
    
    /**
     * Setter for the meta_value argument
     *
     * @param $value
     *
     * @return $this
     */
    public function metaValue($value)
    {
        $this->args['meta_value'] = $value;
        return $this;
    }
    
    
    /**
     * Setter for the meta_value_num argument
     *
     * @param $num
     *
     * @return $this
     */
    public function metaValueNum($num)
    {
        $this->args['meta_value_num'] = $num;
        return $this;
    }
    
    
    /**
     * Setter for the meta_compare argument. If you get to this point you'll
     * probably just want to use the meta method.
     *
     * @param $compare
     *
     * @return $this
     */
    public function metaCompare($compare)
    {
        $this->args['meta_compare'] = $compare;
        return $this;
    }
    
    
    /**
     * Setter for the meta type argument
     *
     * @param $type
     *
     * @return $this
     */
    public function metaType($type)
    {
        $this->args['meta_type'] = $type;
        return $this;
    }
    
    
    /**
     * Create a meta query based on arguments. See docs for more info.
     *
     * @param      $inputA
     * @param null $inputB
     * @param null $inputC
     * @param null $type
     *
     * @return $this
     */
    public function meta($inputA, $inputB=null, $inputC=null, $type=null)
    {
        $this->args['meta_query'][] = $this->metaQuery($inputA, $inputB, $inputC, $type);
        return $this;
    }
    
    
    /**
     * Returns an array to be added to a nested meta query by a closure. You'll want to
     * read up on this in the docs.
     *
     * @param      $inputA
     * @param null $inputB
     * @param null $inputC
     * @param null $type
     *
     * @return array
     */
    public function metaQuery($inputA, $inputB=null, $inputC=null, $type=null)
    {
        if(is_callable($inputA)) return $inputA($this);
        
        if(is_array($inputA)) {

            $metaQuery = $inputA;

        } else {

            $metaKey = $inputA;
            $metaValue = $inputC ? $inputC : $inputB;
            $metaCompare = ($inputB and $inputC) ? $inputB : null;
            $metaType = $type;
            
            $metaQuery['key'] = $metaKey;
            if($metaValue) $metaQuery['value'] = $metaValue;
            if($metaCompare) $metaQuery['compare'] = $metaCompare;
            if($metaType) $metaQuery['type'] = $metaType;

            if($metaValue and !$metaType and is_numeric($metaValue)) {
                $metaQuery['type'] = 'numeric';
            }

        }
        
        return $metaQuery;
    }
    
    
    /**
     * Adds an additional meta query and set the relation between the queries to "and"
     * See docs for more
     *
     * @param      $inputA
     * @param null $inputB
     * @param null $inputC
     * @param null $type
     *
     * @return QueryPostType
     */
    public function andMeta($inputA, $inputB=null, $inputC=null, $type=null)
    {
        $this->setMetaQueryRelation('AND');
        return $this->meta($inputA, $inputB, $inputC, $type);
    }
    
    
    /**
     * Adds an additional meta query and set the relation between the queries to "or"
     * See docs for more
     *
     * @param      $inputA
     * @param null $inputB
     * @param null $inputC
     * @param null $type
     *
     * @return QueryPostType
     */
    public function orMeta($inputA, $inputB=null, $inputC=null, $type=null)
    {
        $this->setMetaQueryRelation('OR');
        return $this->meta($inputA, $inputB, $inputC, $type);
    }
    
    
    /**
     * Manually set the tax query relation when defining multiple tax query entries.
     *
     * @param      $relation
     * @param bool $countCheck
     */
    protected function setMetaQueryRelation($relation, $countCheck = false)
    {
        $pass = true;
        
        if ($countCheck) {
            if (count($this->args['meta_query']) <= 1 or isset($this->args['meta_query']['relation'])) {
                $pass = false;
            }
        }
        
        if ($pass) {
            $this->args['meta_query'] = ['relation' => $relation] + $this->args['meta_query'];
        }
    }
    
    
    /**
     * Setter for the s argument
     *
     * @param $string
     *
     * @return $this
     */
    public function search($string)
    {
        $this->args['s'] = $string;
        return $this;
    }
    
    
    /**
     * Set the post_parent__in or post_parent arguments.
     *
     * @param $ids
     *
     * @return $this
     */
    public function parent($ids)
    {
        if(is_array($ids)) {
            $this->args['post_parent__in'] = $ids;
        } else {
            $this->args['post_parent'] = $ids;
        }
        
        return $this;
    }
    
    
    /**
     * Setter for the has_password and post_password arguments
     *
     * @param bool $password
     *
     * @return $this
     */
    public function password($password=true)
    {
        if($password === true or $password === false or $password === null) {
            $this->args['has_password'] = $password;
        } else {
            $this->args['post_password'] = $password;
        }

        return $this;
    }
    
    
    /**
     * Setter for the post_status argument
     *
     * @param $status
     *
     * @return $this
     */
    public function status($status)
    {
        $this->args['post_status'] = $status;
        return $this;
    }
    
    
    /**
     * Setter for the offset argument
     *
     * @param $offset
     *
     * @return $this
     */
    public function offset($offset)
    {
        $this->args['offset'] = $offset;
        return $this;
    }
    
    
    /**
     * Setter for the page argument
     *
     * @param $page
     *
     * @return $this
     */
    public function page($page)
    {
        $this->args['page'] = $page;
        return $this;
    }
    
    
    /**
     * Setter for the page argument.
     *
     * @param $paged
     *
     * @return $this
     */
    public function paged($paged)
    {
        $this->args['paged'] = $paged;
        return $this;
    }
    
    
    /**
     * Setter fro the no_paging argument
     *
     * @param bool $paging
     *
     * @return $this
     */
    public function paging($paging=true)
    {
        if(!$paging) {
            $this->args['no_paging'] = true;
        }
        return $this;
    }

    /**
     * Setter for post__in arguments
     *
     * @param  array   $post_ids
     * @return $this
     */
    public function postIn($post_ids)
    {
        if (is_array($ids)) {
            $this->args['post__in'] = $ids;
        }
        return $this;
    }

    /**
     * Setter for post__not_in arguments
     *
     * @param  array   $post_ids
     * @return $this
     */
    public function postNotIn($post_ids)
    {
        if (is_array($ids)) {
            $this->args['post__not_in'] = $ids;
        }
        return $this;
    }
    
    
    /**
     * Setter for one of several arguments for sticky posts. You'll want to check
     * the manual for this one.
     * @param $input
     *
     * @return $this
     */
    public function sticky($input)
    {
        if(!$input) {
            $this->args['ignore_sticky_posts'] = true;
        } elseif($input == 'in') {
            $this->args['post__in'] = get_option('sticky_posts');
        } elseif($input == 'not' or $input == 'not in') {
            $this->args['post__not_in'] = get_option('sticky_posts');
        }

        return $this;
    }
    
    
    /**
     * Setter for order and orderby arguments
     *
     * @param      $inputA
     * @param null $inputB
     *
     * @return $this
     */
    public function order($inputA, $inputB=null)
    {
        if(!$inputB) {
            if(is_array($inputA)) {
                $this->args['orderby'] = $inputA;
            } else {
                $this->args['order'] = $inputA;
            }
        } else {
            $this->args['orderby'] = $inputA;
            $this->args['order'] = $inputB;
        }
        return $this;
    }
    
    
    /**
     * Setter for the orderb argument
     * @param $field
     *
     * @return $this
     */
    public function orderBy($field)
    {
        $this->args['orderby'] = $field;
        return $this;
    }
    
    
    /**
     * Setter for the year argument
     *
     * @param $year
     *
     * @return $this
     */
    public function year($year)
    {
        $this->args['year'] = $year;
        return $this;
    }
    
    
    /**
     * Setter for the week argument
     *
     * @param $week
     *
     * @return $this
     */
    public function week($week)
    {
        $this->args['w'] = $week;
        return $this;
    }
    
    
    /**
     * Setter for the day argument
     *
     * @param $day
     *
     * @return $this
     */
    public function day($day)
    {
        $this->args['day'] = $day;
        return $this;
    }
    
    
    /**
     * Setter for the hour argument
     *
     * @param $hour
     *
     * @return $this
     */
    public function hour($hour)
    {
        $this->args['hour'] = $hour;
        return $this;
    }
    
    
    /**
     * Setter for the minute argument
     * @param $minute
     *
     * @return $this
     */
    public function minute($minute)
    {
        $this->args['minute'] = $minute;
        return $this;
    }
    
    
    /**
     * Setter for the second argument
     *
     * @param $second
     *
     * @return $this
     */
    public function second($second)
    {
        $this->args['second'] = $second;
        return $this;
    }
    
    
    /**
     * Setter for the year and month arguments
     * @param $year
     * @param $month
     *
     * @return $this
     */
    public function yearMonth($year, $month)
    {
        $this->args['m'] = $year . $month;
        return $this;
    }
    
    
    /**
     * Create a date query from passed in arguments.
     *
     * @param $input
     *
     * @return $this
     */
    public function date($input)
    {
        $this->args['date_query'][] = $input;
        return $this;
    }
    
    
    /**
     * Setter for the perm arguments
     * @param $perm
     *
     * @return $this
     */
    public function permission($perm)
    {
        $this->args['perm'] = $perm;
        return $this;
    }
    
    
    /**
     * Setter for the post_mime_type and post_status arguments
     *
     * @param      $mimeType
     * @param bool $addPostStatus
     *
     * @return $this
     */
    public function mimeType($mimeType, $addPostStatus=true)
    {
        $this->args['post_mime_type'] = $mimeType;
        if($addPostStatus) $this->args['post_status'] = 'inherit';
        return $this;
    }
    
    
    /**
     * Setter for cache_results or update_post_meta_cache or update_post_term_cache
     * arguments
     *
     * @param bool $inputA
     * @param null $inputB
     *
     * @return $this
     */
    public function cache($inputA=true, $inputB=null)
    {
        if($inputB === null) {
            $this->args['cache_results'] = $inputA;
        } else {
            if(in_array($inputA, ['meta', 'term'])) {
                $this->args['update_post_' . $inputA . '_cache'] = $inputB;
            }
        }
        return $this;
    }
    
    
    /**
     * Setter for the fields arguments
     *
     * @param $input
     *
     * @return $this
     */
    public function fields($input)
    {
        if($input == 'id')
        {
            $this->args['fields'] = 'ids';
        }
        if($input == 'parent')
        {
            $this->args['fields'] = 'id=>parent';
        }
        return $this;
    }



    /**
     * Limit the number of records to return.
     *
     * @param $numberOfRecords
     * @return $this
     */
    public function limit($numberOfRecords)
    {
        $this->args['posts_per_page'] = $numberOfRecords;
        
        return $this;
    }
    
    
    /**
     * Return the arguments to see what QueryPostType has created for you
     *
     * @return array
     */
    public function getArgs()
    {
        if (!isset($this->args['orderby']) or !isset($this->args['order'])) {
            $this->setPostTypeDefaultOrder();
        }
        
        return $this->args;
    }
    
    
    /**
     * Manually set all arguments
     *
     * @param $args
     *
     * @return $this
     */
    public function setArgs($args)
    {
        $this->args = $args;
        return $this;
    }
    
    
    /**
     * Manually set a single argument by key
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setArg($key, $value)
    {
        $this->args[$key] = $value;
        return $this;
    }
    
    
    /**
     * Find the first result based on id or slug
     *
     * @param      $id
     * @param bool $capability
     *
     * @return bool
     */
    public function find($id, $capability=false)
    {
        if(is_numeric($id)) {
            
            $this->args['post__in'] = [$id];
            
        } else {

            $postType = $this->getFirstPostType();
            
            if($postType == 'post') $capability = 'post';
            if($postType == 'page') $capability = 'page';
            
            if(!$capability) {
                
                $capability = 'post';
                
                $postTypeData = wpm('store.var')->get($postType . 'PostType');
                
                if(isset($postTypeData['capability'])) {
                    $capability = $postTypeData['capability'];
                }
            }
            
            if($capability == 'post') {
                $this->args['name'] = $id;
            } else { 
                $this->args['pagename'] = $id;
            }
        }
        
        return $this->first();
    }
    
    
    
    /**
     * Return only the first result based on the query.
     *
     * @param bool $wpQuery
     * @return bool
     */
    public function first($wpQuery = false)
    {
        $this->limit(1);
        $results = $this->get($wpQuery);

        if (isset($results[0]) and !$wpQuery) {
            return $results[0];
        }
        
        return false;
    }
    
    
    /**
     * Gets the results based on the generated query arguments.
     *
     * @param bool $wpQuery
     * @return mixed|WP_Query
     */
    public function get($wpQuery = false)
    {
        // if no order is set, get post types default order
        if (!isset($this->args['orderby']) or !isset($this->args['order'])) {
            $this->setPostTypeDefaultOrder();
        }
        
        if (!$wpQuery) {
            $results = get_posts($this->args);

            return wpm_post($results);
            
        } else {
            return new \WP_Query($this->args);
        }
    }
    
    
    /**
     * Paginate the results by creating a new WP Query object.
     *
     * @param array $args
     */
    function paginate($args = [])
    {
        global $wpm___wp_query;
        
        $paged = 'paged';
        $page = (get_query_var($paged)) ? get_query_var($paged) : 1;
        
        $this->args['paged'] = $page;
        $wpQueryArgs = array_merge($this->args, $args);
        
        $wpm___wp_query = new WP_Query($wpQueryArgs);
        
        add_action('wp_head', array($this, 'paginateAction'));
    }
    
    
    /**
     * An action to allow for the new paginated WP Query Object. Previous WP Query
     * is stored in $wpm__wp_query global for retrieval if necessary.
     */
    public function paginateAction()
    {
        global $wpm___wp_query;
        
        if ($wpm___wp_query) {
            global $wp_query, $wpm__wp_query;
            $wpm__wp_query = $wp_query;
            $wp_query = $wpm___wp_query;
        }
    }
    
    
    /**
     * Returns the first post type
     *
     * @return mixed
     */
    protected function getFirstPostType()
    {
        if (is_array($this->args['post_type'])) {
            return $this->args['post_type'][0];
        }
        
        return $this->args['post_type'];
    }

    
    
    /**
     * Set a default results order based on the post type
     *
     * @return bool
     */
    protected function setPostTypeDefaultOrder()
    {
        $postType = $this->getFirstPostType();
        
        if (!$postType) return false;

        $defaultOrder = 'desc';
        $defaultOrderBy = 'post_date';
        
        $order = wpm('store.var')->get($postType . 'PostType');
        
        if (!empty($order)) {
            $defaultOrder = $order['order'];
            $defaultOrderBy = $order['orderBy'];
        }
        
        if ($postType == 'page') {
            $defaultOrder = 'asc';
            $defaultOrderBy = 'menu_order';
        }
        
        if(!isset($this->args['orderby'])) $this->args['orderby'] = $defaultOrderBy;
        if(!isset($this->args['order'])) $this->args['order'] = $defaultOrder;
    }
    
    
    /**
     * Set the orderby and order argument.
     *
     * @param null $orderBy
     * @param null $order
     * @return bool
     */
    protected function setOrder($orderBy = null, $order = null)
    {
        $this->args['orderby'] = $orderBy;
        $this->args['order'] = $order;
        
        if ($orderBy or $order) return true;
        
        return false;
    }
}
