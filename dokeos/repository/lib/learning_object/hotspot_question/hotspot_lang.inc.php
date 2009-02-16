<?php
	require_once dirname(__FILE__).'/../../../../common/global.inc.php';

	$language_consts = array(
		'select' => 'Select',
		'&square' => 'Square',
		'&circle' => 'Elipse',
		'&polygon' => 'Polygon',
		'&status1' => 'DrawAHotspot',
		'&status2_poly' => 'RightClickToClosePolygon',
		'&status2_other' => 'ReleaseMouseButtonToSave',
		'&status3' => 'HotspotSaved',
		'&exercise_status_1' => 'QuestionNotTerminated',
		'&exercise_status_2' => 'ValidateAnswers',
		'&exercise_status_3' => 'QuestionTerminated',
		'&showUserPoints' => 'ShowHideUserclicks',
		'&showHotspots' => 'ShowHideHotspots',
		'&labelPolyMenu' => 'ClosePolygon',
		'&triesleft' => 'AttemptsLeft',
		'&exeFinished' => 'AllAnswersDone',
		'&nextAnswer' => 'NowClickOn'
	);
		
	$all = '';
	
	foreach ($language_consts as $key => $word)
	{
		$translation = htmlspecialchars(Translation :: get($word));
		//$translation = str_replace('&quot', '', $translation);
		//$translation = str_replace(';', '', $translation);
		$all .= $key.'='.$translation;
	}
	
	echo $all.'&done=done';
?>