<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/wiki.class.php';
/**
 * @package repository.learningobject
 * @subpackage wiki
 */
class WikiForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[1];	
		parent :: set_values($defaults);
	}
	function create_learning_object()
	{
		$object = new Wiki();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>
