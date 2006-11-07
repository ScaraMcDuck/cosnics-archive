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
*	EXERCISE RESULT  
*
*	This script gets informations from the script "exercise_submit.php",
*	through the session, and calculates the score of the student for
*	that exercise.
*
*	Then it shows the results on the screen.
*
*	@author Olivier Brouckaert, main author
*	@author Roan Embrechts, some refactoring
*	@package dokeos.exercise
*	@todo	split more code up in functions, move functions to library?
============================================================================== 
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/

include('exercise.class.php');
include('question.class.php');
include('answer.class.php');

// answer types
define('UNIQUE_ANSWER',	1);
define('MULTIPLE_ANSWER',	2);
define('FILL_IN_BLANKS',	3);
define('MATCHING',		4);
define('FREE_ANSWER', 5);

$langFile='exercice';

include('../inc/claro_init_global.inc.php');
$this_section=SECTION_COURSES;

include(api_get_library_path().'/text.lib.php');

$TBL_EXERCICE_QUESTION = $_course['dbNameGlu'].'quiz_rel_question';
$TBL_EXERCICES         = $_course['dbNameGlu'].'quiz';
$TBL_QUESTIONS         = $_course['dbNameGlu'].'quiz_question';
$TBL_REPONSES          = $_course['dbNameGlu'].'quiz_answer';

//temp values to move to AWACS
$dsp_percent = false; //false to display total score as absolute values

//debug param. 0: no display - 1: debug display
$debug=0;
if($debug>0){echo str_repeat('&nbsp;',0).'Entered exercise_result.php'."<br />\n";var_dump($_POST);}

// general parameters passed via POST/GET
if ( empty ( $origin ) ) {
    $origin = $_REQUEST['origin'];
}
if ( empty ( $learnpath_id ) ) {
    $learnpath_id       = mysql_real_escape_string($_REQUEST['learnpath_id']);
}
if ( empty ( $learnpath_item_id ) ) {
    $learnpath_item_id  = mysql_real_escape_string($_REQUEST['learnpath_item_id']);
}
if ( empty ( $formSent ) ) {
    $formSent       = $_REQUEST['formSent'];
}
if ( empty ( $exerciseResult ) ) {
    $exerciseResult = $_SESSION['exerciseResult'];
}
if ( empty ( $questionId ) ) {
    $questionId = $_REQUEST['questionId'];
}
if ( empty ( $choice ) ) {
    $choice = $_REQUEST['choice'];
}
if ( empty ( $questionNum ) ) {
    $questionNum    = mysql_real_escape_string($_REQUEST['questionNum']);
} 
if ( empty ( $nbrQuestions ) ) {
    $nbrQuestions   = mysql_real_escape_string($_REQUEST['nbrQuestions']);
}
if ( empty ( $questionList ) ) {
    $questionList = $_SESSION['questionList'];
}
if ( empty ( $objExercise ) ) {
    $objExercise = $_SESSION['objExercise'];
}


// if the above variables are empty or incorrect, stops the script
if(!is_array($exerciseResult) || !is_array($questionList) || !is_object($objExercise))
{
	header('Location: exercice.php');
	exit();
}
$exerciseTitle=$objExercise->selectTitle();

$nameTools=get_lang('Exercice');

$interbredcrump[]=array("url" => "exercice.php","name" => get_lang('Exercices'));


if ($origin != 'learnpath')
{
	//so we are not in learnpath tool
	Display::display_header($nameTools,"Exercise");
}
else
{
	?> <link rel="stylesheet" type="text/css" href="<?php echo api_get_path(WEB_CODE_PATH); ?>css/default.css"> <?php
}


/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

