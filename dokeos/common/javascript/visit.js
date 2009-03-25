/**
 * @author Michael Kyndt
 */
( function($)
{
	$(window).unload( function()
	{
        var response = $.ajax({
			type: "POST",
			url: "./user/ajax/leave.php",
			data: { tracker: tracker},
			async: false
		}).responseText;

        //alert(response);
        //alert('bla');
		//$(".charttype").bind('change',handle_charttype);
	});
})(jQuery);