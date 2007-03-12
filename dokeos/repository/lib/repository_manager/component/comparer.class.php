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
class RepositoryManagerComparerComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$id1 = $_POST['vgl1'];
		$id2 = $_POST['vgl2'];
		if ($id1 && $id2)
		{
			if ($id1 > $id2)
			{
				$tmp = $id2;
				$id2 = $id1;
				$id1 = $tmp;
			}
			$object1 = $this->retrieve_learning_object($id1);
			$object2 = $this->retrieve_learning_object($id2);

			$display = LearningObjectDisplay :: factory($object1);
			$breadcrumbs = array();

			if ($object1->get_state() == LearningObject :: STATE_RECYCLED)
			{
				$breadcrumbs[] = array('url' => $this->get_recycle_bin_url(), 'name' => get_lang('RecycleBin'));
				$this->force_menu_url($this->get_recycle_bin_url());
			}
			$breadcrumbs[] = array('url' => $this->get_url(), 'name' => $object1->get_title() . ($object1->is_latest_version() ? '' : ' ('.get_lang('Compared').')'));
			$this->display_header($breadcrumbs);
			echo RepositoryUtilities :: diff_to_html($object1->get_description(), $object2->get_description());
			
			// TODO what else do we need to display here?
			die();
			$version_data = array();
			$versions = $object->get_learning_object_versions();
			
			$publication_attr = array();
			
			foreach ($object->get_learning_object_versions() as $version)
			{
				// If this learning object is published somewhere in an application, these locations are listed here.
				$publications = $this->get_learning_object_publication_attributes($version->get_id());
				$publication_attr = array_merge($publication_attr, $publications);
			}
			
			if (count($versions) >= 2 || count($publication_attr) > 0)
			{
				echo RepositoryUtilities :: build_block_hider('script');
				echo RepositoryUtilities :: build_block_hider('begin', 'lox', 'LearningObjectExtras');
			}			
			
			if (count($versions) >= 2)
			{
				RepositoryUtilities :: order_learning_objects_by_id_desc(& $versions);
				foreach ($versions as $version)
				{
					$version_entry = array();
					$version_entry['id'] = $version->get_id();
					$version_entry['title'] = $version->get_title();
					$version_entry['date'] = date('(d M Y, H:i:s O)', $version->get_creation_date());
					$version_entry['viewing_link'] = $this->get_learning_object_viewing_url($version);
					
					$delete_url = $this->get_learning_object_deletion_url($version, 'version');
					if (isset($delete_url))
					{
						$version_entry['delete_link'] = $delete_url;
					}
					
					$revert_url = $this->get_learning_object_revert_url($version, 'version');
					if (isset($revert_url))
					{
						$version_entry['revert_link'] = $revert_url;
					}
					
					$version_data[] = $version_entry;	
				}
				
				echo $display->get_versions_as_html($version_data);
			}
			
			if (count($publication_attr) > 0)
			{				
				echo RepositoryUtilities :: build_uses($publication_attr);
			}
			
			if (count($versions) >= 2 || count($publication_attr) > 0)
			{
				echo RepositoryUtilities :: build_block_hider('end', 'lox');
			}
			
			$edit_url = $this->get_learning_object_editing_url($object);
			if (isset($edit_url))
			{
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
					
					$edit_url = $this->get_learning_object_editing_url($object);
					if (isset($edit_url))
					{
						$toolbar_data[] = array(
							'href' => $edit_url,
							'img' => api_get_path(WEB_CODE_PATH).'img/edit.gif',
							'label' => get_lang('Edit'),
							'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
						);
					}
					else
					{
						$toolbar_data[] = array(
							'img' => api_get_path(WEB_CODE_PATH).'img/edit_na.gif',
							'label' => get_lang('Edit'),
							'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
						);
					}
				
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