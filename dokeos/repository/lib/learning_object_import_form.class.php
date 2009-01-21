<?php
/**
 * @package repository
 * @author Hans De Bisschop
 */

require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_library_path() . 'html/menu/options_menu_renderer.class.php';

require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_category_menu.class.php';
require_once Path :: get_repository_path() . 'lib/quota_manager.class.php';
require_once Path :: get_repository_path() . 'lib/repository_manager/repository_manager.class.php';
require_once Path :: get_repository_path() . 'lib/repository_manager/repository_manager_component.class.php';
require_once Path :: get_repository_path() . 'lib/import/learning_object_import.class.php';
/**
 * A form to import a LearningObject.
 */
class LearningObjectImportForm extends FormValidator
{
	const IMPORT_FILE_NAME = 'learning_object_file';
	
	private $category;
	private $user;

	/**
	 * Constructor.
	 * @param string $form_name The name to use in the form tag.
	 * @param string $method The method to use ('post' or 'get').
	 * @param string $action The URL to which the form should be submitted.
	 */
	function LearningObjectImportForm($form_name, $method = 'post', $action = null, $category, $user)
	{
		parent :: __construct($form_name, $method, $action);
		$this->category = $category;
		$this->user = $user;
		$this->build_basic_form();
		$this->setDefaults();
	}

	/**
	 * Gets the categories defined in the user's repository.
	 * @return array The categories.
	 */
	function get_categories()
	{
		$categorymenu = new LearningObjectCategoryMenu($this->get_user()->get_id());
		$renderer = new OptionsMenuRenderer();
		$categorymenu->render($renderer, 'sitemap');
		return $renderer->toArray();
	}

	/**
	 * Builds a form to import a learning object.
	 */
	private function build_basic_form()
	{
		$category_select = $this->add_select(RepositoryManager :: PARAM_CATEGORY_ID, Translation :: get('CategoryTypeName'), $this->get_categories());
		$this->addElement('file', self :: IMPORT_FILE_NAME, Translation :: get('FileName'));
		//$this->addElement('submit', 'learning_object_import', Translation :: get('Ok'));
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Ok'), array('class' => 'positive'));
		//$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$defaults[RepositoryManager :: PARAM_CATEGORY_ID] = $this->get_category();				
		parent :: setDefaults($defaults);
	}
	
	function set_values($defaults)
	{
		parent :: setDefaults($defaults);
	}
	
	/**
	 * Imports a learning object from the submitted form values.
	 * @return LearningObject The newly imported learning object.
	 */
	function import_learning_object()
	{
		$path_parts = pathinfo($_FILES[self :: IMPORT_FILE_NAME]['name']);
		$type = $path_parts['extension'];
		$type = ($type == 'zip' ? 'dlof' : $type);
		
		$values = $this->exportValues();
		
		if(LearningObjectImport :: type_supported($type))
		{
			$importer = LearningObjectImport :: factory($type, $_FILES[self :: IMPORT_FILE_NAME], $this->get_user(), $this->get_category());
			return $importer->import_learning_object();
		}
		else
		{
			return false;
		}
	}

	/**
	 * Displays the form
	 */
	function display()
	{
		$quotamanager = new QuotaManager($this->get_user());
		if ($quotamanager->get_available_database_space() <= 0)
		{
			Display :: warning_message(htmlentities(Translation :: get('MaxNumberOfLearningObjectsReached')));
		}
		else
		{
			parent :: display();
		}
	}
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
	
	function get_category()
	{
		return $this->category;
	}
	
	function get_user()
	{
		return $this->user;
	}	
}
?>
