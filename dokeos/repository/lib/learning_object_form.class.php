<?php
/**
 * $Id$
 * @package repository
 * 
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
require_once dirname(__FILE__).'/repository_data_manager.class.php';
require_once dirname(__FILE__).'/quota_manager.class.php';
require_once dirname(__FILE__).'/learning_object_category_menu.class.php';
require_once dirname(__FILE__).'/learning_object.class.php';
require_once dirname(__FILE__).'/abstract_learning_object.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_library_path() . 'html/menu/options_menu_renderer.class.php';
/**
 * A form to create and edit a LearningObject.
 */
abstract class LearningObjectForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const TYPE_COMPARE = 3;
	const TYPE_REPLY = 4;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';

	private $owner_id;

	/**
	 * The learning object.
	 */
	private $learning_object;

	/**
	 * Any extra information passed to the form.
	 */
	private $extra;
	
	protected $form_type;

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
	protected function __construct($form_type, $learning_object, $form_name, $method = 'post', 
						$action = null, $extra = null)
	{
		parent :: __construct($form_name, $method, $action);
		$this->form_type = $form_type;
		$this->learning_object = $learning_object;
		$this->owner_id = $learning_object->get_owner_id();
		$this->extra = $extra;
		if ($this->form_type == self :: TYPE_EDIT || $this->form_type == self :: TYPE_REPLY)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}
		elseif ($this->form_type == self :: TYPE_COMPARE)
		{
			$this->build_version_compare_form();
		}
		if ($this->form_type != self :: TYPE_COMPARE)
		{
			$this->add_progress_bar(2);
			$this->add_footer();
		}
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
	
	protected function get_learning_object_type()
	{
		return $this->learning_object->get_type();
	}
	
	protected function get_learning_object_class()
	{
		return DokeosUtilities :: underscores_to_camelcase($this->get_learning_object_type());
	}

	/**
	 * Sets the learning object associated with this form.
	 * @param LearningObject $learning_object The learning object.
	 */
	protected function set_learning_object($learning_object)
	{
		$this->learning_object = $learning_object;
	}
	
	protected function get_form_type() {
		return $this->form_type;
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
		$this->addElement('category', true, Translation :: get('GeneralProperties'));
		$this->build_basic_form();
		$this->addElement('category');
	}

	/**
	 * Builds a form to edit a learning object.
	 */
	protected function build_editing_form()
	{
		$object = $this->learning_object;
		$owner = UserDataManager :: get_instance()->retrieve_user($this->get_owner_id());
		$quotamanager = new QuotaManager($owner);
		
		$this->addElement('category', true, Translation :: get('GeneralProperties'));
		$this->build_basic_form();
		if($object->is_versionable())
		{
			if ($object->get_version_count() < $quotamanager->get_max_versions($object->get_type()))
			{
				$this->add_element_hider('script_block');
				$this->addElement('checkbox','version', Translation :: get('CreateAsNewVersion'), null, 'onclick="javascript:showElement(\''. LearningObject :: PROPERTY_COMMENT .'\')"');
				$this->add_element_hider('begin', LearningObject :: PROPERTY_COMMENT);
				$this->addElement('text', LearningObject :: PROPERTY_COMMENT, Translation :: get('VersionComment'));
				$this->add_element_hider('end', LearningObject :: PROPERTY_COMMENT);
			}
			else
			{
				$this->add_warning_message(null, Translation :: get('VersionQuotaExceeded'));
			}
		}
		$this->addElement('hidden', LearningObject :: PROPERTY_ID);
		$this->addElement('category');
	}

	/**
	 * Builds a form to compare learning object versions.
	 */
	private function build_version_compare_form()
	{
		$renderer = $this->defaultRenderer();
		$form_template = <<<EOT

<form {attributes}>
{content}
	<div class="clear">
		&nbsp;
	</div>
</form>

EOT;
		$renderer->setFormTemplate($form_template);
		$element_template = <<<EOT
	<div>
			<!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}
	</div>

