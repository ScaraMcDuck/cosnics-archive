$(document).ready(function()
{
	var tool = $("#tool_bar").attr('class');

	$("#tool_bar_hide_container").toggle();
	
	$("#tool_bar_hide").bind("click", hideBlockScreen);
	$("#tool_bar_show").bind("click", showBlockScreen);
	
	function hideBlockScreen()
	{
		$("#tool_bar_hide_container").attr('class', 'show');
		
		if (tool == 'tool_bar tool_bar_left')
		{
			$("div.tool_bar_left").animate(
				{
					left: "-181px"
				}
				, 300, function()
					{
						$("#tool_bar_hide").toggle();
						$("#tool_bar_show").toggle();
					}
			);
			$("#tool_browser_left").animate({marginLeft: "0px"}, 300);
		}
		else
		{
			$("div.tool_bar_right").animate(
				{
					right: "-181px"
				}
				, 300, function()
					{
						$("#tool_bar_hide").toggle();
						$("#tool_bar_show").toggle();
					}
			);
			$("#tool_browser_right").animate({marginRight: "0px"}, 300);
		}
		
		return false;
	}
	
	function showBlockScreen()
	{
		$("#tool_bar_hide_container").attr('class', 'hide');
		
		if (tool == 'tool_bar tool_bar_left')
		{
			$("div.tool_bar_left").animate(
				{
					left: "0px"
				}
				, 300, function()
					{
						$("#tool_bar_hide").toggle();
						$("#tool_bar_show").toggle();
					}
			);
			
			$("#tool_browser_left").animate({marginLeft: "180px"}, 300);
		}
		else
		{
			$("div.tool_bar_right").animate(
				{
					right: "0px"
				}
				, 300, function()
					{
						$("#tool_bar_hide").toggle();
						$("#tool_bar_show").toggle();
					}
			);
			
			$("#tool_browser_right").animate({marginRight: "180px"}, 300);
		}
		
		return false;
	}
});