<?php
/**
 * @package reservation.lib.reservation_manager.component
 */
require_once dirname(__FILE__).'/../category_manager.class.php';
require_once dirname(__FILE__).'/../category_manager_component.class.php';
require_once dirname(__FILE__).'/category_browser/category_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class CategoryManagerBrowserComponent extends CategoryManagerComponent
{
	private $ab;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$this->ab = new ActionBarRenderer($this->get_left_toolbar_data(), array(), $this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $this->get_category())));	
		echo $this->ab->as_html() . '<br />';
		echo $this->get_user_html();
	}
	
	function get_user_html()
	{		
		$table = new CategoryBrowserTable($this, array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $this->get_category()), $this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width: 70%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		$cat_id = $this->get_category();
		$condition = new EqualityCondition(Category :: PROPERTY_PARENT, $cat_id);
		
		$search = $this->ab->get_query();
		if(isset($search) && ($search != ''))
		{
			$conditions = array();
			$conditions[] = new LikeCondition(Category :: PROPERTY_NAME, $search);
			$orcondition = new OrCondition($conditions);
			
			$conditions = array();
			$conditions[] = $orcondition;
			$conditions[] = $condition;
			$condition = new AndCondition($conditions);
		}
		return $condition;
	}
	
	function get_category()
	{
		return (isset($_GET[CategoryManager :: PARAM_CATEGORY_ID])?$_GET[CategoryManager :: PARAM_CATEGORY_ID]:0);
	}
	
	function get_left_toolbar_data()
	{
		$tb_data = array();
		
		$tb_data[] = array(
				'href' => $this->get_create_category_url($_GET[CategoryManager :: PARAM_CATEGORY_ID]),
				'label' => Translation :: get('Add'),
				'img' => Theme :: get_common_img_path() . 'action_add.png'
		);
		
		return $tb_data;
	}
}