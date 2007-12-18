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

require_once dirname(__FILE__).'/../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../users/lib/usersdatamanager.class.php';
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
	protected function __construct($form_type, $learning_object, $form_name, $method = 'post', $action = null, $extra = null)
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
		$owner = UsersDataManager :: get_instance()->retrieve_user($this->get_owner_id());
		$quotamanager = new QuotaManager($owner);
		$this->build_basic_form();
		if($object->is_versionable())
		{
			if ($object->get_version_count() < $quotamanager->get_max_versions($object->get_type()))
			{
				$this->add_element_hider('script_block');
				$this->addElement('checkbox','version', get_lang('CreateAsNewVersion'), null, 'onclick="javascript:showElement(\''. LearningObject :: PROPERTY_COMMENT .'\')"');
				$this->add_element_hider('begin', LearningObject :: PROPERTY_COMMENT);
				$this->addElement('text', LearningObject :: PROPERTY_COMMENT, get_lang('VersionComment'));
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
	 * Builds a form to compare learning object versions.
	 */
	private function build_version_compare_form()
	{
		$renderer = & $this->defaultRenderer();
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

			$html[] = '<div class="versions_title">'.htmlentities(get_lang('Versions')).'</div>';

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
			$this->addElement('submit', 'submit', get_lang('CompareVersions'));
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
		//$this->add_textfield(LearningObject :: PROPERTY_TITLE, get_lang('Title'), true, 'size="100" style="width: 100%"');
		$this->add_textfield(LearningObject :: PROPERTY_TITLE, get_lang('Title'));
		if ($this->allows_category_selection())
		{
			$select = $this->add_select(LearningObject :: PROPERTY_PARENT_ID, get_lang('CategoryTypeName'), $this->get_categories());
			$select->setSelected($this->learning_object->get_parent_id());
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
			if ($this->form_type != self :: TYPE_REPLY)
			{
				$attached_objects = $object->get_attached_learning_objects();
				$attachments = RepositoryUtilities :: learning_objects_for_element_finder(& $attached_objects);
			}
			else
			{
				$attachments = array();
			}
			$url = api_get_path(WEB_PATH).'repository/xml_feed.php';
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
	
		if ($this->form_type == self :: TYPE_REPLY)
		{
			$defaults[LearningObject :: PROPERTY_TITLE] = get_lang('ReplyShort'). ' ' . $lo->get_title();
		}
		else
		{			
			$defaults[LearningObject :: PROPERTY_TITLE] = $lo->get_title();
			$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $lo->get_description();
		}
		parent :: setDefaults($defaults);
	}
	function setValues($defaults)
	{
		parent :: setDefaults($defaults);
		echo 'setvalues gepasseerd<br />';
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
		$owner = UsersDataManager :: get_instance()->retrieve_user($this->get_owner_id());
		$quotamanager = new QuotaManager($owner);
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
	static function factory($form_type, $learning_object, $form_name, $method = 'post', $action = null, $extra = null)
	{
		$type = $learning_object->get_type();
		$class = LearningObject :: type_to_class($type).'Form';
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_form.class.php';
		return new $class ($form_type, $learning_object, $form_name, $method, $action, $extra);
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


	function validatecsv($value)
	{
		echo 'ik ben in validatecsv<br />';
		include_once('HTML/QuickForm/RuleRegistry.php');
		$registry =& HTML_QuickForm_RuleRegistry::singleton();
		$rulenr='-1';
		
			echo 'aantal regels is'.count($this->_rules);
		foreach ($this->_rules as $target => $rules) 
		{
			$rulenr++;
			//echo 'regelnummer ter check'.$rulenr'<br />'; 
			//echo 'waarde om te checken int begin   =   '.$submitValue;
		        $submitValue = $value[$rulenr];
		        foreach ($rules as $elementName => $rule) 
		        {
			        //DEEL 1
			        if ((isset($rule['group']) && isset($this->_errors[$rule['group']])) ||
		                isset($this->_errors[$target])) 
			        {
			                continue 2;
			        }

			        //DEEL 2
			        // If element is not required and is empty, we shouldn't validate it
			        if (!$this->isElementRequired($target)) 
			        {
			                if (!isset($submitValue) || '' == $submitValue) 
			                {
					        continue 2;		
			                } 
	                		// Fix for bug #3501: we shouldn't validate not uploaded files, either.
			                // Unfortunately, we can't just use $element->isUploadedFile() since
			                // the element in question can be buried in group. Thus this hack.
	                		elseif (is_array($submitValue)) 
	                		{
	                			if (false === ($pos = strpos($target, '['))) 
	                			{
	                			        $isUpload = !empty($this->_submitFiles[$target]);
	                			} 
	                		else 
	                		{
	                        		$base = substr($target, 0, $pos);
	                        		$idx  = "['" . str_replace(array(']', '['), array('', "']['"), substr($target, $pos + 1, -1)) . "']";
	                        		eval("\$isUpload = isset(\$this->_submitFiles['{$base}']['name']{$idx});");
	                		}

	                		if ($isUpload && (!isset($submitValue['error']) || 0 != $submitValue['error'])) 
			                {
			                        continue 2;
			                }
		                }
		        }

		        //DEEL 3 
		        if (isset($rule['dependent']) && is_array($rule['dependent'])) 
		        {
		                $values = array($submitValue);
		                foreach ($rule['dependent'] as $elName) 
		                {
			                $values[] = $this->getSubmitValue($elName);
		                }
		                $result = $registry->validate($rule['type'], $values, $rule['format'], true);
		        } 
		        elseif (is_array($submitValue) && !isset($rule['howmany'])) 
		        {       
		            $result = $registry->validate($rule['type'], $submitValue, $rule['format'], true);
		        
			} 
		        else 
		        {
				echo 'format '.$rule['format'].'<br />';
				echo 'regel = '.$rule['type'].'<br />';
				echo 'submitvalue = '.$submitValue.'<br />';
				$result = $registry->validate($rule['type'], $submitValue, $rule['format'], false);
		       	}

	        	
			//DEEL 4
		        if (!$result || (!empty($rule['howmany']) && $rule['howmany'] > (int)$result)) 
			{
		                if (isset($rule['group'])) 
		                {
			                $this->_errors[$rule['group']] = $rule['message'];
		                } 
		                else 
		                {
			                $this->_errors[$target] = $rule['message'];
		                }
		        }
			echo 'aantal fouten '.count($this->_errors);
	        }
	}

		// process the global rules now
		foreach ($this->_formRules as $rule) 
		{
		        if (true !== ($res = call_user_func($rule, $this->_submitValues, $this->_submitFiles))) 
			{
			        if (is_array($res)) 
				{
					echo 'ik zet error dr in';
					
			                $this->_errors += $res;
			        } 
				else 
				{
			                return PEAR::raiseError(null, QUICKFORM_ERROR, null, E_USER_WARNING, 'Form rule callback returned invalid value in HTML_QuickForm::validate()', 'HTML_QuickForm_Error', true);
			        }
		        }
		}
		
		$comma_separated = implode(",", $this->_errors);
		echo $comma_separated; 
		return (0 == count($this->_errors));
	}// end func validatecsv		
}
?>
