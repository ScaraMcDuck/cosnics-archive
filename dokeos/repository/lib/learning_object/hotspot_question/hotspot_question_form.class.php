<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/hotspot_question.class.php';
/**
 * This class represents a form to create or update hotspot questions
 */
class HotspotQuestionForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		//dump($_POST);
		parent :: build_creation_form();
		$this->check_upload();
		if (!isset($_SESSION['web_path']))
		{
			$this->addElement('file', 'file', Translation :: get('UploadImage'));
			$this->addElement('submit', 'fileupload', Translation :: get('Submit'));
		}
		else
		{
			$var = ($_SESSION['web_path']);
			$this->add_options();
			$modifyAnswers = true;
			$this->addElement('text', 'filename', Translation :: get('Filename'), array('DISABLED'));
			//$this->addElement('html', '<img src="'.$var.'" alt="" />');
			$this->addElement($this->get_scripts_element());
			$this->output_action_script();
		}
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('category');
	}
	
	protected function build_editing_form()
	{
		parent :: build_creation_form();
		$this->check_upload();
		if (!isset($_SESSION['web_path']))
		{
			$this->addElement('file', 'file', Translation :: get('UploadImage'));
			$this->addElement('submit', 'fileupload', Translation :: get('Submit'));
		}
		else
		{
			$var = ($_SESSION['web_path']);
			$this->addElement('text', 'filename', Translation :: get('Filename'), array('DISABLED'));
			//$this->addElement('html', '<img src="'.$var.'" alt="" />');
		}
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->add_options();
		$this->addElement('category');
	}
	
	function setDefaults($defaults = array ())
	{
		if(!$this->isSubmitted())
		{
			$object = $this->get_learning_object();
			if(!is_null($object))
			{
				$options = $object->get_answers();
				foreach($options as $index => $option)
				{
					$defaults['option'][$index] = $option->get_value();
					$defaults['weight'][$index] = $option->get_weight();
				}
			}
		}
		$defaults['filename'] = $_SESSION['web_path'];	
		parent :: setDefaults($defaults);
	}
	
	function create_learning_object()
	{
		$object = new HotspotQuestion();
		$object->set_image($_SESSION['web_path']);
		$this->set_learning_object($object);
		$this->add_options_to_object();
		unset($_SESSION['web_path']);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$this->add_options_to_object();
		return parent :: update_learning_object();
	}
	
	private function add_options_to_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		$options = array();
		foreach($values['option'] as $option_id => $value)
		{
			$weight = $values['weight'][$option_id];
			$options[] = new FillInBlanksQuestionAnswer($value, $weight);
		}
		$object->set_answers($options);
	}
	
	function validate()
	{
		if(isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['fileupload']))
		{
			return false;
		}
		return parent::validate();
	}
	
	function check_upload()
	{
		if ($_FILES['file'] != null && $_SESSION['web_path'] == null)
		{
			$owner = $this->get_owner_id();
			$filename = Filesystem :: create_unique_name(Path :: get(SYS_REPO_PATH).$owner, $_FILES['file']['name']);
			$path = $owner.'/'.$filename;
			$full_path = Path :: get(SYS_REPO_PATH).$path;
			$web_path = Path :: get(WEB_REPO_PATH).$path;
			move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "'.$full_path.'"');
			chmod($full_path, 0777);
			$_SESSION['hotspot_path'] = Path ::get(WEB_PATH).'/files/repository/'.$owner.'/'.$filename;
			$_SESSION['web_path'] = $web_path;
			$_SESSION['full_path'] = $full_path;
			$_FILES['file'] = null;
		}
	}
	
	function output_lang_action_script()
	{
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
		
		echo $all;
	}
	
	function output_action_script()
	{
		$picturePath   = $_SESSION['full_path'];
		$hotspotImagePath = $_SESSION['hotspot_path'];
		$pictureParts = split('/', $picturePath);
		$pictureName   = $pictureParts[count($pictureParts) - 1];
		$pictureSize   = getimagesize($picturePath);
		$pictureWidth  = $pictureSize[0];
		$pictureHeight = $pictureSize[1];
		
		//$courseLang = Translation :: get_language();
		//$courseCode = $_course['sysCode'];
		//$coursePath = $_course['path'];
		$courseLang = Translation :: get_language();
		$courseCode = 'false';
		$coursePath = 'false';
		$output = "hotspot_lang=".$courseLang."&hotspot_image=".$hotspotImagePath."&hotspot_image_width=".$pictureWidth."&hotspot_image_height=".$pictureHeight."&courseCode=".$coursePath;
		$i = 0;
		$nmbrTries = 0;
		
		$answers = $_POST['answer'];
		//dump($answers);
		$weights = $_POST['weight'];
		$types = $_POST['type'];
		$coordinates = $_POST['coordinates'];
		$nbrAnswers = count($answers);
		
		for($i = 0;$i < $nbrAnswers;$i++)
		{
		   	$output .= "&hotspot_".$i."=true";
			$output .= "&hotspot_".$i."_answer=".$answers[$i];
		
			$output .= "&hotspot_".$i."_type=".$types[$i];
			// This is a good answer, count + 1 for nmbr of clicks
			if ($weights[$i] > 0)
			{
				$nmbrTries++;
			}
		
			$output .= "&hotspot_".$i."_coord=".$coordinates[$i]."";
		}
		
		// Generate empty
		$i++;
		for ($i; $i <= 12; $i++)
		{
			$output .= "&hotspot_".$i."=false";
		}
		// Output
		echo $output."&nmbrTries=".$nmbrTries."&done=done";
	}
	
	function get_scripts_element()
	{
		$hotspot_path = Path :: get(WEB_PLUGIN_PATH).'/hotspot/hotspot/hotspot_admin.swf';
		//dump($hotspot_path);
		return $this->createElement('html','
				<script type="text/vbscript" />
				Function VBGetSwfVer(i)
				  on error resume next
				  Dim swControl, swVersion
				  swVersion = 0
				
				  set swControl = CreateObject("ShockwaveFlash.ShockwaveFlash." + CStr(i))
				  if (IsObject(swControl)) then
				    swVersion = swControl.GetVariable("\$version")
				  end if
				  VBGetSwfVer = swVersion
				End Function
				</script>
				<script type="text/javascript">
				<!--
					//Globals
					// Major version of Flash required
					var requiredMajorVersion = 7;
					// Minor version of Flash required
					var requiredMinorVersion = 0;
					// Minor version of Flash required
					var requiredRevision = 0;
					// the version of javascript supported
					var jsVersion = 1.0;
					// 
					
					var isIE  = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
					var isWin = (navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
					var isOpera = (navigator.userAgent.indexOf("Opera") != -1) ? true : false;
					jsVersion = 1.1;
					// JavaScript helper required to detect Flash Player PlugIn version information
					function JSGetSwfVer(i){
						// NS/Opera version >= 3 check for Flash plugin in plugin array
						if (navigator.plugins != null && navigator.plugins.length > 0) {
							if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
								var swVer2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
					      		var flashDescription = navigator.plugins["Shockwave Flash" + swVer2].description;
								descArray = flashDescription.split(" ");
								tempArrayMajor = descArray[2].split(".");
								versionMajor = tempArrayMajor[0];
								versionMinor = tempArrayMajor[1];
								if ( descArray[3] != "" ) {
									tempArrayMinor = descArray[3].split("r");
								} else {
									tempArrayMinor = descArray[4].split("r");
								}
					      		versionRevision = tempArrayMinor[1] > 0 ? tempArrayMinor[1] : 0;
					            flashVer = versionMajor + "." + versionMinor + "." + versionRevision;
					      	} else {
								flashVer = -1;
							}
						}
						// MSN/WebTV 2.6 supports Flash 4
						else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != -1) flashVer = 4;
						// WebTV 2.5 supports Flash 3
						else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != -1) flashVer = 3;
						// older WebTV supports Flash 2
						else if (navigator.userAgent.toLowerCase().indexOf("webtv") != -1) flashVer = 2;
						// Can\'t detect in all other cases
						else {
					
							flashVer = -1;
						}
						return flashVer;
					}
					// When called with reqMajorVer, reqMinorVer, reqRevision returns true if that version or greater is available
					function DetectFlashVer(reqMajorVer, reqMinorVer, reqRevision)
					{
					 	reqVer = parseFloat(reqMajorVer + "." + reqRevision);
					   	// loop backwards through the versions until we find the newest version
						for (i=25;i>0;i--) {
							if (isIE && isWin && !isOpera) {
								versionStr = VBGetSwfVer(i);
							} else {
								versionStr = JSGetSwfVer(i);
							}
							if (versionStr == -1 ) {
								return false;
							} else if (versionStr != 0) {
								if(isIE && isWin && !isOpera) {
									tempArray         = versionStr.split(" ");
									tempString        = tempArray[1];
									versionArray      = tempString .split(",");
								} else {
									versionArray      = versionStr.split(".");
								}
								versionMajor      = versionArray[0];
								versionMinor      = versionArray[1];
								versionRevision   = versionArray[2];
					
								versionString     = versionMajor + "." + versionRevision;   // 7.0r24 == 7.24
								versionNum        = parseFloat(versionString);
					        	// is the major.revision >= requested major.revision AND the minor version >= requested minor
								if ( (versionMajor > reqMajorVer) && (versionNum >= reqVer) ) {
									return true;
								} else {
									return ((versionNum >= reqVer && versionMinor >= reqMinorVer) ? true : false );
								}
							}
						}
					}
									
				var requiredMajorVersion = 7;
				var requiredMinorVersion = 0;
				var requiredRevision = 0;
				//var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
				var hasRequestedVersion = true;
				// Check to see if the version meets the requirements for playback
				if (hasRequestedVersion) {  // if weve detected an acceptable version
				    var oeTags = \'<object type="application/x-shockwave-flash" data="'.$hotspot_path.'?modifyAnswers=' . $modifyAnswers.'" width="720" height="650">\'
								+ \'<param name="movie" value="'.$hotspot_path.'?modifyAnswers=' . $id.'" />\'
								//+ \'<param name="test" value="OOoowww fo shooww" />\'
								+ \'</object>\';
				    document.write(oeTags);   // embed the Flash Content SWF when all tests are passed
				} else {  // flash is too old or we can\'t detect the plugin
					var alternateContent = "Error<br \/>"
						+ \'This content requires the Macromedia Flash Player.<br \/>\'
						+ \'<a href="http://www.macromedia.com/go/getflash/">Get Flash<\/a>\';
					document.write(alternateContent);  // insert non-flash content
				}
				// -->
			</script>
			'
			);
	}
	
	/**
	 * Adds the form-fields to the form to provide the possible options for this
	 * multiple choice question
	 */
	private function add_options()
	{
		if(!$this->isSubmitted())
		{
			unset($_SESSION['mc_number_of_options']);
			unset($_SESSION['mc_skip_options']);
		}
		if(!isset($_SESSION['mc_number_of_options']))
		{
			$_SESSION['mc_number_of_options'] = 1;
		}
		if(!isset($_SESSION['mc_skip_options']))
		{
			$_SESSION['mc_skip_options'] = array();
		}
		if(isset($_POST['add']))
		{
			$_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options']+1;
		}
		/*if(isset($_FILES['file']))
		{
			$filename = Filesystem::create_unique_name(Path :: get(SYS_REPO_PATH).$owner, $_FILES['file']['name']);
			$path = $owner.'/'.$filename;
			$full_path = Path :: get(SYS_REPO_PATH).$path;
			move_uploaded_file($_FILES['file']['tmp_name'], $full_path);
			$_SESSION['fileupload'] = $full_path;
		}*/
		if(isset($_POST['remove']))
		{
			$indexes = array_keys($_POST['remove']);
			$_SESSION['mc_skip_options'][] = $indexes[0];
		}
		$object = $this->get_learning_object();
		if(!$this->isSubmitted() && !is_null($object))
		{
			$_SESSION['mc_number_of_options'] = $object->get_number_of_options();
			$_SESSION['mc_answer_type'] = $object->get_answer_type();
		}
		$number_of_options = intval($_SESSION['mc_number_of_options']);
		$show_label = true;
		
		if (isset($_SESSION['file']))
		{
			$this->addElement('html', '<div class="learning_object">');
			$this->addElement('html', '</div>');
		}
		for($option_number = 0; $option_number <$number_of_options ; $option_number++)
		{
			if(!in_array($option_number,$_SESSION['mc_skip_options']))
			{
				$group = array();
				$group[] = $this->createElement('text','answer['.$option_number.']', 'Answer:','size="40"');
				//$group[] = $this->createElement('text','comment['.$option_number.']', '','size="40"');
				$group[] = $this->createElement('text','weight['.$option_number.']','','size="2"  class="input_numeric"');
				$group[] = $this->createElement('hidden','coordinates['.$option_number.']', '');
				$group[] = $this->createElement('text','type['.$option_number.']', '');
				if($number_of_options - count($_SESSION['mc_skip_options']) > 1)
				{
					$group[] = $this->createElement('image','remove['.$option_number.']',Theme :: get_common_image_path().'action_list_remove.png');
				}
				$label = $show_label ? Translation :: get('Answers') : '';
				$show_label = false;
				$this->addGroup($group,'options_group_'.$option_number,$label,'',false);
				$this->addGroupRule('options_group_'.$option_number,
					array(
						'option['.$option_number.']' =>
							array(
								array(
									Translation :: get('ThisFieldIsRequired'),'required'
								)
							),
						'weight['.$option_number.']' =>
							array(
								array(
									Translation :: get('ThisFieldIsRequired'), 'required'
								),
								array(
									Translation :: get('ValueShouldBeNumeric'),'numeric'
								)
							)
					)
				);
			}
		}
		//Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
		$this->addElement('image','add[]',Theme :: get_common_image_path().'action_list_add.png');
	}
}
?>
