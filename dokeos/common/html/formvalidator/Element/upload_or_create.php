<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) Bart Mollet, Hogeschool Gent

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
require_once 'HTML/QuickForm/group.php';
require_once 'HTML/QuickForm/radio.php';
require_once 'HTML/QuickForm/file.php';
/**
 * Form element to upload or create a document
 * This element contains 2 radio-
 * buttons. One with label 'upload document' and one with label 'create
 * document'. Only if the second radio-button is selected, a HTML-editor appears
 * to allow the user to create a HTML document
 */
class HTML_QuickForm_upload_or_create extends HTML_QuickForm_group
{
	/**
	 * Constructor
	 * @param string $elementName
	 * @param string $elementLabel
	 * @param array $attributes This should contain the keys 'receivers' and
	 * 'receivers_selected'
	 */
	function HTML_QuickForm_upload_or_create($elementName = null, $elementLabel = null, $attributes = null)
	{
		$this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
		$this->_persistantFreeze = true;
		$this->_appendName = false;
		$this->_type = 'upload_or_create';
	}
	/**
	 * Create the form elements to build this element group
	 */
	function _createElements()
	{
		$this->_elements[0] = new HTML_QuickForm_Radio('choice', '', get_lang('Upload'), '0', array ('onclick' => 'javascript:editor_hide(\'editor_html_content\')'));
		$this->_elements[0]->setChecked(true);
		$this->_elements[1] = new HTML_QuickForm_file('file','');
		$this->_elements[2] = new HTML_QuickForm_Radio('choice', '', get_lang('Create'), '1', array ('onclick' => 'javascript:editor_show(\'editor_html_content\')'));
		$this->_elements[3] = new HTML_QuickForm_html_editor('html_content','');
		$this->_elements[3]->fullPage = true;
	}
	/**
	 * HTML representation
	 */
	function toHtml()
	{
		$html[] = $this->_elements[0]->toHtml();
		$html[] = $this->_elements[1]->toHtml();
		$html[] = '<br />';
		$html[] = $this->_elements[2]->toHtml();
		$html[] = '<div style="margin-left:20px;display:block;" id="editor_html_content">';
		$html[] = $this->_elements[3]->toHtml();
		$html[] = '</div>';
		$html[] = $this->getElementJS();
		return implode("\n",$html);
	}
	/**
	 * Get the necessary javascript
	 */
	function getElementJS()
	{
		$js = "<script language=\"JavaScript\" type=\"text/javascript\">
					editor_hide('editor_html_content');
					function editor_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function editor_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					</script>
				";
		return $js;
	}
	/**
	 * accept renderer
	 */
	function accept($renderer, $required = false, $error = null)
	{
		$renderer->renderElement($this, $required, $error);
	}
}
?>