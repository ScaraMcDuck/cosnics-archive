<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
 *	EXERCISE ADMINISTRATION
 * This script allows to manage (create, modify) an exercise and its questions
 *
 * Following scripts are includes for a best code understanding :
 *
 * - exercise.class.php : for the creation of an Exercise object
 * - question.class.php : for the creation of a Question object
 * - answer.class.php : for the creation of an Answer object
 *
 * - exercise.lib.php : functions used in the exercise tool
 *
 * - exercise_admin.inc.php : management of the exercise
 * - question_admin.inc.php : management of a question (statement & answers)
 * - statement_admin.inc.php : management of a statement
 * - answer_admin.inc.php : management of answers
 * - question_list_admin.inc.php : management of the question list
 *
 * Main variables used in this script :
 *
 * - $is_allowedToEdit : set to 1 if the user is allowed to manage the exercise
 *
 * - $objExercise : exercise object
 * - $objQuestion : question object
 * - $objAnswer : answer object
 *
 * - $aType : array with answer types
 * - $exerciseId : the exercise ID
 * - $picturePath : the path of question pictures
 *
 * - $newQuestion : ask to create a new question
 * - $modifyQuestion : ID of the question to modify
 * - $editQuestion : ID of the question to edit
 * - $submitQuestion : ask to save question modifications
 * - $cancelQuestion : ask to cancel question modifications
 * - $deleteQuestion : ID of the question to delete
 * - $moveUp : ID of the question to move up
 * - $moveDown : ID of the question to move down
 * - $modifyExercise : ID of the exercise to modify
 * - $submitExercise : ask to save exercise modifications
 * - $cancelExercise : ask to cancel exercise modifications
 * - $modifyAnswers : ID of the question which we want to modify answers for
 * - $cancelAnswers : ask to cancel answer modifications
 * - $buttonBack : ask to go back to the previous page in answers of type "Fill in blanks"
 *
 *	@author Olivier Brouckaert
 *	@package dokeos.exercise
 */

include('exercise.class.php');
include('question.class.php');
include('answer.class.php');

include('exercise.lib.php');

api_use_lang_files('exercice');

include("../inc/claro_init_global.inc.php");
$this_section=SECTION_COURSES;

include_once(api_get_library_path().'/fileUpload.lib.php');
include_once(api_get_library_path().'/document.lib.php');
/****************************/
/*  stripslashes POST data  */
/****************************/

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	foreach($_POST as $key=>$val)
	{
		if(is_string($val))
		{
			$_POST[$key]=stripslashes($val);
		}
		elseif(is_array($val))
		{
			foreach($val as $key2=>$val2)
			{
				$_POST[$key][$key2]=stripslashes($val2);
			}
		}

		$GLOBALS[$key]=$_POST[$key];
	}
}

// get vars from GET
if ( empty ( $exerciseId ) ) {
    $exerciseId = mysql_real_escape_string($_GET['exerciseId']);
}
if ( empty ( $newQuestion ) ) {
    $newQuestion = $_GET['newQuestion'];
}
if ( empty ( $modifyAnswers ) ) {
    $modifyAnswers = $_GET['modifyAnswers'];
}
if ( empty ( $editQuestion ) ) {
    $editQuestion = $_GET['editQuestion'];
}
if ( empty ( $modifyQuestion ) ) {
    $modifyQuestion = $_GET['modifyQuestion'];
}
if ( empty ( $deleteQuestion ) ) {
    $deleteQuestion = $_GET['deleteQuestion'];
}
if ( empty ( $questionId ) ) {
    $questionId = $_SESSION['questionId'];
}
if ( empty ( $modifyExercise ) ) {
    $modifyExercise = $_GET['modifyExercise'];
}


// get from session
$objExercise = $_SESSION['objExercise'];
$objQuestion = $_SESSION['objQuestion'];
$objAnswer   = $_SESSION['objAnswer'];

// answer types
define(UNIQUE_ANSWER,	1);
define(MULTIPLE_ANSWER,	2);
define(FILL_IN_BLANKS,	3);
define(MATCHING,		4);
define(FREE_ANSWER, 5);

// allows script inclusions
define(ALLOWED_TO_INCLUDE,1);

$is_allowedToEdit=$is_courseAdmin;

// document path
$documentPath=api_get_path(SYS_COURSE_PATH).$_course['path'].'/document';

// picture path
$picturePath=$documentPath.'/images';

// audio path
$audioPath=$documentPath.'/audio';

// the 4 types of answers
$aType=array(get_lang('langUniqueSelect'),get_lang('langMultipleSelect'),get_lang('langFillBlanks'),get_lang('langMatching'),get_lang('langFree'));

