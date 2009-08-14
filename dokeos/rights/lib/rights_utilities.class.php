<?php
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_rights_path() . 'lib/location.class.php';
require_once Path :: get_library_path() . 'configuration/configuration.class.php';
require_once 'Tree/Tree.php';
require_once 'XML/Unserializer.php';

/*
 * This should become the class which all applications use
 * to retrieve and add rights. This class should NOT be used by the
 * RightsManager itself. Its is meant to be be used as an interface
 * to the RightsManager / RightsDataManager functionality.
 */

class RightsUtilities
{
    function install_initial_application_locations()
    {
		$core_applications = array('webservice','admin', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu');

		foreach ($core_applications as $core_application)
		{
			// Add code here
		}

		$path = Path :: get_application_path() . 'lib/';
		$applications = FileSystem :: get_directory_content($path, FileSystem :: LIST_DIRECTORIES, false);

		foreach($applications as $application)
		{
			$toolPath = $path.'/'. $application .'/install';
			if (is_dir($toolPath) && Application :: is_application_name($application))
			{
				$check_name = 'install_' . $application;
				if (isset($values[$check_name]) && $values[$check_name] == '1')
				{
					$installer = Installer :: factory($application, $values);
					$result = $installer->install();
					$installer->create_root_rights_location();
					$this->process_result($application, $result, $installer->retrieve_message());
					unset($installer, $result);
					flush();
				}
				else
				{
					// TODO: Does this work ?
					$application_path = dirname(__FILE__).'/../../application/lib/' . $application . '/';
					if (!FileSystem::remove($application_path))
					{
						$this->process_result($application, array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('ApplicationRemoveFailed')));
					}
					else
					{
						$this->process_result($application, array(Installer :: INSTALL_SUCCESS => true, Installer :: INSTALL_MESSAGE => Translation :: get('ApplicationRemoveSuccess')));
					}
				}
			}
			flush();
		}
    }

    function create_application_root_location($application)
    {
    	$xml = self :: parse_locations_file($application);

    	$root = new Location();
    	$root->set_location($xml['name']);
    	$root->set_application($application);
    	$root->set_type($xml['type']);
    	$root->set_identifier($xml['identifier']);
    	$root->set_inherit(0);
    	$root->set_locked(0);
    	if (!$root->create())
    	{
    		return false;
    	}

		if (isset($xml['children']) && isset($xml['children']['location']) && count($xml['children']['location']) > 0)
		{
			self :: parse_tree($application, $xml, $root->get_id());
		}

		return true;
    }

	function parse_locations_file($application)
	{
		$base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
		$file = $base_path . $application . '/rights/' . $application . '_locations.xml';

		$result = array();

		if (file_exists($file))
		{
			$unserializer = new XML_Unserializer();
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('location'));

			// userialize the document
			$status = $unserializer->unserialize($file, true);

			if (PEAR::isError($status))
			{
				echo 'Error: ' . $status->getMessage();
			}
			else
			{
				$data = $unserializer->getUnserializedData();
			}
		}

