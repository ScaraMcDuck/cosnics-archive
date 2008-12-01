<?php
require_once dirname(__FILE__).'/../lib/admin_block.class.php';

class AdminPortalHome extends AdminBlock
{
	/**
	 * Runs this component and displays its output.
	 * This component is only meant for use within the home-component and not as a standalone item.
	 */
	function run()
	{
		return $this->as_html();
	}
	
	function as_html()
	{
		$html[] = $this->display_header();
		
		$object_id = PlatformSetting :: get('portal_home');
		
		if (!isset($object_id) || $object_id == 0)
		{
			$html[] = Translation :: get('ConfigureBlockFirst');
		}
		else
		{
			$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($object_id);
			$html[] = $learning_object->get_description();
		}
		
		$html[] = $this->display_footer();
		return implode("\n", $html);
	}
	
	function is_editable()
	{
		return false;
	}
	
	function is_hidable()
	{
		return false;
	}
	
	function is_deletable()
	{
		return false;
	}

}
?>