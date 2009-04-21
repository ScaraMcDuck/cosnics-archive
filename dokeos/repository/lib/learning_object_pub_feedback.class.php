<?php

require_once dirname(__FILE__).'/repository_data_manager.class.php';

class LearningObjectPubFeedback extends LearningObject
{
	const CLASS_NAME = __CLASS__;

	const PROPERTY_PUBLICATION_ID = 'pid';
	const PROPERTY_CLOI_ID = 'cid';
	const PROPERTY_FEEDBACK_ID = 'fid';

	/**
	 * Default properties of the learning_object_feedback object, stored in an associative
	 * array.
	 */
	private $defaultProperties;


	function LearningObjectPubFeedback($publication_id = 0,$cloi_id = 0, $feedback_id = 0 , $defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}


	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}


	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}


	static function get_default_property_names()
	{
		return array (self :: PROPERTY_PUBLICATION_ID, self :: PROPERTY_CLOI_ID, self :: PROPERTY_FEEDBACK_ID);
	}


	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}


	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}


	function get_publication_id()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
	}


	function get_cloi_id()
	{
		return $this->get_default_property(self :: PROPERTY_CLOI_ID);
	}

    function get_feedback_id()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK_ID);
	}


	function set_publication_id($publication_id)
	{
		$this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
	}

	function set_cloi_id($cloi_id)
	{
		return $this->set_default_property(self :: PROPERTY_CLOI_ID, $cloi_id );
	}

    function set_feedback_id($feedback_id)
	{
		return $this->set_default_property(self :: PROPERTY_FEEDBACK_ID , $feedback_id);
	}

	function delete()
	{
		return RepositoryDataManager :: get_instance()->delete_learning_object_pub_feedback($this);
	}

	function create()
	{
		$gdm = RepositoryDataManager :: get_instance();
        return $gdm->create_learning_object_pub_feedback($this);
	}

	function update()
	{
		$gdm = RepositoryDataManager :: get_instance();
		$success = $gdm->update_learning_object_pub_feedback($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>
