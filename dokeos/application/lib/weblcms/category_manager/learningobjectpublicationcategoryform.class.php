<?php
require_once dirname(__FILE__).'/../../../../claroline/inc/lib/formvalidator/FormValidator.class.php';
class LearningObjectPublicationCategoryForm extends FormValidator
{
	function LearningObjectPublicationCategoryForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
}
?>