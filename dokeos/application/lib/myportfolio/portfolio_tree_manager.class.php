<?php
/**
 * $Id:$
 * @package application.portfolio
 */

require_once dirname(__FILE__).'/portfolio_data_manager.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/learning_object/portfolio_item/portfolio_item.class.php';
require_once dirname(__FILE__).'/portfolio_publication.class.php';

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
	private $current;

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
		$root=$pdm->get_root_element($user);
		if($root==-1)
		{
			//$pdm->create_root_element($user);
			//Now create the learning object that is attached to the first tree element
			
			$lo = new PortfolioItem();
			$lo->set_owner_id($user->get_id());
			$lo->set_title(htmlspecialchars(Translation :: get('PortfolioOf').$user->get_firstname()." ".$user->get_lastname()));
			$lo->set_description(htmlspecialchars(Translation :: get('Pf_intro_text')));
			$lo->set_parent_id(0);
			$lo->create();

			$this->current=-1;

			$pub = new PortfolioPublication();
			$pub->set_portfolio_item($lo->get_id());
			$pub->set_publisher($user->get_id());
			$pub->set_published(time());
			if ($pub->create())
			{
				return $pub->get_treeitem();
			}
		}
		else return $root;
	}

	function show_tree($url, $user)
	{
		$root=$this->get_root_element($user);
		//$this->show_item_old($url, $root, 0);
		print '<script type="text/javascript" src="'.Path::get(WEB_LIB_PATH).'/javascript/treemenu.js"></script>';
		print '<ul class=tree-menu>';
		$this->show_item($url, $root, 0);
		//echo '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_PUBS.'&user='.$this->owner->get_user_id().'>'.Translation :: get('Mypubs').'</a></li>';
		//print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_PFPUBS.'&user='.$user->get_id().'>'.Translation :: get('MyResearch').'</a>';
		//if ($_GET['alles']==1){
        print '<li><a>'.Translation :: get('MyResearch').'</a>';
		print '<ul>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_PFPUBS.'&user='.$user->get_id().'&item='.$root.'>'.Translation :: get('Mypubs').'</a>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_PFPROJ.'&user='.$user->get_id().'&item='.$root.'>'.Translation :: get('Myproj').'</a>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_PFTHES.'&user='.$user->get_id().'&item='.$root.'>'.Translation :: get('Mythes').'</a>';
		print '</ul>';	
		print '</li>';
       // } else {
          //  print '<a href="'.$_SERVER["REQUEST_URI"].'&alles=1">+</a>';
        //}
		print '</ul><br />';
		print '<ul class=tree-menu>';	
		print '<li><a>Browse Portfolios</a>';
		print '<ul>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_BROWSE.'&'.MyPortfolioManager :: PARAM_EXAMPLE.'=1 class=type_home>Examples</a></li>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_BROWSE.'&'.MyPortfolioManager :: PARAM_FIRSTLETTER.'=a class=type_home>A-C</a></li>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_BROWSE.'&'.MyPortfolioManager :: PARAM_FIRSTLETTER.'=d class=type_home>D-F</a></li>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_BROWSE.'&'.MyPortfolioManager :: PARAM_FIRSTLETTER.'=g class=type_home>G-I</a></li>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_BROWSE.'&'.MyPortfolioManager :: PARAM_FIRSTLETTER.'=j class=type_home>J-L</a></li>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_BROWSE.'&'.MyPortfolioManager :: PARAM_FIRSTLETTER.'=m class=type_home>M-O</a></li>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_BROWSE.'&'.MyPortfolioManager :: PARAM_FIRSTLETTER.'=p class=type_home>P-R</a></li>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_BROWSE.'&'.MyPortfolioManager :: PARAM_FIRSTLETTER.'=s class=type_home>S-U</a></li>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_BROWSE.'&'.MyPortfolioManager :: PARAM_FIRSTLETTER.'=v class=type_home>V-X</a></li>';
		print '<li><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_BROWSE.'&'.MyPortfolioManager :: PARAM_FIRSTLETTER.'=y class=type_home>Y-Z</a></li>';
		print '</ul>';
		print '</li>';
		
		
		print '</ul>';
	}

	function show_item_old($url, $item, $indent = 0)
	{
		$cur=$this->get_current_item();
		$pdm = PortfolioDataManager :: get_instance();
		$portpub = $pdm->retrieve_portfolio_publication_from_item($item);
		$title = $portpub->get_publication_object()->get_title();
		//$title=$pdm->get_item_title($item);
		if(true)//is_parent, is_sibling, is_child)
		{
			for($i=0;$i<($indent*2);$i++)
			{
				print "&nbsp;";
			}
			print '<a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_VIEW.'&item='.$item.'>'.$title.'</a><br>';
		}
		$children=$pdm->get_item_children($item);
		
		foreach($children as $child)
		{
			$this->show_item_old($url, $child, $indent+1);
		}
		
	}
	function show_item($url, $item)
	{
		$cur=$this->get_current_item();
		$pdm = PortfolioDataManager :: get_instance();
		$portpub = $pdm->retrieve_portfolio_publication_from_item($item);
		$title = $portpub->get_publication_object()->get_title();
		//$title=$pdm->get_item_title($item);
		
		print '<li';
		if($cur==$item) print ' class=current';
		print '><a href='.$_SERVER['PHP_SELF'].'?application=myportfolio&portfolio_action='.MyPortfolioManager :: ACTION_VIEW.'&item='.$item.' class=type_document>'.$title.'</a>';
		
		$children=$pdm->get_item_children($item);
		if ($children) print '<ul>';
		foreach($children as $child)
		{
			$this->show_item($url, $child);
		}
		if ($children) print '</ul>';
		print '</li>';
		
	}
	
	
	function create_child($item, $user)
	{
		$pdm = PortfolioDataManager :: get_instance();
		$new_item=$pdm->create_page($user);
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
	
	function get_children($item)
	{
		$pdm = PortfolioDataManager :: get_instance();
		return $pdm->get_item_children($item);
	}
	
	function get_current_item()
	{
		return $this->current;
	}

	function set_current_item($item)
	{
		$this->current=$item;
	}
}
?>