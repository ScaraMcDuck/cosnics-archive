<?php

/**
 * This abstract class defines a page which is used in a publisher wizard.
 */
abstract class PublisherWizardPage extends HTML_QuickForm_Page
{
	/**
	 * The repository tool in which the wizard runs.
	 */
	private $parent;
	
	public function PublisherWizardPage($name,$parent)
	{
		$this->parent = $parent;
		parent::HTML_QuickForm_Page($name,'post');
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