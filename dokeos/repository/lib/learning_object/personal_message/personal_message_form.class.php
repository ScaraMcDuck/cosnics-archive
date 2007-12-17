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
	const TOTAL_PROPERTIES = 2;
	function setCsvValues($valuearray)
	{
		if(count($valuearray) == self :: TOTAL_PROPERTIES)
		{
			$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
			$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[1];	
			parent :: setValues($defaults);
			return true;
		}
		return false;		
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
