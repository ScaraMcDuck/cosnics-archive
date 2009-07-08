<?php
// Deze klasse wordt niet gebruikt dus mag worden verwijderd
/**
 * $Id:$
 * @package application.portfolio
 */

require_once dirname(__FILE__).'/portfoliodatamanager.class.php';

/**
================================================================================
 *
 *
 *	@author Roel Neefs
================================================================================
 */

class PFTreeManager
{

	private static $instance;

	function PFTreeManager()
	{
		$this->initialize();
	}

	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			self :: $instance = new PFTreeManager();
		}
		return self :: $instance;
	}

	function initialize()
	{
	}

	function get_root_element($user)
	{
		$pdm = PortfolioDataManager :: get_instance();
		return $pdm->get_root_element($user);
	}

	function show_tree($url, $user)
	{
		$root=$this->get_root_element($user);
		$this->show_item($url, $root, 0);
	}

	function show_item($url, $item, $indent = 0)
	{
		$pdm = PortfolioDataManager :: get_instance();
		$title=$pdm->get_item_title($item);
		for($i=0;$i<($indent*2);$i++)
		{
			print "&nbsp;";
		}
		print '<a href='.$_SERVER['PHP_SELF'].'?portfolio_action='.MyPortfolioManager :: ACTION_VIEW.'&item='.$item.'>'.$title.'</a><br />';
		$children=$pdm->get_item_children($item);

		foreach($children as $child)
		{
			$this->show_item($url, $child, $indent+1);
		}

	}

	function create_child($item, $user, $title)
	{
		$pdm = PortfolioDataManager :: get_instance();
		$new_item=$pdm->create_page($title,$user);
		$pdm->connect_parent_to_child($item, $new_item,$user);
		return $new_item;
	}

	function delete_item($item,$user)
	{
		$pdm = PortfolioDataManager :: get_instance();
		$root= $pdm->get_root_element($user);
		if($item != "" && $item != $root)
		{
			$parent=$this->get_parent($item);
			$children=$pdm->get_item_children($item);
			foreach($children as $child)
			{
				$this->set_parent($child, $parent);
			}
			$pdm->remove_item($item);
		}
	}

	function get_parent($item)
	{
		$pdm = PortfolioDataManager :: get_instance();
		return $pdm->get_parent($item);
	}

	function set_parent($item,$new_parent)
	{
		$pdm = PortfolioDataManager :: get_instance();
		return $pdm->set_parent($item,$new_parent);
	}

}
?>