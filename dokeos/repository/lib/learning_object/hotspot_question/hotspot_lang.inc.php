<?php
require_once '../../../../common/global.inc.php';
require_once '../../../../common/translation/translation.class.php';

//$hotspot_lang_file = api_get_path(SYS_LANG_PATH);

	$language_consts = array(
			'select' => '"Select"',
			'&square' => '"Square"',
			'&circle' => '"Elipse"',
			'&polygon' => '"Polygon"',
			'&status1' => '"DrawAHotspot"',
			'&status2_poly' => '"RightClickToClosePolygon"',
			'&status2_other' => '"ReleaseMouseButtonToSave"',
			'&status3' => '"HotspotSaved"',
			'&exercise_status_1' => '"QuestionNotTerminated"',
			'&exercise_status_2' => '"ValidateAnswers"',
			'&exercise_status_3' => '"QuestionTerminated"',
			'&showUserPoints' => '"ShowHideUserclicks"',
			'&showHotspots' => '"ShowHideHotspots"',
			'&labelPolyMenu' => '"ClosePolygon"',
			'&triesleft' => '"AttemptsLeft"',
			'&exeFinished' => '"AllAnswersDone"',
			'&nextAnswer' => '"NowClickOn"'
		);
		
		$all = '';
		
		foreach ($language_consts as $key => $word)
		{
			$translation = Translation :: get($word);
			$all .= $key.'='.$translation;
		}
		
		echo $all.'&done=done';
?>