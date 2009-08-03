<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of feedback
 *
 * @author Pieter Hens
 */

require_once dirname(__FILE__).'/admin_data_manager.class.php';
require_once Path :: get_common_path() . 'data_class.class.php';

class FeedbackPublication extends DataClass
{
    const CLASS_NAME				= __CLASS__;

	const PROPERTY_APPLICATION		= 'application';
	const PROPERTY_PID              = 'pid';
	const PROPERTY_CID              = 'cid';
    const PROPERTY_FID              = 'fid';
    const PROPERTY_TEXT              = 'text';

	/**
	 * Get the default properties of all feedbacks.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array(self :: PROPERTY_APPLICATION, self :: PROPERTY_PID, self :: PROPERTY_CID, self :: PROPERTY_FID));
	}
	
	/*
	 * Gets the table name for this class
	 */
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(__CLASS__);
	}
	
	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return AdminDataManager :: get_instance();	
	}

	/**
	 * Returns the application of this feedback object
	 * @return string The feedback application
	 */
	function get_application()
	{
		return $this->get_default_property(self :: PROPERTY_APPLICATION);
	}

	/**
	 * Returns publication id
	 * @return integer the pid
	 */
	function get_pid()
	{
	 	return $this->get_default_property(self :: PROPERTY_PID);
	}

	 /**
	  * Returns complex id (id within complex learning object)
	  * @return integer the cid
	  */
	function get_cid()
	{
		return $this->get_default_property(self :: PROPERTY_CID);
	}

    /**
	  * Returns feedback id
	  * @return integer the fid
	  */
	function get_fid()
	{
		return $this->get_default_property(self :: PROPERTY_FID);
	}

	/**
	 * Sets the application of this feedback.
	 * @param string $application the feedback application.
	 */
	function set_application($application)
	{
		$this->set_default_property(self :: PROPERTY_APPLICATION, $application);
	}

	/**
	 * Sets the pid of this feedback.
	 * @param integer $pid the pid.
	 */
	function set_pid($pid)
	{
		$this->set_default_property(self :: PROPERTY_PID, $pid);
	}

	/**
	 * Sets the cid of this feedback.
	 * @param integer $cid the cid.
	 */
	function set_cid($cid)
	{
		$this->set_default_property(self :: PROPERTY_CID, $cid);
	}

    /**
	 * Sets the fid of this feedback.
	 * @param integer $fid the fid.
	 */
    function set_fid($fid)
    {
        $this->set_default_property(self :: PROPERTY_FID, $fid);
    }
}
?>
