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
/*>>>>>>>>>>>>>>>>>>>> ANSWER ADMINISTRATION <<<<<<<<<<<<<<<<<<<<*/
/**
==============================================================================
 * This script allows to manage answers
 *
 * It is included from the script admin.php
 *	@author Olivier Brouckaert
 *	@package dokeos.exercise
==============================================================================
 */

// ALLOWED_TO_INCLUDE is defined in admin.php
if(!defined('ALLOWED_TO_INCLUDE'))
{
	exit();
}

$questionName=$objQuestion->selectTitle();
$answerType=$objQuestion->selectType();
$pictureName=$objQuestion->selectPicture();
$debug = 0; // debug variable to get where we are

$okPicture=empty($pictureName)?false:true;

// if we come from the warning box "this question is used in serveral exercises"
if($modifyIn)
{
    if($debug>0){echo '$modifyIn was set'."<br />\n";}
    // if the user has chosed to modify the question only in the current exercise
    if($modifyIn == 'thisExercise')
    {
        // duplicates the question
        $questionId=$objQuestion->duplicate();

        // deletes the old question
        $objQuestion->delete($exerciseId);

        // removes the old question ID from the question list of the Exercise object
        $objExercise->removeFromList($modifyAnswers);

        // adds the new question ID into the question list of the Exercise object
        $objExercise->addToList($questionId);

        // construction of the duplicated Question
        $objQuestion=new Question();

        $objQuestion->read($questionId);

        // adds the exercise ID into the exercise list of the Question object
        $objQuestion->addToList($exerciseId);

        // copies answers from $modifyAnswers to $questionId
        $objAnswer->duplicate($questionId);

        // construction of the duplicated Answers
        $objAnswer=new Answer($questionId);
    }

    if($answerType == UNIQUE_ANSWER || $answerType == MULTIPLE_ANSWER)
    {
        $correct=unserialize($correct);
        $reponse=unserialize($reponse);
        $comment=unserialize($comment);
        $weighting=unserialize($weighting);
    }
    //matching
    elseif($answerType == MATCHING)
    {
        $option=unserialize($option);
        $match=unserialize($match);
        $sel=unserialize($sel);
        $weighting=unserialize($weighting);
    }
    //free answer
    elseif($answerType == FREE_ANSWER ) 
    {
        $reponse=unserialize($reponse);
        $comment=unserialize($comment);
        $free_comment=$comment;
        $weighting=unserialize($weighting);
    }
    //fill in blanks
  else
    {
        $reponse=unserialize($reponse);
        $comment=unserialize($comment);
        $blanks=unserialize($blanks);
        $weighting=unserialize($weighting);
    }

    unset($buttonBack);
}

