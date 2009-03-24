<?php
/**
 * @package users
 */

require_once dirname(__FILE__) . '/user_data_manager.class.php';

/**
==============================================================================
 *	This is a buddy list for a user
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class BuddyList
{
	// The user to which the buddy list belongs
	private $user;
	
	// The parent object
	private $parent;
	
	function BuddyList($user, $parent)
	{
		$this->user = $user;
		$this->parent = $parent;
	}
	
	/**
	 * Creates the buddy list in html code
	 * @return String the html code of the buddy list
	 */
	function to_html()
	{
		$categories = $this->retrieve_buddy_list_categories();
		$buddies = $this->retrieve_buddies();
		
		$html = array();
		$html[] = $this->display_buddy_list_header();
		
		while($category = $categories->next_result())
		{
			$html[] = $this->display_buddy_list_category($category, $buddies[$category->get_id()]);
		}
		
		$category = new BuddyListCategory();
		$category->set_id(0);
		$category->set_title(Translation :: get('OtherBuddies'));
		$category->set_user_id($this->user->get_id());
		
		$html[] = $this->display_buddy_list_category($category, $buddies[$category->get_id()]);
		$html[] = $this->display_buddy_list_footer();
		
		return implode("\n", $html);
	}
	
	function display_buddy_list_header()
	{
		$html = array();
		
		$html[] = '<div class="buddylist">';
		
		$html[] = '<div class="buddylist_header">';
		$html[] = '<img src="' . Theme :: get_image_path('admin') . 'place_mini_user.png" alt="user" />';
		$html[] = '<span class="title">' . Translation :: get('MyBuddies') . '</span>';
		$html[] = '</div>';
		
		$html[] = '<div class="buddylist_content">';
		$html[] = '<ul class="category_list">';
		
		return implode("\n", $html);
	}
	
	function display_buddy_list_category($category, $buddies)
	{
		$udm = UserDataManager :: get_instance();
		$html = array();
		
		$html[] = '<li class="category_list_item"><img class="category_toggle" src="' . Theme :: get_common_image_path() . 'treemenu/bullet_toggle_minus.png" />';
		$html[] = '<div class="buddy_list_item_text">';
		$html[] = '<span class="title">' . $category->get_title() . '</span></div>';
		
		$html[] = '<div class="buddy_list_item_actions" style="position: relative;">';
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->parent->get_url(),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_edit.png',
		);
		
		$toolbar_data[] = array(
			'href' => $this->parent->get_url(),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
			'confirm' => true
		);
		
		$html[] = DokeosUtilities :: build_toolbar($toolbar_data);
		$html[] = '</div><div class="clear">&nbsp;</div>';
		
		$html[] = '<ul class="buddy_list">';
				
		foreach($buddies as $buddy)
		{
			$user = $udm->retrieve_user($buddy->get_buddy_id());
			$html[] = '<li class="buddy_list_item"><img class="category_toggle" src="' . Theme :: get_common_image_path() . 'treemenu/user.png" />';
			$html[] = '<div class="buddy_list_item_text">' . $user->get_fullname() .'<span class="info">';
			
			switch($buddy->get_status())
			{
				case 1: $html[] = '(' . Translation :: get('Requested') . ')'; break;
				case 2: $html[] = '(' . Translation :: get('Rejected') . ')'; break;
			}
			
			$html[] = '</span></div>';
			
			$html[] = '<div class="buddy_list_item_actions">';
			$toolbar_data = array();

			$toolbar_data[] = array(
				'href' => $this->parent->get_url(),
				'label' => Translation :: get('DeleteUser'),
				'img' => Theme :: get_common_image_path().'action_unsubscribe.png',
				'confirm' => true
			);
			
			$html[] = DokeosUtilities :: build_toolbar($toolbar_data);
			$html[] = '</div>';
			
			$html[] = '<div class="clear">&nbsp;</div>';
			$html[] = '</li>';
		}
		
		$html[] = '</ul></li>';
		
		return implode("\n", $html);
	}
	
	function display_buddy_list_footer()
	{
		$html = array();
		
		$html[] = '</ul></div>';
		$html[] = '<div class="buddylist_footer">';
		
		$html[] = '<div class="buddylist_footer_actions">';
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->parent->get_url(),
			'label' => Translation :: get('AddCategory'),
			'img' => Theme :: get_common_image_path().'action_add.png'
		);
		
		$toolbar_data[] = array(
			'href' => $this->parent->get_url(),
			'label' => Translation :: get('AddUser'),
			'img' => Theme :: get_common_image_path().'action_subscribe.png'
		);
		
		$html[] = DokeosUtilities :: build_toolbar($toolbar_data);
		$html[] = '</div></div>';
		
		$html[] = '</div>';
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/buddy_list.js' .'"></script>';
		
		return implode("\n", $html);
	}
	
	/**
	 * Retrieves the categories for the buddy list
	 * @return ResultSet of categories
	 */
	function retrieve_buddy_list_categories()
	{
		$condition = new EqualityCondition(BuddyListCategory :: PROPERTY_USER_ID, $this->user->get_id());
		$udm = UserDataManager :: get_instance();
		return $udm->retrieve_buddy_list_categories($condition);
	}
	
	/**
	 * Retrieves the buddies for the buddy list
	 * @return Array of buddies with category as index
	 */
	function retrieve_buddies()
	{
		$condition = new EqualityCondition(BuddyListItem :: PROPERTY_USER_ID, $this->user->get_id());
		$udm = UserDataManager :: get_instance();
		$items = $udm->retrieve_buddy_list_items($condition, null, null, array(BuddyListItem :: PROPERTY_CATEGORY_ID), array(SORT_ASC));
											   
		while($item = $items->next_result())
		{
			$buddies[$item->get_category_id()][] = $item;
		}
		
		return $buddies;
	}
}

?>
