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
				
				header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
				header('Cache-Control: public');
				header('Pragma: no-cache');
				header('Content-type: application/octet-stream');
				header('Content-length: '.filesize($path));
				
				if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT']))
				{
					header('Content-Disposition: filename=content_objects.dlof');
				}
				else
				{
					header('Content-Disposition: attachment; filename=content_objects.dlof');
				}
				
				if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
				{
					header('Pragma: ');
					header('Cache-Control: ');
					header('Cache-Control: public'); // IE cannot download from sessions without a cache
				}
				
				header('Content-Description: content_objects.dlof');
				header('Content-transfer-encoding: binary');
				$fp = fopen($path, 'r');
				fpassthru($fp);
				fclose($fp);
				Filesystem :: remove($path);
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