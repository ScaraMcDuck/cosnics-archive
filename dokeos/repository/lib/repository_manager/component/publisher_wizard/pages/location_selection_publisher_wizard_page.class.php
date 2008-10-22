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
	function get_title()
	{
		return Translation :: get('LocationSelection');
	}
	
	function get_info()
	{
		$learning_object = $this->get_parent()->retrieve_learning_object($_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID]);
		return Translation :: get('LocationSelectionInfo') . '<br /><br />' . $this->display_learning_object($learning_object);//' <b>' . $learning_object->get_type() . ' - ' . $learning_object->get_title() . '</b>';
	}
	
	function display_learning_object($learning_object)
	{
		$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_img_path(). 'learning_object/' .$learning_object->get_icon_name().'.png);">';
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
					$html[] = '<li><img src="'.Theme :: get_common_img_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
				}
				$html[] = '</ul>';
				return implode("\n",$html);
			}
		}
		return '';
	}
	
	function buildForm()
	{
		$this->_formBuilt = true;

		$applications = Application::load_all_from_filesystem(false);
		foreach($applications as $application)
		{
			$this->addElement('html', '<br /><br /><h3 style="padding-left: 15%;">' . Translation :: get(Application::application_to_class($application)) . '</h3>');
			$this->addElement('checkbox', $application, '', Translation :: get(Application::application_to_class($application)));
			$appDefaults[$application] = '1';
		}
		
		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->setDefaults($appDefaults);
	}
}
?>