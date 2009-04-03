/*global $, document, jQuery, window */

$(function () {
	
	var windowHeight = getWindowHeight(), resizeTimer = null;
	
	function applyScroll()
	{
		var elementHeight, dimensions, htmlHeight = null, theWindowHeight = null, extraSpace = null, scrollableHeight = null, newScrollableHeight = null, maxElements = null;
		
		var self = this, initialized = false, currentPage;
		
		elementHeight = $("div.tab:visible div.vertical_action:visible").outerHeight();
		
		//alert("elementHeight " + elementHeight);

		htmlHeight = $("body").outerHeight();
		theWindowHeight = getWindowHeight();
		//alert("windowHeight " + theWindowHeight);
		extraSpace = theWindowHeight - htmlHeight;
		//alert("extraSpace " + extraSpace);
		
		scrollableHeight = $("div.tab:visible div.scrollable").height();
		//alert("scrollableHeight " + scrollableHeight);
		scrollableHeight = scrollableHeight + extraSpace;
		
		//alert("scrollableHeight " + scrollableHeight);
		
		newScrollableHeight = scrollableHeight - (scrollableHeight % elementHeight);
		maxElements = newScrollableHeight / elementHeight;
		
		//alert(maxElements);
		
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
			else
			{
				var nonScrollHeight = elementCount * elementHeight;
				
				$("div.scrollable", $(this)).next().hide();
				$("div.scrollable", $(this)).prev().hide();
				$("div.scrollable", $(this)).height(nonScrollHeight);
				
				$("div.scrollable div.items", $(this)).height("");
				$("div.scrollable div.items", $(this)).css("position", "static");
			}
		});
		
		placeFooter();
		$(window).bind('resize', handleResize);
	}
	
	function handleResize() {
		var currentHeight = getWindowHeight();
		
		if (resizeTimer)
		{
			clearTimeout(resizeTimer);
		}
		
		if (windowHeight != currentHeight)
		{
			resizeTimer = setTimeout(reinit, 100);
		}
	}
	
	function getWindowHeight()
	{
		if (window.innerHeight)
		{
			return window.innerHeight;
		}
		else if (document.documentElement)
		{
			return document.documentElement.offsetHeight;
		}
	}
	
	function reinit() {	
		windowHeight = getWindowHeight();
		destroy();
		applyScroll();
	}
	
	function destroy() {
		$(window).unbind('resize', handleResize);
	}
	
	// Extension to jQuery selectors which only returns visible elements
	$.extend($.expr[':'], {
	    visible: function (a) {
	        return $(a).css('display') !== 'none';
	    }
	});
	
	function placeFooter()
	{
		htmlHeight = $("body").outerHeight();
		
		if (htmlHeight > windowHeight)
		{
			$("#footer").css("position", "static");
			$("#footer").css("bottom", "");
			$("#footer").css("left", "");
			$("#footer").css("right", "");
			
			$("#main").css("margin-bottom", '0px;');
		}
		else
		{
			$("#footer").css("position", "fixed");
			$("#footer").css("bottom", "0px");
			$("#footer").css("left", "0px");
			$("#footer").css("right", "0px");
			
			$("#main").css("margin-bottom", '30px;');
		}
	}

	$(document).ready(function () {
		
		$("#tabs").tabs();
		$('#tabs').tabs('paging', { cycle: false, follow: false, nextButton : "", prevButton : "" } );
		
		applyScroll();
		placeFooter();
	});

});