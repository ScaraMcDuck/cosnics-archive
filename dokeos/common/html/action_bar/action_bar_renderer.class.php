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
		
		$html[] = '<div id="abtext" style="clear: both; margin-bottom: 10px; display: none;"><a href="#"><img src="'. Theme :: get_common_img_path() .'action_bar.png" style="vertical-align: middle;" />&nbsp;'. Translation :: get('ShowActionBar') .'</a></div>';
		$html[] = '<div id="actionbar" class="actionbar">';
		
		//$html[] = '<div style="float: left; padding: 5px; margin: -5px 0px -5px -5px; background-color: #4271B5;"><img src="'. Theme :: get_common_img_path() .'actionbar_title.png"><br /><img src="'. Theme :: get_common_img_path() .'action_actionbar_add.png" id="abhide" /></div>';
		$html[] = '<div id="abhidecontainer" style="float: left; padding: 5px; margin: -5px 0px -5px -5px; background-color: #4271B5;"><img src="'. Theme :: get_common_img_path() .'actionbar_title.png" id="abhide" /></div>';
		
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
		$html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
		//$html[] = '<div id="abhidecontainer" style="display: none; clear: both; margin: 0px 0px -6px 0px; text-align: center; background-image: url('. Theme :: get_common_img_path() .'background_ajax_add.png); background-repeat: no-repeat; background-position: top center; padding-top: 5px; padding-bottom: 5px;"><img src="'. Theme :: get_common_img_path() .'action_ajax_add.png" id="abhide" /></div>';
		$html[] = '</div>';
		
		$html[] = '<script language="JavaScript">';
		$html[] = '  $("#abhide").attr(\'src\', \''. Theme :: get_common_img_path() .'action_actionbar_hide.png\');';
		
		$html[] = '  $("#abtext").bind("click", showBlockScreen);';
		$html[] = '  function showBlockScreen()';
		$html[] = '  {';
		$html[] = '     $("#abtext").slideToggle(300, function()';
		$html[] = '     {';
		$html[] = '     	$("div.actionbar").slideToggle(300);';
		$html[] = '     });';
		$html[] = '     return false;';
		$html[] = '  }';
		
		$html[] = '  $("#abhide").bind("click", hideBlockScreen);';
		$html[] = '  function hideBlockScreen()';
		$html[] = '  {';
		$html[] = '     $("div.actionbar").slideToggle(300, function()';
		$html[] = '     {';
		$html[] = '     	$("#abtext").slideToggle(300);';
		$html[] = '     });';
		$html[] = '     return false;';
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