<?php
/**
 * Description tool - list renderer
 * @package application.weblcms.tool
 * @subpackage description
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/listlearningobjectpublicationlistrenderer.class.php';

class DescriptionPublicationListRenderer extends ListLearningObjectPublicationListRenderer
{
	/**
	 * No categories available in the description tool at this moment, so the
	 * option to move descriptions between categories is not available.
	 * @return empty string
	 */
	function render_move_to_category_action($publication)
	{
		return '';
	}
}
?>