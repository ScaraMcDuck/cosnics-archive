<?php
require_once 'HTML/Menu/DirectTreeRenderer.php';

/**
 * Renderer which can be used to include a tree menu on your page.
 * @package repository
 * @author Bart Mollet
 * @author Tim De Pauw
 */
class TreeMenuRenderer extends HTML_Menu_DirectTreeRenderer
{
	private static $initialized;
	function TreeMenuRenderer()
	{
		$entryTemplates = array (HTML_MENU_ENTRY_INACTIVE => '<a href="{url}" onclick="{onclick}" id="{id}" class="{class}">{title}</a>', HTML_MENU_ENTRY_ACTIVE => '<!--A--><a href="{url}" onclick="{onclick}" id="{id}" class="{class}">{title}</a>', HTML_MENU_ENTRY_ACTIVEPATH => '<!--P--><a href="{url}" onclick="{onclick}" id="{id}" class="{class}">{title}</a>');
		$this->setEntryTemplate($entryTemplates);
		$this->setItemTemplate('<li>', '</li>'."\n");
	}
	function finishLevel($level)
	{
		$root = ($level == 0);
		if ($root)
		{
			$this->setLevelTemplate('<ul class="tree-menu">'."\n", '</ul>'."\n");
		}
		parent :: finishLevel($level);
		if ($root)
		{
			$this->setLevelTemplate('<ul>'."\n", '</ul>'."\n");
		}
	}
	function renderEntry($node, $level, $type)
	{
		// Add some extra keys, so they always get replaced in the template.
		foreach (array('class','onclick','id') as $key)
		{
			if (!array_key_exists($key, $node))
			{
				$node[$key] = '';
			}
		}
		parent :: renderEntry($node, $level, $type);
	}
	function toHtml()
	{
		$html = parent :: toHtml();
		$class = array ('A' => 'current', 'P' => 'current_path');
		$html = preg_replace('/(?<=<li)><!--([AP])-->/e', '\' class="\'.$class[\1].\'">\'', $html);
		$html = preg_replace('/\s*\b(onclick|id)="\s*"\s*/', ' ', $html); 
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