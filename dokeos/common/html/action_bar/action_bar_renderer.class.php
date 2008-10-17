<?php
require_once dirname(__FILE__) . '/action_bar_search_form.class.php';
require_once Path :: get_library_path().'html/toolbar/toolbar.class.php';
require_once Path :: get_library_path().'html/toolbar/toolbar_item.class.php';
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
	private $search_form;
	
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
		$this->search_form = new ActionBarSearchForm($search_url);
	}
	
	function as_html()
	{
		$html = array();
		
		$html[] = '<div id="action_bar_text" style="float:left; display: none; margin-bottom: 10px;"><a href="#"><img src="'. Theme :: get_common_img_path() .'action_bar.png" style="vertical-align: middle;" />&nbsp;'. Translation :: get('ShowActionBar') .'</a></div>';
		$html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
		$html[] = '<div id="action_bar" class="action_bar">';
		
		$common_actions = $this->get_common_actions();
		$tool_actions = $this->get_tool_actions();
		
		if (count($common_actions) > 0)
		{
			$html[] = '<div class="common_menu">';
			$toolbar = new Toolbar();
			$toolbar->set_items($common_actions);
			$html[] = $toolbar->as_html();
			$html[] = '</div>';
		}
		
		if (count($tool_actions) > 0)
		{
			$html[] = '<div class="tool_menu">';
			$html[] = $this->build_toolbar($tool_actions);
			$html[] = '</div>';
		}
		
		if (!is_null($this->search_form))
		{
			$search_form = $this->search_form;
			
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
	
	function as_left_html()
	{
		$html = array();
		
		$html[] = '<div id="action_bar_left" class="action_bar_left">';
//		$html[] = '<div id="action_bar_left_options"';
		
		$common_actions = $this->get_common_actions();
		$tool_actions = $this->get_tool_actions();
		
		$action_bar_has_search_form = !is_null($this->search_form);
		$action_bar_has_common_actions = (count($common_actions) > 0);
		$action_bar_has_tool_actions = (count($tool_actions) > 0);
		$action_bar_has_common_and_tool_actions = (count($common_actions) > 0) && (count($tool_actions) > 0);
		
		if (!is_null($this->search_form))
		{
			$search_form = $this->search_form;
			$html[] = $search_form->as_html();
		}
		
		if ($action_bar_has_search_form && ($action_bar_has_common_actions || $action_bar_has_tool_actions))
		{
			$html[] = '<div class="divider"></div>';
		}
		
		if ($action_bar_has_common_actions)
		{
			$html[] = '<div class="clear"></div>';
			
			$toolbar = new Toolbar();
			$toolbar->set_items($common_actions);
			$toolbar->set_type(Toolbar :: TYPE_VERTICAL);
			$html[] = $toolbar->as_html();
		}
		
		if ($action_bar_has_common_and_tool_actions)
		{
			$html[] = '<div class="divider"></div>';
		}
		
		if ($action_bar_has_tool_actions)
		{
			$html[] = '<div class="clear"></div>';
			
			$toolbar = new Toolbar();
			$toolbar->set_items($tool_actions);
			$toolbar->set_type(Toolbar :: TYPE_VERTICAL);
			$html[] = $toolbar->as_html();
		}
		
		$html[] = '<div class="clear"></div>';
//		$html[] = '</div>';
		
		$html[] = '<div id="action_bar_left_hide_container" class="hide">';
		$html[] = '<a id="action_bar_left_hide" href="#"><img src="'. Theme :: get_common_img_path() .'action_action_bar_hide.png" /></a>';
		$html[] = '<a id="action_bar_left_show" href="#"><img src="'. Theme :: get_common_img_path() .'action_action_bar_show.png" /></a>';
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
			{
				$html[] = '<div style="margin: auto; padding-left: 5px; float: left;">';
			} 
				 
			$label = htmlentities($elmt['label']);
			$button = '';
			if (isset ($elmt['img']))
			{
				$button .= '<img src="'.htmlentities($elmt['img']).'" alt="'.$label.'" title="'.$label.'"'. 'class="labeled")/> <span>'.$label.'</span>';
			}
				
			if (isset ($elmt['href']))
			{
				$button = '<a href="'.htmlentities($elmt['href']).'" title="'.$label.'"'. ($elmt['confirm'] ? ' onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"' : '').'>'.$button.'</a>';
			}
				
			$html[] = $button;
			if($i % 2 == 0)
			{
				$html[] = '<br />';
			}
			else
			{
				$html[] = '</div>';
			}
				
			$i++;
		}
		
		if(count($toolbar_data) % 2 != 0)
		{
			$html[] = '</div>';
		}
		
		return implode($html);
	}
	
	function get_query()
	{
		if($this->search_form)
		{
			return $this->search_form->get_query();
		}
		else
		{
			return null;
		}
	}
}

?>