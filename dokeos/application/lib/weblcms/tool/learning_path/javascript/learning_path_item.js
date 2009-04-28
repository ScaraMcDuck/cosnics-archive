/**
 * @author Sven Vanpoucke
 */
( function($)
{
	$(window).bind('beforeunload', function(e)
	{
        var response = $.ajax({
			type: "POST",
			url: "./application/lib/weblcms/tool/learning_path/javascript/ajax/leave_item.php",
			data: { tracker_id: tracker_id},
			async: false
		}).responseText;

        //alert(response);
        alert('bla');
		//$(".charttype").bind('change',handle_charttype);
	});
})(jQuery);