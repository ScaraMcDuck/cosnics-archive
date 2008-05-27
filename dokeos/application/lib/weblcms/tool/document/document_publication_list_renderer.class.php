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
    	$this->set_header($column++, Translation :: get('Type'), true);
    	$this->set_header($column++, Translation :: get('Title'), true);
		$this->set_header($column++, Translation :: get('Description'), true);
		$this->set_header($column++, Translation :: get('PublishedOn'), true);
		$this->set_header($column++, Translation :: get('PublishedBy'), true);
		$this->set_header($column++, Translation :: get('PublishedFor'), true);
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++, '', false);
    	}
    }

    function render_title($publication) {
    	$html[] = '<a href="'.htmlentities($publication->get_learning_object()->get_url()).'" style="float:left;margin-right: 20px;">'.parent::render_title($publication).'</a>';
    	$download_parameters[RepositoryTool :: PARAM_ACTION] = DocumentTool :: ACTION_DOWNLOAD;
    	$download_parameters[RepositoryTool :: PARAM_PUBLICATION_ID] =  $publication->get_id();
    	$html[] = '<a href="'.$this->get_url($download_parameters).'"><img src="'.Theme :: get_common_img_path().'action_save.png" alt="" style="float: right;"/></a>';
    	return implode("\n",$html);
    }
}
?>