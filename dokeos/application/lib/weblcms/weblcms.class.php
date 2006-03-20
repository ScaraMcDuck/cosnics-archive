<?php
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../application.class.php';

class WebLCMS extends Application
{
	private $tools;

	private $currentTool;

	function WebLCMS($tool = null)
	{
		$this->currentTool = (isset ($tool) ? $tool : $_GET['tool']);
		$this->tools = array ();
		$this->load_tools();
	}

	function run()
	{
		$tool = $this->get_current_tool();
		if (isset ($tool))
		{
			$class = self :: tool_to_class($tool);
			api_display_tool_title(get_lang($class.'Title'));
			$t = new $class ();
			$t->run();
		}
		else
		{
			echo '<ul>';
			foreach ($this->get_registered_tools() as $tool)
			{
				$class = self :: tool_to_class($tool);
				echo '<li><a href="'.$_SERVER['SCRIPT_NAME'].'?tool='.$tool.'">'.get_lang($class.'Title').'</a></li>';
			}
			echo '</ul>';
		}
	}

	function get_current_tool()
	{
		return $this->currentTool;
	}

	function get_registered_tools()
	{
		return $this->tools;
	}

	function register_tool($tool)
	{
		if (in_array($tool, $this->tools))
		{
			die('Tool already registered: '.$tool);
		}
		$this->tools[] = $tool;
	}

	private function load_tools()
	{
		$path = dirname(__FILE__).'/tool';
		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				$p = $path.'/'.$file;
				if (is_dir($p) && self :: is_tool_name($file))
				{
					require_once $p.'/'.$file.'tool.class.php';
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

	static function tool_to_class($tool)
	{
		return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $tool)).'Tool';
	}

	static function class_to_tool($class)
	{
		return preg_replace(array ('/Tool$/', '/^([A-Z])/e', '/([A-Z])/e'), array ('', 'strtolower(\1)', '"_".strtolower(\1)'), $class);
	}

	static function is_tool_name($name)
	{
		return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
	}
}
?>