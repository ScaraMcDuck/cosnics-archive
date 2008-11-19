$(document).ready(function()
{
	$("#menu_left").bind("click", toggleMenu);
	
	function toggleMenu()
	{
		if($("#menu_left").css("margin-left") == ("-14px"))
		{
			$("#menu_left").animate({marginLeft: "-18%"}, 300);
			$("#mainbox").css("width", "95%"); 
		}
		else
		{
			$("#menu_left").animate({marginLeft: "-14px"}, 300);
			$("#mainbox").css("width", "80%"); 
		}
		
		return false;
	}
	
});