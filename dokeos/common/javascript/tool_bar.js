$(document).ready(function()
{
	var tool = $("#tool_bar").attr('class');

	$("#tool_bar_hide_container").toggle();
	
	$("#tool_bar_hide").bind("click", hideBlockScreen);
	$("#tool_bar_show").bind("click", showBlockScreen);
	
	if(hide)
		hideBlockScreen();
	
	function toggleButtons()
	{
		$("#tool_bar_hide").toggle();
		$("#tool_bar_show").toggle();
	}
	
	function hideBlockScreen()
	{
		$("#tool_bar_hide_container").attr('class', 'show');
		
		switch(tool)
		{
			case 'tool_bar tool_bar_left':
				$("div.tool_bar_left").animate({left: "-181px"}, 300, toggleButtons);
				$("#tool_browser_left").animate({marginLeft: "10px"}, 300);
				break;
			case 'tool_bar tool_bar_icon_left':
				$("div.tool_bar_icon_left").animate({left: "-55px"}, 300, toggleButtons);
				$("#tool_browser_icon_left").animate({marginLeft: "0px"}, 300);
				break;
			case 'tool_bar tool_bar_right':
				$("div.tool_bar_right").animate({right: "-181px"}, 300, toggleButtons);
				$("#tool_browser_right").animate({marginRight: "10px"}, 300);
				break;
			case 'tool_bar tool_bar_icon_right':
				$("div.tool_bar_icon_right").animate({right: "-55px"}, 300, toggleButtons);
				$("#tool_browser_icon_right").animate({marginRight: "0px"}, 300);
				break;
		}
		
		$.ajax({
			type: "POST",
			url: "./common/javascript/ajax/toolbar_memory.php",
			data: { state: 'hide'},
			async: false
		})
		
		return false;
	}
	
	function showBlockScreen()
	{
		$("#tool_bar_hide_container").attr('class', 'hide');
		
		switch(tool)
		{
			case 'tool_bar tool_bar_left':
				$("div.tool_bar_left").animate({left: "0px"}, 300, toggleButtons);
				$("#tool_browser_left").animate({marginLeft: "180px"}, 300);
				break;
			case 'tool_bar tool_bar_icon_left':
				$("div.tool_bar_icon_left").animate({left: "0px"}, 300, toggleButtons);
				$("#tool_browser_icon_left").animate({marginLeft: "54px"}, 300);
				break;
			case 'tool_bar tool_bar_right':
				$("div.tool_bar_right").animate({right: "0px"}, 300, toggleButtons);			
				$("#tool_browser_right").animate({marginRight: "180px"}, 300);
				break;
			case 'tool_bar tool_bar_icon_right':
				$("div.tool_bar_icon_right").animate({right: "0px"}, 300, toggleButtons);
				$("#tool_browser_icon_right").animate({marginRight: "54px"}, 300);
				break;
		}
		
		$.ajax({
			type: "POST",
			url: "./common/javascript/ajax/toolbar_memory.php",
			data: { state: 'show'},
			async: false
		})
		
		return false;
	}
});