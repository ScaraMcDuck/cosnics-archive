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
		$this->addElement('text', PortfolioPublication :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(PortfolioPublication :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', PortfolioPublication :: PROPERTY_LEARNING_OBJECT, Translation :: get('LearningObject'));
		$this->addRule(PortfolioPublication :: PROPERTY_LEARNING_OBJECT, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', PortfolioPublication :: PROPERTY_FROM_DATE, Translation :: get('FromDate'));
		$this->addRule(PortfolioPublication :: PROPERTY_FROM_DATE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', PortfolioPublication :: PROPERTY_TO_DATE, Translation :: get('ToDate'));
		$this->addRule(PortfolioPublication :: PROPERTY_TO_DATE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', PortfolioPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
		$this->addRule(PortfolioPublication :: PROPERTY_HIDDEN, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', PortfolioPublication :: PROPERTY_PUBLISHER, Translation :: get('Publisher'));
		$this->addRule(PortfolioPublication :: PROPERTY_PUBLISHER, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', PortfolioPublication :: PROPERTY_PUBLISHED, Translation :: get('Published'));
		$this->addRule(PortfolioPublication :: PROPERTY_PUBLISHED, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', PortfolioPublication :: PROPERTY_ID);

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

    function create_portfolio_publication()
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

   		return $portfolio_publication->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$portfolio_publication = $this->portfolio_publication;

    	$defaults[PortfolioPublication :: PROPERTY_ID] = $portfolio_publication->get_id();
    	$defaults[PortfolioPublication :: PROPERTY_LEARNING_OBJECT] = $portfolio_publication->get_learning_object();
    	$defaults[PortfolioPublication :: PROPERTY_FROM_DATE] = $portfolio_publication->get_from_date();
    	$defaults[PortfolioPublication :: PROPERTY_TO_DATE] = $portfolio_publication->get_to_date();
    	$defaults[PortfolioPublication :: PROPERTY_HIDDEN] = $portfolio_publication->get_hidden();
    	$defaults[PortfolioPublication :: PROPERTY_PUBLISHER] = $portfolio_publication->get_publisher();
    	$defaults[PortfolioPublication :: PROPERTY_PUBLISHED] = $portfolio_publication->get_published();

		parent :: setDefaults($defaults);
	}
}
?>