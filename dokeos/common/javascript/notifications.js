( function($) {
	
	function hideMessages() {
	    $(".normal-message").animate({opacity: 1.0}, 15000,function(){
	    	$(".normal-message").fadeOut(500);
	        });
	    
	    $(".error-message").animate({opacity: 1.0}, 45000,function(){
	    	$(".error-message").fadeOut(500);
	        }); 
	}
	
	function addClosers() {
		var closeNormalHtml = '<div id="closeNormalMessage"></div>';
		var closeWarningHtml = '<div id="closeWarningMessage"></div>';
		var closeErrorHtml = '<div id="closeErrorMessage"></div>';
			
		$(".normal-message").append(closeNormalHtml);
		$(".normal-message").bind('mouseenter', function(e){$("#closeNormalMessage", this).fadeIn(150);});
		$(".normal-message").bind('mouseleave', function(e){$("#closeNormalMessage", this).fadeOut(150);});
		$("#closeNormalMessage").bind('click', function(e){$(".normal-message").fadeOut(500)});
		
		$(".warning-message").append(closeWarningHtml);
		$(".warning-message").bind('mouseenter', function(e){$("#closeWarningMessage", this).fadeIn(150);});
		$(".warning-message").bind('mouseleave', function(e){$("#closeWarningMessage", this).fadeOut(150);});
		$("#closeWarningMessage").bind('click', function(e){$(".warning-message").fadeOut(500)});
		
		$(".error-message").append(closeErrorHtml);
		$(".error-message").bind('mouseenter', function(e){$("#closeErrorMessage", this).fadeIn(150);});
		$(".error-message").bind('mouseleave', function(e){$("#closeErrorMessage", this).fadeOut(150);});
		$("#closeErrorMessage").bind('click', function(e){$(".error-message").fadeOut(500)});
	}

	$(document).ready( function() {
		addClosers();
		//hideMessages();
	});

})(jQuery);