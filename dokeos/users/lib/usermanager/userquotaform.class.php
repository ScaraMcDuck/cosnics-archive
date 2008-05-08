<?php
/**
 * @package users.lib.usermanager
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../userquota.class.php';
require_once Path :: get_repository_path(). 'lib/repositorydatamanager.class.php';
require_once Path :: get_repository_path(). 'lib/abstractlearningobject.class.php';


class UserQuotaForm extends FormValidator {
	
	const RESULT_SUCCESS = 'UserQuotaUpdated';
	const RESULT_ERROR = 'UserQuotaUpdateFailed';
	
	private $parent;
	private $user;
	private $rdm;
	private $learning_object_types;
	
	/**
	 * Creates a new UserQuotaForm
	 * Used to set the different quota limits for each learning object
	 */
    function UserQuotaForm($user, $action) {
    	parent :: __construct('quota_settings', 'post', $action);
    	
    	$this->user = $user;
    	$this->learning_object_types = $this->filter_learning_object_types();
    
		$this->build_editing_form();
		$this->setDefaults();
    }
    
    /**
     * Builds a basic form
     */
    function build_basic_form()
    {
    	foreach($this->learning_object_types as $type)
    	{
    		$this->addElement('text', $type, Translation :: get($type));
    		$this->addRule($type, Translation :: get('FieldMustBeNumeric'), 'numeric', null, 'server');
    	}
    	
		// Submit button
		$this->addElement('submit', 'quota_settings', 'OK');
    }
    
    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
    	$user = $this->user;
    	$parent = $this->parent;
    	
    	$this->build_basic_form();
    	
    	$this->addElement('hidden', User :: PROPERTY_USER_ID);
    }
    
    /**
     * Updates the quota
     */
    function update_quota()
    {
    	$user = $this->user;
    	$values = $this->exportValues();
    	$failures = 0;
    	foreach($this->learning_object_types as $type)
    	{
    		$userquota = new Userquota();
    		$userquota->set_learning_object_type($type);
    		$userquota->set_user_quota($values[$type]);
    		$userquota->set_user_id($user->get_user_id());
    		if ($values[$type] != '')
    		{
    			if (!$userquota->update())
    			{
    				$failures++;
    			}
    		}	
    	}
		if ($failures != 0)
		{
			return false;
		}
		else 
		{
			Events :: trigger_event('quota', 'users', array('target_user_id' => $user->get_user_id(), 'action_user_id' => $user->get_user_id()));
			return true;
		}
    
    }
    
	/**
	 * Sets default values. 
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$user = $this->user;
		$defaults[User :: PROPERTY_USER_ID] = $user->get_user_id();
		foreach ($this->learning_object_types as $type)
		{
			$defaults[$type] = $this->user->get_version_type_quota($type);
		}
		parent :: setDefaults($defaults);
	}
	
	/**
	 * Filters learning object types
	 */
	function filter_learning_object_types()
	{
		$user = $this->user;
		$rdm = RepositoryDataManager :: get_instance();
    	$learning_object_types = $rdm->get_registered_types();
    	$filtered_object_types = array();
    	
		foreach ($learning_object_types as $type)
		{
			$object = new AbstractLearningObject($type, $user->get_user_id());
			if ($object->is_versionable())
			{
				$filtered_object_types[] = $type;
			}
		}
    	
    	return $filtered_object_types;
	}
}
?>