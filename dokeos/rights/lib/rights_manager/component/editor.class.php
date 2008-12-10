<?php
/**
 * @package user.usermanager
 */
require_once dirname(__FILE__).'/../rights_manager.class.php';
require_once dirname(__FILE__).'/../rights_manager_component.class.php';
require_once dirname(__FILE__).'/../../rights_data_manager.class.php';
require_once dirname(__FILE__).'/../../rights_utilities.class.php';

class RightsManagerEditorComponent extends RightsManagerComponent
{
	private $application;
	private $location_id;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$application = $this->application = Request :: get('application');
		if (!isset($application))
		{
			$application = $this->application = 'admin';
		}
		
		$this->location_id = $_GET['location_id'];
		$component_action = $_GET[RightsManager :: PARAM_COMPONENT_ACTION];
		
		switch($component_action)
		{
			case 'edit':
				$this->edit_right();
				break;
			default :
				$this->show_rights_list();
		}
	}
	
	function edit_right()
	{
		$role_id = $_GET['role_id'];
		$right_id = $_GET['right_id'];
		$location_id =  $this->location_id;
		
		if (isset($role_id) && isset($right_id) && isset($location_id))
		{
			$rolerightlocation = $this->retrieve_role_right_location($right_id, $role_id, $location_id);
			$value = $rolerightlocation->get_value();
			if ($value == 0)
			{
				$rolerightlocation->set_value('1');
			}
			else
			{
				$rolerightlocation->set_value('0');
			}
			$success = $rolerightlocation->update();
			
			$this->redirect('url', Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS, 'location_id' =>$location_id));
		}
	}
	
	function show_rights_list()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Rights')));
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), 'name' => Translation :: get('Rights'));
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('EditRights'));
		$this->display_header($trail);
		echo $this->get_applications();
		
		echo '<div class="clear">&nbsp;</div>';
		
		echo $this->get_locations_list_html();
		
		if (isset($this->location_id))
		{
			$location = $this->retrieve_location($this->location_id);
			echo $this->get_rights_table_html();
		}
		else
		{
			echo Translation :: get('SelectLocationFull');
		}
		
		$this->display_footer();
	}
	
	function get_locations_list_html()
	{
		$location_id = $this->location_id;
		
		$html = array();
		
		$html[] = '<div>';
//		$html[] = '<form method="get" action="'.$this->get_url().'" style="display: inline;">';
//		$html[] = '<input type="hidden" name="'.RightsManager :: PARAM_ACTION.'" value="'. RightsManager :: ACTION_EDIT_RIGHTS .'" />';
//		$html[] = Translation :: get('Location') . ':&nbsp;<select name="location_id" onchange="submit();">';
//		
//		$locations = $this->retrieve_locations();
//		$html[] = '<option value=""'. ($location_id == null ? ' selected="selected"' : '').' disabled>'.Translation :: get('SelectLocation').'</option>';
//		
//		while ($location = $locations->next_result())
//		{
//			$html[] = '<option value="'.$location->get_id().'"'. ($location_id == $location->get_id() ? ' selected="selected"' : '').'>'. $location->get_location() .'</option>';
//		}
//		
//		$html[] = '</select>';
//		$html[] = '</form>';

		$tree = RightsUtilities :: get_tree('admin');
		$root = $tree->getRoot();
		
		$html[] = '<ul id ="tree" class="treeview-gray">';
		$html[] = '</ul>';
		
		$html[] = '</div>';
		
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/rightstree.js' .'"></script>';
		
		return implode("\n", $html);
	}
	
	function get_rights_table_html()
	{
		$location_id = $this->location_id;
		
		$html = array();
		
		$rights = $this->retrieve_rights();
		$rights_array = array();
		
		$html = array();
		
		$html[] = '<div>';
		$html[] = '<div style="padding: 5px; border-bottom: 1px solid #DDDDDD;">';
		$html[] = '<div style="float: left; width: 50%;"></div>';
		$html[] = '<div style="float: right; width: 40%;">';
		
		while ($right = $rights->next_result())
		{
			$html[] = '<div style="float: left; width: 24%; text-align: center;">'. Translation :: get($right->get_name()) .'</div>';
			$rights_array[$right->get_id()] = $right->get_name();
		}
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';
		
		$roles = $this->retrieve_roles();
		
		while ($role = $roles->next_result())
		{
			$html[] = '<div style="padding: 5px; border-bottom: 1px solid #DDDDDD;">';
			$html[] = '<div style="float: left; width: 50%;">'. Translation :: get($role->get_name()) .'</div>';
			$html[] = '<div style="float: right; width: 40%;">';
			
			foreach ($rights_array as $id => $name)
			{
				$html[] = '<div style="float: left; width: 24%; text-align: center;">';
				$value = $this->is_allowed($id, $role->get_id(), $location_id);
				$html[] = '<a href="'. $this->get_url(array(RightsManager :: PARAM_COMPONENT_ACTION => 'edit', 'role_id' => $role->get_id(), 'right_id' => $id, 'location_id' => $location_id)) .'">' . ($value == 1 ? '<img src="'. Theme :: get_common_image_path() .'action_setting_true.png" />' : '<img src="'. Theme :: get_common_image_path() .'action_setting_false.png" />') . '</a>';
				$html[] = '</div>';
			}
			
			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
		}
		
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	function get_applications()
	{
		$application = $this->application;
		
		$html = array();
		
		$html[] = '
<script type="text/javascript">jQuery(document).ready(function($){

$(\'div.application, div.application_current\').css(\'fontSize\', \'37%\');
$(\'div.application, div.application_current\').css(\'width\', \'40px\');
$(\'div.application, div.application_current\').css(\'height\', \'32px\');
$(\'div.application, div.application_current\').css(\'margin-top\', \'19px\');
$(\'div.application, div.application_current\').css(\'margin-bottom\', \'19px\');

$(\'div.application, div.application_current\').mouseover(function(){
	$(this).css(\'fontSize\', \'75%\');
	$(this).css(\'width\', \'80px\');
	$(this).css(\'height\', \'68px\');
	$(this).css(\'margin-top\', \'0px\');
	$(this).css(\'margin-bottom\', \'0px\');
	$(this).css(\'background-color\', \'#EBEBEB\');
	$(this).css(\'border\', \'1px solid #c0c0c0\');
})

$(\'div.application\').mouseout(function(){
	$(this).css(\'fontSize\', \'37%\');
	$(this).css(\'width\', \'40px\');
	$(this).css(\'height\', \'32px\');
	$(this).css(\'margin-top\', \'19px\');
	$(this).css(\'margin-bottom\', \'19px\');
	$(this).css(\'background-color\', \'#FFFFFF\');
	$(this).css(\'border\', \'1px solid #EBEBEB\');
})

$(\'div.application_current\').mouseout(function(){
	$(this).css(\'fontSize\', \'37%\');
	$(this).css(\'width\', \'40px\');
	$(this).css(\'height\', \'32px\');
	$(this).css(\'margin-top\', \'19px\');
	$(this).css(\'margin-bottom\', \'19px\');
	$(this).css(\'background-color\', \'#EBEBEB\');
	$(this).css(\'border\', \'1px solid #c0c0c0\');
})

})</script>';
		
		$html[] = '<div class="configure">';
			
		$the_applications = Application :: load_all();
		$the_applications = array_merge(array('admin', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu'), $the_applications);
		
					
		foreach ($the_applications as $the_application)
		{
			if (isset($application) && $application == $the_application)
			{
				$html[] = '<div class="application_current">';
			}
			else
			{
				$html[] = '<div class="application">';
			}
			
			$application_name = Translation :: get(DokeosUtilities :: underscores_to_camelcase($the_application)); 
			
			$html[] = '<a href="'. $this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS, RightsManager :: PARAM_APPLICATION => $the_application)) .'">';
			$html[] = '<img src="'. Theme :: get_image_path('admin') . 'place_' . $the_application .'.png" border="0" style="vertical-align: middle;" alt="' . $application_name . '" title="' . $application_name . '"/><br />'. $application_name;
			$html[] = '</a>';
			$html[] = '</div>';
		}
		
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';

		return implode("\n", $html);
	}
}