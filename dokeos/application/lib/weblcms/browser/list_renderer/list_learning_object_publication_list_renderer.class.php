<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learning_object_publication_list_renderer.class.php';
/**
 * Renderer to display a list of learning object publications
 */
class ListLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
{
	/**
	 * Returns the HTML output of this renderer.
	 * @return string The HTML output
	 */
	function as_html()
	{
		$publications = $this->get_publications();
		if(count($publications) == 0)
		{
			$html[] = Display::display_normal_message(Translation :: get('NoPublicationsAvailable'),true);
		}
		if($this->get_actions())
			$html[] = '<form name="publication_list" action="' . $this->get_url() . '" method="GET" >';
			
		foreach ($publications as $index => $publication)
		{
			$first = ($index == 0);
			$last = ($index == count($publications) - 1);
			$html[] = $this->render_publication($publication, $first, $last);
		}
		
		if($this->get_actions() && count($publications) > 0)
		{
			foreach($_GET as $parameter => $value)
			{
				$html[] = '<input type="hidden" name="' . $parameter . '" value="' . $value . '" />';
			}
			$html[] = '<script type="text/javascript">
							/* <![CDATA[ */
							function setCheckbox(formName, value) {
								var d = document[formName];
								for (i = 0; i < d.elements.length; i++) {
									if (d.elements[i].type == "checkbox") {
									     d.elements[i].checked = value;
									}
								}
							}
							/* ]]> */
							</script>';
			
			$html[] = '<div style="text-align: right;">';
			$html[] = '<a href="?" onclick="setCheckbox(\'publication_list\', true); return false;">'.Translation :: get('SelectAll').'</a>';
			$html[] = '- <a href="?" onclick="setCheckbox(\'publication_list\', false); return false;">'.Translation :: get('UnSelectAll').'</a><br />';
			$html[] = '<select name="tool_action">';
			foreach ($this->get_actions() as $action => $label)
			{
				$html[] = '<option value="'.$action.'">'.$label.'</option>';
			}
			$html[] = '</select>';
			$html[] = ' <input type="submit" value="'.Translation :: get('Ok').'"/>';
			$html[] = '</form>';
			$html[] = '</div>';
		}
		
		return implode("\n", $html);
	}

	/**
	 * Renders a single publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @param boolean $first True if the publication is the first in the list
	 *                       it is a part of.
	 * @param boolean $last True if the publication is the last in the list
	 *                      it is a part of.
	 * @return string The rendered HTML.
	 */
	function render_publication($publication, $first = false, $last = false)
	{
		// TODO: split into separate overrideable methods
		$html = array ();
		$last_visit_date = $this->browser->get_last_visit_date();
		$icon_suffix = '';
		if($publication->is_hidden())
		{
			$icon_suffix = '_na';
		}
		elseif( $publication->get_publication_date() >= $last_visit_date)
		{
			$icon_suffix = '_new';
		}
		
		$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$publication->get_learning_object()->get_icon_name().$icon_suffix.'.png);">';
		$html[] = '<div class="title'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_title($publication);
		$html[] = '</div>';
		$html[] = '<div class="description'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_description($publication);
		$html[] = $this->render_attachments($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_info'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_publication_information($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_actions">';
		$html[] = $this->render_publication_actions($publication,$first,$last);
		if($this->get_actions())
			$html[] = '<input type="checkbox" name="pid[]" value="' . $publication->get_id() . '"/>';
		$html[] = '</div>';
		$html[] = '</div><br />';
		return implode("\n", $html);
	}
}
?>