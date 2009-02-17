<?php
require_once Path :: get_library_path() . 'html/formvalidator/Element/html_editor.php';
require_once Path :: get_plugin_path() . 'html_editor/fckeditor/fckeditor.php';

class HTML_QuickForm_fckeditor_html_editor extends HTML_QuickForm_html_editor
{
	function set_type()
	{
		$this->_type = 'fckeditor_html_editor';
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
	 * Build this element using FCKeditor
	 */
	function build_editor()
	{
		global $language_interface;
		if(!$this->browserSupported())
		{
			return $this->render_textarea();
		}
	
		$adm = AdminDataManager :: get_instance();
		$editor_lang = $adm->retrieve_language_from_english_name($language_interface)->get_isocode();
		$language_file = Path :: get_plugin_path().'html_editor/fckeditor/editor/lang/'.$editor_lang.'.js';
		if (empty ($editor_lang) || !file_exists($language_file))
		{
			//if there was no valid iso-code, use the english one
			$editor_lang = 'en';
		}
		$name = $this->getAttribute('name');
		$result []= ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH).'html_editor/fckeditor/fckeditor.js');
		$result []= '<script type="text/javascript">';
		$result []= "\n/* <![CDATA[ */\n";
		$result []= 'var oFCKeditor = new FCKeditor( \''.$name.'\' ) ;';
		$result []= 'oFCKeditor.BasePath = "'.Path :: get(WEB_PLUGIN_PATH).'html_editor/fckeditor/";';
		$result []= 'oFCKeditor.Width = 650;';
		$result []= 'oFCKeditor.Height = '. ($this->fullPage ? '500' : '150').';';
		$result []= 'oFCKeditor.Config[ "FullPage" ] = '. ($this->fullPage ? 'true' : 'false').';';
		$result []= 'oFCKeditor.Config[ "DefaultLanguage" ] = "'.$editor_lang.'" ;';
		$result []= 'oFCKeditor.Value = "'.str_replace('"', '\"', str_replace(array ("\r\n", "\n", "\r", "/"), array (' ', ' ', ' ', '\/'), $this->getValue())).'" ;';
		$result []= 'oFCKeditor.ToolbarSet = \''. ($this->fullPage ? 'FullHTML' : 'Basic' ).'\';';
		$result []= 'oFCKeditor.Config[ "SkinPath" ] = oFCKeditor.BasePath + "editor/skins/'. Theme :: get_theme() .'/";';
		$result []= 'oFCKeditor.Config["CustomConfigurationsPath"] = "'. Path :: get(WEB_LIB_PATH) .'configuration/html_editor/fckconfig.js";';
		$result []= 'oFCKeditor.Create();';
		$result []= "\n/* ]]> */\n";
		$result []= '</script>';
		$result []= '<noscript>'. $this->render_textarea().'</noscript>';
		$result []= '<br/><small><a href="#" onclick="MyWindow=window.open('."'".Path :: get(WEB_LIB_PATH)."html/allowed_html_tags.php?fullpage=". ($this->fullPage ? '1' : '0')."','MyWindow','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=500,height=600,left=200,top=20'".'); return false;">'.Translation :: get('AllowedHTMLTags').'</a></small>';
		@mkdir(Path :: get(SYS_PATH).'files/fckeditor/'. Session :: get_user_id().'/');
		return implode("\n",$result);
	}
	
	function exportValue()
	{
		$value = parent::getValue();
		/*$test = fopen(dirname(__FILE__) . '/test.txt', 'w+');
		fwrite($test, $value);
		
		$path = Path :: get(REL_REPO_PATH) . Session :: get_user_id() . '/';
		fwrite($test, $path);
		
		fclose($test);*/
		
		
		
		$values[$this->getName()] = $value;
		return $values;
	}
}
?>
