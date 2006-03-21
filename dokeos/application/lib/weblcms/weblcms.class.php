<?php
require_once dirname(__FILE__).'/../application.class.php';
require_once dirname(__FILE__).'/weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/learningobjectpublicationcategory.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../../claroline/inc/lib/groupmanager.lib.php';

/**
==============================================================================
 *	This is an application that creates a fully fledged web-based learning
 *	content management system. The Web-LCMS is based on so-called "tools",
 *	which each represent a segment in the application.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class WebLCMS extends Application
{
	/**
	 * The tools that this application offers.
	 */
	private $tools;

	/**
	 * The parameters that should be passed with every request.
	 */
	private $parameters;

	/**
	 * Constructor. Optionally takes a default tool; otherwise, it is taken
	 * from the query string.
	 * @param Tool $tool The default tool, or null if none.
	 */
	function WebLCMS($tool = null)
	{
		$this->parameters = array ();
		$this->set_parameter('tool', $_GET['tool']);
		$this->tools = array ();
		$this->load_tools();
	}
	
	/*
	 * Inherited.
	 */
	function run()
	{
		$tool = $this->get_parameter('tool');
		if (isset ($tool))
		{
			$class = self :: tool_to_class($tool);
			api_display_tool_title(get_lang($class.'Title'));
			$toolObj = new $class ($this);
			$toolObj->run();
		}
		else
		{
			echo '<ul>';
			foreach ($this->get_registered_tools() as $tool)
			{
				$class = self :: tool_to_class($tool);
				echo '<li><a href="'.$this->get_url(array ('tool' => $tool)).'">'.get_lang($class.'Title').'</a></li>';
			}
			echo '</ul>';
		}
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
	
	function get_groups()
	{
		return GroupManager :: get_group_ids($this->get_course_id(), $this->get_user_id());
	}
	
	function get_categories()
	{
		/*
		 * Add the root category.
		 */
		$course = $this->get_course_id();
		$tool = $this->get_parameter('tool');
		$cats = WebLCMSDataManager :: get_instance()->retrieve_publication_categories($course, $tool);
		$root = array();
		$root['obj'] = & new LearningObjectPublicationCategory(0, get_lang('RootCategory'), $course, $tool, 0);
		$root['sub'] = & $cats;
		$tree = array();
		$tree[] = & $root;
		return $tree;
	}

	/**
	 * Gets the URL of the current page in the application. Optionally takes
	 * an associative array of name/value pairs representing additional query
	 * string parameters; these will either be added to the parameters already
	 * present, or override them if a value with the same name exists.
	 * @param array $parameters The additional parameters, or null if none.
	 * @return string The URL.
	 */
	function get_url($parameters = array ())
	{
		$string = '';
		if (count($parameters))
		{
			$parameters = array_merge($this->parameters, $parameters);
		}
		else
		{
			$parameters = & $this->parameters;
		}
		foreach ($parameters as $name => $value)
		{
			$string .= '&'.urlencode($name).'='.urlencode($value);
		}
		return $_SERVER['PHP_SELF'].'?'.$string;
	}

	/**
	 * Returns the current URL parameters.
	 * @return array The parameters.
	 */
	function get_parameters()
	{
		return $this->parameters;
	}
	
	/**
	 * Returns the value of the given URL parameter.
	 * @param string $name The parameter name.
	 * @return string The parameter value.
	 */
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}
	
	/**
	 * Sets the value of a URL parameter.
	 * @param string $name The parameter name.
	 * @param string $value The parameter value.
	 */
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
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
	 * Converts a tool name to the corresponding class name.
	 * @param string $tool The tool name.
	 * @return string The class name.
	 */
	static function tool_to_class($tool)
	{
		return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $tool)).'Tool';
	}
	
	/**
	 * Converts a tool class name to the corresponding tool name.
	 * @param string $class The class name.
	 * @return string The tool name.
	 */
	static function class_to_tool($class)
	{
		return preg_replace(array ('/Tool$/', '/^([A-Z])/e', '/([A-Z])/e'), array ('', 'strtolower(\1)', '"_".strtolower(\1)'), $class);
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
}
?>