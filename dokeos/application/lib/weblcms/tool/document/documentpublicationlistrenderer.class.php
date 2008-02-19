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
    	$html[] = '<a href="'.htmlentities($publication->get_learning_object()->get_url()).'" style="float:left;margin-right: 20px;">'.parent::render_title($publication).'</a>';
    	$download_parameters[RepositoryTool :: PARAM_ACTION] = DocumentTool :: ACTION_DOWNLOAD;
    	$download_parameters[RepositoryTool :: PARAM_PUBLICATION_ID] =  $publication->get_id();
    	$html[] = '<a href="'.$this->get_url($download_parameters).'"><img src="'.api_get_path(WEB_IMG_PATH).'save.png" alt="" style="float: right;"/></a>';
    	return implode("\n",$html);
    }
}
?>