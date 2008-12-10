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
	
	const haha = 'help';

    function RightsUtilities()
    {
    }
    
    function install_initial_application_locations()
    {
		$core_applications = array('admin', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu');
		
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
    	$tree = self :: get_tree($application);
		
		$root_id = $tree->add( array(
						'name'	=>	$xml['name'],
						'application' => $application,
						'type' => $xml['type'],
						'identifier' => $xml['identifier']
					));

		if (isset($xml['children']) && isset($xml['children']['location']) && count($xml['children']['location']) > 0)
		{
			self :: parse_tree($application, $xml, $root_id);
		}
					
		if (PEAR::isError($root_id))
		{
			return false;
		}
		
		return true;
    }
    
	function parse_locations_file($application)
	{
		$base_path = (Application :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
		$file = $base_path . $application . '/rights/' . $application . '_locations.xml';
		
		$result = array();
		
		if (file_exists($file))
		{			
			$unserializer = &new XML_Unserializer();
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
		$tree = self :: get_tree($application);
		$previous = null;
		
		$children = $xml['children'];
		foreach ($children['location'] as $child)
		{
			$element = $tree->add( array(
							'name'	=>	$child['name'],
							'application' => $application,
							'type' => $child['type'],
							'identifier' => $child['identifier'],
						), $parent, ($previous != null ? $previous : 0));
						
			$previous = $element;
			
			if (isset($child['children']) && isset($child['children']['location']) && count($child['children']['location']) > 0)
			{
				self :: parse_tree($application, $child, $element);
			}
		}
	}
	
	function get_tree($application)
	{
		$configuration = Configuration :: get_instance();
		$dsn = $configuration->get_parameter('database', 'connection_string');
    	
		$config = array(
		    'type' => 'Nested',
		    'storage' => array(
		        'name' => 'MDB2',
		        'dsn' => $dsn
		        ,
		        // 'connection' =>
		    ),
		    'options' => array(
		        'table' => 'rights_location',
		        'order' =>  'id',
		        'fields' => array(
		            'id' => array('type' => 'integer', 'name' => 'id'),
		            'name' => array('type' => 'text', 'name' => 'location'),
		            'left'      =>  array('type' => 'text', 'name' => 'left_value'),
		            'right'     =>  array('type' => 'text', 'name' => 'right_value'),
		            'parent_id'  =>  array('type' => 'integer', 'name' => 'parent')
		        ),
		        'whereAddOn' => ' application = "' . $application . '"'
		    )
		);
		
		return Tree :: factoryDynamic($config);
	}
	
	function is_allowed($right, $location, $type, $application = 'admin')
	{
		$rdm = RightsDataManager :: get_instance();
		$udm = UserDataManager :: get_instance();
		
		$user = $udm->retrieve_user(Session :: get_user_id());
		
//		if (is_object($user) && $user->is_platform_admin())
//		{
//			return true; 
//		}
		
		$locations = self :: get_tree($application);
		
		$conditions = array();
		$conditions[] = new EqualityCondition('identifier', $location);
		$conditions[] = new EqualityCondition('type', $type);
		
		$condition = new AndCondition($conditions);
		
		$location_set = $rdm->retrieve_locations($condition, 0, 1);
		
		if ($location_set->size() > 0)
		{
			$location = $location_set->next_result();
			$parents = $locations->getParents($location->get_id());
			
			$parents = array_reverse($parents);
		}
		else
		{
			return false;
		}
		
		if (isset($user))
		{
			$roles = array();
			
			$user_groups = $user->get_groups();
			while ($group = $user_groups->next_result())
			{
				//$group_roles[] = $group->get_role();
				$group_roles = $group->get_roles();
				
				while ($group_role = $group_roles->next_result())
				{
					$roles[] = $group_role->get_role_id();
				}
			}
			
			// TODO: Do we want to seperate checks for group roles and user roles ? Not doing so may let user roles override group roles
			
			$user_roles = $user->get_roles();
			
			while ($user_role = $user_roles->next_result())
			{
				$role = $user_role->get_role_id();
				if (!in_array($role, $roles))
				{
					$roles[] = $role;
				}
			}
		}
		else
		{
			// TODO: Use anonymous user for this, he may or may not have some rights too
			return false;
		}
		
		foreach($parents as $parent)
		{
			foreach($roles as $role)
			{
				$has_right = $rdm->retrieve_role_right_location($right, $role, $parent['id'])->get_value();
				
				if ($has_right)
				{
					return true;
				}
				elseif(!$parent['inherit'])
				{
					return false;
				}
			}
		}
		
		return false;
	}
}
?>