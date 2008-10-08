<?php
require_once dirname(__FILE__) . '/action_bar_search_form.class.php';
/**
 * Class that renders an action bar divided in 3 parts, a left menu for actions, a middle menu for actions
 * and a right menu for a search bar.
 */
class ActionBarRenderer
{
	private $left_actions = array();
	private $middle_actions = array();
	private $search_form = null;
	
	function ActionBarRenderer($left_actions = array(), $middle_actions = array(), $url = null)
	{
		$this->left_actions = $left_actions;
		$this->middle_actions = $middle_actions;
		
		if($url)
			$this->search_form = new ActionBarSearchForm($url);
	}
	
	function as_html()
	{
		$html = array();
		
		$html[] = '<a id="abtext" href="#">ActionBar</a>';
		$html[] = '<div id="actionbar" class="actionbar">';
		
		if(count($this->left_actions) > 0)
		{
			if(count($this->middle_actions) > 0)
				$html[] = '  <div class="leftmenu" style="border-right: 1px solid grey;">';
			else
				$html[] = '  <div class="leftmenu">';
				
			$html[] =      $this->build_toolbar($this->left_actions);
			$html[] = '  </div>';
		}
		
		$html[] = '  <div class="middlemenu">';
		$html[] =      $this->build_toolbar($this->middle_actions);
		$html[] = '  </div>';
		
		if($this->search_form)
		{
			$html[] = '  <div class="rightmenu" style="border-left: 1px solid grey;">';
			$html[] = $this->search_form->as_html();
		}
		else
			$html[] = '  <div class="rightmenu">';
		
		$html[] = '  </div>';
		$html[] = '</div>';
		
		$html[] = '<script language="JavaScript">';
		$html[] = '  $("#abtext").bind("click", showBlockScreen);';
		$html[] = '  function showBlockScreen()';
		$html[] = '  {';
		$html[] = '     $("div.actionbar").toggle(); return false;';
		$html[] = '  }';
		$html[] = '</script>';
		
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