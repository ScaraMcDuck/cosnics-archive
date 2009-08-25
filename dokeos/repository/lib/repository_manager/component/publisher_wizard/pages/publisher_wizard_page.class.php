<?php

/**
 * This abstract class defines a page which is used in a publisher wizard.
 */

require_once Path :: get_common_path() . 'html/formvalidator/form_validator_page.class.php';

abstract class PublisherWizardPage extends FormValidatorPage
{
	/**
	 * The repository tool in which the wizard runs.
	 */
	private $parent;
	
	public function PublisherWizardPage($name,$parent)
	{
		$this->parent = $parent;
		parent:: __construct($name,'post');
		$this->updateAttributes(array('action'=>$parent->get_url(array(RepositoryManager :: PARAM_LEARNING_OBJECT_ID => Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_ID)))));
	}
	/**
	 * Returns the repository tool in which this wizard runs
	 * @return Tool
	 */
	function get_parent()
	{
		return $this->parent;
	}

}
?>