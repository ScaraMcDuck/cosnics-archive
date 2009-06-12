<?php
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../wiki_publication.class.php';

/**
 * This class describes the form for a WikiPublication object.
 * @author Sven Vanpoucke & Stefan Billiet
 **/
class WikiPublicationForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $wiki_publication;
	private $user;

    function WikiPublicationForm($form_type, $wiki_publication, $action, $user)
    {
    	parent :: __construct('wiki_publication_settings', 'post', $action);

    	$this->wiki_publication = $wiki_publication;
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
		$this->addElement('text', WikiPublication :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(WikiPublication :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_LEARNING_OBJECT, Translation :: get('LearningObject'));
		$this->addRule(WikiPublication :: PROPERTY_LEARNING_OBJECT, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_PARENT_ID, Translation :: get('ParentId'));
		$this->addRule(WikiPublication :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_CATEGORY, Translation :: get('Category'));
		$this->addRule(WikiPublication :: PROPERTY_CATEGORY, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_FROM_DATE, Translation :: get('FromDate'));
		$this->addRule(WikiPublication :: PROPERTY_FROM_DATE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_TO_DATE, Translation :: get('ToDate'));
		$this->addRule(WikiPublication :: PROPERTY_TO_DATE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
		$this->addRule(WikiPublication :: PROPERTY_HIDDEN, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_PUBLISHER, Translation :: get('Publisher'));
		$this->addRule(WikiPublication :: PROPERTY_PUBLISHER, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_PUBLISHED, Translation :: get('Published'));
		$this->addRule(WikiPublication :: PROPERTY_PUBLISHED, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_MODIFIED, Translation :: get('Modified'));
		$this->addRule(WikiPublication :: PROPERTY_MODIFIED, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_DISPLAY_ORDER, Translation :: get('DisplayOrder'));
		$this->addRule(WikiPublication :: PROPERTY_DISPLAY_ORDER, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', WikiPublication :: PROPERTY_EMAIL_SENT, Translation :: get('EmailSent'));
		$this->addRule(WikiPublication :: PROPERTY_EMAIL_SENT, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', WikiPublication :: PROPERTY_ID);

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

    function update_wiki_publication()
    {
    	$wiki_publication = $this->wiki_publication;
    	$values = $this->exportValues();

    	$wiki_publication->set_id($values[WikiPublication :: PROPERTY_ID]);
    	$wiki_publication->set_learning_object($values[WikiPublication :: PROPERTY_LEARNING_OBJECT]);
    	$wiki_publication->set_parent_id($values[WikiPublication :: PROPERTY_PARENT_ID]);
    	$wiki_publication->set_category($values[WikiPublication :: PROPERTY_CATEGORY]);
    	$wiki_publication->set_from_date($values[WikiPublication :: PROPERTY_FROM_DATE]);
    	$wiki_publication->set_to_date($values[WikiPublication :: PROPERTY_TO_DATE]);
    	$wiki_publication->set_hidden($values[WikiPublication :: PROPERTY_HIDDEN]);
    	$wiki_publication->set_publisher($values[WikiPublication :: PROPERTY_PUBLISHER]);
    	$wiki_publication->set_published($values[WikiPublication :: PROPERTY_PUBLISHED]);
    	$wiki_publication->set_modified($values[WikiPublication :: PROPERTY_MODIFIED]);
    	$wiki_publication->set_display_order($values[WikiPublication :: PROPERTY_DISPLAY_ORDER]);
    	$wiki_publication->set_email_sent($values[WikiPublication :: PROPERTY_EMAIL_SENT]);

    	return $wiki_publication->update();
    }

    function create_wiki_publication()
    {
    	$wiki_publication = $this->wiki_publication;
    	$values = $this->exportValues();

    	$wiki_publication->set_id($values[WikiPublication :: PROPERTY_ID]);
    	$wiki_publication->set_learning_object($values[WikiPublication :: PROPERTY_LEARNING_OBJECT]);
    	$wiki_publication->set_parent_id($values[WikiPublication :: PROPERTY_PARENT_ID]);
    	$wiki_publication->set_category($values[WikiPublication :: PROPERTY_CATEGORY]);
    	$wiki_publication->set_from_date($values[WikiPublication :: PROPERTY_FROM_DATE]);
    	$wiki_publication->set_to_date($values[WikiPublication :: PROPERTY_TO_DATE]);
    	$wiki_publication->set_hidden($values[WikiPublication :: PROPERTY_HIDDEN]);
    	$wiki_publication->set_publisher($values[WikiPublication :: PROPERTY_PUBLISHER]);
    	$wiki_publication->set_published($values[WikiPublication :: PROPERTY_PUBLISHED]);
    	$wiki_publication->set_modified($values[WikiPublication :: PROPERTY_MODIFIED]);
    	$wiki_publication->set_display_order($values[WikiPublication :: PROPERTY_DISPLAY_ORDER]);
    	$wiki_publication->set_email_sent($values[WikiPublication :: PROPERTY_EMAIL_SENT]);

   		return $wiki_publication->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$wiki_publication = $this->wiki_publication;

    	$defaults[WikiPublication :: PROPERTY_ID] = $wiki_publication->get_id();
    	$defaults[WikiPublication :: PROPERTY_LEARNING_OBJECT] = $wiki_publication->get_learning_object();
    	$defaults[WikiPublication :: PROPERTY_PARENT_ID] = $wiki_publication->get_parent_id();
    	$defaults[WikiPublication :: PROPERTY_CATEGORY] = $wiki_publication->get_category();
    	$defaults[WikiPublication :: PROPERTY_FROM_DATE] = $wiki_publication->get_from_date();
    	$defaults[WikiPublication :: PROPERTY_TO_DATE] = $wiki_publication->get_to_date();
    	$defaults[WikiPublication :: PROPERTY_HIDDEN] = $wiki_publication->get_hidden();
    	$defaults[WikiPublication :: PROPERTY_PUBLISHER] = $wiki_publication->get_publisher();
    	$defaults[WikiPublication :: PROPERTY_PUBLISHED] = $wiki_publication->get_published();
    	$defaults[WikiPublication :: PROPERTY_MODIFIED] = $wiki_publication->get_modified();
    	$defaults[WikiPublication :: PROPERTY_DISPLAY_ORDER] = $wiki_publication->get_display_order();
    	$defaults[WikiPublication :: PROPERTY_EMAIL_SENT] = $wiki_publication->get_email_sent();

		parent :: setDefaults($defaults);
	}
}
?>