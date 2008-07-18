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
require_once ('HTML/QuickForm/textarea.php');
require_once (Path :: get_plugin_path().'fckeditor/fckeditor.php');
require_once (Path :: get_library_path().'resource_manager.class.php');
require_once (Path :: get_admin_path().'lib/admin_data_manager.class.php');
/**
* A html editor field to use with QuickForm
*/
class HTML_QuickForm_html_editor extends HTML_QuickForm_textarea
{
	/**
	 * Full page
	 */
	var $fullPage;
	/**
	 * Class constructor
	 * @param   string  HTML editor name/id
	 * @param   string  HTML editor  label
	 * @param   string  Attributes for the textarea
	 */
	function HTML_QuickForm_html_editor($elementName = null, $elementLabel = null, $attributes = null)
	{
		HTML_QuickForm_element :: HTML_QuickForm_element($elementName, $elementLabel, $attributes);
		$this->_persistantFreeze = true;
		$this->_type = 'html_editor';
		$this->fullPage = false;
	}
	/**
	 * Check if the browser supports FCKeditor
	 *
	 * @access public
	 * @return boolean
	 */
	function browserSupported()
	{
		return FCKeditor :: IsCompatible();
	}
	/**
	 * Return the HTML editor in HTML
	 * @return string
	 */
	function toHtml()
	{
		$value = $this->getValue();
		if ($this->fullPage)
		{
			if (strlen(trim($value)) == 0)
			{
				$value = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
							<head>
							<title></title>
							<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
							<style type="text/css" media="screen, projection">/*<![CDATA[*/body{font-family: arial, verdana, helvetica, sans-serif;font-size: 12px;}/*]]>*/</style>
							</head>
							<body>
							</body>
							</html>';
				$this->setValue($value);
			}
		}
		if ($this->_flagFrozen)
		{
			return $this->getFrozenHtml();
		}
		else
		{
			return $this->build_FCKeditor();
		}
	}
	/**
	 * Returns the frozen content in HTML
	 *@return string
	 */
	function getFrozenHtml()
	{
		$val = $this->getValue();
		return $val
			. '<input type="hidden" name="' . htmlspecialchars($this->getName()) . '"'
			. ' value="' . htmlspecialchars($val) . '"/>';
	}
	/**
	 * Build this element using FCKeditor
	 */
	function build_FCKeditor()
	{
		global $language_interface;
		if(! FCKeditor :: IsCompatible())
		{
			return parent::toHTML();
		}
	
		$adm = AdminDataManager :: get_instance();
		$editor_lang = $adm->retrieve_language_from_english_name($language_interface)->get_isocode();
		$language_file = Path :: get_plugin_path().'fckeditor/editor/lang/'.$editor_lang.'.js';
		if (empty ($editor_lang) || !file_exists($language_file))
		{
			//if there was no valid iso-code, use the english one
			$editor_lang = 'en';
		}
		$name = $this->getAttribute('name');
		$result []= ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH).'fckeditor/fckeditor.js');
		$result []= '<script type="text/javascript">';
		$result []= "\n/* <![CDATA[ */\n";
		$result []= 'var oFCKeditor = new FCKeditor( \''.$name.'\' ) ;';
		$result []= 'oFCKeditor.BasePath = "'.Path :: get(WEB_PATH).'plugin/fckeditor/";';
		$result []= 'oFCKeditor.Width = 650;';
		$result []= 'oFCKeditor.Height = '. ($this->fullPage ? '500' : '150').';';
		$result []= 'oFCKeditor.Config[ "FullPage" ] = '. ($this->fullPage ? 'true' : 'false').';';
		$result []= 'oFCKeditor.Config[ "DefaultLanguage" ] = "'.$editor_lang.'" ;';
		$result []= 'oFCKeditor.Value = "'.str_replace('"', '\"', str_replace(array ("\r\n", "\n", "\r", "/"), array (' ', ' ', ' ', '\/'), $this->getValue())).'" ;';
		$result []= 'oFCKeditor.ToolbarSet = \''. ($this->fullPage ? 'FullHTML' : 'Basic' ).'\';';
		$result []= 'oFCKeditor.Config[ "SkinPath" ] = oFCKeditor.BasePath + "editor/skins/'. Theme :: get_theme() .'/";';
		//$result []= 'alert(oFCKeditor.BasePath + \'editor/skins/'. Theme :: get_theme() .'/\');';
		$result []= 'oFCKeditor.Create();';
		$result []= "\n/* ]]> */\n";
		$result []= '</script>';
		$result []= '<noscript>'.parent :: toHTML().'</noscript>';
		$result []= '<small><a href="#" onclick="MyWindow=window.open('."'".Path :: get(WEB_LIB_PATH)."html/allowed_html_tags.php?fullpage=". ($this->fullPage ? '1' : '0')."','MyWindow','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=500,height=600,left=200,top=20'".'); return false;">'.Translation :: get('AllowedHTMLTags').'</a></small>';
		@mkdir(Path :: get(SYS_PATH).'files/fckeditor/'. Session :: get_user_id().'/');
		return implode("\n",$result);
	}
}
?>