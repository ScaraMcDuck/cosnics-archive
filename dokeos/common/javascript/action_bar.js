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
	
	var menuHeight = $("div.action_bar_left").height();
	var newMarginTop = '-' + (menuHeight / 2) + 'px';
	
	$("div.action_bar_left").css('margin-top', newMarginTop);
	
	$("#action_bar_left_hide_container").toggle();
	
	$("#action_bar_left_hide").bind("click", hideBlockScreenLeft);
	$("#action_bar_left_show").bind("click", showBlockScreenLeft);
	
	$("#action_bar_browser").css('margin-left', '230px');
	
	function hideBlockScreenLeft()
	{
		$("#action_bar_left_hide_container").attr('class', 'show');
		$("div.action_bar_left").animate(
			{
				left: "-231px"
			}
			, 300, function()
				{
					$("#action_bar_left_hide").toggle();
					$("#action_bar_left_show").toggle();
				}
		);
		$("#action_bar_browser").animate({marginLeft: "0px"}, 300);
		
		return false;
	}
	
	function showBlockScreenLeft()
	{
		$("#action_bar_left_hide_container").attr('class', 'hide');
		$("div.action_bar_left").animate(
			{
				left: "0px"
			}
			, 300, function()
				{
					$("#action_bar_left_hide").toggle();
					$("#action_bar_left_show").toggle();
				}
		);
		
		$("#action_bar_browser").animate({marginLeft: "230px"}, 300);
		
		return false;
	}
});