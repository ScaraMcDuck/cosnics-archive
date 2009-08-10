<?php
/**
 * @package reservations.lib.forms
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../reservations_data_manager.class.php';

class SubscriptionUserForm extends FormValidator 
{
	private $subscription;
	private $user;
	private $reservation;
	private $item;

    function SubscriptionUserForm($action, $subscription, $user, $reservation, $item) 
    {
    	parent :: __construct('subscription_user_form', 'post', $action);

		$this->subscription = $subscription;
		$this->user = $user;
		$this->item = $item;
		$this->reservation = $reservation;
		
		$this->build_form();
		$this->setDefaults();
    }

    function build_form()
    {
		$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">' . Translation :: get('SelectAdditionalUsers') . '</span>');
		
    	$userslist = UserDataManager :: get_instance()->retrieve_users();
    	while($user = $userslist->next_result())
    	{
    		if($user->get_id() != $this->user->get_id())
    			$users[$user->get_id()] = array('title' => $user->get_fullname(), 'description' => $user->get_fullname(), 'class' => 'user');
    	}
    	
    	$subscription = $this->subscription;
		
		$condition = new EqualityCondition(SubscriptionUser :: PROPERTY_SUBSCRIPTION_ID, $subscription->get_id());
		$selected_users = ReservationsDataManager :: get_instance()->retrieve_subscription_users($condition);
		while($selected_user = $selected_users->next_result())
		{
			$user = UserDataManager :: get_instance()->retrieve_user($selected_user->get_user_id());
			$user_list[$user->get_id()] = array('title' => $user->get_fullname(), 'description' => $user->get_fullname(), 'class' => 'user');
		}
    	
    	$url = Path :: get(WEB_PATH).'users/xml_user_feed.php';
    	
    	//$this->addElement('advmultiselect', 'users', Translation :: get('SelectUsers'), 
					//			  $users, array('style' => 'width:200px;'));
	
    	$locale = array ();
		$locale['Display'] = Translation :: get('AddUsers');
		$locale['Searching'] = Translation :: get('Searching');
		$locale['NoResults'] = Translation :: get('NoResults');
		$locale['Error'] = Translation :: get('Error');
		$hidden = true;
		
		$elem = $this->addElement('element_finder', 'users', null, $url, $locale, $user_list);
		$elem->setDefaults($users);
    	
    	$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
    	
    	// Submit button
		$this->addElement('submit', 'submit', 'OK');
    }

	function update_subscription_users()
	{
		$result = true;
		
		$subscription = $this->subscription;
		$export_users = $this->exportValue('users');
		
		$condition = new EqualityCondition(SubscriptionUser :: PROPERTY_SUBSCRIPTION_ID, $subscription->get_id());
		$users = ReservationsDataManager :: get_instance()->retrieve_subscription_users($condition);
		while($user = $users->next_result())
		{
			if(($search = array_search($user->get_user_id(), $export_users)) === false)
			{
				$user->delete();
			}
			else
			{
				unset($export_users[$search]);
			}
		}
	
		if($this->item->get_salto_id() != null && $this->item->get_salto_id() != 0)
		{
			require_once Path :: get_plugin_path() . 'nusoap/nusoap.php';
		}
	
		$udm = UserDataManager :: get_instance();
		$logger = Logger :: get_instance('webservices.txt');
		
		foreach($export_users as $user)
		{
			$subscription_user = new SubscriptionUser();
			$subscription_user->set_subscription_id($subscription->get_id());
			$subscription_user->set_user_id($user);
			
			$usr = $udm->retrieve_user($user);
			
			if($this->item->get_salto_id() != null && $this->item->get_salto_id() != 0)
			{
				$maakreservatieresult = $client->call('MaakReservatie', array(
				'sExtUserID' => $usr->get_official_code(), 
				'sExtDoorID' => $this->item->get_salto_id(), 
				'sTimezoneTableID' => "1"));
				
				$res = $maakreservatieresult['MaakReservatieResult'];
			
				$logger->write('Webservice MaakReservatie called (UserID: ' . $usr->get_official_code() .
						   ', DoorID: ' . $this->item->get_salto_id() . ', TimeZone: ' . "1" . ') Result: ' .
						   $res);
			
				if($res != $usr->get_official_code())
					continue;
			}
			
			$result &= $subscription_user->create();
		}
		
		Logger :: close_logs();
		
		return $result;
	}

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{

	}
}
?>