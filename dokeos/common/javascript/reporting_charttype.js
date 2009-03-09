( function($)
{
	var handle_charttype = function(ev, ui)
	{
		var parent = $(this).parent().parent();
		var block = parent.attr('id');
		var type = $(this).val();
		
		$.post("./reporting/ajax/reporting_change_charttype.php", 
	    {
	    	block:  block,
	    	type: type
	    },	function(data)
	    	{
	    		if(data.length > 0)
	    		{
                    var pare = $('.reporting_content', parent);
                    pare.html(data);
	    		}
	    	}
	    );
		
		return false;
	}
	
	$(document).ready( function()
	{
		$(".charttype").bind('change',handle_charttype);
	});
})(jQuery);