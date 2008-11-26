( function($) 
{
	var handle_drop = function(ev, ui) 
	{ 
	       //$(this).empty();
		var target = $(this).attr("id");
		var source = $(ui.draggable).attr("id");
		var course_code = $("#coursecode").html();
	    
	    $(ui.draggable).parent().remove();
	    
	    $.post("./run.php?go=courseviewer&course=" + course_code + "&tool=course_sections&application=weblcms&tool_action=change_section", 
	    {
			target : target,
			source : source
		},  function(data) 
			{
	    		//alert(data);
	    		$("#" + target + " > * > .description").empty();
	    		$("#" + target + " > * > .description").append(data);
	    		$(".tooldrag").css('display', 'inline');
	    	}
	    );
	} 

	$(document).ready( function() 
	{
		$(".toolblock").droppable(
		{	 
			accept: ".tool", 
			drop: handle_drop
		});
		
		$(".tooldrag").css('display', 'inline');
	});
	
})(jQuery);