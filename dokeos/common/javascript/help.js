( function($) 
{
	var handle_help = function(ev, ui) 
	{ 
	    var href = $(this).attr("href");
	   
	    var loadingHTML  = '<iframe style="width: 100%; height:100%;" src="' + href + '" frameborder="0">';
	    loadingHTML += '</iframe>';
	    
	    var box = $("#helpbox");
	    box.empty();
	    box.append(loadingHTML);
	   
	    /*$.modal(loadingHTML, {
			overlayId: 'modalOverlay',
		  	containerId: 'modalContainer',
		  	opacity: 75
		});*/
		
		box.dialog({
			width: '800px',
			height: '600px',
			title: 'Help'
		});
		
		return false;
	} 

	$(document).ready( function() 
	{
		$(".help").bind('click', handle_help);
	});
	
})(jQuery);