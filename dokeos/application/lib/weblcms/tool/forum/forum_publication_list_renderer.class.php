<?php
/**
 * $Id$
 * Forum tool - list renderer
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/table_learning_object_publication_list_renderer.class.php';

class ForumPublicationListRenderer extends TableLearningObjectPublicationListRenderer
{
    function ForumPublicationListRenderer($browser)
    {
    	parent :: __construct($browser);
    	$column = 0;
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++,'',false);
    	}
    	$this->set_header($column++, '' , false);
    	$this->set_header($column++, Translation :: get('Forum'), false);
		$this->set_header($column++, Translation :: get('Topics'), false);
		$this->set_header($column++, Translation :: get('Posts'), false);
		$this->set_header($column++, Translation :: get('LastPost'), false);
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++,'',false);
    	}
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$actions[Tool::ACTION_DELETE_SELECTED] = Translation :: get('Delete');
    		$this->table->set_form_actions($actions);
    	}
    }
}
?>