// the answer form has been submitted
if($submitAnswers || $buttonBack)
{
    if($debug>0){echo '$submitAnswers or $buttonBack was set'."<br />\n";}
    if($answerType == UNIQUE_ANSWER || $answerType == MULTIPLE_ANSWER)
    {
      if($debug>0){echo '&nbsp;&nbsp;$answerType is UNIQUE_ANSWER or MULTIPLE_ANSWER'."<br />\n";}
        $questionWeighting=$nbrGoodAnswers=0;

        for($i=1;$i <= $nbrAnswers;$i++)
        {
            $reponse[$i]=trim($reponse[$i]);
            $comment[$i]=trim($comment[$i]);
            $weighting[$i]=intval($weighting[$i]);

            if($answerType == UNIQUE_ANSWER)
            {
            if($debug>0){echo str_repeat('&nbsp;',4).'$answerType is UNIQUE_ANSWER'."<br />\n";}
                $goodAnswer=($correct == $i)?1:0;
            }
            else
            {
                $goodAnswer=$correct[$i];
            }

            if($goodAnswer)
            {
                $nbrGoodAnswers++;

                // a good answer can't have a negative weighting
                $weighting[$i]=abs($weighting[$i]);

                // calculates the sum of answer weighting only if it is different from 0 and the answer is good
                if($weighting[$i])
                {
                    $questionWeighting+=$weighting[$i];
                }
            }
            elseif($answerType == MULTIPLE_ANSWER)
            {
            if($debug>0){echo str_repeat('&nbsp;',4).'$answerType is MULTIPLE_ANSWER'."<br />\n";}
                // a bad answer can't have a positive weighting
                $weighting[$i]=0-abs($weighting[$i]);
            }

            // checks if field is empty
            if(empty($reponse[$i]) && $reponse[$i] != '0')
            {
                $msgErr=get_lang('langGiveAnswers');

                // clears answers already recorded into the Answer object
                $objAnswer->cancel();

                break;
            }
            else
            {
                // adds the answer into the object
                $objAnswer->createAnswer($reponse[$i],$goodAnswer,$comment[$i],$weighting[$i],$i);
            }
        }  // end for()

        if(empty($msgErr))
        {
            if(!$nbrGoodAnswers)
            {
                $msgErr=($answerType == UNIQUE_ANSWER)?get_lang('langChooseGoodAnswer'):get_lang('langChooseGoodAnswers');

                // clears answers already recorded into the Answer object
                $objAnswer->cancel();
            }
            // checks if the question is used in several exercises
            elseif($exerciseId && !$modifyIn && $objQuestion->selectNbrExercises() > 1)
            {
                $usedInSeveralExercises=1;
            }
            else
            {
                // saves the answers into the data base
                $objAnswer->save();

                // sets the total weighting of the question
                $objQuestion->updateWeighting($questionWeighting);
                $objQuestion->save($exerciseId);

                $editQuestion=$questionId;

                unset($modifyAnswers);
            }
        }
    }
    elseif($answerType == FILL_IN_BLANKS)
    {
        if($debug>0){echo str_repeat('&nbsp;',2).'$answerType is FILL_IN_BLANKS'."<br />\n";}
        $reponse=trim($reponse);

        if(!$buttonBack)
        {
            if($debug>0){echo str_repeat('&nbsp;',4).'$buttonBack is not set'."<br />\n";}
            if($setWeighting)
            {
                $blanks=unserialize($blanks);

                // checks if the question is used in several exercises
                if($exerciseId && !$modifyIn && $objQuestion->selectNbrExercises() > 1)
                {
                    $usedInSeveralExercises=1;
                }
                else
                {
                    // separates text and weightings by '::'
                    $reponse.='::';

                    $questionWeighting=0;

                    foreach($weighting as $val)
                    {
                        // a blank can't have a negative weighting
                        $val=abs($val);

                        $questionWeighting+=$val;

                        // adds blank weighting at the end of the text
                        $reponse.=$val.',';
                    }

                    $reponse=substr($reponse,0,-1);

                    $objAnswer->createAnswer($reponse,0,'',0,'');
                    $objAnswer->save();

                    // sets the total weighting of the question
                    $objQuestion->updateWeighting($questionWeighting);
                    $objQuestion->save($exerciseId);

                    $editQuestion=$questionId;

                    unset($modifyAnswers);
                }
            }
            // if no text has been typed or the text contains no blank
            elseif(empty($reponse))
            {
                $msgErr=get_lang('langGiveText');
            }
            elseif(!ereg('\[.+\]',$reponse))
            {
                $msgErr=get_lang('langDefineBlanks');
            }
            else
            {
                // now we're going to give a weighting to each blank
                $setWeighting=1;

                unset($submitAnswers);

                // removes character '::' possibly inserted by the user in the text
                $reponse=str_replace('::','',$reponse);

                // we save the answer because it will be modified
                $temp=$reponse;

                // 1. find everything between the [tex] and [/tex] tags
                $startlocations=strpos($temp,'[tex]');
                $endlocations=strpos($temp,'[/tex]');

                if($startlocations !== false && $endlocations !== false)
                {
                    $texstring=substr($temp,$startlocations,$endlocations-$startlocations+6);

                    // 2. replace this by {texcode}
                    $temp=str_replace($texstring,"{texcode}",$temp);
                }

                // blanks will be put into an array
                $blanks=Array();

                $i=1;

                // the loop will stop at the end of the text
                while(1)
                {
                    // quits the loop if there are no more blanks
                    if(($pos = strpos($temp,'[')) === false)
                    {
                        break;
                    }

                    // removes characters till '['
                    $temp=substr($temp,$pos+1);

                    // quits the loop if there are no more blanks
                    if(($pos = strpos($temp,']')) === false)
                    {
                        break;
                    }

                    // stores the found blank into the array
                    $blanks[$i++]=substr($temp,0,$pos);

                    // removes the character ']'
                    $temp=substr($temp,$pos+1);
                }
            }
        }
        else
        {
            unset($setWeighting);
        }
    }
    elseif($answerType == FREE_ANSWER)
    {
        if($debug>0){echo str_repeat('&nbsp;',2).'$answerType is FREE_ANSWER'."<br />\n";}
        if ( empty ( $free_comment ) ) {
            $free_comment = $_POST['comment'];
        }
        if ( empty ( $weighting ) ) {
            $weighting = $_POST['weighting'];
        }

        if(!$buttonBack)
        {
            if($debug>0){echo str_repeat('&nbsp;',4).'$buttonBack is not set'."<br />\n";}
            if($setWeighting)
            {
                if($debug>0){echo str_repeat('&nbsp;',6).'$setWeighting is set'."<br />\n";}
                // checks if the question is used in several exercises
                if($exerciseId && !$modifyIn && $objQuestion->selectNbrExercises() > 1)
                {
                    $usedInSeveralExercises=1;
                }
                else
                {
                    
                    $objAnswer->createAnswer('',0,$free_comment,$weighting,'');
                    $objAnswer->save();

                    // sets the total weighting of the question
                    $objQuestion->updateWeighting($weighting);
                    $objQuestion->save($exerciseId);

                    $editQuestion=$questionId;

                    unset($modifyAnswers);
                }
            }
            // if no text has been typed or the text contains no blank
            elseif(empty($free_comment))
            {
                if($debug>0){echo str_repeat('&nbsp;',6).'$free_comment is empty'."<br />\n";}
                $msgErr=get_lang('langGiveText');
            }
            /*elseif(!ereg('\[.+\]',$reponse))
            {
                $msgErr=get_lang('langDefineBlanks');
            }*/
            else
            {
                if($debug>0){echo str_repeat('&nbsp;',6).'$setWeighting is not set and $free_comment is not empty'."<br />\n";}

                // now we're going to give a weighting to each blank
                $setWeighting=1;

                unset($submitAnswers);
            }
        }
        else
        {
            unset($setWeighting);
        }
    }
    elseif($answerType == MATCHING)
    {
        if($debug>0){echo str_repeat('&nbsp;',2).'$answerType is MATCHING'."<br />\n";}
        for($i=1;$i <= $nbrOptions;$i++)
        {
            $option[$i]=trim($option[$i]);

            // checks if field is empty
            if(empty($option[$i]) && $option[$i] != '0')
            {
                $msgErr=get_lang('langFillLists');

                // clears options already recorded into the Answer object
                $objAnswer->cancel();

                break;
            }
            else
            {
                // adds the option into the object
                $objAnswer->createAnswer($option[$i],0,'',0,$i);
            }
        }

        $questionWeighting=0;

        if(empty($msgErr))
        {
            for($j=1;$j <= $nbrMatches;$i++,$j++)
            {
                $match[$i]=trim($match[$i]);
                $weighting[$i]=abs(intval($weighting[$i]));

                $questionWeighting+=$weighting[$i];

                // checks if field is empty
                if(empty($match[$i]) && $match[$i] != '0')
                {
                    $msgErr=get_lang('langFillLists');

                    // clears matches already recorded into the Answer object
                    $objAnswer->cancel();

                    break;
                }
                // check if correct number
                else
                {
                    // adds the answer into the object
                    $objAnswer->createAnswer($match[$i],$sel[$i],'',$weighting[$i],$i);
                }
            }
        }

        if(empty($msgErr))
        {
            // checks if the question is used in several exercises
            if($exerciseId && !$modifyIn && $objQuestion->selectNbrExercises() > 1)
            {
                $usedInSeveralExercises=1;
            }
            else
            {
                // all answers have been recorded, so we save them into the data base
                $objAnswer->save();

                // sets the total weighting of the question
                $objQuestion->updateWeighting($questionWeighting);
                $objQuestion->save($exerciseId);

                $editQuestion=$questionId;

                unset($modifyAnswers);
            }
        }
    }
    if($debug>0){echo '$modifyIn was set - end'."<br />\n";}

}

