<?php
require_once dirname(__FILE__).'/../../../learningobjecttree.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathTree extends LearningObjectTree
{
    function LearningPathTree($root, $active = 0)
    {
    	parent :: __construct($root, array('learning_path_chapter', 'learning_path_item'), array('learning_path_item'));
    	$this->set_active_item($active);
    }
}
?>