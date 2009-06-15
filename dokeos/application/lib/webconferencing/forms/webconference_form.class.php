<?php
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../webconference.class.php';
require_once dirname(__FILE__) . '/../webconference_option.class.php';

/**
 * This class describes the form for a Webconference object.
 * @author Stefaan Vanbillemont
 **/
class WebconferenceForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $webconference;
	private $user;

    function WebconferenceForm($form_type, $webconference, $action, $user)
    {
    	parent :: __construct('webconference_settings', 'post', $action);

    	$this->webconference = $webconference;
    	$this->user = $user;
		$this->form_type = $form_type;

		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}

		$this->setDefaults();
    }

    function build_basic_form()
    {
		$this->addElement('text', Webconference :: PROPERTY_CONFNAME, Translation :: get('Confname'));
		$this->addRule(Webconference :: PROPERTY_CONFNAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Webconference :: PROPERTY_DURATION, Translation :: get('DurationInMinutes'));
		$this->addRule(Webconference :: PROPERTY_DURATION, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addRule(Webconference :: PROPERTY_DURATION, Translation :: get('ValueShouldBeNumeric'), 'numeric');
		
		$this->add_html_editor('option[agenda]', Translation :: get('Agenda'),false);
		$network_options = array('L' => Translation :: get('Low'),'M' => Translation :: get('Medium'), 'H' => Translation :: get('High'));
		$this->addElement('select', 'option[network]', Translation :: get('NetworkQuality'),$network_options);
		$mike_options = array(1,2,3,4,5);
		$this->addElement('select', 'option[mikes]', Translation :: get('Mikes'),$mike_options);
		$this->addElement('text', 'option[moderatorPassCode]', Translation :: get('ModeratorPassCode'));
		$this->addElement('text', 'option[attendeePassCode]', Translation :: get('AttendeePassCode'));
		$this->addElement('text', 'option[presenterPwd]', Translation :: get('PresenterPwd'));
		$this->addElement('text', 'option[attendeePwd]', Translation :: get('AttendeePwd'));
		$audio_video_options = array('A' => Translation :: get('Audio'),'X' => Translation :: get('VideoOnly'),'V' => Translation :: get('AudioVideoAllowed'),'D' => Translation :: get('AudioVideoDisabled'));
		$this->addElement('select', 'option[audioVideo]', Translation :: get('AudioVideo'),$audio_video_options);
		
		$yes_no_items = array('waitingarea','featureWhiteboard','featurePublisher','featurePrivateChat','featurePublicChat','featureDocShare','featureCobShare','featureRecording','feedback','participantList','AssignMikeOnJoin','HandsFreeOnLoad','allowAttendeeInvites');
		foreach($yes_no_items as $yes_no_item)
		{
			$group = array();
			$group[] =& $this->createElement('radio', $yes_no_item, null,Translation :: get('Yes'),1);
			$group[] =& $this->createElement('radio', $yes_no_item, null,Translation :: get('No'),0);
			$this->addGroup($group, 'option', Translation :: get('Option' . DokeosUtilities :: underscores_to_camelcase($yes_no_item)), '&nbsp;');
			$defaults['option[' . $yes_no_item . ']'] = 1;
		}

		$this->setDefaults($defaults);
    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', Webconference :: PROPERTY_ID);

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_webconference()
    {
    	$webconference = $this->webconference;
    	$values = $this->exportValues();

    	$webconference->set_id($values[Webconference :: PROPERTY_ID]);
    	$webconference->set_confkey('test');
    	$webconference->set_confname($values[Webconference :: PROPERTY_CONFNAME]);
    	$webconference->set_duration($values[Webconference :: PROPERTY_DURATION]);

    	//delete all webconference_options
    	
    	WebconferencingDataManager :: get_instance()->delete_webconference_options($webconference);
    	
    	foreach($options as $name => $value)
    	{
    		if(!$value || !$name) continue;
    		
    		$webconference_item = new WebconferenceOption();
    		$webconference_item->set_conf_id($webconference->get_id());
    		$webconference_item->set_name($name);
    		$webconference_item->set_value($value);
    		$webconference_item->create(); 
    	}
    	
		return $webconference->update();
    }

    function create_webconference()
    {
    	$webconference = $this->webconference;
    	$values = $this->exportValues();

    	$webconference->set_confkey('test');
    	$webconference->set_confname($values[Webconference :: PROPERTY_CONFNAME]);
    	$webconference->set_duration($values[Webconference :: PROPERTY_DURATION]);
		$webconference->create();
    	
		$options = $values['option'];
		
    	foreach($options as $name => $value)
    	{
    		if(!$value || !$name) continue;
    		
    		$webconference_item = new WebconferenceOption();
    		$webconference_item->set_conf_id($webconference->get_id());
    		$webconference_item->set_name($name);
    		$webconference_item->set_value($value);
    		$webconference_item->create(); 
    	}
   	
   		return true;
    }

    function remove_XSS_recursive()
    {
    	
    }
    
	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$webconference = $this->webconference;

    	$defaults[Webconference :: PROPERTY_ID] = $webconference->get_id();
    	//$defaults[Webconference :: PROPERTY_CONFKEY] = $webconference->get_confkey();
    	$defaults[Webconference :: PROPERTY_CONFNAME] = $webconference->get_confname();
    	$defaults[Webconference :: PROPERTY_DURATION] = $webconference->get_duration();

    	//loop all webconference_options and place them in defaults
    	/*
    	if($webconference)
    	{
    		//loop all webconference_options and place them in defaults
    		$wdm = WebconferencingDataManager :: get_instance();
    		$options = $wdm->->retrieve_webconference_options($webconference->get_id());
    		foreach($options as $name => $value)
    		{
	    		$defaults['option['. $name . ']'] = $value;
    		}
    	}
		*/
		parent :: setDefaults($defaults);
	}
}
?>