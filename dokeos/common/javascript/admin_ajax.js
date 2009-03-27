/*global $, document, jQuery, window */

$(function () {
	
	// Extension to jQuery selectors which only returns visible elements
	$.extend($.expr[':'], {
	    visible: function (a) {
	        return $(a).css('display') !== 'none';
	    }
	});

	$(document).ready(function () {
		var elementHeight, dimensions, htmlHeight, windowHeight, extraSpace, scrollableHeight, newScrollableHeight, maxElements;
		
		$("#tabs").tabs();
		$('#tabs').tabs('paging', { cycle: false, follow: false, nextButton : "", prevButton : "" } );
		
		elementHeight = $("div.scrollable:first div.items div:first").outerHeight();
		
		dimensions = {width: 0, height: 0};
		
		if (window.innerWidth && window.innerHeight)
		{
			dimensions.width = window.innerWidth;
			dimensions.height = window.innerHeight;
		}
		else if (document.documentElement)
		{
			dimensions.width = document.documentElement.offsetWidth;
			dimensions.height = document.documentElement.offsetHeight;
		}

		htmlHeight = $("body").outerHeight();
		windowHeight = dimensions.height;
		extraSpace = windowHeight - htmlHeight;
		
		scrollableHeight = $("div.scrollable:first").height();
		scrollableHeight = scrollableHeight + extraSpace;
		
		newScrollableHeight = scrollableHeight - (scrollableHeight % elementHeight);
		maxElements = newScrollableHeight / elementHeight;
		
		$("div.tab").each(function (i) {
			var elementCount = $("div.scrollable:first div.items div.vertical_action", $(this)).size();
			
			if (elementCount > maxElements)
			{
				$("div.scrollable", $(this)).next().show();
				$("div.scrollable", $(this)).prev().show();
				$("div.scrollable", $(this)).height(newScrollableHeight);
	  			
				$("div.scrollable div.items", $(this)).height("20000em");
				$("div.scrollable div.items", $(this)).css("position", "absolute");
	  			
				$("div.scrollable", $(this)).scrollable({
					size : maxElements,
					clickable : false,
					vertical : true,
					hoverClass : "hover"
				});
			}
		});
	});

});