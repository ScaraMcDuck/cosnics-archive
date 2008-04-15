<?php
/**
 * @package application.lib.calendar
 */
require_once dirname(__FILE__).'/../../../repository/lib/abstractlearningobject.class.php';

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class PersonalMessengerBlock
{
	const PARAM_ACTION = 'block_action';
		
	private $parent;
	private $type;
	private $block_info;
	private $configuration;
	
	/**
	 * Constructor.
	 * @param array $types The learning object types that may be published.
	 * @param  boolean $email_option If true the publisher has the option to
	 * send the published learning object by email to the selecter target users.
	 */
	function PersonalMessengerBlock($parent, $type, $block_info)
	{
		$this->parent = $parent;
		$this->type = $type;
		$this->block_info = $block_info;
		$this->configuration = $block_info->get_configuration();
		//$parent->set_parameter(CalendarBlock :: PARAM_ACTION, $this->get_action());
	}

	/**
	 * Returns the publisher's output in HTML format.
	 * @return string The output.
	 */
	function run()
	{
		$type = $this->type;
		require_once dirname(__FILE__).'/block/personal_messenger'.$type.'.class.php';
		$class = 'PersonalMessenger'.ucfirst($type);
		$component = new $class ($this);
		return $component->run();
	}

	/**
	 * Returns the tool which created this publisher.
	 * @return RepositoryTool The tool.
	 */
	function get_parent()
	{
		return $this->parent;
	}
	
	function get_configuration()
	{
		return $this->configuration;
	}

	/**
	 * @see RepositoryTool::get_user_id()
	 */
	function get_user_id()
	{
		return $this->parent->get_user_id();
	}
	
	function get_user()
	{
		return $this->parent->get_user();
	}

	/**
	 * Returns the types of learning object that this object may publish.
	 * @return array The types.
	 */
	function get_type()
	{
		return $this->type;
	}
	
	function get_block_info()
	{
		return $this->block_info;
	}
	
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}

//	/**
//	 * Returns the action that the user selected, or "publicationcreator" if none.
//	 * @return string The action.
//	 */
//	function get_action()
//	{
//		return ($_GET[CalendarPublisher :: PARAM_ACTION] ? $_GET[CalendarPublisher :: PARAM_ACTION] : 'publicationcreator');
//	}

	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}
	
	function get_link($parameters = array(), $encode = false)
	{
		return $this->parent->get_link($parameters, $encode);
	}

	function get_parameters()
	{
		return $this->parent->get_parameters();
	}

	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}
	
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->parent->redirect($action, $message, $error_message, $extra_params);
	}
	
	function get_events($from_date,$to_date)
	{
		return $this->parent->get_events($from_date,$to_date);
	}

	function retrieve_personal_message_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->parent->retrieve_personal_message_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	function count_personal_message_publications($condition = null)
	{
		return $this->parent->count_personal_message_publications($condition);
	}
	
	function get_publication_viewing_link($publication)
	{
		return $this->parent->get_publication_viewing_link($publication);
	}
}
?>