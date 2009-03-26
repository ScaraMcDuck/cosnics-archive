/*global $, document, jQuery */

$(function () {
	
	// Extension to jQuery selectors which only returns visible elements
	$.extend($.expr[':'], {
	    visible: function (a) {
	        return $(a).css('display') !== 'none';
	    }
	});

	$(document).ready(function () {
		
		$("a.prev").show();
		$("a.next").show();
		
		$("#tabs").tabs();
		
//		//var currentTab = $("div.tab:visible");
//		
//		var elementWidth = $("div.scrollable:first div.items div:first").outerWidth();
//		var scrollableWidth = $("div.scrollable").outerWidth();
//		var scrollableSize = (scrollableWidth - (scrollableWidth % elementWidth)) / elementWidth;
//		var scrollableNewWidth = scrollableSize * elementWidth;
//		
//		$("div.tab").each(function (i) {
//			var elementCount = $("div.scrollable div.items div", $(this)).size();
//	    	  
//	  		if (elementCount > scrollableSize)
//			{
//				$("div.scrollable", $(this)).scrollable({
//					size : scrollableSize,
//					clickable : false
//				});
//			}
//			else
//			{
//				$("div.scrollable", $(this)).next().hide();
//				$("div.scrollable", $(this)).prev().hide();
//				$("div.scrollable", $(this)).width("100%");
//				
//			}
//	  	});
//		
//		$("div#tabs", $(this)).scrollable({
//			size : scrollableSize,
//			clickable : false,
//			items: "ul"
//		});
	});

});