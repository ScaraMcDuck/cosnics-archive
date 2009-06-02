<?php

require_once dirname(__FILE__) . '/../forum_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

class ForumBuilderStickyComponent extends ForumBuilderComponent
{
	function run()
	{
        echo 'sticky';
	}
}

?>
