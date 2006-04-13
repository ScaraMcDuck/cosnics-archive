<?php
require_once dirname(__FILE__).'/../../learningobjecttree.class.php';
class ForumTree extends LearningObjectTree
{
    function ForumTree($root, $active = 0)
    {
    	parent :: __construct($root, array('forum_topic', 'forum_post'), array('forum_post'));
    	$this->set_active_item($active);
    }
}
?>