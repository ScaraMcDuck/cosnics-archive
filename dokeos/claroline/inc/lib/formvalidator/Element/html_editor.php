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
require_once (api_get_path(SYS_CODE_PATH).'../plugin/fckeditor/fckeditor.php');
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
				$value = '<html><head><title></title><style type="text/css" media="screen, projection">/*<![CDATA[*/body{font-family: arial, verdana, helvetica, sans-serif;font-size: 12px;}/*]]>*/</style></head><body></body></html>';
				$this->setValue($value);
			}
		}
		if ($this->_flagFrozen)
		{
			return $this->getFrozenHtml();
		}
		else
		{
			if(api_get_setting('server_type') == 'production')
			{
				return $this->build_HTMLArea();
			}
			else
			{
				return $this->build_FCKeditor();	
			}
		}
	}
	/**
	 * Returns the htmlarea content in HTML
	 *@return string
	 */
	function getFrozenHtml()
	{
		return $this->getValue();
	}
	/**
	 * Build this element using HTMLArea
	 */
	function build_HTMLArea()
	{
		global $_course;
		$navigator = api_get_navigator();
		if (($navigator['name'] == 'Internet Explorer' && $navigator['version'] >= 6.0) || ($navigator['name'] == 'Mozilla' && $navigator['version'] >= 1.4))
		{
			$display_htmlarea = true;
		}
		else
		{
			$display_htmlarea = false;
		}
		if ($display_htmlarea)
		{
			$htmlarea_path = api_get_code_web_path()."plugin/htmlarea/";
			// We set the language and use the platform language if there is no course language (for using htmlarea in the admin section)
			if (!$_course)
			{
				$user_selected_language = $_SESSION["user_language_choice"] ? $_SESSION["user_language_choice"] : get_setting('platformLanguage');
				$lang = $user_selected_language;
			}
			else
			{
				$lang = $_course['language'];
			}
		}
		$name = $this->getAttribute('name');
		$this->_attributes['id'] = $name;
		$value = $this->getValue();
		$result[] = parent :: toHTML();
		if ($display_htmlarea)
		{
			$result[] = '<script type="text/javascript">';
			$result[] = '/* <![CDATA[ */';
			$result[] = "_editor_url='$htmlarea_path';";
			$result[] = "_image_url='".api_get_code_web_path()."img/htmlarea/';";
			$result[] = "_css_url='".api_get_code_web_path()."css/';";
			$result[] = "if(typeof _document_path != 'string')";
			$result[] = "{";
			$result[] = "_document_path='". (empty ($_course['path']) ? api_get_path(SYS_CODE_PATH).'upload/' : api_get_path(SYS_COURSE_PATH).$_course['path'].'/document/')."';";
			$result[] = "}";
			$result[] = "_document_url=_document_path.replace(/".str_replace('/', '\/', api_get_path(SYS_PATH))."/,'".addslashes(api_get_path(WEB_PATH))."');";
			$result[] = '/* ]]> */';
			$result[] = '</script>';
			$result[] = '<script type="text/javascript" src="'.$htmlarea_path.'htmlarea.js"></script>';
			$result[] = '<script type="text/javascript" src="'.api_get_code_web_path().'lang/'.$lang.'/htmlarea.js.php"></script>';
			$result[] = '<script type="text/javascript" src="'.$htmlarea_path.'dialog.js"></script>';
			if ($this->fullPage)
			{
				$result[] = '<script type="text/javascript" src="'.$htmlarea_path.'plugins/FullPage/full-page.js"></script>';
				$result[] = '<script type="text/javascript" src="'.$htmlarea_path.'plugins/FullPage/lang/en.js"></script>';
			}
			$result[] = '<script type="text/javascript">';
			$result[] = '/* <![CDATA[ */';
			$result[] = 'var	editor=null';
			$result[] = 'function initEditor()';
			$result[] = '{';
			$result[] = '	var config = new HTMLArea.Config();';
			$result[] = '	config.hideSomeButtons(" showhelp undo redo popupeditor ");';
			$result[] = '	editor=new HTMLArea("'.$name.'",config);';
			$result[] = '	config.width=650;';
			$result[] = '	config.height=350;';
			if ($this->fullPage)
			{
				$result[] = 'editor.registerPlugin(FullPage);';
			}
			$result[] = '	editor.generate();';
			$result[] = '	return false;';
			$result[] = '}';
			$result[] = 'initEditor();';
			$result[] = '/* ]]> */';
			$result[] = '</script>';
		}
		return implode("\n", $result);
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
		@ $editor_lang = Database :: get_language_isocode($language_interface);
		$language_file = api_get_path(SYS_PATH).'plugin/fckeditor/editor/lang/'.$editor_lang.'.js';
		if (empty ($editor_lang) || !file_exists($language_file))
		{
			//if there was no valid iso-code, use the english one
			$editor_lang = 'en';
		}
		$name = $this->getAttribute('name');
		$result .= '<script type="text/javascript" src="'.api_get_path(WEB_PATH).'plugin/fckeditor/fckeditor.js"></script>';
		$result .= '<script type="text/javascript">';
		$result .= "\n/* <![CDATA[ */\n";
		$result .= 'var oFCKeditor = new FCKeditor( \''.$name.'\' ) ;';
		$result .= 'oFCKeditor.BasePath = "'.api_get_path(WEB_PATH).'plugin/fckeditor/";';
		$result .= 'oFCKeditor.Width = 650;';
		$result .= 'oFCKeditor.Height = '. ($this->fullPage ? '500' : '300').';';
		$result .= 'oFCKeditor.Config[ "FullPage" ] = '. ($this->fullPage ? 'true' : 'false').';';
		$result .= 'oFCKeditor.Config[ "DefaultLanguage" ] = "'.$editor_lang.'" ;';
		$result .= 'oFCKeditor.Value = "'.str_replace('"', '\"', str_replace(array ("\r\n", "\n", "\r", "/"), array (' ', ' ', ' ', '\/'), $this->getValue())).'" ;';
		$result .= 'oFCKeditor.Create();';
		$result .= "\n/* ]]> */\n";
		$result .= '</script>';
		$result .= '<noscript>'.parent :: toHTML().'</noscript>';
		$result .= '<small><a href="#" onclick="MyWindow=window.open('."'".api_get_path(WEB_CODE_PATH)."help/allowed_html_tags.php?fullpage=". ($this->fullPage ? '1' : '0')."','MyWindow','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=500,height=600,left=200,top=20'".'); return false;">'.get_lang('AllowedHTMLTags').'</a></small>';
		return $result;
	}
}
?>