<?php
namespace Wpm\Components;

/*
 * An instance of Action will respond to all wpm('wp.action') calls
 */
class Action
{
    /**
     * @param $actionTag
     * @param $closure
     *
     * @return true|void
     */
    public function add($actionTag, $closure)
    {
        return add_action($actionTag, $closure);
    }
    
    
    /**
     * @param $actionTag
     * @param $functionNameToCheck
     *
     * @return bool|int
     */
    public function has($actionTag, $functionNameToCheck)
    {
        return has_action($actionTag, $functionNameToCheck);
    }
    
    
    /**
     * @param       $actionTag
     * @param array $args
     *
     * @return bool|int
     */
    public function run($actionTag, $args=[])
    {
        if($args) {
            return do_action_ref_array($actionTag, $args);
        }
        return do_action($actionTag, $args);
    }
    
    
    /**
     * @param $actionTag
     *
     * @return bool
     */
    public function running($actionTag)
    {
        return doing_action($actionTag);
    }
    
    
    /**
     * @param $actionTag
     *
     * @return int|void
     */
    public function ran($actionTag)
    {
        return did_action($actionTag);
    }
    
    
    /**
     * @param     $actionTag
     * @param     $functionNameToRemove
     * @param int $priority
     *
     * @return bool
     */
    public function remove($actionTag, $functionNameToRemove, $priority=10)
    {
        return remove_action($actionTag, $functionNameToRemove, $priority);
    }
    
    
    /**
     * @param      $actionTag
     * @param bool $priority
     *
     * @return true
     */
    public function removeAll($actionTag, $priority=false)
    {
        return remove_all_actions($actionTag, $priority);
    }
}