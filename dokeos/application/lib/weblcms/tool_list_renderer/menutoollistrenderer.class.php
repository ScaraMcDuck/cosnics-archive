<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once(dirname(__FILE__).'/../toollistrenderer.class.php');
require_once('HTML/Table.php');
/**
 * Tool list renderer to display a navigation menu.
 */
class MenuToolListRenderer extends ToolListRenderer
{
	/**
	 *
	 */
	const MENU_TYPE_TOP_NAVIGATION = 1;
	const MENU_TYPE_LIST_NAVIGATION = 2;
	/**
	 *
	 */
	private $type;
	/**
	 * Constructor
	 * @param  WebLcms $parent The parent application
	 */
	function MenuToolListRenderer($parent, $type = MENU_TYPE_LIST_NAVIGATION)
	{
		parent::ToolListRenderer($parent);
		$this->type = $type;
	}
	/**
	 * Sets the type of this navigation menu renderer
	 * @param int $type
	 */
	function set_type($type)
	{
		$this->type = $type;
	}
	// Inherited
	function display()
	{
		$parent = $this->get_parent();
		$tools = $parent->get_registered_tools();
		$this->show_tools($tools);
	}
	/**
	 * Show the tools of a given section
	 * @param array $tools
	 */
	private function show_tools($tools)
	{
		$parent = $this->get_parent();
		if( $this->type == MENU_TYPE_LIST_NAVIGATION)
		{
			$html[] = '<ul>';
		}
		foreach ($tools as $index => $tool)
		{
			if($tool->visible )
			{
				$new = '';
				if($parent->tool_has_new_publications($tool->name))
				{
					$new = '_new';
				}
				$tool_image = $tool->name.'_tool'.$new.'.gif';
				$title = htmlspecialchars(get_lang(Tool :: type_to_class($tool->name).'Title'));
				if( $this->type == MENU_TYPE_LIST_NAVIGATION)
				{
					$html[] = '<li>';
				}
				$html[] = '<a href="'.$parent->get_url(array (WebLcms :: PARAM_ACTION=>null,WebLcms :: PARAM_TOOL => $tool->name), true).'" title="'.$title.'">';
				$html[] = '<img src="'.$parent->get_path(WEB_IMG_PATH).$tool_image.'" style="vertical-align: middle;" alt="'.$title.'"/> ';
				$html[] = '</a>';
				if( $this->type == MENU_TYPE_LIST_NAVIGATION)
				{
					$html[] = '<a href="'.$parent->get_url(array (WebLcms :: PARAM_ACTION=>null,WebLcms :: PARAM_TOOL => $tool->name), true).'" title="'.$title.'">';
					$html[] = $title;
					$html[] = '</a>';
					$html[] = '</li>';
				}
			}
		}
		if( $this->type == MENU_TYPE_LIST_NAVIGATION)
		{
			$html[] = '</ul>';
		}
		echo implode("\n",$html);
	}
}
?>