<?php

/*
 * This is the component that allows the user view all statisctics about a wiki.
 * 
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once Path :: get_repository_path().'/lib/complex_display/complex_display.class.php';

class WikiToolStatisticsViewerComponent extends WikiToolComponent
{
    function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

        $cd = ComplexDisplay :: factory($this);
        $cd->run();

        switch($cd->get_action())
        {
            case WikiDisplay :: ACTION_STATISTICS:
                Events :: trigger_event('statistics', 'weblcms', array('course' => Request :: get('course'), Tool :: PARAM_PUBLICATION_ID => Request :: get('pid')));
                break;
        }
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