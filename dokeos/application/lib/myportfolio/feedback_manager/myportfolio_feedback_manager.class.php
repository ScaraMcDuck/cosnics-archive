<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path(). 'category_manager/category_manager.class.php';
require_once dirname(__FILE__) . '/../profiler_data_manager.class.php';
require_once dirname(__FILE__) . '/profiler_category.class.php';

class MyportfolioFeedbackManager extends FeedbackManager
{
	private $trail;
	
	function MyportfolioFeedbackManager($parent, $trail)
	{
		parent :: __construct($parent, $trail);
		$this->trail = $trail;
	}

	function get_feedback()
	{
		return new MyportfolioFeedback();
	}
	
	
	function retrieve_feedbacks($condition, $offset, $count, $order_property, $order_direction)
	{
		$wdm = PortfolioDataManager :: get_instance();
		return $wdm->retrieve_feedbacks($condition, $offset, $count, $order_property, $order_direction);
	}
	
	
	function get_breadcrumb_trail()
	{
		return $this->trail;
	}
}
?>