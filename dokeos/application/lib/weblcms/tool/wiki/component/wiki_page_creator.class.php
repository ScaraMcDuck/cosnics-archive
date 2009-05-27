<?php

/*
 * This is the compenent that allows the user to create a wiki_page.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */
require_once Path :: get_repository_path().'/lib/complex_display/complex_display.class.php';

class WikiToolPageCreatorComponent extends WikiToolComponent
{
    function run()
	{
		if(!$this->is_allowed(EDIT_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

        $cd = ComplexDisplay :: factory($this);
        $cd->run();

        switch($cd->get_action())
        {
            case WikiDisplay :: ACTION_CREATE_PAGE:
                Events :: trigger_event('create_page', 'weblcms', array('course' => Request :: get('course'), Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_COMPLEX_ID => Request :: get('cid')));
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