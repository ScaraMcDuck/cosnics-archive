<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../webapplication.class.php';
require_once dirname(__FILE__).'/weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/learningobjectpublicationcategory.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../../claroline/inc/lib/groupmanager.lib.php';
require_once dirname(__FILE__).'/tool/tool.class.php';

/**
==============================================================================
 *	This is an application that creates a fully fledged web-based learning
 *	content management system. The Web-LCMS is based on so-called "tools",
 *	which each represent a segment in the application.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class Weblcms extends WebApplication
{
	const PARAM_TOOL = 'tool';

	/**
	 * The tools that this application offers.
	 */
	private $tools;
	/**
	 * The class of the tool currently active in this application
	 */
	private $tool_class;

	/**
	 * Constructor. Optionally takes a default tool; otherwise, it is taken
	 * from the query string.
	 * @param Tool $tool The default tool, or null if none.
	 */
	function Weblcms($tool = null)
	{
		parent :: __construct();
		$this->set_parameter(self :: PARAM_TOOL, $_GET[self :: PARAM_TOOL]);
		$this->tools = array ();
		$this->load_tools();
	}

	/*
	 * Inherited.
	 */
	function run()
	{
		$tool = $this->get_parameter(self :: PARAM_TOOL);
		if (isset ($tool))
		{
			$class = Tool :: type_to_class($tool);
			$toolObj = new $class ($this);
			$this->tool_class = $class;
			$toolObj->run();
		}
		else
		{
			$this->display_header();
			echo '<ul>';
			foreach ($this->get_registered_tools() as $tool)
			{
				$class = Tool :: type_to_class($tool);
				echo '<li><a href="'.$this->get_url(array (self :: PARAM_TOOL => $tool), true).'">'.htmlentities(get_lang($class.'Title')).'</a></li>';
			}
			echo '</ul>';
			$this->display_footer();
		}
	}
	/**
	 * Gets the identifier of the current tool
	 * @return string The identifier of current tool
	 */
	function get_tool_id()
	{
		return $this->get_parameter(self :: PARAM_TOOL);
	}

	/**
	 * Returns the numeric identifier of the active user.
	 * @return string The user identifier.
	 */
	function get_user_id()
	{
		return api_get_user_id();
	}

	/**
	 * Returns the identifier of the course that is being used.
	 * @return string The course identifier.
	 */
	function get_course_id()
	{
		return api_get_course_id();
	}
	/**
	 * Gets a list of all groups of the current active course in which the
	 * current user is subscribed.
	 */
	function get_groups()
	{
		return GroupManager :: get_group_ids($this->get_course_id(), $this->get_user_id());
	}
	/**
	 * Gets the defined categories in the current tool.
	 * @param boolean $list When true the categories will be returned as a list.
	 * If false (default value) a tree structure of the categories will be
	 * returned
	 * @return array The categories
	 */
	function get_categories($list = false)
	{
		return ($list ? $this->get_category_list() : $this->get_category_tree());
	}
	/**
	 * Gets the defined categories in the current tool structured as a tree.
	 * @return array
	 */
	private function get_category_tree()
	{
		/*
		 * Add the root category.
		 */
		$course = $this->get_course_id();
		$tool = $this->get_parameter(self :: PARAM_TOOL);
		$cats = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_categories($course, $tool);
		$root = array();
		$root['obj'] = & new LearningObjectPublicationCategory(0, get_lang('RootCategory'), $course, $tool, 0);
		$root['sub'] = & $cats;
		$tree = array();
		$tree[] = & $root;
		return $tree;
	}
	/**
	 * Gets a list of the defined categories in the current tool.
	 */
	private function get_category_list()
	{
		$categories = array ();
		$tree = $this->get_category_tree();
		self :: translate_category_tree(& $tree, & $categories);
		return $categories;
	}
	/**
	 * Makes a category tree ready for displaying by adding a prefix to the
	 * category title based on the level of that category in the tree structure.
	 * @param array $tree The category tree
	 * @param array $categories In this array the new category titles (with
	 * prefix) will be stored. The keys in this array are the category ids, the
	 * values are the new titles
	 * @param int $level The current level in the tree structure
	 */
	private static function translate_category_tree(& $tree, & $categories, $level = 0)
	{
		foreach ($tree as $node)
		{
			$obj = $node['obj'];
			$prefix = ($level ? str_repeat('&nbsp;&nbsp;&nbsp;', $level).'&mdash; ' : '');
			$categories[$obj->get_id()] = $prefix.$obj->get_title();
			$subtree = $node['sub'];
			if (is_array($subtree) && count($subtree))
			{
				self :: translate_category_tree(& $subtree, & $categories, $level +1);
			}
		}
	}
	/**
	 * Gets a category
	 * @param int $id The id of the requested category
	 * @return LearningPublicationCategory The requested category
	 */
	function get_category($id)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_category($id);
	}
	/**
	 * Displays the header of this application
	 * @param array $breadcrumbs The breadcrumbs which should be displayed
	 */
	function display_header($breadcrumbs = array())
	{
		global $interbredcrump;
		$tool = $this->get_parameter(self :: PARAM_TOOL);
		if ($tool)
		{
			array_unshift($breadcrumbs, array('url' => $this->get_url(), 'name' => get_lang(Tool :: type_to_class($tool).'Title')));
		}
		$current_crumb = array_pop($breadcrumbs);
		$interbredcrump = $breadcrumbs;
		$title = $current_crumb['name'];
		Display :: display_header($title);
		echo '<div style="float: right; margin: 0 0 0.5em 0.5em; padding: 0.5em; border: 1px solid #DDD; background: #FAFAFA;">';
		echo '<form method="get" action="'.$this->get_url().'" style="display: inline;">';
		echo '<select name="' . self :: PARAM_TOOL . '" onchange="submit();">';
		if (!isset($this->tool_class))
		{
			echo '<option selected="selected">Pick a Tool &hellip;</option>';
		}
		$tools = array();
		foreach ($this->get_registered_tools() as $t)
		{
			$tools[$t] = htmlentities(get_lang(Tool :: type_to_class($t).'Title'));
		}
		asort($tools);
		foreach ($tools as $tool => $title)
		{
			$class = Tool :: type_to_class($tool);
			echo '<option value="'.$tool.'"'.($class == $this->tool_class ? ' selected="selected"' : '').'>'.htmlentities($title).'</option>';
		}
		echo '</select></form></div>';
		if (isset($this->tool_class))
		{
			api_display_tool_title(htmlentities(get_lang($this->tool_class.'Title')));
		}
	}
	/**
	 * Displays the footer of this application
	 */
	function display_footer()
	{
		// TODO: Find out why we need to reconnect here.
		global $dbHost, $dbLogin, $dbPass, $mainDbName;
		mysql_connect($dbHost, $dbLogin, $dbPass);
		mysql_select_db($mainDbName);
		Display :: display_footer();
	}

	/**
	 * Returns the names of the tools known to this application.
	 * @return array The tools.
	 */
	function get_registered_tools()
	{
		return $this->tools;
	}

	/**
	 * Registers a tool with this application.
	 * @param string $tool The tool name.
	 */
	function register_tool($tool)
	{
		if (in_array($tool, $this->tools))
		{
			die('Tool already registered: '.$tool);
		}
		$this->tools[] = $tool;
	}

	/**
	 * Loads the tools installed on the system. Tools are classes in the
	 * tool/ subdirectory. Each tool is a directory, which in its turn
	 * contains a class file named after the tool. For instance, the link
	 * tool is the class LinkTool, defined in tool/link/linktool.class.php.
	 * Tools must extend the Tool class.
	 */
	private function load_tools()
	{
		$path = dirname(__FILE__).'/tool';
		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				$toolPath = $path.'/'.$file;
				if (is_dir($toolPath) && self :: is_tool_name($file))
				{
					require_once $toolPath.'/'.$file.'tool.class.php';
					$this->register_tool($file);
				}
			}
			closedir($handle);
		}
		else
		{
			die('Failed to load tools');
		}

	}

	/**
	 * Determines whether or not the given name is a valid tool name.
	 * @param string $name The name to evaluate.
	 * @return True if the name is a valid tool name, false otherwise.
	 */
	static function is_tool_name($name)
	{
		return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
	}
	/*
	 * Inherited
	 */
	function learning_object_is_published($object_id)
	{
		return WeblcmsDataManager :: get_instance()->learning_object_is_published($object_id);
	}
	/*
	 * Inherited
	 */
	function any_learning_object_is_published($object_ids)
	{
		return WeblcmsDataManager::get_instance()->any_learning_object_is_published($object_ids);
	}
	/*
	 * Inherited
	 */
	function get_learning_object_publication_attributes($object_id)
	{
		return WeblcmsDataManager :: get_instance()->get_learning_object_publication_attributes($object_id);
	}
}
?>