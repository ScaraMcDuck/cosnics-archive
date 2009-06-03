<?php

/*
 * This is the history page. Here a user can follow the changes made to a wiki_page.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path().'/lib/complex_display/complex_display.class.php';
require_once Path :: get_repository_path().'/lib/complex_display/wiki/component/wiki_parser.class.php';
require_once Path :: get_repository_path().'lib/learning_object_display.class.php';
require_once Path :: get_repository_path().'lib/learning_object_difference_display.class.php';
require_once Path :: get_repository_path().'lib/learning_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/wiki/wiki_display.class.php';


class WikiToolHistoryComponent extends WikiToolComponent
{
    function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

        $_GET['display_action'] = 'history';
        $cd = ComplexDisplay :: factory($this, 'wiki');
        $cd->run();
        $this->display_footer();
    }

    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
	{
        //$parameters[Tool :: PARAM_ACTION] = GlossaryTool :: ACTION_BROWSE_GLOSSARIES;
		return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
	}

    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
	{
        //$parameters[Tool :: PARAM_ACTION] = GlossaryTool :: ACTION_BROWSE_GLOSSARIES;
		$this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
	}
}
?>
