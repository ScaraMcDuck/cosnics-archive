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
*	EXERCISE ADMINISTRATION
*
*	This script allows to manage an exercise.
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

// the exercise form has been submitted
if($submitExercise)
{
	$exerciseTitle=trim($exerciseTitle);
	$exerciseDescription=trim($exerciseDescription);
	$randomQuestions=$randomQuestions?$questionDrawn:0;
	$deleteSound=$deleteSound?true:false;

	// no title given
	if(empty($exerciseTitle))
	{
		$msgErr=get_lang('GiveExerciseName');
	}
	else
	{
		$objExercise->updateTitle($exerciseTitle);
		$objExercise->updateDescription($exerciseDescription);
		$objExercise->updateSound($_FILES['exerciseSound'],$deleteSound);
		$objExercise->updateType($exerciseType);
		$objExercise->setRandom($randomQuestions);
		$objExercise->save();

		if($deleteSound)
		{
			$exerciseSound='';
		}
		else
		{
			$exerciseSound=$objExercise->selectSound();
		}

		// reads the exercise ID (only usefull for a new exercise)
		$exerciseId=$objExercise->selectId();

		unset($modifyExercise);
	}
}
else
{
	$exerciseTitle=$objExercise->selectTitle();
	$exerciseDescription=$objExercise->selectDescription();
	$exerciseSound=$objExercise->selectSound();
	$exerciseType=$objExercise->selectType();
	$randomQuestions=$objExercise->isRandom();
}

// shows the form to modify the exercise
if($modifyExercise)
{
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?modifyExercise=<?php echo $modifyExercise; ?>" style="margin:0px;" enctype="multipart/form-data">
<table border="0" cellpadding="5">

<?php
if(!empty($msgErr))
{
?>

<tr>
  <td colspan="2">

<?php
	Display::display_normal_message($msgErr); //main API
?>

  </td>
</tr>

<?php
}
?>

<tr>
  <td><?php echo get_lang('langExerciseName'); ?> :</td>
  <td><input type="text" name="exerciseTitle" size="50" maxlength="200" value="<?php echo htmlentities($exerciseTitle); ?>" style="width:400px;"></td>
</tr>
<tr>
  <td valign="top"><?php echo get_lang('langExerciseDescription'); ?> :</td>
  <td>

<?php
    api_disp_html_area('exerciseDescription',$exerciseDescription,'250px');
?>

  </td>
</tr>

<!--
<tr>
  <td valign="top"><?php echo get_lang('langSound'); ?> :

  <?php if(!empty($exerciseSound)): ?>
  <a href="../document/download.php?doc_url=%2Faudio%2F<?php echo $exerciseSound; ?>" target="_blank"><img src="../img/wav.gif" border="0" align="absmiddle" alt="<?php echo get_lang('langSound'); ?>"></a>
  <?php endif; ?>

  </td>
  <td><input type="file" name="exerciseSound" size="50" style="width:400px;">

  <?php if(!empty($exerciseSound)): ?>
  <br>
  <input class="checkbox" type="checkbox" name="deleteSound" value="1"> <?php echo get_lang('langDeleteSound'); ?>
  <?php endif; ?>

  </td>
</tr>
//-->

<tr>
  <td valign="top"><?php echo get_lang('langExerciseType'); ?> :</td>
  <td><input class="checkbox" type="radio" name="exerciseType" value="1" <?php if($exerciseType <= 1) echo 'checked="checked"'; ?>> <?php echo get_lang('langSimpleExercise'); ?><br>
      <input class="checkbox" type="radio" name="exerciseType" value="2" <?php if($exerciseType >= 2) echo 'checked="checked"'; ?>> <?php echo get_lang('langSequentialExercise'); ?></td>
</tr>

<?php
	if($exerciseId && $nbrQuestions)
	{
?>

<tr>
  <td valign="top"><?php echo get_lang('langRandomQuestions'); ?> :</td>
  <td><input class="checkbox" type="checkbox" name="randomQuestions" value="1" <?php if($randomQuestions) echo 'checked="checked"'; ?>> <?php echo get_lang('langYes'); ?>, <?php echo get_lang('langTake'); ?>
    <select name="questionDrawn">

<?php
		for($i=1;$i <= $nbrQuestions;$i++)
		{
?>

	<option value="<?php echo $i; ?>" <?php if(($formSent && $questionDrawn == $i) || (!$formSent && ($randomQuestions == $i || ($randomQuestions <= 0 && $i == $nbrQuestions)))) echo 'selected="selected"'; ?>><?php echo $i; ?></option>

<?php
		}
?>

	</select> <?php echo strtolower(get_lang('langQuestions')).' '.get_lang('langAmong').' '.$nbrQuestions; ?>
  </td>
</tr>

<?php
	}
?>

<tr>
  <td>&nbsp;</td>
  <td>
	<input type="submit" name="submitExercise" value="<?php echo get_lang('langOk'); ?>">
	<!-- &nbsp;&nbsp;<input type="submit" name="cancelExercise" value="<?php echo get_lang('langCancel'); ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('langConfirmYourChoice'))); ?>')) return false;"> //-->
  </td>
</tr>
</table>
</form>

<?php
}
else
{
?>

<h3>
  <?php
  $exerciseTitle = api_parse_tex($exerciseTitle);
  echo $exerciseTitle; ?>
  <?php if(!empty($exerciseSound)): ?>
  <a href="../document/download.php?doc_url=%2Faudio%2F<?php echo $exerciseSound; ?>" target="_blank"><img src="../img/wav.gif" border="0" align="absmiddle" alt="<?php echo get_lang('langSound'); ?>"></a>
  <?php endif; ?>
</h3>

<blockquote>
  <?php
  $exerciseDescription = api_parse_tex($exerciseDescription);
  echo $exerciseDescription; ?>
</blockquote>

<a href="<?php echo $_SERVER['PHP_SELF']; ?>?modifyExercise=yes"><img src="../img/edit.gif" border="0" align="absmiddle" alt="<?php echo get_lang('langModify'); ?>"></a>

<?php
}
?>
