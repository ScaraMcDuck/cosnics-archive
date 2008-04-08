<?php
/**
 * $Id$
 * @package repository.repositorymanager
 * 
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectdisplay.class.php';
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
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
				$breadcrumbs[] = array('url' => $this->get_recycle_bin_url(), 'name' => Translation :: get('RecycleBin'));
				$this->force_menu_url($this->get_recycle_bin_url());
			}
			$breadcrumbs[] = array('url' => $this->get_url(), 'name' => $object->get_title() . ($object->is_latest_version() ? '' : ' ('.Translation :: get('OldVersion').')'));

			$version_data = array();
			$versions = $object->get_learning_object_versions();

			$publication_attr = array();

			foreach ($object->get_learning_object_versions() as $version)
			{
				// If this learning object is published somewhere in an application, these locations are listed here.
				$publications = $this->get_learning_object_publication_attributes($this->get_user(), $version->get_id());
				$publication_attr = array_merge($publication_attr, $publications);
			}

			if (count($versions) >= 2)
			{
				RepositoryUtilities :: order_learning_objects_by_id_desc($versions);
				foreach ($versions as $version)
				{
					$version_entry = array();
					$version_entry['id'] = $version->get_id();
					if (strlen($version->get_title()) > 20)
					{
						$version_entry['title'] = substr($version->get_title(), 0, 20) .'...';
					}
					else
					{
						$version_entry['title'] = $version->get_title();
					}
					$version_entry['date'] = date('d M y, H:i', $version->get_creation_date());
					$version_entry['comment'] = $version->get_comment();
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

					$version_data[] = $display->get_version_as_html($version_entry);
				}

				$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_COMPARE, $object, 'compare', 'post', $this->get_url(array(RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $object->get_id())), array('version_data' => $version_data));
				if ($form->validate())
				{
					$params = $form->compare_learning_object();
					$this->redirect(RepositoryManager :: ACTION_COMPARE_LEARNING_OBJECTS, null, null, false, $params);
				}
				else
				{
					$this->display_header($breadcrumbs);
					echo $display->get_full_html();
					echo RepositoryUtilities :: build_block_hider('script');
					echo RepositoryUtilities :: build_block_hider('begin', 'lox', 'LearningObjectExtras');
					$form->display();
				}
				echo $display->get_version_quota_as_html($version_data);
			}
			elseif (count($publication_attr) > 0)
			{
				$this->display_header($breadcrumbs);
				echo $display->get_full_html();
				echo RepositoryUtilities :: build_block_hider('script');
				echo RepositoryUtilities :: build_block_hider('begin', 'lox', 'LearningObjectExtras');
			}
			else
			{
				$this->display_header($breadcrumbs);
				echo $display->get_full_html();
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
						'img' => $this->get_path(WEB_IMG_PATH).'recycle_bin.gif',
						'label' => Translation :: get('Remove'),
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
							'img' => $this->get_path(WEB_IMG_PATH).'delete.gif',
							'label' => Translation :: get('Delete'),
							'confirm' => true,
							'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
						);
						$in_recycle_bin = true;
					}
					else
					{
						$recycle_bin_button = array(
							'img' => $this->get_path(WEB_IMG_PATH).'recycle_bin_na.gif',
							'label' => Translation :: get('Remove'),
							'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
						);
					}
				}

				if(!$in_recycle_bin)
				{

					$delete_link_url = $this->get_learning_object_delete_publications_url($object);

					if (!isset($recycle_url))
					{
						$force_delete_button = array(
							'href' => $delete_link_url,
							'img' => $this->get_path(WEB_IMG_PATH).'unlink.gif',
							'label' => Translation :: get('Unlink'),
							'confirm' => true,
							'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
						);
					}

					$edit_url = $this->get_learning_object_editing_url($object);
					if (isset($edit_url))
					{
						$toolbar_data[] = array(
							'href' => $edit_url,
							'img' => $this->get_path(WEB_IMG_PATH).'edit.gif',
							'label' => Translation :: get('Edit'),
							'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
						);
					}
					else
					{
						$toolbar_data[] = array(
							'img' => $this->get_path(WEB_IMG_PATH).'edit_na.gif',
							'label' => Translation :: get('Edit'),
							'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
						);
					}

					$toolbar_data[] = $recycle_bin_button;


					if (isset($force_delete_button))
					{
						$toolbar_data[] = $force_delete_button;
					}
					$dm = RepositoryDataManager::get_instance();
					if($dm->get_number_of_categories($this->get_user_id()) > 1)
					{
						$toolbar_data[] = array(
							'href' =>  $this->get_learning_object_moving_url($object),
							'img' => $this->get_path(WEB_IMG_PATH).'move.gif',
							'label' => Translation :: get('Move'),
							'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
						);
					}
					$toolbar_data[] = array(
						'href' => $this->get_learning_object_metadata_editing_url($object),
						'label' => Translation :: get('Metadata'),
						'img' => $this->get_path(WEB_IMG_PATH).'info_small.gif',
						'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
					);
					$toolbar_data[] = array(
						'href' => $this->get_learning_object_rights_editing_url($object),
						'label' => Translation :: get('Rights'),
						'img' => $this->get_path(WEB_IMG_PATH).'group_small.gif',
						'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
					);
				}
				else
				{
					$toolbar_data[] = array(
						'href' => $this->get_learning_object_restoring_url($object),
						'label' => Translation :: get('Restore'),
						'img' => $this->get_path(WEB_IMG_PATH).'restore.gif',
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
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>