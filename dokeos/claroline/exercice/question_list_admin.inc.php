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
============================================================================== 
*	QUESTION LIST ADMINISTRATION 
*
*	This script allows to manage the question list
*	It is included from the script admin.php
*
*	@author Olivier Brouckaert
*	@package dokeos.exercise
============================================================================== 
*/

// ALLOWED_TO_INCLUDE is defined in admin.php
if(!defined('ALLOWED_TO_INCLUDE'))
{
	exit();
}

// moves a question up in the list
if($moveUp)
{
	$objExercise->moveUp($moveUp);
	$objExercise->save();
}

// moves a question down in the list
if($moveDown)
{
	$objExercise->moveDown($moveDown);
	$objExercise->save();
}

// deletes a question from the exercise (not from the data base)
if($deleteQuestion)
{
	// construction of the Question object
	$objQuestionTmp=new Question();

	// if the question exists
	if($objQuestionTmp->read($deleteQuestion))
	{
		$objQuestionTmp->delete($exerciseId);

		// if the question has been removed from the exercise
		if($objExercise->removeFromList($deleteQuestion))
		{
			$nbrQuestions--;
		}
	}

	// destruction of the Question object
	unset($objQuestionTmp);
}
?>

<hr size="1" noshade="noshade">

  <a href="question_pool.php?fromExercise=<?php echo $exerciseId; ?>"><?php echo get_lang('langGetExistingQuestion'); ?></a>
&nbsp;|&nbsp;
 <b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?newQuestion=yes"><?php echo get_lang('langNewQu'); ?></a></b>

<form method="get" action="exercice.php" style="margin:10px; margin-left:0px;">
<input type="submit" value="<?php echo htmlentities(get_lang('langFinishTest')); ?>">
</form>

<b><?php echo get_lang('langQuestionList'); ?></b>

<table border="0" align="center" cellpadding="2" cellspacing="2" width="100%">

<?php
if($nbrQuestions)
{
	$questionList=$objExercise->selectQuestionList();

	$i=1;

	foreach($questionList as $id)
	{
		$objQuestionTmp=new Question();

		$objQuestionTmp->read($id);
?>

<tr>
  <td><?php echo "$i. ".$objQuestionTmp->selectTitle(); ?><br><?php echo $aType[$objQuestionTmp->selectType()-1]; ?></td>
</tr>
<tr>
  <td>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?editQuestion=<?php echo $id; ?>"><img src="../img/edit.gif" border="0" align="absmiddle" alt="<?php echo get_lang('langModify'); ?>"></a>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?deleteQuestion=<?php echo $id; ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('langConfirmYourChoice'))); ?>')) return false;"><img src="../img/delete.gif" border="0" align="absmiddle" alt="<?php echo get_lang('langDelete'); ?>"></a>

<?php
		if($i != 1)
		{
?>

	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?moveUp=<?php echo $id; ?>"><img src="../img/up.gif" border="0" align="absmiddle" alt="<?php echo get_lang('langMoveUp'); ?>"></a>

<?php
		}

		if($i != $nbrQuestions)
		{
?>

	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?moveDown=<?php echo $id; ?>"><img src="../img/down.gif" border="0" align="absmiddle" alt="<?php echo get_lang('langMoveDown'); ?>"></a>

<?php
		}
?>

  </td>
</tr>

<?php
		$i++;

		unset($objQuestionTmp);
	}
}

if(!$i)
{
?>

<tr>
  <td><?php echo get_lang('langNoQuestion'); ?></td>
</tr>

<?php
}
?>

</table>
