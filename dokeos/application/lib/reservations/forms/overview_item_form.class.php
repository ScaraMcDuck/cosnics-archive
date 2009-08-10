<?php
/**
 * @package reservations.lib.forms
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../reservations_data_manager.class.php';

class OverviewItemForm extends FormValidator 
{
	private $user;
	
    function OverviewItemForm($action, $user) 
    {
    	parent :: __construct('overview_item', 'post', $action);
		$this->user = $user;
		$this->build_form();
    }

    function build_form()
    {
		$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">' . Translation :: get('SelectItems') . '</span>');
    	
    	$dm = ReservationsDataManager :: get_instance();
    	
		$condition = new EqualityCondition(OverviewItem :: PROPERTY_USER_ID, $this->user->get_id());
		$overview_items = $dm->retrieve_overview_items($condition);
		while($overview_item = $overview_items->next_result())
		{
			$item = $dm->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $overview_item->get_item_id()))->next_result();
			$item_list[$item->get_id()] = array('title' => $item->get_name(), 'description' => $item->get_name(), 'class' => 'type type_group');
		}
    	
    	$url = Path :: get(WEB_PATH).'reservations/item_xml_feed.php';
	
    	$locale = array ();
		$locale['Display'] = Translation :: get('AddItems');
		$locale['Searching'] = Translation :: get('Searching');
		$locale['NoResults'] = Translation :: get('NoResults');
		$locale['Error'] = Translation :: get('Error');
		$hidden = true;
		
		$elem = $this->addElement('element_finder', 'items', null, $url, $locale, $item_list);
    	
    	$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
    	
    	// Submit button
		$this->addElement('submit', 'submit', 'OK');
    }

	function update_overview()
	{
		$values = $this->exportValues();
		$dm = ReservationsDataManager :: get_instance();
		
		$dm->empty_overview_for_user($this->user->get_id());
		
		$succes = true;
		
		foreach($values['items'] as $item)
		{
			$overview_item = new OverviewItem();
			$overview_item->set_user_id($this->user->get_id());
			$overview_item->set_item_id($item);
			$succes &= $overview_item->create();
		}
		
		return $succes;
	}
}
?>