<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../learning_object_form.class.php';
require_once dirname(__FILE__) . '/hotspot_question.class.php';
require_once dirname(__FILE__) . '/hotspot_question_answer.class.php';
/**
 * This class represents a form to create or update hotspot questions
 */
class HotspotQuestionForm extends LearningObjectForm
{

	private $colours = array('#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429', '#f6d7c5', '#7a2893');

    protected function build_creation_form()
    {
        parent :: build_creation_form();

        $this->check_upload();

        $this->addElement('category', Translation :: get(get_class($this) . 'Hotspots'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.draw.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/serializer.pack.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/hotspot_question.js'));

        $_SESSION['full_path'] = 'G:\Wamp\www\LCMS\files\repository\2\Mig_Messenger.jpg';
		$_SESSION['web_path'] = 'http://localhost/LCMS/files/repository/2/Mig_Messenger.jpg';

//        if (! $this->isSubmitted())
//        {
//            unset($_SESSION['web_path']);
//        }
//
//        if (! isset($_SESSION['web_path']))
//        {
//            $this->addElement('file', 'file', Translation :: get('UploadImage'));
//            $this->addElement('style_submit_button', 'fileupload', Translation :: get('Upload'), array('class' => 'positive upload'));
//            $this->addElement('category');
//        }
//        else
//        {
            $this->add_options();
            $this->addElement('hidden', 'filename', Translation :: get('Filename'));
            $this->addElement('category');
            $this->add_image();
//        }
        $this->set_session_answers();
    }

    protected function build_editing_form()
    {
        parent :: build_creation_form();

        $_SESSION['full_path'] = 'G:\Wamp\www\LCMS\files\repository\2\Mig_Messenger.jpg';
		$_SESSION['web_path'] = 'http://localhost/LCMS/files/repository/2/Mig_Messenger.jpg';

        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.draw.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/serializer.pack.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/hotspot_question.js'));
        $this->add_options();
        $this->addElement('hidden', 'filename', Translation :: get('Filename'));
        $this->addElement('category');

		$this->add_image();
		$this->set_session_answers();
    }

    function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_learning_object();
            if (! is_null($object))
            {
                $answers = $object->get_answers();
                foreach ($answers as $i => $answer)
                {
                    $defaults['answer'][$i] = $answer->get_answer();
                    $defaults['type'][$i] = $answer->get_hotspot_type();
                    $defaults['comment'][$i] = $answer->get_comment();
                    $defaults['coordinates'][$i] = $answer->get_hotspot_coordinates();
                    $defaults['option_weight'][$i] = $answer->get_weight();
                }

                /*$options = $object->get_answers();
				foreach($options as $index => $option)
				{
					$defaults['option'][$index] = $option->get_value();
					$defaults['weight'][$index] = $option->get_weight();
				}*/
                for($i = count($answers); $i < $_SESSION['mc_number_of_options']; $i ++)
                {
                    $defaults['option_weight'][$i] = 1;
                }
                $this->set_session_answers($defaults);
            }
        }
        else
        {
            $number_of_options = intval($_SESSION['mc_number_of_options']);

            for($option_number = 0; $option_number < $number_of_options; $option_number ++)
            {
                $defaults['option_weight'][$option_number] = 1;
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
        $object->set_answers('');
        $values = $this->exportValues();
        $answers = $values['answer'];
        $comments = $values['comment'];
        $types = $values['type'];
        $coordinates = $values['coordinates'];
        $weights = $values['option_weight'];

        for($i = 0; $i < $_SESSION['mc_number_of_options']; $i ++)
        {
            $answer = new HotspotQuestionAnswer($answers[$i], $comments[$i], $weights[$i], $coordinates[$i], $types[$i]);
            $object->add_answer($answer);
        }
    }

    function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['fileupload']))
        {
            return false;
        }
        return parent :: validate();
    }

    function check_upload()
    {
        if ($_FILES['file'] != null && $_SESSION['web_path'] == null)
        {
            $allowed_types = array();
            $allowed_types[] = 'image/pjpeg';
            $allowed_types[] = 'image/jpeg';
            $allowed_types[] = 'image/png';
            $allowed_types[] = 'image/gif';

            $filetype = $_FILES['file']['type'];

            if (in_array($filetype, $allowed_types))
            {
                $owner = $this->get_owner_id();
                $filename = Filesystem :: create_unique_name(Path :: get(SYS_REPO_PATH) . $owner, $_FILES['file']['name']);

                $repo_path = Path :: get(SYS_REPO_PATH) . $owner . '/';
                $full_path = $repo_path . $filename;

                if (! is_dir($repo_path))
                {
                    Filesystem :: create_dir($repo_path);
                }

                $web_path = Path :: get(WEB_REPO_PATH) . $owner . '/' . $filename;
                if (move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "' . $full_path . '"'))
                {
                    chmod($full_path, 0777);

                    $image_manipulation = ImageManipulation :: factory($full_path);
                    $image_manipulation->scale(600, 600);
                    $image_manipulation->write_to_file();

                    $_SESSION['hotspot_path'] = htmlspecialchars($owner . '/' . $filename);
                    $_SESSION['web_path'] = $web_path;
                    $_SESSION['full_path'] = $full_path;
                    $_FILES['file'] = null;
                }
            }
        }
    }

    function set_session_answers($defaults = array())
    {
        if (count($defaults) == 0)
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
            $_SESSION['answers'] = $defaults['answer'];
            $_SESSION['types'] = $defaults['type'];
            $_SESSION['weights'] = $defaults['weight'];
            $_SESSION['coordinates'] = $defaults['coordinates'];
        }
    }

    function add_image()
    {
        $this->addElement('category', Translation :: get('HotspotImage'));

        $dimensions = getimagesize($_SESSION['full_path']);

        $html = array();
        $html[] = '<div id="hotspot_marking"><div class="colour_box_label">' . Translation :: get('CurrentlyMarking') . '</div><div class="colour_box"></div></div>';
        $html[] = '<div id="hotspot_container"><div id="hotspot_image" style="width: '. $dimensions[0] .'px; height: '. $dimensions[1] .'px; background-image: url('. $_SESSION['web_path'] .')"></div></div>';

        $this->addElement('html', implode("\n", $html));
        $this->addElement('category');
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this
     * multiple choice question
     */
    private function add_options()
    {
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
        }
        if (! isset($_SESSION['mc_number_of_options']) || $_SESSION['mc_number_of_options'] < 1)
        {
            $_SESSION['mc_number_of_options'] = 1;
        }
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            /*$indexes = array_keys($_POST['remove']);
			if (!in_array($indexes[0],$_SESSION['mc_skip_options']))
				$_SESSION['mc_skip_options'][] = $indexes[0];*/
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] - 1;
            //$this->move_answer_arrays($indexes[0]);
        }
        $object = $this->get_learning_object();
        if (! $this->isSubmitted() && ! is_null($object))
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

        $buttons = array();
        $buttons[] = $this->createElement('style_button', 'add[]', Translation :: get('AddMultipleChoiceOption'), array('class' => 'normal add'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer = $this->defaultRenderer();

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['show_toolbar'] = false;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';

        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('HotspotDescription') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));

        $colours = $this->colours;

        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
                $group[] = $this->createElement('static', null, null, '<div class="colour_box" style="background-color: ' . $colours[$option_number] . '"></div>');
                $group[] = $this->createElement('hidden', 'type[' . $option_number . ']', '');
                $group[] = $this->createElement('hidden', 'coordinates[' . $option_number . ']', '');
                $group[] = $this->create_html_editor('answer[' . $option_number . ']', Translation :: get('Answer'), $html_editor_options);
                $group[] = $this->create_html_editor('comment[' . $option_number . ']', Translation :: get('Comment'), $html_editor_options);
                $group[] = $this->createElement('text', 'option_weight[' . $option_number . ']', Translation :: get('Weight'), 'size="2"  class="input_numeric"');

				$hotspot_actions = array();
				$hotspot_actions[] = $this->createElement('image', 'edit[' . $option_number . ']', Theme :: get_common_image_path() . 'action_edit.png', array('class' => 'edit_option', 'id' => 'edit_' . $option_number));
				$hotspot_actions[] = $this->createElement('image', 'reset[' . $option_number . ']', Theme :: get_common_image_path() . 'action_reset.png', array('class' => 'reset_option', 'id' => 'reset_' . $option_number));

                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                   $hotspot_actions[] = $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => 'remove_' . $option_number));
                }
                else
                {
                    $hotspot_actions[] = $this->createElement('static', null, null, '<img class="remove_option" src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }
                $group[] = $this->createElement('static', null, null, $this->createElement('group', null, null, $hotspot_actions, '&nbsp;&nbsp;', false)->toHtml());

                $this->addGroup($group, 'option_' . $option_number, null, '', false);

                $renderer->setElementTemplate('<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $option_number);

                $this->addGroupRule('option_' . $option_number, array('answer[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required')), 'option_weight[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required'), array(Translation :: get('ValueShouldBeNumeric'), 'numeric'))));
            }
        }

        $this->setDefaults();

        $_SESSION['mc_num_options'] = $number_of_options;
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));

        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');
    }
}
?>
