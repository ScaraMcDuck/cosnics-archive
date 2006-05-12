<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectdisplay.class.php';
/**
 * Repository manager component which can be used to view a learning object.
 */
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
			$breadcrumbs = array();
			if ($object->get_state() == LearningObject :: STATE_RECYCLED)
			{
				$breadcrumbs[] = array('url' => $this->get_recycle_bin_url(), 'name' => get_lang('RecycleBin'));
				$this->force_menu_url($this->get_recycle_bin_url());
			}
			$breadcrumbs[] = array('url' => $this->get_url(), 'name' => $object->get_title());
			$this->display_header($breadcrumbs);
			echo $display->get_full_html();
			echo '<ul class="learning_object_management_buttons" style="list-style: none; margin: 1em 0; padding: 0;">';
			echo '<li style="display: inline; margin: 0; padding: 0;">';
			echo '<a href="'.$this->get_learning_object_editing_url($object).'" title="'.get_lang('Edit').'"><img src="'.$this->get_web_code_path().'img/edit.gif" alt="'.get_lang('Edit').'" style="vertical-align: middle;"/></a>';
			echo '</li>';
			$recycle_url = $this->get_learning_object_recycling_url($object);
			if (isset($recycle_url))
			{
				echo '<li style="display: inline; margin: 0; padding: 0;">';
				echo '<a href="'.$recycle_url.'" title="'.get_lang('Remove').'"  onclick="return confirm(&quot;'.htmlentities(get_lang('ConfirmYourChoice')).'&quot;);"><img src="'.$this->get_web_code_path().'img/recycle_bin.gif" alt="'.get_lang('Recycle').'" style="vertical-align: middle;"/></a>';
				echo '</li>';
			}
			else
			{
				$delete_url = $this->get_learning_object_deletion_url($object);
				echo '<li style="display: inline; margin: 0; padding: 0;">';
				echo '<a href="'.$delete_url.'" title="'.get_lang('Delete').'"  onclick="return confirm(&quot;'.htmlentities(get_lang('ConfirmYourChoice')).'&quot;);"><img src="'.$this->get_web_code_path().'img/delete.gif" alt="'.get_lang('Delete').'" style="vertical-align: middle;"/></a>';
				echo '</li>';
			}
			echo '<li style="display: inline; margin: 0; padding: 0;">';
			echo '<a href="'.$this->get_learning_object_metadata_editing_url($object).'" title="'.get_lang('Metadata').'"><img src="'.$this->get_web_code_path().'img/info_small.gif" alt="'.get_lang('Metadata').'" style="vertical-align: middle;"/></a>';
			echo '</li>';
			echo '<li style="display: inline; margin: 0; padding: 0;">';
			echo '<a href="'.$this->get_learning_object_rights_editing_url($object).'" title="'.get_lang('Rights').'"><img src="'.$this->get_web_code_path().'img/group_small.gif" alt="'.get_lang('Rights').'" style="vertical-align: middle;"/></a>';
			echo '</li>';
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