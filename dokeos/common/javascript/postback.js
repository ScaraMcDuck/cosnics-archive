( function($) 
{
	var handle_postback = function() 
	{ 
	   var form = $(this).closest("form");
	   $("button:submit", form).click();
	} 

	$(document).ready( function() 
	{
		$(".postback").change(handle_postback);
		$(".postback").each(function(i){
				 var form = $(this).closest("form");
				 $("button:submit", form).hide();
		});

	});
	
})(jQuery);