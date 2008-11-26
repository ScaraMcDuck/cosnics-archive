( function($) 
{
	var toggleItem = function(e, ui) 
	{
		$("#feedbacklist").toggle();
	};
	
	function bindIcons() 
	{
		$("#showfeedback").unbind();
		$("#showfeedback").bind('click', toggleItem);
	}
	
	$(document).ready( function() 
	{
		$("#feedbacklist").toggle();
		$("#showfeedback").toggle();
		
		bindIcons();
		
	});
	
})(jQuery);