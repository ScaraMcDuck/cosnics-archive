<?php
/**
 * $Id:$
 * @package application.portfolio
 */
require_once dirname(__FILE__).'/../../web_application.class.php';
require_once dirname(__FILE__).'/../myportfolio_manager/portfolio_component.class.php';
require_once dirname(__FILE__).'/../portfolio_tree_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_data_manager.class.php';
require_once dirname(__FILE__).'/../myportfolio_block.class.php';

/**
================================================================================
 *	The Portfolio supplies an electronic portfolio on top of the Dokeos LCMS
 *
 *	@author Roel Neefs
================================================================================
 */

class MyPortfolio extends WebApplication
{	

	const APPLICATION_NAME = 'myportfolio';

	const PARAM_ACTION = 'portfolio_action';
	const PARAM_ITEM = 'item';
	const PARAM_TITLE = 'title';
	const PARAM_FIRSTLETTER = 'l';
	const PARAM_EXAMPLE = 'ex';
	

	const ACTION_CREATE = 'pf_create_child';
	const ACTION_EDIT = 'pf_edit_child';
	const ACTION_VIEW = 'pf_item_view';
	const ACTION_BROWSE = 'pf_browse';
	const ACTION_PROPS = 'pf_props';
	
	//VUB
	const ACTION_PFPUBS = 'pf_pubs';
	const ACTION_PFPROJ = 'pf_proj';
	const ACTION_PFTHES = 'pf_thes';

	const ACTION_CREATED = 'pf_created_child';
	const ACTION_DELETE = 'pf_delete_child';

	private $user;
	private $action;
	private $owner;

//	public static $item;

	function MyPortfolio($user)
	{
        
		parent :: __construct();
		if(isset($_POST[self :: PARAM_ACTION]))
		{
			$this->set_parameter(self :: PARAM_ACTION, $_POST[self :: PARAM_ACTION]);
		}
		else {
			$this->set_parameter(self :: PARAM_ACTION, $_GET[self :: PARAM_ACTION]);
		}
		if(isset($_POST[self :: PARAM_ITEM]))
		{
			$this->set_parameter(self :: PARAM_ITEM, $_POST[self :: PARAM_ITEM]);
		}
		else {
			$this->set_parameter(self :: PARAM_ITEM, $_GET[self :: PARAM_ITEM]);
		}

		$this->user = $user;

		if($this->get_parameter(self::PARAM_ITEM)=="")
			$this->owner=$user;
		else {

			$pdm = PortfolioDataManager :: get_instance();
			$owner_id = $pdm->get_owner($this->get_parameter(self::PARAM_ITEM));
			$usermgr = new UserManager($owner_id);
			$this->owner=$usermgr->retrieve_user($owner_id);
		}
		$ptm = PFTreeManager :: get_instance();
		if($user) $ptm->set_current_item($this->get_item_id());
	}

	/*
	 * Inherited.
	 */
	function run()
	{
        
		$this->action = $this->get_parameter(self::PARAM_ACTION);
		$ptm = PFTreeManager :: get_instance();
		$component = PortfolioComponent :: factory('Viewer', $this);

		if($this->action)
		{
			switch($this->action)
			{
				case self::ACTION_CREATE: 
					$component = PortfolioComponent :: factory('Publishing', $this );
					break;
				case self::ACTION_EDIT: 
					$component = PortfolioComponent :: factory('Editor', $this);
					break;
				case self::ACTION_BROWSE: 
					$component = PortfolioComponent :: factory('Browser', $this);
					break;
				case self::ACTION_PROPS: 
					$component = PortfolioComponent :: factory('Props', $this);
					break;
                // vub specific
				case self::ACTION_PFPUBS: 
					$component = PortfolioComponent :: factory('Publications', $this);
					break;
				case self::ACTION_PFPROJ: 
					$component = PortfolioComponent :: factory('Projects', $this);
					break;
				case self::ACTION_PFTHES: 
					$component = PortfolioComponent :: factory('Theses', $this);
					break;
                //end vub specific
				case self::ACTION_VIEW:
				default:
					$component = PortfolioComponent :: factory('Viewer', $this);
			}
		}
		else {
			$this->action = self::ACTION_VIEW;
//			$this->show_item($ptm->get_root_element($this->user));
			$component = PortfolioComponent :: factory('Viewer', $this);
		}
//		echo $this->action;
		$component->run();
//		$this->display_footer();
	}

	function display_header($breadcrumbtrail, $display_search = false)
	{
        
		if (is_null($breadcrumbtrail))
		{
			$breadcrumbtrail = new BreadcrumbTrail();
		}
		
		/*
		 * $title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		*/
		Display :: header($breadcrumbtrail);
		echo '<div style="float: left; width: 20%;">';
		$this->display_treeview();
		echo '<br />';
		echo '</div>';
		echo '<div style="float: left; width: 70%;">';

	}
	/**
	 * Displays the footer of this application
	 */
	function display_footer()
	{
		echo '</div>';
		echo '<div style="float: right; width: 10%;">';
		//FIXME $this->owner->photo or so
		if($this->owner->get_id()==6)
		echo '<img align=right src="'.Theme :: get_instance()->get_common_img_path().'fquestie.jpeg"></img><br />';
		else echo '<img align=right src="'.Theme :: get_instance()->get_common_image_path().'unknown.jpg"></img><br />';
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}

	function render_block($block)
        {
                $myportfolio_block = MyPortfolioBlock :: factory($this, $block);
                return $myportfolio_block->run();
        }

