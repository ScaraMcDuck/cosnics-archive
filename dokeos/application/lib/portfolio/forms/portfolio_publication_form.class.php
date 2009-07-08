<?php
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../portfolio_publication.class.php';

/**
 * This class describes the form for a PortfolioPublication object.
 * @author Sven Vanpoucke
 **/
class PortfolioPublicationForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $portfolio_publication;
	private $user;

    function PortfolioPublicationForm($form_type, $portfolio_publication, $action, $user)
    {
    	parent :: __construct('portfolio_publication_settings', 'post', $action);

    	$this->portfolio_publication = $portfolio_publication;
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
		$attributes = array();
		$attributes['search_url'] = Path :: get(WEB_PATH).'application/lib/portfolio/xml_feeds/xml_user_group_feed.php';
		$locale = array ();
		$locale['Display'] = Translation :: get('SelectRecipients');
		$locale['Searching'] = Translation :: get('Searching');
		$locale['NoResults'] = Translation :: get('NoResults');
		$locale['Error'] = Translation :: get('Error');
		$attributes['locale'] = $locale;
		$attributes['exclude'] = array('user_' . $this->user->get_id());
		$attributes['defaults'] = array();

		$this->add_receivers('target', Translation :: get('PublishFor'), $attributes);

		$this->add_forever_or_timewindow();
		$this->addElement('checkbox', PortfolioPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	$this->addElement('hidden', PortfolioPublication :: PROPERTY_ID);

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
		
     	$defaults = array();
        $defaults['target_option'] = 0;
        $defaults['forever'] = 1;
        parent :: setDefaults($defaults);
    }

    function update_portfolio_publication()
    {
    	$portfolio_publication = $this->portfolio_publication;
    	$values = $this->exportValues();

    	$portfolio_publication->set_id($values[PortfolioPublication :: PROPERTY_ID]);
    	$portfolio_publication->set_learning_object($values[PortfolioPublication :: PROPERTY_LEARNING_OBJECT]);
    	$portfolio_publication->set_from_date($values[PortfolioPublication :: PROPERTY_FROM_DATE]);
    	$portfolio_publication->set_to_date($values[PortfolioPublication :: PROPERTY_TO_DATE]);
    	$portfolio_publication->set_hidden($values[PortfolioPublication :: PROPERTY_HIDDEN]);
    	$portfolio_publication->set_publisher($values[PortfolioPublication :: PROPERTY_PUBLISHER]);
    	$portfolio_publication->set_published($values[PortfolioPublication :: PROPERTY_PUBLISHED]);

    	return $portfolio_publication->update();
    }

    function create_portfolio_publications($objects)
    {
    	$values = $this->exportValues();

    	//dump($values); exit();
    	
    	if($values['forever'] == 1)
    	{
    		$from = $to = 0;
    	}
    	else 
    	{
    		$from = DokeosUtilities :: time_from_datepicker($values['from_date']);
    		$to = DokeosUtilities :: time_from_datepicker($values['to_date']);
    	}
    	
    	$succes = true;
    	
    	foreach($objects as $object)
    	{
	    	$portfolio_publication = new PortfolioPublication();
    		$portfolio_publication->set_learning_object($object);
	    	$portfolio_publication->set_from_date($from);
	    	$portfolio_publication->set_to_date($to);
	    	$portfolio_publication->set_hidden($values[PortfolioPublication :: PROPERTY_HIDDEN]);
	    	$portfolio_publication->set_publisher($this->user->get_id());
	    	$portfolio_publication->set_published(time());
	    	$portfolio_publication->set_target_groups($values['target_elements']['group']);
	    	$portfolio_publication->set_target_users($values['target_elements']['user']);
	    	
	    	$succes &= $portfolio_publication->create();
    	}
    	
   		return $succes;
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$portfolio_publication = $this->portfolio_publication;

    	$defaults[PortfolioPublication :: PROPERTY_FROM_DATE] = $portfolio_publication->get_from_date();
    	$defaults[PortfolioPublication :: PROPERTY_TO_DATE] = $portfolio_publication->get_to_date();
    	$defaults[PortfolioPublication :: PROPERTY_HIDDEN] = $portfolio_publication->get_hidden();

		parent :: setDefaults($defaults);
	}
}
?>