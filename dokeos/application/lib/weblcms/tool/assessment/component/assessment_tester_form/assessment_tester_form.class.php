<?php

require_once Path::get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/question_types/open_question.class.php';
require_once dirname(__FILE__).'/question_types/multiple_choice_question.class.php';
require_once dirname(__FILE__).'/question_types/fill_in_blanks_question.class.php';
require_once dirname(__FILE__).'/question_types/matching_question.class.php';
require_once dirname(__FILE__).'/question_types/score_question.class.php';
require_once dirname(__FILE__).'/question_types/hotspot_question.class.php';

class AssessmentTesterForm extends FormValidator
{
	
	function AssessmentTesterForm($assessment, $url, $page)
	{
		parent :: __construct('assessment', 'post', $url);
		$this->initialize($assessment, $page);
		
	}
	
	function toHtml()
	{
		$renderer = $this->defaultRenderer();
		
		$element_template = array();
		$element_template[] = '<div class="row">';
		
		$element_template[] = '<div class="questionform" style="float: left; text-align: left;">';
		$element_template[] = '<!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
		$element_template[] = '</div>';
		$element_template[] = '<div style="padding-left: 15px; overflow: hidden; font-weight: bold; color: #565656;">';
		$element_template[] = '{label}<!-- BEGIN required --><span class="form_required"><img src="'. Theme :: get_common_image_path() .'/action_required.png" alt="*" title ="*"/></span> <!-- END required -->';
		$element_template[] = '</div>';
		$element_template[] = '<div class="clear">&nbsp;</div>';
		$element_template[] = '</div>';
		$element_template = implode("\n", $element_template);
		
		$renderer->setElementTemplate($element_template);
	
		return parent :: toHtml();
	}
	
	function initialize($assessment, $page) 
	{
		$assessment_id = $assessment->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment_id);
		$clo_questions = $dm->retrieve_complex_learning_object_items($condition);
		
		//$this->addElement('html', '<br/><div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$assessment->get_icon_name().'.png);">');
		//$this->addElement('html', '<div class="title" style="font-size: 14px">');
		$this->addElement('hidden', 'hiddentime', '', array('id' => 'hiddentime'));
		$this->add_textfield('time', Translation :: get('TimeTaken'), false, array('DISABLED', 'size' => 5));
		
		$this->addElement('html', '<h3>');
		$this->addElement('html', $assessment->get_title());
		//$this->addElement('html', '</div>');
		$this->addElement('html', '</h3>');
		/*$this->addElement('html', '<div class="description">');*/
		if ($assessment->get_assessment_type() == Survey :: TYPE_SURVEY && $page == 1)
			$this->addElement('html', $assessment->get_introduction_text());
		else if ($assessment->get_assessment_type() != Survey :: TYPE_SURVEY)
			$this->addElement('html', $assessment->get_description());
		/*$this->addElement('html', '</div>');
		$this->addElement('html', '</div>');*/
		
		$start_question = ($page - 1) * $assessment->get_questions_per_page() + 1;
		$stop_question = $start_question + $assessment->get_questions_per_page();
		$count = 1;
		
		while($clo_question = $clo_questions->next_result())
		{
			if ($start_question != $stop_question)
			{
				if ($count >= $start_question && $count < $stop_question)
				{
					//dump('c'.$count);
					//dump('s'.$start_question);
					//$count2 = $count + (($page - 1) * $assessment->get_questions_per_page());
					$question_display = QuestionDisplay :: factory($clo_question, $count);
					if (isset($question_display))
						$question_display->add_to($this);
						
					$this->addElement('html', '<br />');
				}
			}
			else
			{
				$question_display = QuestionDisplay :: factory($clo_question, $count);
				if (isset($question_display))
					$question_display->add_to($this);
					
				$this->addElement('html', '<br />');
			}
			
			$count++;
		}
		//$this->addElement('submit', 'submit', Translation :: get('Submit'));
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SubmitAnswers'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		
		$this->add_timer_script($assessment);
	}
	
	function add_timer_script($assessment)
	{
		$elapsed = 0;
		if ($_POST['hiddentime'])
			$elapsed = $_POST['hiddentime'];
			
		$max_time = $assessment->get_maximum_time() * 60;
		if ($max_time > 0)
		{
			$this->addElement('html', 
				'<script type="text/javascript"">
				<!--
				var secs
				var maxSecs
				var timerID = null
				var timerRunning = false
				var delay = 1000

				InitializeTimer()
				
				function InitializeTimer()
				{
				    // Set the length of the timer, in seconds
				    secs = '.$elapsed.'
				    maxSecs = '.$max_time.'
				    StopTheClock()
				    StartTheTimer()
				}
				
				function StopTheClock()
				{
				    if(timerRunning)
				        clearTimeout(timerID)
				    timerRunning = false
				}
				
				function StartTheTimer()
				{
				    if (secs >= maxSecs)
				    {
				        StopTheClock()
				        alert("'.Translation :: get('OutOfTime').'")
				        document.assessment.submit.click()
				    }
				    else
				    {
				        secs = secs + 1
				        timerRunning = true
				        timerID = self.setTimeout("StartTheTimer()", delay)
				        UpdateTime(secs);
				    }
				}
				
				function UpdateTime(secs)
				{
					mins = Math.floor(secs / 60)
					seconds = secs % 60
					
					if (seconds < 10)
						seconds = "0" + seconds
						
					htime = document.getElementById("hiddentime");
					if (htime == null)
						htime = document.assessment["hiddentime"];
						
					htime.value = secs
					document.assessment.time.value = mins + ":" + seconds
				}
				//-->
				</SCRIPT>'
			);
		}
	}
}
?>