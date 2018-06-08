<?php
namespace Wpm\Components;

/*
 * An instance of Filter will respond to all wpm('wp.filter') calls
 */
class Filter
{
    /**
     * @param $filterTag
     * @param $closure
     *
     * @return true|void
     */
    public function add($filterTag, $closure)
    {
        return add_filter($filterTag, $closure);
    }
    
    
    /**
     * @param $filterTag
     * @param $functionNameToCheck
     *
     * @return false|int
     */
    public function has($filterTag, $functionNameToCheck)
    {
        return has_filter($filterTag, $functionNameToCheck);
    }
    
    
    /**
     * @param        $filterTag
     * @param string $args
     *
     * @return mixed|void
     */
    public function run($filterTag, $args='')
    {
        if(is_array($args)) {
            return apply_filters_ref_array($filterTag, $args);
        }
        return apply_filters($filterTag, $args);
    }
    
    
    /**
     * @param $filterTag
     *
     * @return bool
     */
    public function running($filterTag)
    {
        return doing_filter($filterTag);
    }
    
    
    /**
     * @param     $filterTag
     * @param     $functionNameToRemove
     * @param int $priority
     *
     * @return bool
     */
    public function remove($filterTag, $functionNameToRemove, $priority=10)
    {
        return remove_action($filterTag, $functionNameToRemove, $priority);
    }
    
    
    /**
     * @param      $filterTag
     * @param bool $priority
     *
     * @return true
     */
    public function removeAll($filterTag, $priority=false)
    {
        return remove_all_actions($filterTag, $priority);
    }
}