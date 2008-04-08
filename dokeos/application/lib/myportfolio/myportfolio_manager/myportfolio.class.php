<?php
/**
 * $Id:$
 * @package application.portfolio
 */
require_once dirname(__FILE__).'/../../webapplication.class.php';
require_once dirname(__FILE__).'/../myportfolio_manager/portfoliocomponent.class.php';
require_once dirname(__FILE__).'/../pftreemanager.class.php';
require_once dirname(__FILE__).'/../portfoliodatamanager.class.php';

/**
================================================================================
 *	The Portfolio supplies an electronic portfolio on top of the Dokeos LCMS
 *
 *	@author Roel Neefs
================================================================================
 */

class MyPortfolio extends WebApplication
{
	const PARAM_ACTION = 'portfolio_action';
	const PARAM_ITEM = 'item';
	const PARAM_TITLE = 'title';
	const APPLICATION_NAME = 'portfolio';

	const ACTION_CREATE = 'pf_create_child';
	const ACTION_EDIT = 'pf_edit_child';
	const ACTION_VIEW = 'pf_item_view';
	const ACTION_SHARING = 'pf_sharing';
	const ACTION_STATE = 'pf_state';
	const ACTION_PROPS = 'pf_props';

	const ACTION_CREATED = 'pf_created_child';
	const ACTION_DELETE = 'pf_delete_child';

	private $user;
	private $action;
	private $owner;

	public static $item;

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
//		self :: $item= $this->get_item_id();
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
				case self::ACTION_CREATED: 
					$this->created_child($this->get_parameter(self::PARAM_ITEM));
					break;
				case self::ACTION_DELETE:
					$this->delete_child($this->get_parameter(self::PARAM_ITEM));
					break;
				default:
			}
		}

//		$this->display_header();

//		echo $this->action.$this->get_parameter(self::PARAM_ITEM);
		if($this->action)
		{
			switch($this->action)
			{
				case self::ACTION_CREATE: 
					//$this->create_child($this->get_parameter(self::PARAM_ITEM));
					$component = PortfolioComponent :: factory('Publishing', $this);
					break;
				case self::ACTION_EDIT: 
					//$this->edit_item($this->get_parameter(self::PARAM_ITEM));
					$component = PortfolioComponent :: factory('Editor', $this);
					break;
				case self::ACTION_PROPS: 
					//$this->edit_item($this->get_parameter(self::PARAM_ITEM));
					$component = PortfolioComponent :: factory('Props', $this);
					break;
				case self::ACTION_STATE: 
					//$this->edit_item($this->get_parameter(self::PARAM_ITEM));
					$component = PortfolioComponent :: factory('State', $this);
					break;
				case self::ACTION_SHARING: 
					//$this->edit_item($this->get_parameter(self::PARAM_ITEM));
					$component = PortfolioComponent :: factory('Sharing', $this);
					break;
				case self::ACTION_VIEW:
				default:
					//$this->show_item($this->get_parameter(self::PARAM_ITEM));
					$component = PortfolioComponent :: factory('Viewer', $this);
			}
		}
		else {
			$this->action = self::ACTION_VIEW;
//			$this->show_item($ptm->get_root_element($this->user));
			$component = PortfolioComponent :: factory('Viewer', $this);
		}
		$component->run();
//		$this->display_footer();
	}

/*	function show_item($item)
	{
		$this->show_item_header($item);
		$pdm = PortfolioDataManager :: get_instance();
		if($item == "")
		{
			$item=$pdm->get_root_element($this->user);
		}
	
		$title=$pdm->get_item_title($item);
		print '<h3 style="float: left;" title="'.$title.'">'.$title.'</h3><br /><br />';

		print "<br />some publication content will come here<br /><br />";
		
	}
*/

/*	function show_item_header($item)
	{
		print '<a href="'.$this->get_url(array (self :: PARAM_ACTION=>self::ACTION_CREATE,self :: PARAM_ITEM => $item), true).'">'.Translation :: get("pf_create_child").'</a>';
		print '<a href="'.$this->get_url(array (self :: PARAM_ACTION=>self::ACTION_EDIT,self :: PARAM_ITEM => $item), true).'">'.Translation :: get("pf_edit_item").'</a>';
		print '<a href="'.$this->get_url(array (self :: PARAM_ACTION=>self::ACTION_DELETE,self :: PARAM_ITEM => $item), true).'">'.Translation :: get("pf_delete_item").'</a><br /><br />';
	}
*/