// tables used in the exercise tool
$TBL_EXERCICE_QUESTION = $_course['dbNameGlu'].'quiz_rel_question';
$TBL_EXERCICES         = $_course['dbNameGlu'].'quiz';
$TBL_QUESTIONS         = $_course['dbNameGlu'].'quiz_question';
$TBL_REPONSES          = $_course['dbNameGlu'].'quiz_answer';
$TBL_DOCUMENT          = $_course['dbNameGlu']."document";

if(!$is_allowedToEdit)
{
	api_not_allowed();
}

// intializes the Exercise object
if(!is_object($objExercise))
{
	// construction of the Exercise object
	$objExercise=new Exercise();

	// creation of a new exercise if wrong or not specified exercise ID
	if($exerciseId)
	{
	
    $objExercise->read($exerciseId);
	}

	// saves the object into the session
	api_session_register('objExercise');
}

// doesn't select the exercise ID if we come from the question pool
if(!$fromExercise)
{

	// gets the right exercise ID, and if 0 creates a new exercise
	if(!$exerciseId=$objExercise->selectId())
	{
		$modifyExercise='yes';
	}
}

$nbrQuestions=$objExercise->selectNbrQuestions();

// intializes the Question object
if($editQuestion || $newQuestion || $modifyQuestion || $modifyAnswers)
{
	if($editQuestion || $newQuestion)
	{
		// construction of the Question object
		$objQuestion=new Question();

		// saves the object into the session
		api_session_register('objQuestion');

		// reads question data
		if($editQuestion)
		{
			// question not found
			if(!$objQuestion->read($editQuestion))
			{
				die(get_lang('QuestionNotFound'));
			}
		}
	}

	// checks if the object exists
	if(is_object($objQuestion))
	{
		// gets the question ID
		$questionId=$objQuestion->selectId();
	}
	// question not found
	else
	{
		die(get_lang('QuestionNotFound'));
	}
}

// if cancelling an exercise
if($cancelExercise)
{
	// existing exercise
	if($exerciseId)
	{
		unset($modifyExercise);
	}
	// new exercise
	else
	{
		// goes back to the exercise list
		header('Location: exercice.php');
		exit();
	}
}

// if cancelling question creation/modification
if($cancelQuestion)
{
	// if we are creating a new question from the question pool
	if(!$exerciseId && !$questionId)
	{
		// goes back to the question pool
		header('Location: question_pool.php');
		exit();
	}
	else
	{
		// goes back to the question viewing
		$editQuestion=$modifyQuestion;

		unset($newQuestion,$modifyQuestion);
	}
}

// if cancelling answer creation/modification
if($cancelAnswers)
{
	// goes back to the question viewing
	$editQuestion=$modifyAnswers;

	unset($modifyAnswers);
}

// modifies the query string that is used in the link of tool name
if($editQuestion || $modifyQuestion || $newQuestion || $modifyAnswers)
{
	$nameTools=get_lang('QuestionManagement');
}
else
{
	$nameTools=get_lang('ExerciseManagement');
}

$interbredcrump[]=array("url" => "exercice.php","name" => get_lang('Exercices'));

// shows a link to go back to the question pool
if(!$exerciseId && $nameTools != get_lang('ExerciseManagement'))
{
	$interbredcrump[]=array("url" => "question_pool.php?fromExercise=$fromExercise","name" => get_lang('QuestionPool'));
}

// if the question is duplicated, disable the link of tool name
if($modifyIn == 'thisExercise')
{
	if($buttonBack)
	{
		$modifyIn='allExercises';
	}
	else
	{
		$noPHP_SELF=true;
	}
}

Display::display_header($nameTools,"Exercise");
?>

<h4>
  <?php echo $nameTools; ?>
</h4>

<?php
if($newQuestion || $modifyQuestion)
{
	// statement management
	include('statement_admin.inc.php');
}

if($modifyAnswers)
{ // this might be loaded after statement_admin (second step of answers writing)
  // and $modifyAnswers is then set within statement_admin.inc.php
	// answer management
	include('answer_admin.inc.php');
}

if($editQuestion || $usedInSeveralExercises)
{
	// question management
	include('question_admin.inc.php');
}

if(!$newQuestion && !$modifyQuestion && !$editQuestion && !$modifyAnswers)
{
	// exercise management
	include('exercise_admin.inc.php');

	if(!$modifyExercise)
	{
		// question list management
		include('question_list_admin.inc.php');
	}
}

api_session_register('objExercise');
api_session_register('objQuestion');
api_session_register('objAnswer');

Display::display_footer();
?>
