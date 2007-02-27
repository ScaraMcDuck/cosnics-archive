<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/exercise.class.php';
/**
 * This class represents a form to create or update exercises
 */
class ExerciseForm extends LearningObjectForm
{
	// Inherited
	function create_learning_object()
	{
		$object = new Exercise();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>