( function($) 
{
	var handle_postback = function() 
	{ 
	   var form = $(this).closest("form"); alert(form.attr("id"));
	   form.submit();
	} 

	$(document).ready( function() 
	{
		$(".postback").change(handle_postback);

	});
	
})(jQuery);