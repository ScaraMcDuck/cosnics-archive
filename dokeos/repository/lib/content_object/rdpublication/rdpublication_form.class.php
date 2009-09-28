<?php
/**
 * $Id: announcement_form.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../content_object_form.class.php';
require_once dirname(__FILE__).'/rdpublication.class.php';
/**
 * This class represents a form to create or update announcements
 */
    class RdpublicationForm extends ContentObjectForm
{
	// Inherited
	function create_content_object()
	{

		$object = new Rdpublication();
        $object->set_ref_id($this->exportValue(Rdpublication :: PROPERTY_REF_ID));
		$object->set_pub_type($this->exportValue(Rdpublication :: PROPERTY_PUB_TYPE));
		$this->set_content_object($object);
		return parent :: create_content_object();

	}

    protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->add_textfield(Rdpublication :: PROPERTY_REF_ID, Translation :: get('REFERENCE'), true, array('size' => '100'));
        $this->add_textfield(Rdpublication :: PROPERTY_PUB_TYPE, Translation :: get('TYPE'), true, array('size' => '100'));
        $this->addElement('category');
	}

	function set_csv_values($valuearray)
	{
		$defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Rdpublication :: PROPERTY_REF_ID] = $valuearray[3];
        $defaults[Rdpublication :: PROPERTY_PUB_TYPE] = $valuearray[4];
        parent :: set_values($defaults);			
	}	
}
?>
