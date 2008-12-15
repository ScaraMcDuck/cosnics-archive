<?php
/**
 * $Id: tree_menu_renderer.class.php 15818 2008-07-18 09:38:19Z Scara84 $
 * @package repository
 */
require_once 'HTML/Menu/DirectTreeRenderer.php';
require_once Path :: get_library_path().'resource_manager.class.php';

/**
 * Renderer which can be used to include a tree menu on your page.
 * @author Bart Mollet
 * @author Tim De Pauw
 */
class DragAndDropTreeMenuRenderer extends HTML_Menu_DirectTreeRenderer
{
	/**
	 * Boolean to check if this tree menu is allready initialized
	 */
	private static $initialized;
	
	private $id;
	private $current_id;
	/**
	 * Constructor.
	 */
	function DragAndDropTreeMenuRenderer($id, $current_id)
	{
		$this->id = $id;
		$this->current_id = $current_id;
		$entryTemplates = array (HTML_MENU_ENTRY_INACTIVE => '<span id={id} class="textHolder"><a href="{url}" style="{style}" onclick="{onclick}" id="{id}" class="{class}">{title}</a></span>', HTML_MENU_ENTRY_ACTIVE => '<!--A--><span id={id} class="textHolder"><a href="{url}" style="{style}" onclick="{onclick}" id="{id}" class="{class}">{title}</a></span>', HTML_MENU_ENTRY_ACTIVEPATH => '<!--P--><span id={id} class="textHolder"><a href="{url}" style="{style}" onclick="{onclick}" id="{id}" class="{class}">{title}</a></span>');
		$this->setEntryTemplate($entryTemplates);
		$this->setItemTemplate('<li class="treeItem"><img src="' . Theme :: get_common_image_path() . 'treemenu/tree-folder-open.png" class="folderImage" />', '</li>'."\n");
	}
	/**
	 * Finishes rendering a level in the tree menu
	 * @see HTML_Menu_DirectTreeRenderer::finishLevel
	 */
	function finishLevel($level)
	{
		$root = ($level == 0);
		if ($root)
		{
			$id = $_GET['application'];
			if(!isset($id))
			{
				$id = $_SERVER['PHP_SELF'];
				$id = substr($id, strpos($id, 'index_') + 6);
				$id = substr($id, 0, strlen($id) - 4);
			}
			
			$id .= '_' . $this->id;
			$gc = '<li id="deletediv" style="display:none;"><img src="' .Theme :: get_common_image_path() . 'action_recycle_bin.png" /> <span id="deleter" style="font-weight: bold; color: #4171B5;">' . Translation :: get('Delete') . '</span></li>';
			$this->setLevelTemplate('<ul id="' . $id . '" class="myTree">'."\n", $gc . '</ul><br />' . "\n");
		}
		
		if (!$root)
		{
			$this->setLevelTemplate('<ul>'."\n", '</ul>'."\n");
		} 
		
		parent :: finishLevel($level);
	}
	/**
	 * Renders an entry in the tree menu
	 * @see HTML_Menu_DirectTreeRenderer::renderEntry
	 */
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
		
		if($node['id'] == $this->current_id)
		{
			$node['style'] = 'text-decoration: underline;';
		}
		
		parent :: renderEntry($node, $level, $type);
	}
	/**
	 * Gets a HTML representation of the tree menu
	 * @return string
	 */
	function toHtml()
	{
		$html[] = parent :: toHtml();
		
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH).'javascript/new_treemenu.js');
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH).'jquery/interface/interface.js');
		return implode("\n", $html);
	}
}
?>