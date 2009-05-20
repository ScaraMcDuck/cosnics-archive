<?php
/**
 * @package main
 * @subpackage install
 */
require_once dirname(__FILE__).'/publisher_wizard_page.class.php';
/**
 * Class for application settings page
 * Displays a form where the user can enter the installation settings
 * regarding the applications
 */
class LocationSelectionPublisherWizardPage extends PublisherWizardPage
{
	private $learning_objects;
	private $type;
	
	public function LocationSelectionPublisherWizardPage($name,$parent)
	{
		parent :: PublisherWizardPage($name, $parent);
		$ids = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];

        $trail = new BreadCrumbTrail();
        $trail->add(new Breadcrumb($this->get_parent()->get_url(), 'test'));
		
		if(empty($ids)) 
		{
			$_GET['message'] = Translation :: get('NoObjectSelected');
			$this->get_parent()->display_header($trail, false, true, 'repository publication wizard');
			$this->get_parent()->display_footer();
			exit();
		}
		
		if(!is_array($ids))
			$ids = array($ids);
		
		foreach($ids as $id)
		{
			$lo = $this->get_parent()->retrieve_learning_object($id);
			$this->learning_objects[] = $lo;
			if($this->type == null)
				$this->type = $lo->get_type();
			else
			{
				if($this->type != $lo->get_type())
				{
					$_GET['message'] = Translation :: get('ObjectsNotSameType');
					$this->get_parent()->display_header($trail, false, true, 'repository publication wizard');
					$this->get_parent()->display_footer();
					exit();
				}
			}
		}
	}
	
	function get_title()
	{
		return Translation :: get('LocationSelection');
	}
	
	function get_info()
	{
		return Translation :: get('LocationSelectionInfo') . '<br /><br />'; //$this->display_learning_objects();//' <b>' . $learning_object->get_type() . ' - ' . $learning_object->get_title() . '</b>';
	}
	
	/*function display_learning_objects()
	{
		$html = array();
		foreach ($this->learning_objects as $lo)
			$html[] = $this->display_learning_object($lo);
		
		return implode("\n", $html);
	}
	
	function display_learning_object($learning_object)
	{
		$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$learning_object->get_icon_name().'.png);">';
		$html[] = '<div class="title">';
		$html[] = $learning_object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $learning_object->get_description();
		$html[] = $this->render_attachments($learning_object);
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	function render_attachments($learning_object)
	{
		if ($learning_object->supports_attachments())
		{
			$attachments = $learning_object->get_attached_learning_objects();
			if(count($attachments)>0)
			{
				$html[] = '<ul class="attachments_list">';
				DokeosUtilities :: order_learning_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$disp = LearningObjectDisplay :: factory($attachment);
					$html[] = '<li><img src="'.Theme :: get_common_image_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
				}
				$html[] = '</ul>';
				return implode("\n",$html);
			}
		}
		return '';
	}*/

	function buildForm()
	{
		$this->_formBuilt = true;

		$html = '<script type="text/javascript">
							/* <![CDATA[ */
							function setCheckbox(app_name, value) {
								var d = document[\'page_locations\'];
								for (i = 0; i < d.elements.length; i++) {
									if (d.elements[i].type == "checkbox") 
									{
									     if(app_name.length == null || d.elements[i].name.substr(0, app_name.length) == app_name)
									     		d.elements[i].checked = value;
									}
								}
							}
							/* ]]> */
							</script>';
		$this->addElement('html', $html);

		$applications = Application::load_all_from_filesystem(true);
		$apps = array();
		foreach($applications as $application_name)
		{
			$application = Application::factory($application_name);
			$locations = $application->get_learning_object_publication_locations($this->learning_objects[0], $this->get_parent()->get_user());
			if(count($locations) == 0) continue;
			
			//$apps[] =
			
			//$this->addElement('html', '<br /><br /><h3 style="margin-left: 15%;">' . Translation :: get(Application::application_to_class($application_name)) . '</h3>');
			
			$this->addElement('html', '<div class="block" id="block_introduction" style="background-image: url('.Theme :: get_image_path('home').'block_' . $application_name . '.png);">');
			$this->addElement('html', '<div class="title"><div style="float:left;">'. Translation :: get(Application::application_to_class($application_name)));
			$this->addElement('html', '</div><div style="float:right;"><a href="#" class="closeEl"><img class="visible" src="'.Theme :: get_common_image_path().'action_visible.png" /><img class="invisible" style="display: none;") src="'.Theme :: get_common_image_path().'action_invisible.png" /></a></div><div class="clear">&nbsp;</div></div>');
			$this->addElement('html', '<div class="description"><br />');
		
			$application_name = DokeosUtilities :: underscores_to_camelcase($application_name);
		
			foreach($locations as $id => $location )
			{
				$cbname = $application_name . '_' . $id;
				$this->addElement('checkbox', $cbname, '', $location);
				$appDefaults[$cbname] = '1';
			}
			
			$this->addElement('html', '<br /><br /><a href="?" style="margin-left: 5%" onclick="setCheckbox(\'' . $application_name . '\', true); return false;">'.Translation :: get('SelectAll').'</a>');
			$this->addElement('html', ' - <a href="?" onclick="setCheckbox(\'' . $application_name . '\', false); return false;">'.Translation :: get('UnSelectAll').'</a>');
			
			$this->addElement('html', '<div style="clear: both;"></div></div></div><br />');
		}
		
		$this->addElement('html', '<br /><br />');
		//$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>', 'style=\'margin-left: -20%;\'');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
	
		if(count($apps) > 1)
		{
			$this->addElement('html', '<br /><br /><a href="?" style="margin-left: 0%"  onclick="setCheckbox(\'\', true); return false;">'.Translation :: get('SelectAll').'</a>');
			$this->addElement('html', ' - <a href="?" onclick="setCheckbox(\'\', false); return false;">'.Translation :: get('UnSelectAll').'</a>');
		}
		
		$this->addElement('html', '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/home_ajax.js' .'"></script>');
		
		$this->setDefaultAction('next');
		$this->setDefaults($appDefaults);
	}
}
?>