function display_unique_or_multiple_answer($answerType, $studentChoice, $answer, $answerComment, $answerCorrect)
{
	?>	
	<tr>
	<td width="5%" align="center">
		<img src="../img/<?php echo ($answerType == UNIQUE_ANSWER)?'radio':'checkbox'; echo $studentChoice?'_on':'_off'; ?>.gif"
		border="0" alt="" />
	</td>
	<td width="5%" align="center">
		<img src="../img/<?php echo ($answerType == UNIQUE_ANSWER)?'radio':'checkbox'; echo $answerCorrect?'_on':'_off'; ?>.gif"
		border="0" alt=" " />
	</td>
	<td width="45%" style="border-bottom: 1px solid #4171B5;">
		<?php
		$answer=api_parse_tex($answer);
		echo $answer; ?>
	</td>
	<td width="45%" style="border-bottom: 1px solid #4171B5;">
		<?php
		$answerComment=api_parse_tex($answerComment);
		if($studentChoice) echo nl2br(make_clickable($answerComment)); else echo '&nbsp;'; ?>
	</td>
	</tr>
	<?php
}

function display_fill_in_blanks_answer($answer)
{
	?>					
		<tr>
		<td>
			<?php echo nl2br($answer); ?>
		</td>
		</tr>					
	<?php
}

function display_free_answer($answer)
{
	?>					
		<tr>
		<td width="55%">
			<?php echo nl2br($answer); ?>
		</td>
   <td width="45%">
    <?php echo get_lang('notCorrectedYet');?>
   </td>
		</tr>					
	<?php
}
					
/*
==============================================================================
		MAIN CODE
==============================================================================
*/
$exerciseTitle=api_parse_tex($exerciseTitle);
	
?>	
	<h3><?php echo $exerciseTitle ?>: <?php echo get_lang("Result"); ?></h3>
	<form method="get" action="exercice.php">
	<input type="hidden" name="origin" value="<?php echo $origin; ?>" />
    <input type="hidden" name="learnpath_id" value="<?php echo $learnpath_id; ?>" />
    <input type="hidden" name="learnpath_item_id" value="<?php echo $learnpath_item_id; ?>" />

