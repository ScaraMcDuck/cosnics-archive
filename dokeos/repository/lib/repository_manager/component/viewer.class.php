<?php
/**
 * $Id$
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
	/**
	 * Runs this component and displays its output.
	 */
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
			$toolbar_data = array();
			$recycle_url = $this->get_learning_object_recycling_url($object);
			$in_recycle_bin = false;
			if (isset($recycle_url))
			{
				$recycle_bin_button = array(
					'href' => $recycle_url,
					'img' => api_get_path(WEB_CODE_PATH).'img/recycle_bin.gif',
					'label' => get_lang('Remove'),
					'confirm' => true,
					'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
			}
			else
			{
				$delete_url = $this->get_learning_object_deletion_url($object);
				if (isset($delete_url))
				{
					$recycle_bin_button = array(
						'href' => $delete_url,
						'img' => api_get_path(WEB_CODE_PATH).'img/delete.gif',
						'label' => get_lang('Delete'),
						'confirm' => true,
						'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
					);
					$in_recycle_bin = true;
				}
				else
				{
					$recycle_bin_button = array(
						'img' => api_get_path(WEB_CODE_PATH).'img/recycle_bin_na.gif',
						'label' => get_lang('Remove'),
						'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
					);
				}
			}
			if(!$in_recycle_bin)
			{
				$toolbar_data[] = array(
					'href' => $this->get_learning_object_editing_url($object),
					'img' => api_get_path(WEB_CODE_PATH).'img/edit.gif',
					'label' => get_lang('Edit'),
					'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
				$toolbar_data[] = $recycle_bin_button;
				$toolbar_data[] = array(
						'href' =>  $this->get_learning_object_moving_url($object),
						'img' => api_get_path(WEB_CODE_PATH).'img/move.gif',
						'label' => get_lang('Move'),
						'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
				$toolbar_data[] = array(
					'href' => $this->get_learning_object_metadata_editing_url($object),
					'label' => get_lang('Metadata'),
					'img' => api_get_path(WEB_CODE_PATH).'img/info_small.gif',
					'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
				$toolbar_data[] = array(
					'href' => $this->get_learning_object_rights_editing_url($object),
					'label' => get_lang('Rights'),
					'img' => api_get_path(WEB_CODE_PATH).'img/group_small.gif',
					'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
			}
			else
			{
				$toolbar_data[] = array(
					'href' => $this->get_learning_object_restoring_url($object),
					'label' => get_lang('Restore'),
					'img' => api_get_path(WEB_CODE_PATH).'img/restore.gif',
					'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
				$toolbar_data[] = $recycle_bin_button;
			}
			echo RepositoryUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
			// If this learning object is published somewhere in an application, these locations are listed here.
			$publication_attr = $this->get_learning_object_publication_attributes($object->get_id());
			if (count($publication_attr) > 0)
			{
				// TODO: Use a function for this or something.
				echo '<div class="publication_attributes">';
				echo '<div class="publication_attributes_title">'.htmlentities(get_lang('ThisObjectIsPublished')).'</div>';
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
			$this->display_error_page(htmlentities(get_lang('NoObjectSelected')));
		}
	}
}
?>