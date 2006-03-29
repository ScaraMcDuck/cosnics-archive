<?php
require_once 'HTML/Menu/DirectTreeRenderer.php';

/**
 * Renderer which can be used to include a tree menu in your page.
 */
class TreeMenuRenderer extends HTML_Menu_DirectTreeRenderer
{
	private static $initialized;
	/**
	 * Constructor
	 */
	public function TreeMenuRenderer()
	{
		$entryTemplates = array (HTML_MENU_ENTRY_INACTIVE => '<a href="{url}">{title}</a>', HTML_MENU_ENTRY_ACTIVE => '<!--ACTIVE--><a href="{url}">{title}</a>', HTML_MENU_ENTRY_ACTIVEPATH => '<a href="{url}">{title}</a>');
		parent :: setEntryTemplate($entryTemplates);
		parent :: setItemTemplate('<li>', '</li>');
	}
	/**
	 * @see HTML_Menu_DirectTreeRenderer::finishLevel()
	 */
	function finishLevel($level)
	{
		if ($level == 0)
		{
			parent :: setLevelTemplate('<ul class="treeMenu">', '</ul>');
		}
		parent :: finishLevel($level);
		if ($level == 0)
		{
			parent :: setLevelTemplate('<ul>', '</ul>');
		}
	}
	/**
	 * @see HTML_Menu_DirectTreeRenderer::toHtml()
	 */
	function toHtml()
	{
		$html = parent :: toHtml();
		$html = str_replace('<li><!--ACTIVE-->', '<li>', $html);
		if (self :: $initialized)
		{
			return $html;
		}
		self :: $initialized = true;
		$html = '<script language="JavaScript" type="text/javascript" src="'.api_get_path(WEB_CODE_PATH).'javascript/treemenu.js"></script>'.$html;
		return $html;
	}
}
?>