( function($) 
{
	var toggleList = function(e, ui) 
	{
		$("#feedbacklist").toggle();
	};
	
	var toggleForm = function(e, ui) 
	{
		$("#feedbackform").toggle();
	};
	
	function bindIcons() 
	{
		$("#showfeedback").unbind();
		$("#showfeedback").bind('click', toggleList);
		
		$("#showfeedbackform").unbind();
		$("#showfeedbackform").bind('click', toggleForm);
	}
	
	$(document).ready( function() 
	{
		$("#feedbacklist").toggle();
		$("#showfeedback").toggle();
		
		$("#feedbackform").toggle();
		$("#showfeedbackform").toggle();
		
		bindIcons();
		
	});
	
})(jQuery);