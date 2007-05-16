<?php
/**
 * $Id$
 * Forum tool - list renderer
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/tablelearningobjectpublicationlistrenderer.class.php';

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
    	$this->set_header($column++, get_lang('Forum'), false);
		$this->set_header($column++, get_lang('Topics'), false);
		$this->set_header($column++, get_lang('Posts'), false);
		$this->set_header($column++, get_lang('LastPost'), false);
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++,'',false);
    	}
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$actions[RepositoryTool::ACTION_DELETE_SELECTED] = get_lang('Delete');
    		$this->table->set_form_actions($actions);
    	}
    }
}
?>