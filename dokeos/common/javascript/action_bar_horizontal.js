$(document).ready(function()
{	
	$("#action_bar_hide_container").toggle();
	
	$("#action_bar_text").bind("click", showBlockScreen);
	$("#action_bar_hide").bind("click", hideBlockScreen);
	
	function showBlockScreen()
	{
		$("#action_bar_text").slideToggle(300, function()
		{
			$("div.action_bar").slideToggle(300);
		});
		
		return false;
	}
	
	function hideBlockScreen()
	{
		$("div.action_bar").slideToggle(300, function()
		{
			$("#action_bar_text").slideToggle(300);
		});
		
		return false;
	}
});