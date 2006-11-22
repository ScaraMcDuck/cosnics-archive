<?php
/**
 * $Id$
 * Document tool - list renderer
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/tablelearningobjectpublicationlistrenderer.class.php';
class DocumentPublicationListRenderer extends TableLearningObjectPublicationListRenderer
{
    function DocumentPublicationListRenderer($browser)
    {
    	parent :: __construct($browser);
    	$column = 0;
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++, '', false);
    	}
    	$this->set_header($column++, get_lang('Type'), true);
    	$this->set_header($column++, get_lang('Title'), true);
		$this->set_header($column++, get_lang('Description'), true);
		$this->set_header($column++, get_lang('PublishedOn'), true);
		$this->set_header($column++, get_lang('PublishedBy'), true);
		$this->set_header($column++, get_lang('PublishedFor'), true);
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++, '', false);
    	}
    }

    function render_title($publication) {
    	return '<a href="'.htmlentities($publication->get_learning_object()->get_url()).'">'.parent::render_title($publication).'</a>';
    }
}
?>