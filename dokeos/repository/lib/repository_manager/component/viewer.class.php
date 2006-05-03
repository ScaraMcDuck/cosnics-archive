<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectdisplay.class.php';

class RepositoryManagerViewerComponent extends RepositoryManagerComponent
{
	function run()
	{
		$id = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if ($id)
		{
			$object = $this->retrieve_learning_object($id);
			// TODO: Use Roles & Rights here.
			if ($object->get_owner_id() != $this->get_user_id())
			{
				$this->not_allowed();
			}
			$display = LearningObjectDisplay :: factory($object);
			$this->display_header();
			echo $display->get_full_html();
			echo '<ul class="learning_object_management_buttons" style="list-style: none; margin: 1em 0; padding: 0;"">';
			echo '<li style="display: inline; margin: 0; padding: 0 1ex 0 0;">';
			echo '<a href="'.$this->get_learning_object_editing_url($object).'" title="'.get_lang('Edit').'"><img src="'.$this->get_web_code_path().'img/edit.gif" alt="'.get_lang('Edit').'" style="vertical-align: middle;"/></a>';
			echo '</li>';
			$delete_url = $this->get_learning_object_deletion_url($object);
			if (isset($delete_url))
			{
				echo '<li style="display: inline; margin: 0; padding: 0 1ex 0 0;">';
				echo '<a href="'.$delete_url.'" title="'.get_lang('Delete').'"  onclick="return confirm(&quot;'.htmlentities(get_lang('ConfirmYourChoice')).'&quot;);"><img src="'.$this->get_web_code_path().'img/delete.gif" alt="'.get_lang('Delete').'" style="vertical-align: middle;"/></a>';
				echo '</li>';
			}
			echo '</ul>';
			$publication_attr = $this->get_learning_object_publication_attributes($object->get_id());
			if (count($publication_attr) > 0)
			{
				// TODO: Use a function for this or something.
				echo '<div class="publication_attributes">';
				echo '<div class="publication_attributes_title">'.get_lang('ObjectPublished').'</div>';
				echo '<ul class="publication_attributes">';
				foreach ($publication_attr as $info)
				{
					$publisher = $this->get_user_info($info->get_publisher_user_id());
					echo '<li>';
					// TODO: i18n
					echo $info->get_application().': '.$info->get_location().' ('.$publisher['firstName'].' '.$publisher['lastName'].', '.date('r', $info->get_publication_date()).')';
					echo '</li>';
				}
				echo '</ul>';
				echo '</div>';
			}
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(get_lang('NoObjectSelected'));
		}
	}
}
?>