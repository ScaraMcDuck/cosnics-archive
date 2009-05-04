<?php
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/wiki.class.php';
require_once Path :: get_application_path().'/lib/weblcms/tool/wiki/component/wiki_parser.class.php';
/**
 * @package repository.learningobject
 * @subpackage wiki
 */
class WikiForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Wiki :: PROPERTY_LOCKED] = $valuearray[3];
        $defaults[Wiki :: PROPERTY_LINKS] = $valuearray[4];
		parent :: set_values($defaults);
	}
	function create_learning_object()
	{
		$object = new Wiki();
        $parser = new WikiToolParserComponent(Request :: get('pid'), Request :: get('course'));
        $object->set_locked($this->exportValue(Wiki :: PROPERTY_LOCKED));
        $object->set_links($parser->handle_toolbox_links($this->exportValue(Wiki :: PROPERTY_LINKS)));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}

    function update_learning_object()
	{
        $parser = new WikiToolParserComponent(Request :: get('pid'), Request :: get('course'));
		$object = $this->get_learning_object();
		$object->set_locked($this->exportValue(Wiki :: PROPERTY_LOCKED));
        $object->set_links($parser->handle_toolbox_links($this->exportValue(Wiki :: PROPERTY_LINKS)));
		$this->set_learning_object($object);
		return parent :: update_learning_object();
	}

    function build_creation_form()
	{
		parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('checkbox','locked', Translation :: get('WikiLocked'));
        $this->addElement('textarea','links', Translation :: get('WikiToolBoxLinks'),array('rows' => 5, 'cols' => 100));
        $this->addElement('category');
	}

	function build_editing_form()
	{
		parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('checkbox','locked', Translation :: get('WikiLocked'));
        $this->addElement('textarea','links', Translation :: get('WikiToolBoxLinks'),array('rows' => 5, 'cols' => 100));
        $this->addElement('category');
	}

    function setDefaults($defaults = array ())
	{
        $parser = new WikiToolParserComponent();
        
		$lo = $this->get_learning_object();
        if(isset($lo))
        {
            $defaults[LearningObject :: PROPERTY_ID] = $lo->get_id();

            $defaults[LearningObject :: PROPERTY_TITLE] = $lo->get_title();
            $defaults[LearningObject :: PROPERTY_DESCRIPTION] = $lo->get_description();
            $defaults[Wiki :: PROPERTY_LOCKED] = $lo->get_locked();
            $defaults[Wiki :: PROPERTY_LINKS] = $lo->get_links();
        }
        
        parent :: setDefaults($defaults);
	}

}
?>
