<?php
/**
 * $Id$
 * @package repository
 */

require_once dirname(__FILE__).'/../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/quotamanager.class.php';
require_once dirname(__FILE__).'/learningobjectcategorymenu.class.php';
require_once dirname(__FILE__).'/learningobject.class.php';
require_once dirname(__FILE__).'/abstractlearningobject.class.php';
require_once dirname(__FILE__).'/repositoryutilities.class.php';
require_once dirname(__FILE__).'/optionsmenurenderer.class.php';
/**
 * A form to create and edit a LearningObject.
 */
abstract class LearningObjectForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';

	private $owner_id;

	/**
	 * The learning object.
	 */
	private $learning_object;

	/**
	 * Constructor.
	 * @param int $form_type The form type; either
	 *                       LearningObjectForm :: TYPE_CREATE or
	 *                       LearningObjectForm :: TYPE_EDIT.
	 * @param LearningObject $learning_object The object to create or update.
	 *                                        May be an AbstractLearningObject
	 *                                        upon creation.
	 * @param string $form_name The name to use in the form tag.
	 * @param string $method The method to use ('post' or 'get').
	 * @param string $action The URL to which the form should be submitted.
	 */
	function LearningObjectForm($form_type, $learning_object, $form_name, $method = 'post', $action = null)
	{
		parent :: __construct($form_name, $method, $action);
		$this->form_type = $form_type;
		$this->learning_object = $learning_object;
		$this->owner_id = $learning_object->get_owner_id();
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		else
		{
			$this->form_type = self :: TYPE_CREATE;
			$this->build_creation_form();
		}
		$this->add_progress_bar(2);
		$this->add_footer();
		$this->setDefaults();
	}

	/**
	 * Returns the ID of the owner of the learning object being created or
	 * edited.
	 * @return int The ID.
	 */
	protected function get_owner_id()
	{
		return $this->owner_id;
	}

	/**
	 * Returns the learning object associated with this form.
	 * @return LearningObject The learning object, or null if none.
	 */
	protected function get_learning_object()
	{
		/*
		 * For creation forms, $this->learning_object is the default learning
		 * object and therefore may be abstract. In this case, we do not
		 * return it.
		 * For this reason, methods of this class itself will want to access
		 * $this->learning_object directly, so as to take both the learning
		 * object that is being updated and the default learning object into
		 * account.
		 */
		if ($this->learning_object instanceof AbstractLearningObject)
		{
			return null;
		}
		return $this->learning_object;
	}

	/**
	 * Sets the learning object associated with this form.
	 * @param LearningObject $learning_object The learning object.
	 */
	protected function set_learning_object($learning_object)
	{
		$this->learning_object = $learning_object;
	}

	/**
	 * Gets the categories defined in the user's repository.
	 * @return array The categories.
	 */
	function get_categories()
	{
		$categorymenu = new LearningObjectCategoryMenu($this->get_owner_id());
		$renderer = new OptionsMenuRenderer();
		$categorymenu->render($renderer, 'sitemap');
		return $renderer->toArray();
	}

	/**
	 * Builds a form to create a new learning object.
	 */
	protected function build_creation_form()
	{
		$this->build_basic_form();
	}

	/**
	 * Builds a form to edit a learning object.
	 */
	protected function build_editing_form()
	{
		$object = $this->learning_object;
		$quotamanager = new QuotaManager($this->get_owner_id());
		$this->build_basic_form();
		if($object->is_versionable())
		{
			if ($object->get_version_count() < $quotamanager->get_max_versions())
			{
				$this->add_element_hider('script');
				$this->addElement('checkbox','version', get_lang('CreateAsNewVersion'), null, 'onclick="javascript:showElement(\''. LearningObject :: PROPERTY_COMMENT .'\')"');
				$this->add_element_hider('begin', LearningObject :: PROPERTY_COMMENT);
				$this->addElement('textarea', LearningObject :: PROPERTY_COMMENT, get_lang('VersionComment'));
				$this->add_element_hider('end', LearningObject :: PROPERTY_COMMENT);
			}
			else
			{
				$this->add_warning_message(null, get_lang('VersionQuotaExceeded'));
			}
		}
		$this->addElement('hidden', LearningObject :: PROPERTY_ID);
	}

	/**
	 * Builds a form to create or edit a learning object. Creates fields for
	 * default learning object properties. The result of this function is equal
	 * to build_creation_form()'s, but that one may be overridden to extend the
	 * form.
	 */
	private function build_basic_form()
	{
		//$this->add_textfield(LearningObject :: PROPERTY_TITLE, get_lang('Title'), true, 'size="100" style="width: 100%"');
		$this->addElement('text',LearningObject :: PROPERTY_TITLE, get_lang('Title'));
		$this->addRule(LearningObject :: PROPERTY_TITLE, get_lang('ThisFieldIsRequired'), 'required');
		$lo = $this->learning_object;
		if ($this->allows_category_selection())
		{
			$select = $this->addElement('select', LearningObject :: PROPERTY_PARENT_ID, get_lang('CategoryTypeName'), $this->get_categories());
			$select->setSelected($lo->get_parent_id());
			$this->addRule(LearningObject :: PROPERTY_PARENT_ID, get_lang('ThisFieldIsRequired'), 'required');
		}
		$this->add_html_editor(LearningObject :: PROPERTY_DESCRIPTION, get_lang('Description'));
	}

	/**
	 * Adds a footer to the form, including a submit button.
	 */
	private function add_footer()
	{
		if ($this->supports_attachments())
		{
			$object = $this->learning_object;
			$attached_objects = $object->get_attached_learning_objects();
			$attachments = RepositoryUtilities :: learning_objects_for_element_finder(& $attached_objects);
			$url = api_get_root_rel().'repository/xml_feed.php';
			$locale = array ();
			$locale['Display'] = get_lang('AddAttachments');
			$locale['Searching'] = get_lang('Searching');
			$locale['NoResults'] = get_lang('NoResults');
			$locale['Error'] = get_lang('Error');
			$hidden = true;
			$elem = $this->addElement('element_finder', 'attachments', get_lang('Attachments'), $url, $locale, $attachments);
			if ($id = $object->get_id())
			{
				$elem->excludeElements(array($object->get_id()));
			}
			$elem->setDefaultCollapsed(count($attachments) == 0);
		}
		$this->addElement('submit', 'submit', get_lang('Ok'));
	}

	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$lo = $this->learning_object;
		$defaults[LearningObject :: PROPERTY_ID] = $lo->get_id();
		$defaults[LearningObject :: PROPERTY_TITLE] = $lo->get_title();
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $lo->get_description();
		parent :: setDefaults($defaults);
	}

	/**
	 * Creates a learning object from the submitted form values. Traditionally,
	 * you override this method to ensure that the form's learning object is
	 * set to the object that is to be created, and call the super method.
	 * @return LearningObject The newly created learning object.
	 */
	function create_learning_object()
	{
		$values = $this->exportValues();
		$object = $this->learning_object;
		$object->set_owner_id($this->get_owner_id());
		$object->set_title($values[LearningObject :: PROPERTY_TITLE]);
		$object->set_description($values[LearningObject :: PROPERTY_DESCRIPTION]);
		if ($this->allows_category_selection())
		{
			$object->set_parent_id($values[LearningObject :: PROPERTY_PARENT_ID]);
		}
		if ($object->is_ordered() && !$object->get_display_order_index())
		{
			$dm = RepositoryDataManager :: get_instance();
			$dm->assign_learning_object_display_order_index($object);
		}
		$object->create();
		if ($object->supports_attachments())
		{
			foreach ($values['attachments'] as $aid)
			{
				$object->attach_learning_object($aid);
			}
		}
		return $object;
	}

	/**
	 * Updates a learning object with the submitted form values. Traditionally,
	 * you override this method to first set values for the necessary
	 * additional learning object properties, and then call the super method.
	 * @return boolean True if the update succeeded, false otherwise.
	 */
	function update_learning_object()
	{
		$object = $this->learning_object;
		$values = $this->exportValues();
		
		$object->set_title($values[LearningObject :: PROPERTY_TITLE]);
		$object->set_description($values[LearningObject :: PROPERTY_DESCRIPTION]);
		if ($this->allows_category_selection())
		{
			$parent = $values[LearningObject :: PROPERTY_PARENT_ID];
			if ($parent != $object->get_parent_id())
			{
				if ($object->move_allowed($parent))
				{
					$object->set_parent_id($parent);
				}
				else
				{
					/*
					 * TODO: Make this more meaningful, e.g. by returning error
					 * constants instead of booleans, like
					 * LearningObjectForm :: SUCCESS (not implemented).
					 */
					return self :: RESULT_ERROR;
				}
			}
		}
		
		if (isset($values['version']) && $values['version'] == 1)
		{
			$object->set_comment($values[LearningObject :: PROPERTY_COMMENT]);
			$result = $object->version();
		}
		else
		{
			$result = $object->update();
		}
		if ($object->supports_attachments())
		{
			/*
			 * XXX: Make this faster by providing a function that matches the
			 *      existing IDs against the ones that need to be added, and
			 *      attaches and detaches accordingly.
			 */
			foreach ($object->get_attached_learning_objects() as $o)
			{
				$object->detach_learning_object($o->get_id());
			}
			foreach ($values['attachments'] as $aid)
			{
				$object->attach_learning_object($aid);
			}
		}
		return $result;
	}

	/**
	 * Checks whether the learning object that is being created or edited may
	 * have learning objects attached to it.
	 * @return boolean True if attachments are supported, false otherwise.
	 */
	function supports_attachments()
	{
		$lo = $this->learning_object;
		return $lo->supports_attachments();
	}
	/**
	 * Displays the form
	 */
	function display()
	{
		$quotamanager = new QuotaManager($this->get_owner_id());
		if ($this->form_type == self :: TYPE_CREATE && $quotamanager->get_available_database_space() <= 0)
		{
			Display :: display_warning_message(htmlentities(get_lang('MaxNumberOfLearningObjectsReached')));
		}
		else
		{
			parent :: display();
		}
	}

	private function allows_category_selection()
	{
		$lo = $this->learning_object;
		return ($this->form_type == self :: TYPE_CREATE || $lo->get_parent_id());
	}

	/**
	 * Creates a form object to manage a learning object.
	 * @param int $form_type The form type; either
	 *                       LearningObjectForm :: TYPE_CREATE or
	 *                       LearningObjectForm :: TYPE_EDIT.
	 * @param LearningObject $learning_object The object to create or update.
	 *                                        May be an AbstractLearningObject
	 *                                        upon creation.
	 * @param string $form_name The name to use in the form tag.
	 * @param string $method The method to use ('post' or 'get').
	 * @param string $action The URL to which the form should be submitted.
	 */
	static function factory($form_type, $learning_object, $form_name, $method = 'post', $action = null)
	{
		$type = $learning_object->get_type();
		$class = LearningObject :: type_to_class($type).'Form';
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_form.class.php';
		return new $class ($form_type, $learning_object, $form_name, $method, $action);
	}
}
?>