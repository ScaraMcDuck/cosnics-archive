<?php
/**
 * $Id: deleter.class.php 15420 2008-05-26 17:34:32Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../export/content_object_export.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerExporterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
		
		if($ids)
		{
			if(!is_array($ids))
				$ids = array($ids);
			
			if(count($ids) > 0)
			{
				if($ids[0] == 'all')
				{
					$condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
				}
				else 
				{
					$condition = new InCondition(ContentObject :: PROPERTY_ID, $ids, ContentObject :: get_table_name());
				}
				
				$los = $this->retrieve_content_objects(null, $condition);
				while($lo = $los->next_result())
				{
					$content_objects[] = $lo;
				}
				
				$exporter = ContentObjectExport :: factory('dlof', $content_objects);
				$path = $exporter->export_content_object();
				
				/*Filesystem :: file_send_for_download($path, true, 'content_objects.dlof');
				Filesystem :: remove($path);*/
				
				Filesystem :: copy_file($path, Path :: get(SYS_TEMP_PATH) . $this->get_user_id() . '/content_objects.dlof', true);
				$webpath = Path :: get(WEB_TEMP_PATH) . $this->get_user_id() . '/content_objects.dlof';
				
				$this->display_header();
				$this->display_message('<a href="' . $webpath . '">' . Translation :: get('Download') . '</a>');
				$this->display_footer();
			}
			else
			{
				$this->display_header();
				$this->display_error_message(Translation :: get('NoObjectsSelected'));
				$this->display_footer();
			}
		}
	}
}
?>