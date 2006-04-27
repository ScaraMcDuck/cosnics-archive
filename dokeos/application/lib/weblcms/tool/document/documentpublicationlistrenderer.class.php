<?php
/**
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
    	$this->set_header(0, get_lang('Title'), false);
		$this->set_header(1, get_lang('Description'), false);
		$this->set_header(2, get_lang('PublishedOn'), false);
		$this->set_header(3, get_lang('PublishedBy'), false);
		$this->set_header(4, get_lang('PublishedFor'), false);
		$this->set_header(5, get_lang('Actions'), false);
    }
}
?>