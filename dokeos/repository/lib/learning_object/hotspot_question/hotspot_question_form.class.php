<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/hotspot_question.class.php';
require_once dirname(__FILE__).'/hotspot_question_answer.class.php';
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
			$this->addElement($this->get_scripts_element());
		}
		$this->set_session_answers(false);
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('category');
	}
	
	protected function build_editing_form()
	{
		parent :: build_creation_form();
		$this->check_upload();
		if (!isset($_SESSION['web_path']))
		{
			//$this->addElement('file', 'file', Translation :: get('UploadImage'));
			//$this->addElement('submit', 'fileupload', Translation :: get('Submit'));
			$_SESSION['web_path'] = Path :: get(WEB_REPO_PATH).$this->get_learning_object()->get_image();
			$_SESSION['full_path'] = Path :: get(SYS_REPO_PATH).$this->get_learning_object()->get_image();
			$_SESSION['hotspot_path'] = $this->get_learning_object()->get_image();
		}
			$var = ($_SESSION['web_path']);
			$this->add_options();
			$modifyAnswers = true;
			$this->addElement('text', 'filename', Translation :: get('Filename'), array('DISABLED'));
			$this->addElement($this->get_scripts_element());
		
		$this->set_session_answers(false);
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('category');
	}
	
	function setDefaults($defaults = array ())
	{
		if(!$this->isSubmitted())
		{
			$object = $this->get_learning_object();
			if(!is_null($object))
			{
				$answers = $object->get_answers();
				foreach ($answers as $answer)
				{
					$defaults['answer'][] = $answer->get_answer();
					$defaults['type'][] = $answer->get_hotspot_type();
					$defaults['comment'][] = $answer->get_comment();
					$defaults['coordinates'][] = $answer->get_hotspot_coordinates();
					$defaults['option_weight'][] = $answer->get_weight();
				}
				/*$options = $object->get_answers();
				foreach($options as $index => $option)
				{
					$defaults['option'][$index] = $option->get_value();
					$defaults['weight'][$index] = $option->get_weight();
				}*/
			}
		}
		$defaults['filename'] = $_SESSION['web_path'];	
		parent :: setDefaults($defaults);
	}
	
	function create_learning_object()
	{
		$object = new HotspotQuestion();
		$object->set_image($_SESSION['hotspot_path']);
		//dump($object);
		$this->set_learning_object($object);
		$this->add_options_to_object();
		unset($_SESSION['web_path']);
		unset($_SESSION['hotspot_path']);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$this->add_options_to_object();
		unset($_SESSION['web_path']);
		unset($_SESSION['hotspot_path']);
		return parent :: update_learning_object();
	}
	
	private function add_options_to_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		$answers = $values['answer'];
		$comments = $values['comment'];
		$types = $values['type'];
		$coordinates = $values['coordinates'];
		$weights = $values['option_weight'];
		
		for ($i = 0; $i < $_SESSION['mc_number_of_options']; $i++)
		{
			$answer = new HotspotQuestionAnswer($answers[$i], $comments[$i], $weights[$i], $coordinates[$i], $types[$i]);
			$object->add_answer($answer);
			//dump($answer);
		}
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

			$repo_path = Path :: get(SYS_REPO_PATH) . $owner . '/';
			$full_path = $repo_path . $filename;
			
			if(!is_dir($repo_path))
				Filesystem :: create_dir($repo_path);
				
			$web_path = Path :: get(WEB_REPO_PATH).$path;
			move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "'.$full_path.'"');
			chmod($full_path, 0777);
			$_SESSION['hotspot_path'] = htmlspecialchars($owner.'/'.$filename);
			$_SESSION['web_path'] = $web_path;
			$_SESSION['full_path'] = $full_path;
			$_FILES['file'] = null;
		}
	}
	
	function set_session_answers($use_db_answers)
	{
		if (!$use_db_answers)
		{
			$answers = $_POST['answer'];
			$types = $_POST['type'];
			$weights = $_POST['option_weight'];
			$coords = $_POST['coordinates'];
			
			$_SESSION['answers'] = $answers;
			$_SESSION['types'] = $types;
			$_SESSION['option_weight'] = $weights;
			$_SESSION['coordinates'] = $coords;
		}
		else
		{
			
		}
	}
	
	/*function move_answer_arrays($remove_index)
	{
		//dump($_POST);
		$answers = $_POST['answer'];
		$types = $_POST['type'];
		$weights = $_POST['weight'];
		$coords = $_POST['coordinates'];
		
		$_POST['answer'] = $this->remove_index($answers, $remove_index);
		$_POST['type'] = $this->remove_index($types, $remove_index);
		$_POST['weight'] = $this->remove_index($weights, $remove_index);
		$_POST['coordinates'] = $this->remove_index($coords, $remove_index);
		/*$_POST['answer'] = $_SESSION['answers'];
		$_POST['type'] = $_SESSION['types'];
		$_POST['weight'] = $_SESSION['weights'];
		$_POST['coordinates'] = $_SESSION['coordinates'];*/
		//dump($_POST);
	//}
	
	/*function remove_index($array, $index)
	{
		dump($array);
		dump($index);
		for ($i = $index; $i < count($array) - 1; $i++)
		{
			$array[$i] = $array[$i+1];
		}
		unset($array[count($array)-1]);
		dump($array);
		return $array;
	}*/
	
	function get_scripts_element()
	{
		$hotspot_path = Path :: get(WEB_PLUGIN_PATH).'/hotspot/hotspot/hotspot_admin.swf';
		//dump($hotspot_path);
		return $this->createElement('html','
				<script type="text/javascript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/JavaScriptFlashGateway.js" ></script>
				<script type="text/javascript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/hotspot.js" ></script>
				<script type="text/javascript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/jsmethods.js" ></script>
				<script type="text/vbscript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/vbmethods.vbscript" ></script>
				<script type="text/javascript" >		
					var requiredMajorVersion = 7;
					var requiredMinorVersion = 0;
					var requiredRevision = 0;
					//var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
					var hasRequestedVersion = true;
					// Check to see if the version meets the requirements for playback
					if (hasRequestedVersion) {  // if weve detected an acceptable version
					    var oeTags = \'<object type="application/x-shockwave-flash" data="'.$hotspot_path.'?modifyAnswers=' . $id.'" width="720" height="650">\'
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
		if(!isset($_SESSION['mc_number_of_options']) || $_SESSION['mc_number_of_options'] < 1)
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
		if(isset($_POST['remove']))
		{
			/*$indexes = array_keys($_POST['remove']);
			if (!in_array($indexes[0],$_SESSION['mc_skip_options']))
				$_SESSION['mc_skip_options'][] = $indexes[0];*/
			$indexes = array_keys($_POST['remove']);
			$_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options']-1;
			//$this->move_answer_arrays($indexes[0]);
		}
		$object = $this->get_learning_object();
		if(!$this->isSubmitted() && !is_null($object))
		{
			$_SESSION['mc_number_of_options'] = $object->get_number_of_answers();
			//$_SESSION['mc_answer_type'] = $object->get_answer_type();
		}
		$number_of_options = intval($_SESSION['mc_number_of_options']);
		$show_label = true;
		
		if (isset($_SESSION['file']))
		{
			$this->addElement('html', '<div class="learning_object">');
			$this->addElement('html', '</div>');
		}
		$counter = 0;
		for($option_number = 0; $option_number <$number_of_options ; $option_number++)
		{
			if(!in_array($option_number,$_SESSION['mc_skip_options']))
			{
				$counter ++;
				$group = array();
				$group[] = $this->createElement('text','answer['.$option_number.']', 'Answer:','size="40"');
				$group[] = $this->createElement('text','comment['.$option_number.']', '','size="40"');
				$group[] = $this->createElement('text','option_weight['.$option_number.']','','size="2"  class="input_numeric"');
				$group[] = $this->createElement('hidden','coordinates['.$option_number.']', '');
				$group[] = $this->createElement('hidden','type['.$option_number.']', '');
				if($number_of_options - count($_SESSION['mc_skip_options']) > 1 && $option_number == $number_of_options - 1)
				{
					$group[] = $this->createElement('image','remove['.$option_number.']',Theme :: get_common_image_path().'action_list_remove.png');
				}
				$label = $show_label ? Translation :: get('Answers') : '';
				$show_label = false;
				$this->addGroup($group,'options_group_'.$option_number,$label,'',false);
				$this->addGroupRule('options_group_'.$option_number,
					array(
						'answer['.$option_number.']' =>
							array(
								array(
									Translation :: get('ThisFieldIsRequired'),'required'
								)
							),
						'option_weight['.$option_number.']' =>
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
		$_SESSION['mc_num_options'] = $counter;
		//Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
		$this->addElement('image','add[]',Theme :: get_common_image_path().'action_list_add.png');
	}
}
?>
