$(function () 
{
	function typeChanged(evt, ui)
	{
		var value = $(this).attr('value');
		$("#dokeos_type").toggle();
		$("#server_type").toggle();
	}
	
	$(document).ready(function () 
	{
		$("#type").live('change', typeChanged);
		
		var value = $("#type").attr('value');
		if(value == 'server')
			$("#dokeos_type").toggle();
		else
			$("#server_type").toggle();
	});

});