/*	function create_child($item)
	{
		$this->show_item_header($item);
		$pdm = PortfolioDataManager :: get_instance();
		if($item == "")
		{
			$item=$pdm->get_root_element($this->user);
		}
		$title=$pdm->get_item_title($item);
		print '<h3 style="float: left;" title="'.Translation :: get("pf_title_new_page_for").' '.$title.'">'.Translation :: get("pf_title_new_page_for").' '.$title.'</h3>';

		require_once dirname(__FILE__).'/../weblcms/learningobjectpublisher.class.php';
		$pub = new LearningObjectPublisher($this, 'document');
		print  $pub->as_html();


//		$form = new FormValidator('create_page', 'post');
//		$form->addElement('hidden', self :: PARAM_ITEM);
//		$form->addElement('hidden', self :: PARAM_ACTION);
//		$form->add_textfield('title', Translation :: get('title'),$required = true);
//		$form->addElement('submit', 'submit', Translation :: get('Ok'));
//		$form->setDefaults(array (self :: PARAM_ITEM => $item, self :: PARAM_ACTION => self::ACTION_CREATED));
//		print $form->toHtml();
	}
*/

	function edit_item($item)
	{
		$this->show_item_header($item);
		$pdm = PortfolioDataManager :: get_instance();
		if($item == "")
		{
			$item=$pdm->get_root_element($this->user);
		}
		$title=$pdm->get_item_title($item);
		print '<h3 style="float: left;" title="'.Translation :: get("pf_title_new_page_for").' '.$title.'">'.Translation :: get("pf_title_new_page_for").' '.$title.'</h3>';


		$form = new FormValidator('create_page', 'post');
		$form->addElement('hidden', self :: PARAM_ITEM);
		$form->addElement('hidden', self :: PARAM_ACTION);
		$form->add_textfield('title', Translation :: get('title'),$required = true);
		$form->addElement('submit', 'submit', Translation :: get('Ok'));
		$form->setDefaults(array (self :: PARAM_ITEM => $item, self :: PARAM_ACTION => self::ACTION_CREATED));
		print $form->toHtml();
	}

	function delete_child($item)
	{
		$ptm = PFTreeManager :: get_instance();
		$parent=$ptm->get_parent($item);
		$ptm->delete_item($item,$this->user);
		$this->action=self::ACTION_VIEW;
		$this->set_parameter(self :: PARAM_ITEM, $parent);
	}

	function created_child($item)
	{
		$ptm = PFTreeManager :: get_instance();
		$title=$_POST[self :: PARAM_TITLE];
		$new_item = $ptm->create_child($item,$this->user, $title);
		$this->action=self::ACTION_VIEW;
		$this->set_parameter(self :: PARAM_ITEM, $new_item);
	}

	function display_header($breadcrumbtrail, $display_search = false)
	{
		if (is_null($breadcrumbtrail))
		{
			$breadcrumbtrail = new BreadcrumbTrail();
		}
		
		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: display_header($breadcrumbtrail);
		echo '<div style="float: left; width: 20%;">';
		$this->display_treeview();
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
		//$this->owner->photo or so
		echo '<img align=right src="'.$this->get_path(WEB_IMG_PATH).'unknown.jpg"</img><br />';
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: display_footer();
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
	function get_application_platform_admin_links()
	{
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

	function get_user_id()
	{
		return $this->user->get_user_id();
	}

	function get_user()
	{
		return $this->user;
	}
	
	function get_item_id()
	{
		if($this->get_parameter(self::PARAM_ITEM)=="")
		{
			$pdm = PortfolioDataManager :: get_instance();
			//echo "this one?";
			$root=$pdm->get_root_element($this->user);
			//echo "no";
			return $root;
		}
		else return $this->get_parameter(self::PARAM_ITEM);
	}
	function get_action()
	{
		return $this->action;
	}
	
	function get_platform_setting($variable, $application = self :: APPLICATION_NAME)
	{
		$adm = AdminDataManager :: get_instance();
		return $adm->retrieve_setting_from_variable_name($variable, $application);
	}
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
}
?>