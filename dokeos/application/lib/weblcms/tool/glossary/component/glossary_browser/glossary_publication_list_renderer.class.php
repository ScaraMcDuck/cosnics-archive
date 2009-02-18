<?php
/**
 * $Id: glossary_publication_list_renderer.class.php 18134 2009-02-02 11:47:39Z vanpouckesven $
 * Glossary tool - list renderer
 * @package application.weblcms.tool
 * @subpackage glossary
 */
require_once dirname(__FILE__).'/../../../../browser/list_renderer/table_learning_object_publication_list_renderer.class.php';
class GlossaryPublicationListRenderer extends TableLearningObjectPublicationListRenderer
{
    function GlossaryPublicationListRenderer($browser)
    {
    	parent :: __construct($browser);
    	$column = 0;
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++, '', false);
    	}
    	$this->set_header($column++, Translation :: get('Type'), false);
    	$this->set_header($column++, Translation :: get('Title'), false);
		$this->set_header($column++, Translation :: get('Description'), false);
		$this->set_header($column++, Translation :: get('PublishedOn'), false);
		$this->set_header($column++, Translation :: get('PublishedBy'), false);
		$this->set_header($column++, Translation :: get('PublishedFor'), false);
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++, '', false);
    	}
    }

   function render_title($publication) {
    	//$html[] = '<a href="'.htmlentities($publication->get_learning_object()->get_url()).'" style="float:left;margin-right: 20px;">'.parent::render_title($publication).'</a>';
    	$parameters[Tool :: PARAM_ACTION] = GlossaryTool :: ACTION_VIEW_GLOSSARY;
    	$parameters[Tool :: PARAM_PUBLICATION_ID] =  $publication->get_learning_object()->get_id();
    	$html[] = '<a href="'.$this->get_url($parameters).'">' . parent :: render_title($publication) . '</a>';
    	return implode("\n",$html);
    }
    
    function render_description($publication)
    {
    	return strip_tags($publication->get_learning_object()->get_description());
    }
    
	function render_publication_actions($publication,$first,$last)
	{
		$html = array();
		$icons = array();
		
		$html[] = '<span style="white-space: nowrap;">';
		
		if ($this->is_allowed(DELETE_RIGHT))
		{
			$icons[] = $this->render_delete_action($publication);
		}
		if ($this->is_allowed(EDIT_RIGHT))
		{
			$icons[] = $this->render_edit_action($publication);
			$icons[] = $this->render_visibility_action($publication);
			$icons[] = $this->render_up_action($publication,$first);
			$icons[] = $this->render_down_action($publication,$last);
		}
		$icons[] = $this->render_view_action($publication);
		$html[] = implode('&nbsp;', $icons);
		$html[] = '</span>';
		return implode($html);
	}
	
	function render_view_action($publication)
	{
		$img = '<img src="'.Theme :: get_common_image_path().'action_browser.png" alt=""/>';
		$parameters[Tool :: PARAM_ACTION] = GlossaryTool :: ACTION_VIEW_GLOSSARY;
    	$parameters[Tool :: PARAM_PUBLICATION_ID] =  $publication->get_learning_object()->get_id();
    	$html = '<a href="'.$this->get_url($parameters).'">' . $img . '</a>';
    	return $html;
	}
}
?>