<?php
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/wiki_page.class.php';
/**
 * @package repository.learningobject
 * @subpackage wiki_page
 */
class WikiPageForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		parent :: set_values($defaults);
	}
	function create_learning_object()
	{
		$object = new WikiPage();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	function setDefaults($defaults = array ())
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = Request :: get('title')==null?NULL:Request :: get('title');
		
		parent :: setDefaults($defaults);
	}
}
?>
