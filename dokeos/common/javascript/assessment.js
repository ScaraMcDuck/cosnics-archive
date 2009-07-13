var timer;

function handle_timer()
{
	var value = $('#start_time').val();
	value = parseInt(value);
	value++;
	$('#start_time').val(value);
	
	var max_time = $('#max_time').val();
	max_time = parseInt(max_time);
	
	if(value >= max_time)
	{
		alert(getTranslation('TimesUp', 'repository'));
		$(".finish").click();
	}
	else
	{
		timer = setTimeout('handle_timer()', 1000);
	}
}
	
( function($) 
{
	$(document).ready( function() 
	{
		handle_timer();
	});
	
})(jQuery);