<?php
require_once dirname(__FILE__).'/../../browser/list_renderer/tablelearningobjectpublicationlistrenderer.class.php';
class LinkPublicationListRenderer extends TableLearningObjectPublicationListRenderer
{
	function LinkPublicationListRenderer ($browser)
	{
		parent :: __construct($browser);
		$this->set_header(0, get_lang('Title'), false);
		$this->set_header(1, get_lang('Description'), false);
	}
}
?>