		return $data;
	}

	function parse_tree($application, $xml, $parent)
	{
		$previous = null;

		$children = $xml['children'];
		foreach ($children['location'] as $child)
		{
	    	$location = new Location();
	    	$location->set_location($child['name']);
	    	$location->set_application($application);
	    	$location->set_type($child['type']);
	    	$location->set_identifier($child['identifier']);
	    	$location->set_parent($parent);
	    	if (!$location->create($previous != null ? $previous : 0))
	    	{
	    		return false;
	    	}

			$previous = $location->get_id();

			if (isset($child['children']) && isset($child['children']['location']) && count($child['children']['location']) > 0)
			{
				self :: parse_tree($application, $child, $location->get_id());
			}
		}
	}

	function is_allowed($right, $location, $type, $application = 'admin', $user_id = null)
	{
		$rdm = RightsDataManager :: get_instance();
		$udm = UserDataManager :: get_instance();

        $user_id = $user_id ? $user_id : Session :: get_user_id();
		$user = $udm->retrieve_user($user_id);

		if (is_object($user) && $user->is_platform_admin())
		{
			return true;
		}

		$conditions = array();
		$conditions[] = new EqualityCondition('identifier', $location);
		$conditions[] = new EqualityCondition('type', $type);

		$condition = new AndCondition($conditions);

		$location_set = $rdm->retrieve_locations($condition, 0, 1);

		if ($location_set->size() > 0)
		{
			$location = $location_set->next_result();
			$locked_parent = $location->get_locked_parent();

			if (isset($locked_parent))
			{
				$location = $locked_parent;
			}

			$parents = $location->get_parents();
		}
		else
		{
			return false;
		}
		
		$parents = $parents->as_array();

		if (isset($user))
		{
			
			// Check right for the user's groups
			$user_groups = $user->get_groups();

			if (!is_null($user_groups))
			{
				while ($group = $user_groups->next_result())
				{					
					foreach($parents as $parent)
					{
						$conditions = array();
						$conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_LOCATION_ID, $parent->get_id());
						$conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_RIGHT_ID, $right);
						$conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_GROUP_ID, $group->get_id());
						$condition = new AndCondition($conditions);
						
						$rights = $rdm->retrieve_group_right_locations($condition, null, 1);
		
						if ($rights->size() > 0)
						{
							$has_right = $rights->next_result()->get_value();
							
							if ($has_right)
							{
								return true;
							}
						}
						elseif(!$parent->inherits())
						{
							break;
						}
					}
				}
			}

			// Check right for the individual user
			foreach($parents as $parent)
			{
				$conditions = array();
				$conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_LOCATION_ID, $parent->get_id());
				$conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_RIGHT_ID, $right);
				$conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_USER_ID, $user->get_id());
				$condition = new AndCondition($conditions);
				
				$rights = $rdm->retrieve_user_right_locations($condition, null, 1);
				
				if ($rights->size() > 0)
				{
					$has_right = $rights->next_result()->get_value();
					
					if ($has_right)
					{
						return true;
					}
				}
				elseif(!$parent->inherits())
				{
					break;
				}
			}
		}
		else
		{
			// TODO: Use anonymous user for this, he may or may not have some rights too
			return false;
		}

		return false;
	}

	function is_allowed_for_rights_template($rights_template, $right, $location, $application = 'admin')
	{
		$rdm = RightsDataManager :: get_instance();

		$parents = $location->get_parents();

		while($parent = $parents->next_result())
		{
			$has_right = $rdm->retrieve_rights_template_right_location($right, $rights_template, $parent->get_id())->get_value();

			if ($has_right)
			{
				return true;
			}
			elseif(!$parent->inherits())
			{
				return false;
			}
		}

		return false;
	}

	function move_multiple($locations, $new_parent_id, $new_previous_id = 0)
	{
		$rdm = RightsDataManager :: get_instance();

		if (!is_array($locations))
		{
			$locations = array($locations);
		}

		$failures = 0;

		foreach ($locations as $location)
		{
			if (!$rdm->move_location_nodes($location, $new_parent_id, $new_previous_id))
			{
				$failures++;
			}
		}
	}

	function get_root($application)
	{
		$rdm = RightsDataManager :: get_instance();

		$root_conditions = array();
		$root_conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, 0);
		$root_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $application);

		$root_condition = new AndCondition($root_conditions);

		$roots = $rdm->retrieve_locations($root_condition, null, 1);

		if ($roots->size() > 0)
		{
			return $roots->next_result();
		}
		else
		{
			return false;
		}
	}

	function get_root_id($application)
	{
		$root = self :: get_root($application);
		return $root->get_id();
	}

	function get_location_by_identifier($application, $type, $identifier)
	{
		$rdm = RightsDataManager :: get_instance();

		$conditions = array();
		$conditions[] = new EqualityCondition('identifier', $identifier);
		$conditions[] = new EqualityCondition('type', $type);

		$condition = new AndCondition($conditions);

		$locations = $rdm->retrieve_locations($condition, 0, 1);

		return $locations->next_result();
	}

	function get_location_id_by_identifier($application, $type, $identifier)
	{
		$location = self :: get_location_by_identifier($application, $type, $identifier);
		if (isset($location))
		{
			return $location->get_id();
		}
        else
        {
           // echo 'no location for that identifier ';
        }
		return null;
	}

	function get_rights_legend()
	{
		$html = array();

		$html[] = DokeosUtilities :: add_block_hider();
		$html[] = DokeosUtilities :: build_block_hider('rights_legend');
		$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path().'place_legend.png);">';
		$html[] = '<div class="title">'. Translation :: get('Legend') .'</div>';
		$html[] = '<ul class="rights_legend">';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_true', 'png', Translation :: get('True')) .'</li>';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_false', 'png', Translation :: get('False')) .'</li>';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_true_locked', 'png', Translation :: get('LockedTrue')) .'</li>';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_false_locked', 'png', Translation :: get('LockedFalse')) .'</li>';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_true_inherit', 'png', Translation :: get('InheritedTrue')) .'</li>';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_false_inherit', 'png', Translation :: get('InheritedFalse')) .'</li>';
		$html[] = '</ul>';
		$html[] = '</div>';
		$html[] = DokeosUtilities :: build_block_hider();

		return implode("\n", $html);
	}

	function invert_rights_template_right_location($right, $rights_template, $location)
	{
		if (isset($rights_template) && isset($right) && isset($location))
		{
			$rights_templaterightlocation = $this->retrieve_rights_template_right_location($right, $rights_template, $location->get_id());
			$rights_templaterightlocation->invert();
			return $rights_templaterightlocation->update();
		}
		else
		{
			return false;
		}
	}

	function switch_location_lock($location)
	{
		$location->switch_lock();
		return $location->update();
	}

	function switch_location_inherit($location)
	{
		$location->switch_inherit();
		return $location->update();
	}

	static function rights_templates_for_element_finder($linked_rights_templates)
	{
		$rdm = RightsDataManager :: get_instance();
		$rights_templates = array();

		while ($linked_rights_template = $linked_rights_templates->next_result())
		{
			$rights_templates[] = $rdm->retrieve_rights_template($linked_rights_template->get_rights_template_id());
		}

		$return = array();

		foreach($rights_templates as $rights_template)
		{
			$id = $rights_template->get_id();
			$return[$id] = self :: rights_template_for_element_finder($rights_template);
		}

		return $return;
	}

	static function rights_template_for_element_finder($rights_template)
	{
		$return = array ();
		$return['id'] = $rights_template->get_id();
		$return['class'] = 'type type_rights_template';
		$return['title'] = $rights_template->get_name();
		$return['description'] = strip_tags($rights_template->get_description());
		return $return;
	}
	
	function create_location($name, $application, $type = 'root', $identifier = 0, $inherit = 0, $parent = 0)
	{
		$location = new Location();
		$location->set_location($name);
		$location->set_parent($parent);
		$location->set_application($application);
		$location->set_type($type);
		$location->set_identifier($identifier);
		$location->set_inherit($inherit);
		return $location->create();
	}
	
}
?>