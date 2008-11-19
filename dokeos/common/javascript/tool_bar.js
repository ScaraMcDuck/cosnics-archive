$(document).ready(function()
{
	$("#tool_bar_left_hide_container").toggle();
	
	$("#tool_bar_left_hide").bind("click", hideBlockScreenLeft);
	$("#tool_bar_left_show").bind("click", showBlockScreenLeft);
	
	//$("#tool_browser").css('margin-left', '230px');
	
	function hideBlockScreenLeft()
	{
		$("#tool_bar_left_hide_container").attr('class', 'show');
		$("div.tool_bar_left").animate(
			{
				left: "-231px"
			}
			, 300, function()
				{
					$("#tool_bar_left_hide").toggle();
					$("#tool_bar_left_show").toggle();
				}
		);
		$("#tool_browser").animate({marginLeft: "0px"}, 300);
		
		return false;
	}
	
	function showBlockScreenLeft()
	{
		$("#tool_bar_left_hide_container").attr('class', 'hide');
		$("div.tool_bar_left").animate(
			{
				left: "0px"
			}
			, 300, function()
				{
					$("#tool_bar_left_hide").toggle();
					$("#tool_bar_left_show").toggle();
				}
		);
		
		$("#tool_browser").animate({marginLeft: "230px"}, 300);
		
		return false;
	}
});