if($modifyAnswers)
{
    if($debug>0){echo str_repeat('&nbsp;',0).'$modifyAnswers is set'."<br />\n";}

    // construction of the Answer object
    $objAnswer=new Answer($questionId);

    api_session_register('objAnswer');

    if($answerType == UNIQUE_ANSWER || $answerType == MULTIPLE_ANSWER)
    {
       if($debug>0){echo str_repeat('&nbsp;',2).'$answerType is UNIQUE_ANSWER or MULTIPLE_ANSWER'."<br />\n";}
        if(!$nbrAnswers)
        {
            $nbrAnswers=$objAnswer->selectNbrAnswers();

            $reponse=Array();
            $comment=Array();
            $weighting=Array();

            // initializing
            if($answerType == MULTIPLE_ANSWER)
            {
                $correct=Array();
            }
            else
            {
                $correct=0;
            }

            for($i=1;$i <= $nbrAnswers;$i++)
            {
                $reponse[$i]=$objAnswer->selectAnswer($i);
                $comment[$i]=$objAnswer->selectComment($i);
                $weighting[$i]=$objAnswer->selectWeighting($i);

                if($answerType == MULTIPLE_ANSWER)
                {
                    $correct[$i]=$objAnswer->isCorrect($i);
                }
                elseif($objAnswer->isCorrect($i))
                {
                    $correct=$i;
                }
            }
        }

        if($lessAnswers)
        {
            $nbrAnswers--;
        }

        if($moreAnswers)
        {
            $nbrAnswers++;
        }

        // minimum 2 answers
        if($nbrAnswers < 2)
        {
            $nbrAnswers=2;
        }
    }
    elseif($answerType == FILL_IN_BLANKS)
    {
       if($debug>0){echo str_repeat('&nbsp;',2).'$answerType is FILL_IN_BLANKS'."<br />\n";}
        if(!$submitAnswers && !$buttonBack)
        {
            if(!$setWeighting)
            {
                $reponse=$objAnswer->selectAnswer(1);

                list($reponse,$weighting)=explode('::',$reponse);

                $weighting=explode(',',$weighting);

                $temp=Array();

                // keys of the array go from 1 to N and not from 0 to N-1
                for($i=0;$i < sizeof($weighting);$i++)
                {
                    $temp[$i+1]=$weighting[$i];
                }

                $weighting=$temp;
            }
            elseif(!$modifyIn)
            {
                $weighting=unserialize($weighting);
            }
        }
    }
    elseif($answerType == FREE_ANSWER)
    {
        if($debug>0){echo str_repeat('&nbsp;',2).'$answerType is FREE_ANSWER'."<br />\n";}
        if(!$submitAnswers && !$buttonBack)
        {
            if($debug>0){echo str_repeat('&nbsp;',4).'$submitAnswers && $buttonsBack are unset'."<br />\n";}
            if(!$setWeighting)
            {
                if($debug>0){echo str_repeat('&nbsp;',6).'$setWeighting is unset'."<br />\n";}

                //YW: not quite  sure about whether the comment has already been recovered,
                // but as we have passed into the submitAnswers loop, this should be in the
                // objAnswer object.
                $free_comment = $objAnswer->selectComment(1);
            }
            elseif(!$modifyIn)
            {
                if($debug>0){echo str_repeat('&nbsp;',6).'$setWeighting is set and $modifyIn is unset'."<br />\n";}
                $weighting=unserialize($weighting);
            }
        }
    }
    elseif($answerType == MATCHING)
    {
        if($debug>0){echo str_repeat('&nbsp;',2).'$answerType is MATCHING'."<br />\n";}
        if(!$nbrOptions || !$nbrMatches)
        {
            $option=Array();
            $match=Array();
            $sel=Array();

            $nbrOptions=$nbrMatches=0;

            // fills arrays with data from de data base
            for($i=1;$i <= $objAnswer->selectNbrAnswers();$i++)
            {
                // it is a match
                if($objAnswer->isCorrect($i))
                {
                    $match[$i]=$objAnswer->selectAnswer($i);
                    $sel[$i]=$objAnswer->isCorrect($i);
                    $weighting[$i]=$objAnswer->selectWeighting($i);
                    $nbrMatches++;
                }
                // it is an option
                else
                {
                    $option[$i]=$objAnswer->selectAnswer($i);
                    $nbrOptions++;
                }
            }
        }

        if($lessOptions)
        {
            // keeps the correct sequence of array keys when removing an option from the list
            for($i=$nbrOptions+1,$j=1;$nbrOptions > 2 && $j <= $nbrMatches;$i++,$j++)
            {
                $match[$i-1]=$match[$i];
                $sel[$i-1]=$sel[$i];
                $weighting[$i-1]=$weighting[$i];
            }

            unset($match[$i-1]);
            unset($sel[$i-1]);

            $nbrOptions--;
        }

        if($moreOptions)
        {
            // keeps the correct sequence of array keys when adding an option into the list
            for($i=$nbrMatches+$nbrOptions;$i > $nbrOptions;$i--)
            {
                $match[$i+1]=$match[$i];
                $sel[$i+1]=$sel[$i];
                $weighting[$i+1]=$weighting[$i];
            }

            unset($match[$i+1]);
            unset($sel[$i+1]);

            $nbrOptions++;
        }

        if($lessMatches)
        {
            $nbrMatches--;
        }

        if($moreMatches)
        {
            $nbrMatches++;
        }

        // minimum 2 options
        if($nbrOptions < 2)
        {
            $nbrOptions=2;
        }

        // minimum 2 matches
        if($nbrMatches < 2)
        {
            $nbrMatches=2;
        }

    }

    if(!$usedInSeveralExercises)
    {
        if($debug>0){echo str_repeat('&nbsp;',2).'$usedInSeveralExercises is untrue'."<br />\n";}
    
        if($answerType == UNIQUE_ANSWER || $answerType == MULTIPLE_ANSWER)
        {
            if($debug>0){echo str_repeat('&nbsp;',4).'$answerType is UNIQUE_ANSWER or MULTIPLE_ANSWER'."<br />\n";}

?>

<h3>
  <?php echo $questionName; ?>
</h3>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?modifyAnswers=<?php echo $modifyAnswers; ?>">
<input type="hidden" name="formSent" value="1">
<input type="hidden" name="nbrAnswers" value="<?php echo $nbrAnswers; ?>">
<table width="650" border="0" cellpadding="5">

<?php
			if($okPicture)
			{
?>

<tr>
  <td colspan="5" align="center"><img src="../document/download.php?doc_url=%2Fimages%2F<?php echo $pictureName; ?>" border="0"></td>
</tr>

<?php
			}

			if(!empty($msgErr))
			{
?>

<tr>
  <td colspan="5">

<?php
	Display::display_normal_message($msgErr); //main API
?>

  </td>
</tr>

<?php
			}
?>

<tr>
  <td colspan="5"><?php echo get_lang('langAnswers'); ?> :</td>
</tr>
<tr bgcolor="#E6E6E6">
  <td>N�</td>
  <td><?php echo get_lang('langTrue'); ?></td>
  <td><?php echo get_lang('langAnswer'); ?></td>
  <td><?php echo get_lang('langComment'); ?></td>
  <td><?php echo get_lang('langQuestionWeighting'); ?></td>
</tr>

<?php
			for($i=1;$i <= $nbrAnswers;$i++)
			{
?>

<tr>
  <td valign="top"><?php echo $i; ?></td>

<?php
				if($answerType == UNIQUE_ANSWER)
				{
?>

  <td valign="top"><input class="checkbox" type="radio" value="<?php echo $i; ?>" name="correct" <?php if($correct == $i) echo 'checked="checked"'; ?>></td>

<?php
				}
				else
				{
?>

  <td valign="top"><input class="checkbox" type="checkbox" value="1" name="correct[<?php echo $i; ?>]" <?php if($correct[$i]) echo 'checked="checked"'; ?>></td>

<?php
				}
?>

  <td align="left"><textarea wrap="virtual" rows="7" cols="25" name="reponse[<?php echo $i; ?>]"><?php echo htmlentities($reponse[$i]); ?></textarea></td>
  <td align="left"><textarea wrap="virtual" rows="7" cols="25" name="comment[<?php echo $i; ?>]"><?php echo htmlentities($comment[$i]); ?></textarea></td>
  <td valign="top"><input type="text" name="weighting[<?php echo $i; ?>]" size="5" value="<?php echo isset($weighting[$i])?$weighting[$i]:0; ?>"></td>
</tr>

<?php
  			}
?>

<tr>
  <td colspan="5">
	<input type="submit" name="submitAnswers" value="<?php echo get_lang('langOk'); ?>">
	&nbsp;&nbsp;<input type="submit" name="lessAnswers" value="<?php echo get_lang('langLessAnswers'); ?>">
	&nbsp;&nbsp;<input type="submit" name="moreAnswers" value="<?php echo get_lang('langMoreAnswers'); ?>">
	<!-- &nbsp;&nbsp;<input type="submit" name="cancelAnswers" value="<?php echo get_lang('langCancel'); ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('langConfirmYourChoice'))); ?>')) return false;"> //-->
  </td>
</tr>
</table>
</form>

<?php
        }
        elseif($answerType == FILL_IN_BLANKS)
        {
            if($debug>0){echo str_repeat('&nbsp;',4).'$answerType is FILL_IN_BLANKS'."<br />\n";}

?>

<h3>
  <?php echo $questionName; ?>
</h3>

<form name="formulaire" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?modifyAnswers=<?php echo $modifyAnswers; ?>">
<input type="hidden" name="formSent" value="1">
<input type="hidden" name="setWeighting" value="<?php echo $setWeighting; ?>">

<?php
            if(!$setWeighting)
            {
?>

<input type="hidden" name="weighting" value="<?php echo $submitAnswers?htmlentities($weighting):htmlentities(serialize($weighting)); ?>">

<table border="0" cellpadding="5" width="500">

<?php
                if($okPicture)
                {
?>

<tr>
  <td align="center"><img src="../document/download.php?doc_url=%2Fimages%2F<?php echo $pictureName; ?>" border="0"></td>
</tr>

<?php
                }
    
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
  <td><?php echo get_lang('langTypeTextBelow').', '.get_lang('langAnd').' '.get_lang('langUseTagForBlank'); ?> :</td>
</tr>
<tr>
  <td><textarea wrap="virtual" name="reponse" cols="65" rows="6"><?php if(!$submitAnswers && empty($reponse)) echo get_lang('langDefaultTextInBlanks'); else echo htmlentities($reponse); ?></textarea></td>
</tr>
<tr>
  <td colspan="5">
	<!-- <input type="submit" name="cancelAnswers" value="<?php echo get_lang('langCancel'); ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('langConfirmYourChoice'))); ?>')) return false;">
	&nbsp;&nbsp; //--> <input type="submit" name="submitAnswers" value="<?php echo get_lang('langOk'); ?>">
  </td>
</tr>
</table>

<?php
            }
            else
            {
?>

<input type="hidden" name="blanks" value="<?php echo htmlentities(serialize($blanks)); ?>">
<input type="hidden" name="reponse" value="<?php echo htmlentities($reponse); ?>">

<table border="0" cellpadding="5" width="500">

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
  <td colspan="2"><?php echo get_lang('langWeightingForEachBlank'); ?> :</td>
</tr>
<tr>
  <td colspan="2">&nbsp;</td>
</tr>

<?php
                foreach($blanks as $i=>$blank)
                {
?>

<tr>
  <td width="50%"><?php echo $blank; ?> :</td>
  <td width="50%"><input type="text" name="weighting[<?php echo $i; ?>]" size="5" value="<?php echo intval($weighting[$i]); ?>"></td>
</tr>

<?php
                }
?>

<tr>
  <td colspan="2">&nbsp;</td>
</tr>
<tr>
  <td colspan="2">
	<input type="submit" name="buttonBack" value="&lt; <?php echo get_lang('langBack'); ?>">
	&nbsp;&nbsp;<input type="submit" name="submitAnswers" value="<?php echo get_lang('langOk'); ?>">
	<!-- &nbsp;&nbsp;<input type="submit" name="cancelAnswers" value="<?php echo get_lang('langCancel'); ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('langConfirmYourChoice'))); ?>')) return false;"> //-->
  </td>
</tr>
</table>

<?php
            }
?>

</form>

<?php
        }
        elseif($answerType == FREE_ANSWER)
        {
            if($debug>0){echo str_repeat('&nbsp;',4).'$answerType is FREE_ANSWER'."<br />\n";}

?>

<h3>
  <?php echo $questionName; ?>
</h3>

<form name="formulaire" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?modifyAnswers=<?php echo $modifyAnswers; ?>">
<input type="hidden" name="formSent" value="1">
<input type="hidden" name="setWeighting" value="<?php echo $setWeighting; ?>">

<?php
            if(!$setWeighting)
            {
?>

<table border="0" cellpadding="5" width="500">

<?php
                if($okPicture)
                {
?>

<tr>
  <td align="center"><img src="../document/download.php?doc_url=%2Fimages%2F<?php echo $pictureName; ?>" border="0"></td>
</tr>

<?php
                }

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
  <td><?php echo get_lang('langTypeTextBelow'); ?> :</td>
</tr>
<tr>
  <td><textarea wrap="virtual" name="comment" cols="65" rows="3"><?php if(!$submitAnswers && empty($free_comment)) echo ''; else echo htmlentities($free_comment); ?></textarea></td>
  <td align="center"><input type="text" size="8" name="weighting" value="<?php if(!$submitAnswers && !isset($weighting)) echo '5'; else echo $weighting; ?>"></td>
</tr>
<tr>
  <td colspan="5">
	<!-- <input type="submit" name="cancelAnswers" value="<?php echo get_lang('langCancel'); ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('langConfirmYourChoice'))); ?>')) return false;">
	&nbsp;&nbsp; //--> <input type="submit" name="submitAnswers" value="<?php echo get_lang('langOk'); ?>">
  </td>
</tr>
</table>

<?php
            }
            else
            {
?>

<input type="hidden" name="comment" value="<?php echo htmlentities($free_comment); ?>">

<table border="0" cellpadding="5" width="500">

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
  <td colspan="2"><?php echo nl2br($free_comment); ?></td>
</tr>
<tr>
  <td colspan="2">
	<input type="submit" name="buttonBack" value="&lt; <?php echo get_lang('langBack'); ?>">
	&nbsp;&nbsp;<input type="submit" name="submitAnswers" value="<?php echo get_lang('langOk'); ?>">
	<!-- &nbsp;&nbsp;<input type="submit" name="cancelAnswers" value="<?php echo get_lang('langCancel'); ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('langConfirmYourChoice'))); ?>')) return false;"> //-->
  </td>
</tr>
</table>

<?php
            }
?>

</form>

<?php
        }//end of FREE_ANSWER type
        elseif($answerType == MATCHING)
        {
?>

<h3>
  <?php echo $questionName; ?>
</h3>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?modifyAnswers=<?php echo $modifyAnswers; ?>">
<input type="hidden" name="formSent" value="1">
<input type="hidden" name="nbrOptions" value="<?php echo $nbrOptions; ?>">
<input type="hidden" name="nbrMatches" value="<?php echo $nbrMatches; ?>">
<table border="0" cellpadding="5">

<?php
            if($okPicture)
            {
?>

<tr>
  <td colspan="4" align="center"><img src="../document/download.php?doc_url=%2Fimages%2F<?php echo $pictureName; ?>" border="0"></td>
</tr>

<?php
            }

            if(!empty($msgErr))
            {
?>

<tr>
  <td colspan="4">

<?php
                Display::display_normal_message($msgErr); //main API
?>

  </td>
</tr>

<?php
            }

            $listeOptions=Array();

            // creates an array with the option letters
            for($i=1,$j='A';$i <= $nbrOptions;$i++,$j++)
            {
                $listeOptions[$i]=$j;
            }
?>

<tr>
  <td colspan="3"><?php echo get_lang('langMakeCorrespond'); ?> :</td>
  <td><?php echo get_lang('langQuestionWeighting'); ?> :</td>
</tr>

<?php
            for($j=1;$j <= $nbrMatches;$i++,$j++)
            {
?>

<tr>
  <td><?php echo $j; ?></td>
  <td><input type="text" name="match[<?php echo $i; ?>]" size="58" value="<?php if(!$formSent && !isset($match[$i])) echo ${"langDefaultMakeCorrespond$j"}; else echo htmlentities($match[$i]); ?>"></td>
  <td align="center"><select name="sel[<?php echo $i; ?>]">

<?php
                foreach($listeOptions as $key=>$val)
                {
?>

	<option value="<?php echo $key; ?>" <?php if((!$submitAnswers && !isset($sel[$i]) && $j == 2 && $val == 'B') || $sel[$i] == $key) echo 'selected="selected"'; ?>><?php echo $val; ?></option>

<?php
                } // end foreach()
?>

  </select></td>
  <td align="center"><input type="text" size="8" name="weighting[<?php echo $i; ?>]" value="<?php if(!$submitAnswers && !isset($weighting[$i])) echo '5'; else echo $weighting[$i]; ?>"></td>
</tr>

<?php
            } // end for()
?>

<tr>
  <td colspan="4">
	<input type="submit" name="lessMatches" value="<?php echo get_lang('langLessElements'); ?>">
	&nbsp;&nbsp;<input type="submit" name="moreMatches" value="<?php echo get_lang('langMoreElements'); ?>">
  </td>
</tr>
<tr>
  <td colspan="4"><?php echo get_lang('langDefineOptions'); ?> :</td>
</tr>

<?php
            foreach($listeOptions as $key=>$val)
            {
?>

<tr>
  <td><?php echo $val; ?></td>
  <td colspan="3"><input type="text" name="option[<?php echo $key; ?>]" size="80" value="<?php if(!$formSent && !isset($option[$key])) echo get_lang("langDefaultMatchingOpt$val"); else echo htmlentities($option[$key]); ?>"></td>
</tr>

<?php
            } // end foreach()
?>

<tr>
  <td colspan="4">
	<input type="submit" name="lessOptions" value="<?php echo get_lang('langLessElements'); ?>">
	&nbsp;&nbsp;<input type="submit" name="moreOptions" value="<?php echo get_lang('langMoreElements'); ?>">
  </td>
</tr>
<tr>
  <td colspan="4">&nbsp;</td>
</tr>
<tr>
  <td colspan="4">
	<!-- <input type="submit" name="cancelAnswers" value="<?php echo get_lang('langCancel'); ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('langConfirmYourChoice'))); ?>')) return false;">
	&nbsp;&nbsp; //--> <input type="submit" name="submitAnswers" value="<?php echo get_lang('langOk'); ?>">
  </td>
</tr>
</table>
</form>

<?php
        }
    }
    if($debug>0){echo str_repeat('&nbsp;',0).'$modifyAnswers was set - end'."<br />\n";}
}
?>