EOT;
		$renderer->setElementTemplate($element_template);

		if (isset($this->extra['version_data']))
		{
			$object = $this->learning_object;

			if ($object->is_latest_version())
			{
				$html[] = '<div class="versions" style="margin-top: 1em;">';
			}
			else
			{
				$html[] = '<div class="versions_na" style="margin-top: 1em;">';
			}

			$html[] = '<div class="versions_title">'.htmlentities(Translation :: get('Versions')).'</div>';

			$this->addElement('html', implode("\n", $html));
			$this->add_element_hider('script_radio', $object);

			$i = 0;

			$radios = array();

			foreach ($this->extra['version_data'] as $version)
			{
				$versions = array();
				$versions[] =& $this->createElement('static', null, null, '<span '. ($i == ($object->get_version_count() - 1) ? 'style="visibility: hidden;"' : 'style="visibility: visible;"') .' id="A'. $i .'">');
				$versions[] =& $this->createElement('radio','object',null,null, $version['id'], 'onclick="javascript:showRadio(\'B\',\''. $i .'\')"');
				$versions[] =& $this->createElement('static', null, null, '</span>');
				$versions[] =& $this->createElement('static', null, null, '<span '. ($i == 0 ? 'style="visibility: hidden;"' : 'style="visibility: visible;"') .' id="B'. $i .'">');
				$versions[] =& $this->createElement('radio','compare',null,null, $version['id'], 'onclick="javascript:showRadio(\'A\',\''. $i .'\')"');
				$versions[] =& $this->createElement('static', null, null, '</span>');
				$versions[] =& $this->createElement('static', null, null, $version['html']);

				$this->addGroup($versions, null, null, "\n");
				$i++;
			}
			
			$this->addElement('submit', 'submit', Translation :: get('CompareVersions'));
			$this->addElement('html', '</div>');
		}
	}

	/**
	 * Builds a form to create or edit a learning object. Creates fields for
	 * default learning object properties. The result of this function is equal
	 * to build_creation_form()'s, but that one may be overridden to extend the
	 * form.
	 */
	private function build_basic_form()
	{
		//$this->add_textfield(LearningObject :: PROPERTY_TITLE, Translation :: get('Title'), true, 'size="100" style="width: 100%"');
		$this->add_textfield(LearningObject :: PROPERTY_TITLE, Translation :: get('Title'));
		if ($this->allows_category_selection())
		{
			$select = $this->add_select(LearningObject :: PROPERTY_PARENT_ID, Translation :: get('CategoryTypeName'), $this->get_categories());
			$select->setSelected($this->learning_object->get_parent_id());
		}
		$value = PlatformSetting :: get('description_required');
		$required = ($value == 'true')?true:false;
		$this->add_html_editor(LearningObject :: PROPERTY_DESCRIPTION, Translation :: get('Description'), $required);
	}

	/**
	 * Adds a footer to the form, including a submit button.
	 */
	protected function add_footer()
	{
		$object = $this->learning_object;
		//$elem = $this->addElement('advmultiselect', 'ihsTest', 'Hierarchical select:', array("test"), array('style' => 'width: 20em;'), '<br />'); 

		if ($this->supports_attachments())
		{
			
			if ($this->form_type != self :: TYPE_REPLY)
			{
				$attached_objects = $object->get_attached_learning_objects();
				//$attachments = DokeosUtilities :: learning_objects_for_element_finder($attached_objects);
			}
			else
			{
				$attachments = array();
			}
			
			$los = RepositoryDataManager :: get_instance()->retrieve_learning_objects(null, new EqualityCondition('owner', $this->owner_id));
			while($lo = $los->next_result())
			{
				$defaults[$lo->get_id()] = array('title' => $lo->get_title(), 'description', $lo->get_description(), 'class' => $lo->get_type());
			}
			
			$url = $this->get_path(WEB_PATH).'repository/xml_feed.php';
			$locale = array ();
			$locale['Display'] = Translation :: get('AddAttachments');
			$locale['Searching'] = Translation :: get('Searching');
			$locale['NoResults'] = Translation :: get('NoResults');
			$locale['Error'] = Translation :: get('Error');
			$hidden = true;
			
			$this->addElement('category', true, Translation :: get('Attachments'));
			$elem = $this->addElement('element_finder', 'attachments', null, $url, $locale, $attachments);
			$this->addElement('category');
			
			$elem->setDefaults($defaults);
			
			if ($id = $object->get_id())
			{
				$elem->excludeElements(array($object->get_id()));
			}
			$elem->setDefaultCollapsed(count($attachments) == 0);
		}
		
		$this->addElement('submit', 'submit', Translation :: get('Ok'));
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
	
		if ($this->form_type == self :: TYPE_REPLY)
		{
			$defaults[LearningObject :: PROPERTY_TITLE] = Translation :: get('ReplyShort'). ' ' . $lo->get_title();
		}
		else
		{			
			$defaults[LearningObject :: PROPERTY_TITLE] = $lo->get_title();
			$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $lo->get_description();
		}
		parent :: setDefaults($defaults);
	}
	function set_values($defaults)
	{
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

	function compare_learning_object()
	{
		$values = $this->exportValues();
		$ids = array();
		$ids['object'] = $values['object'];
		$ids['compare'] = $values['compare'];
		return $ids;
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
			$object->set_comment(nl2br($values[LearningObject :: PROPERTY_COMMENT]));
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
	
	function is_version()
	{
		$values = $this->exportValues();
		return (isset($values['version']) && $values['version'] == 1);
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
		$owner = UserDataManager :: get_instance()->retrieve_user($this->get_owner_id());
		$quotamanager = new QuotaManager($owner);
		if ($this->form_type == self :: TYPE_CREATE && $quotamanager->get_available_database_space() <= 0)
		{
			Display :: display_warning_message(htmlentities(Translation :: get('MaxNumberOfLearningObjectsReached')));
		}
		else
		{
			parent :: display();
		}
	}

	private function allows_category_selection()
	{
		$lo = $this->learning_object;
		return ($this->form_type == self :: TYPE_CREATE || $this->form_type == self :: TYPE_REPLY || $lo->get_parent_id());
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
	static function factory($form_type, $learning_object, $form_name, $method = 'post', $action = null, $extra = null, $allow_create_complex = false)
	{
		$type = $learning_object->get_type();
		$class = LearningObject :: type_to_class($type).'Form';
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_form.class.php';
		return new $class ($form_type, $learning_object, $form_name, $method, $action, $extra, $allow_create_complex);
	}
	/**
	 * Validates this form
	 * @see FormValidator::validate
	 */
	function validate()
	{
		if($this->isSubmitted() && $this->form_type == self :: TYPE_COMPARE)
		{
			$values = $this->exportValues();
			if(!isset($values['object']) || !isset($values['compare']))
			{
				return false;
			}
		}
		
		return parent :: validate();
	}
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
}
?>
