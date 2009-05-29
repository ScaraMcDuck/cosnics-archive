<?php

require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

class LearningPathToolCloViewerComponent extends LearningPathToolComponent
{
	function run()
	{
        $display = ComplexDisplay :: factory($this, 'forum');
        $display->run();
	}

}
?>