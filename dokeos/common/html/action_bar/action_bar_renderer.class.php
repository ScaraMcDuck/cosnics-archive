<?php
require_once dirname(__FILE__) . '/action_bar_search_form.class.php';
/**
 * Class that renders an action bar divided in 3 parts, a left menu for actions, a middle menu for actions
 * and a right menu for a search bar.
 */
class ActionBarRenderer
{
	const ACTION_BAR_COMMON = 'common';
	const ACTION_BAR_TOOL = 'tool';
	const ACTION_BAR_SEARCH = 'search';
	
	private $actions = array();
	
	function ActionBarRenderer($common_actions = array(), $tool_actions = array(), $search_url = null)
	{
		$this->actions[self :: ACTION_BAR_COMMON] = $common_actions;
		$this->actions[self :: ACTION_BAR_TOOL] = $tool_actions;
		$this->actions[self :: ACTION_BAR_SEARCH] = $search_url;
	}
	
	function add_action($type = self :: ACTION_BAR_COMMON, $action)
	{
		$this->actions[$type][] = $action;
	}
	
	function add_common_action($action)
	{
		$this->actions[self :: ACTION_BAR_COMMON][] = $action;
	}
	
	function add_tool_action($action)
	{
		$this->actions[self :: ACTION_BAR_TOOL][] = $action;
	}
	
	function get_tool_actions()
	{
		return $this->actions[self :: ACTION_BAR_TOOL];
	}
	
	function get_common_actions()
	{
		return $this->actions[self :: ACTION_BAR_COMMON];
	}
	
	function get_search_url()
	{
		return $this->actions[self :: ACTION_BAR_SEARCH];
	}
	
	function set_tool_actions($actions)
	{
		$this->actions[self :: ACTION_BAR_TOOL] = $actions;
	}
	
	function set_common_actions($actions)
	{
		$this->actions[self :: ACTION_BAR_COMMON] = $actions;
	}
	
	function set_search_url($search_url)
	{
		$this->actions[self :: ACTION_BAR_SEARCH] = $search_url;
	}
	
	function as_html()
	{
		$html = array();
		
		$html[] = '<div id="action_bar_text" style="float:left; display: none; margin-bottom: 10px;"><a href="#"><img src="'. Theme :: get_common_img_path() .'action_bar.png" style="vertical-align: middle;" />&nbsp;'. Translation :: get('ShowActionBar') .'</a></div>';
		$html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
		$html[] = '<div id="action_bar" class="action_bar">';
		
		$common_actions = $this->get_common_actions();
		$tool_actions = $this->get_tool_actions();
		$search_url = $this->get_search_url();
		
		if (count($common_actions) > 0)
		{
			$html[] = '<div class="common_menu">';
			$html[] = $this->build_toolbar($common_actions);
			$html[] = '</div>';
		}
		
		if (count($tool_actions) > 0)
		{
			$html[] = '<div class="tool_menu">';
			$html[] = $this->build_toolbar($tool_actions);
			$html[] = '</div>';
		}
		
		if (!is_null($search_url))
		{
			$search_form = new ActionBarSearchForm($search_url);
			
			$html[] = '<div class="search_menu">';
			$html[] = $search_form->as_html();
			$html[] = '</div>';
		}
		
		$html[] = '<div class="clear"></div>';
		$html[] = '<div id="action_bar_hide_container">';
		$html[] = '<a id="action_bar_hide" href="#"><img src="'. Theme :: get_common_img_path() .'action_ajax_hide.png" /></a>';
		$html[] = '</div>';
		$html[] = '</div>';
		
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/action_bar.js' .'"></script>';
		
		return implode("\n", $html);
	}
	
	private function build_toolbar($toolbar_data)
	{
		$html = array ();
		$i = 0;

		foreach ($toolbar_data as $index => $elmt)
		{
			if(($i % 2) == 0)
				$html[] = '<div style="margin: auto; padding-left: 5px; float: left;">'; 
				 
			$label = htmlentities($elmt['label']);
			$button = '';
			if (isset ($elmt['img']))
				$button .= '<img src="'.htmlentities($elmt['img']).'" alt="'.$label.'" title="'.$label.'"'. 'class="labeled")/> <span>'.$label.'</span>';
				
			if (isset ($elmt['href']))
				$button = '<a href="'.htmlentities($elmt['href']).'" title="'.$label.'"'. ($elmt['confirm'] ? ' onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"' : '').'>'.$button.'</a>';
				
			$html[] = $button;
			if($i % 2 == 0)
				$html[] = '<br />';
			else
				$html[] = '</div>';
				
			$i++;
		}
		
		if(count($toolbar_data) % 2 != 0)
			$html[] = '</div>';
		
		return implode($html);
	}
	
	function get_query()
	{
		if($this->search_form)
			return $this->search_form->get_query();
	}
}

?>