	/**
	 * Displays a normal message.
	 * @param string $message The message.
	 */
	function display_message($message)
	{
		Display :: display_normal_message($message);
	}
	/**
	 * Displays an error message.
	 * @param string $message The message.
	 */
	function display_error_message($message)
	{
		Display :: display_error_message($message);
	}
	/**
	 * Displays a warning message.
	 * @param string $message The message.
	 */
	function display_warning_message($message)
	{
		Display :: display_warning_message($message);
	}
	/**
	 * Displays an error page.
	 * @param string $message The message.
	 */
	function display_error_page($message)
	{
		$this->display_header();
		$this->display_error_message($message);
		$this->display_footer();
	}
	
	/**
	 * Displays a warning page.
	 * @param string $message The message.
	 */
	function display_warning_page($message)
	{
		$this->display_header();
		$this->display_warning_message($message);
		$this->display_footer();
	}
	
	/**
	 * Displays a popup form.
	 * @param string $message The message.
	 */
	function display_popup_form($form_html)
	{
		Display :: display_normal_message($form_html);
	}

	function display_treeview()
	{
		$ptm = PFTreeManager :: get_instance();
		$ptm->show_tree($this->get_url(),$this->owner);
	}

	function learning_object_is_published($object_id)
	{
		
	}
	/*
	 * Inherited
	 */
	function any_learning_object_is_published($object_ids)
	{
		
	}
	/*
	 * Inherited
	 */
	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		
	}
	
	/*
	 * Inherited
	 */
	function get_learning_object_publication_attribute($publication_id)
	{
		
	}
	
	function delete_learning_object_publications($object_id)
	{
		
	}
	
	function update_learning_object_publication_id($publication_attr)
	{
		
	}
	function count_publication_attributes($type = null, $condition = null)
	{
		
	}
	
	function get_learning_object_publication_locations($learning_object)
	{
		
	}
	
	function publish_learning_object($learning_object, $location)
	{
		
	}

	
	
	function count_portfolio_publications($condition = null)
	{
		$pmdm = PortfolioDataManager :: get_instance();
		return $pmdm->count_portfolio_publications($condition);
	}
	
	function get_application_platform_admin_links()
	{
		$links = array();
		$links[] = array('name' => Translation :: get('NoOptionsAvailable'), 'action' => 'empty', 'url' => $this->get_link());
		return array('application' => array('name' => self :: APPLICATION_NAME, 'class' => self :: APPLICATION_NAME), 'links' => $links);
	}
	
	/**
	 * Return a link to a certain action of this application
	 * @param array $paramaters The parameters to be added to the url
	 * @param boolean $encode Should the url be encoded ?
	 */
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'run.php';
		$parameters['application'] = self :: APPLICATION_NAME;
		if (count($parameters))
		{
			$link .= '?'.http_build_query($parameters);
		}
		if ($encode)
		{
			$link = htmlentities($link);
		}
		return $link;
	}
	
	/**
	 * Gets an URL.
	 * @param array $additional_parameters Additional parameters to add in the
	 * query string (default = no additional parameters).
	 * @param boolean $include_search Include the search parameters in the
	 * query string of the URL? (default = false).
	 * @param boolean $encode_entities Apply php function htmlentities to the
	 * resulting URL ? (default = false).
	 * @return string The requested URL.
	 */
	function get_url($additional_parameters = array (), $include_search = false, $encode_entities = false, $x = null)
	{
		$eventual_parameters = array_merge($this->get_parameters($include_search), $additional_parameters);
		$url = $_SERVER['PHP_SELF'].'?'.http_build_query($eventual_parameters);
		if ($encode_entities)
		{
			$url = htmlentities($url);
		}

		return $url;
	}
	
	/**
	 * Gets the url for viewing a portfolio publication
	 * @param PortfolioPublication
	 * @return string The url
	 */
	function get_publication_viewing_url($portfolio)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW, self :: PARAM_ITEM => $portfolio->get_id()));
	}
	
	/**
	 * Gets the url for deleting a portfolio publication
	 * @param PortfolioPublication
	 * @return string The url
	 */
	function get_publication_deleting_url($portfolio)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_ITEM => $portfolio->get_id()));
	}
	
	/**
	 * Retrieve a portfolio publication
	 * @param int $id
	 * @return PortfolioPublication
	 */	
	function retrieve_portfolio_publication($id)
	{
		$pdm = PortfolioDataManager :: get_instance();
		return $pdm->retrieve_portfolio_publication($id);
	}

	function retrieve_portfolio_publication_from_item($item)
	{
		$pdm = PortfolioDataManager :: get_instance();
		return $pdm->retrieve_portfolio_publication_from_item($item);
	}

	function retrieve_portfolio_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$pmdm = PortfolioDataManager :: get_instance();
		return $pmdm->retrieve_portfolio_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}

    

	function get_user_id()
	{
		return $this->user->get_id();
	}

	function get_user()
	{
		return $this->user;
	}
	
	function get_owner()
	{
		return $this->owner;
	}
	
	function get_item_id()
	{
		if($this->get_parameter(self::PARAM_ITEM)=="")
		{
			$ptm = PFTreeManager :: get_instance();
			return $ptm->get_root_element($this->user);
		}
		else return $this->get_parameter(self::PARAM_ITEM);
	}
	function get_action()
	{
		return $this->action;
	}
	
	function get_platform_setting($variable, $application = self :: APPLICATION_NAME)
	{
		return PlatformSetting :: get($variable, $application = self :: APPLICATION_NAME);
	}
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}

    function validatePublication($id){
        $pdm = DatabasePortfolioDataManager::get_instance();
                $pdm->validatePublication($id);
    }
}
?>
