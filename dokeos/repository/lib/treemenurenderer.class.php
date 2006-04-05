<?php
require_once 'HTML/Menu/DirectTreeRenderer.php';

/**
 * Renderer which can be used to include a tree menu on your page.
 */
class TreeMenuRenderer extends HTML_Menu_DirectTreeRenderer
{
	private static $initialized;
	/**
	 * Constructor.
	 */
	public function TreeMenuRenderer()
	{
		$entryTemplates = array (HTML_MENU_ENTRY_INACTIVE => '<a href="{url}" class="{class}">{title}</a>', HTML_MENU_ENTRY_ACTIVE => '<!--A--><a href="{url}" class="{class}">{title}</a>', HTML_MENU_ENTRY_ACTIVEPATH => '<!--P--><a href="{url}" class="{class}">{title}</a>');
		$this->setEntryTemplate($entryTemplates);
		$this->setItemTemplate('<li>', '</li>'."\n");
	}
	/**
	 * @see HTML_Menu_DirectTreeRenderer::finishLevel()
	 */
	function finishLevel($level)
	{
		$root = ($level == 0);
		if ($root)
		{
			$this->setLevelTemplate('<ul class="treeMenu">'."\n", '</ul>'."\n");
		}
		parent :: finishLevel($level);
		if ($root)
		{
			$this->setLevelTemplate('<ul>'."\n", '</ul>'."\n");
		}
	}
	/**
	 * @see HTML_Menu_DirectTreeRenderer::renderEntry()
	 */
	function renderEntry($node, $level, $type)
	{
		/*
		 * Make sure there's a 'class' key, so {class} is always replaced in
		 * the entry template.
		 */
		if (!array_key_exists('class', $node))
		{
			$node['class'] = '';
		}
		parent :: renderEntry($node, $level, $type);
	}
	/**
	 * @see HTML_Menu_DirectTreeRenderer::toHtml()
	 */
	function toHtml()
	{
		$html = parent :: toHtml();
		$class = array ('A' => 'active', 'P' => 'active-path');
		$html = preg_replace('/<li><!--([AP])-->/e', '\'<li class="\'.$class[\1].\'">\'', $html);
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