<?php
/**
 * @package repository.learningobject
 * @subpackage personal_message
 * 
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/personal_message.class.php';
/**
 * This class represents a form to create or update personal messages
 */
class PersonalMessageForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		parent :: set_values($defaults);			
	}

	// Inherited
	function create_learning_object()
	{
		$object = new PersonalMessage();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>