<?php
	$i=$totalScore=$totalWeighting=0;
 if($debug>0){echo "ExerciseResult: "; var_dump($exerciseResult); echo "QuestionList: ";var_dump($questionList);}
	// for each question
	foreach($questionList as $questionId)
	{
		// gets the student choice for this question
		$choice=$exerciseResult[$questionId];

		// creates a temporary Question object
		$objQuestionTmp=new Question();

		$objQuestionTmp->read($questionId);

		$questionName=$objQuestionTmp->selectTitle();
		$questionWeighting=$objQuestionTmp->selectWeighting();
		$answerType=$objQuestionTmp->selectType();

		// destruction of the Question object
		unset($objQuestionTmp);

		if($answerType == UNIQUE_ANSWER || $answerType == MULTIPLE_ANSWER)
		{
			$colspan=4;
		}
		elseif($answerType == MATCHING || $answerType == FREE_ANSWER)
		{
			$colspan=2;
		}
		else
		{
			$colspan=1;
		}
		?>
			<table width="100%" border="0" cellpadding="3" cellspacing="2">
			<tr bgcolor="#E6E6E6">
			<td colspan="<?php echo $colspan; ?>">
				<?php echo get_lang("Question").' '.($i+1); ?>
			</td>
			</tr>
			<tr>
			<td colspan="<?php echo $colspan; ?>">
				<?php echo $questionName; ?>
			</td>
			</tr>
		<?php
		if($answerType == UNIQUE_ANSWER || $answerType == MULTIPLE_ANSWER)
		{
			?>
				<tr>
				<td width="5%" valign="top" align="center" nowrap="nowrap">
					<i><?php echo get_lang("Choice"); ?></i>
				</td>
				<td width="5%" valign="top" nowrap="nowrap">
					<i><?php echo get_lang("ExpectedChoice"); ?></i>
				</td>
				<td width="45%" valign="top">
					<i><?php echo get_lang("Answer"); ?></i>
				</td>
				<td width="45%" valign="top">
					<i><?php echo get_lang("Comment"); ?></i>
				</td>
				</tr>
			<?php
		}
		elseif($answerType == FILL_IN_BLANKS)
		{
			?>			
				<tr>
				<td>
					<i><?php echo get_lang("Answer"); ?></i>
				</td>
				</tr>
			<?php
		}
		elseif($answerType == FREE_ANSWER)
		{
			?>			
				<tr>
				<td width="55%">
					<i><?php echo get_lang("Answer"); ?></i>
				</td>
				<td width="45%" valign="top">
					<i><?php echo get_lang("Comment"); ?></i>
				</td>
				</tr>
			<?php
		}
		else
		{
			?>
				<tr>
				<td width="50%">
					<i><?php echo get_lang("ElementList"); ?></i>
				</td>
				<td width="50%">
					<i><?php echo get_lang("CorrespondsTo"); ?></i>
				</td>
				</tr>
			<?php
		}

		// construction of the Answer object
		$objAnswerTmp=new Answer($questionId);

		$nbrAnswers=$objAnswerTmp->selectNbrAnswers();

		$questionScore=0;

		for($answerId=1;$answerId <= $nbrAnswers;$answerId++)
		{
			$answer=$objAnswerTmp->selectAnswer($answerId);
			$answerComment=$objAnswerTmp->selectComment($answerId);
			$answerCorrect=$objAnswerTmp->isCorrect($answerId);
			$answerWeighting=$objAnswerTmp->selectWeighting($answerId);

			switch($answerType)
			{
				// for unique answer
				case UNIQUE_ANSWER :	$studentChoice=($choice == $answerId)?1:0;

										if($studentChoice)
										{
										  	$questionScore+=$answerWeighting;
											$totalScore+=$answerWeighting;
										}

										break;
				// for multiple answers
				case MULTIPLE_ANSWER :	$studentChoice=$choice[$answerId];

										if($studentChoice)
										{
											$questionScore+=$answerWeighting;
											$totalScore+=$answerWeighting;
										}

										break;
				// for fill in the blanks
				case FILL_IN_BLANKS :	// splits text and weightings that are joined with the character '::'
										list($answer,$answerWeighting)=explode('::',$answer);

										// splits weightings that are joined with a comma
										$answerWeighting=explode(',',$answerWeighting);

										// we save the answer because it will be modified
										$temp=$answer;

										// TeX parsing
										// 1. find everything between the [tex] and [/tex] tags
										$startlocations=strpos($temp,'[tex]');
										$endlocations=strpos($temp,'[/tex]');

										if($startlocations !== false && $endlocations !== false)
										{
											$texstring=substr($temp,$startlocations,$endlocations-$startlocations+6);
											// 2. replace this by {texcode}
											$temp=str_replace($texstring,'{texcode}',$temp);
										}

										$answer='';

										$j=0;

										// the loop will stop at the end of the text
										while(1)
										{
											// quits the loop if there are no more blanks
											if(($pos = strpos($temp,'[')) === false)
											{
												// adds the end of the text
												$answer.=$temp;
												// TeX parsing
												$texstring = api_parse_tex($texstring);
												$answer=str_replace("{texcode}",$texstring,$answer);
												break;
											}

											// adds the piece of text that is before the blank and ended by [
											$answer.=substr($temp,0,$pos+1);

											$temp=substr($temp,$pos+1);

											// quits the loop if there are no more blanks
											if(($pos = strpos($temp,']')) === false)
											{
												// adds the end of the text
												$answer.=$temp;
												break;
											}

											$choice[$j]=trim($choice[$j]);

											// if the word entered by the student IS the same as the one defined by the professor
											if(strtolower(substr($temp,0,$pos)) == stripslashes(strtolower($choice[$j])))
											{
												// gives the related weighting to the student
												$questionScore+=$answerWeighting[$j];

												// increments total score
												$totalScore+=$answerWeighting[$j];

												// adds the word in green at the end of the string
												$answer.=stripslashes($choice[$j]);
											}
											// else if the word entered by the student IS NOT the same as the one defined by the professor
											elseif(!empty($choice[$j]))
											{
												// adds the word in red at the end of the string, and strikes it
												$answer.='<font color="red"><s>'.stripslashes($choice[$j]).'</s></font>';
											}
											else
											{
												// adds a tabulation if no word has been typed by the student
												$answer.='&nbsp;&nbsp;&nbsp;';
											}

											// adds the correct word, followed by ] to close the blank
											$answer.=' / <font color="green"><b>'.substr($temp,0,$pos).'</b></font>]';

											$j++;

											$temp=substr($temp,$pos+1);
										}

										break;
				// for free answer
				case FREE_ANSWER :	$studentChoice=$choice;

										if($studentChoice)
										{
										  	$questionScore+=$answerWeighting;
											$totalScore+=$answerWeighting;
										}

										break;
				// for matching
				case MATCHING :			if($answerCorrect)
										{
											if($answerCorrect == $choice[$answerId])
											{
												$questionScore+=$answerWeighting;
												$totalScore+=$answerWeighting;
												$choice[$answerId]=$matching[$choice[$answerId]];
											}
											elseif(!$choice[$answerId])
											{
												$choice[$answerId]='&nbsp;&nbsp;&nbsp;';
											}
											else
											{
												$choice[$answerId]='<font color="red"><s>'.$matching[$choice[$answerId]].'</s></font>';
											}
										}
										else
										{
											$matching[$answerId]=$answer;
										}
										break;
			} // end switch Answertype

			if($answerType != MATCHING || $answerCorrect)
			{
				if($answerType == UNIQUE_ANSWER || $answerType == MULTIPLE_ANSWER)
				{
					display_unique_or_multiple_answer($answerType, $studentChoice, $answer, $answerComment, $answerCorrect);
				}
				elseif($answerType == FILL_IN_BLANKS)
				{
					display_fill_in_blanks_answer($answer);
				}
				elseif($answerType == FREE_ANSWER)
				{
					display_free_answer($choice);
				}
				else
				{
					?>					
						<tr>
						<td width="50%">
							<?php
							$answer=api_parse_tex($answer);
							echo $answer; ?>
						</td>
						<td width="50%">
							<?php echo $choice[$answerId]; ?> / <font color="green"><b>
							<?php
							$matching[$answerCorrect]=api_parse_tex($matching[$answerCorrect]);
							echo $matching[$answerCorrect]; ?></b></font>
						</td>
						</tr>		
					<?php
				}
			}
		} // end for that loops over all answers of the current question
		?>	
			<tr>
			<td colspan="<?php echo $colspan; ?>" align="right">
				<b><?php echo get_lang('Score')." : $questionScore/$questionWeighting"; ?></b>
			</td>
			</tr>
			</table>
		<?php
		// destruction of Answer
		unset($objAnswerTmp);

		$i++;

		$totalWeighting+=$questionWeighting;
	} // end huge foreach() block that loops over all questions
	?>
		<table width="100%" border="0" cellpadding="3" cellspacing="2">
		<tr>
		<td>
			<b><?php echo get_lang('YourTotalScore')." ";
			if($dsp_percent == true){
			  echo number_format(($totalScore/$totalWeighting)*100,1,'.','')."%";
			}else{
			  echo $totalScore."/".$totalWeighting;
			}
                        ?> !</b>
		</td>
		</tr>
		<tr>
		<td>
		<br />
			<input type="submit" value="<?php echo get_lang('Ok'); ?>" />
		</td>
		</tr>
		</table>
		
		</form>
		
		<br />
	<?php
/*
==============================================================================
		Tracking of results 
==============================================================================
*/

if($is_trackingEnabled)
{
	include(api_get_library_path().'/events.lib.inc.php');

	event_exercice($objExercise->selectId(),$totalScore,$totalWeighting);
}

if ($origin != 'learnpath')
{
	//we are not in learnpath tool
	Display::display_footer();
}
else
{
	?> 
		<link rel="stylesheet" type="text/css" href="<?php echo $clarolineRepositoryWeb ?>css/default.css" />
	<?php
}
?>
