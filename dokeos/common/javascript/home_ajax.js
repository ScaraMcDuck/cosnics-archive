(function($){

	var columns = $(".column");
	
	var widthBorder = 1;
	
	var widthColumns = [];
	
	var countColumns = 0;
	
//	var widthTotal = function(){
//		var total;
//	
//		$("div.column").each(function(i){
//			var widthRw = $(this).parent().width();		
//			var widthPx = $(this).width();
//			var widthPc = (widthPx / widthRw) * 100;
//			widthPc = parseInt(widthPc.toFixed(0));
//			
//			total = total + widthPc;
//		});
//		
//		total = total + countColumns - 1;
//	};
	
	var sortableStart = function(e, ui){
		ui.helper.css("width", ui.item.width());
		ui.helper.css("border", "4px solid #c0c0c0");
	};
	
	var sortableChange = function(e, ui){
		if(ui.sender){
			var w = ui.element.width();
			ui.placeholder.width(w);
			ui.helper.css("width",ui.element.children().width());
		}
	};

	var sortableUpdate = function(e, ui){
		var column = $(this).attr("id");
		var order = $(this).sortable("serialize");

		$.post(	"./home/ajax/block_sort.php",
				{ column: column, order: order }//,
				//function(data){alert("Data Loaded: " + data);}
		);
	};
	
	var resizableStop = function(e, ui) {
		var columnId = $(this).attr("id");
		var rowId = $(this).parent().attr("id");
		
		var widthBox = $(this).width();
		var widthRow = $(this).parent().width();
		var widthPercentage = (widthBox / widthRow) * 100;
		widthPercentage = widthPercentage.toFixed(0);
		
		var widthCurrentTotal = 0;
		
		$("#"+ rowId + " div.column").each(function(i){			
			var curWidthBox = $(this).width();
			var curWidthPercentage = (curWidthBox / widthRow) * 100;
			curWidthPercentage = parseInt(curWidthPercentage.toFixed(0));
			
			widthCurrentTotal = widthCurrentTotal + curWidthPercentage;
		});
		
		widthCurrentTotal = widthCurrentTotal + countColumns - 1;
		
		if (widthCurrentTotal > 100)
		{
			var widthSurplus = widthCurrentTotal - 100;
			
			widthPercentage = widthPercentage - widthSurplus;			
			widthBox = ((widthRow / 100) * widthPercentage) - 1;
		}
		
		$(this).css('width', widthPercentage + "%");
		
		$.post(	"./home/ajax/column_width.php",
				{ column: columnId, width: widthPercentage }//,
				//function(data){alert("Data Loaded: " + data);}
		);
	};
	
	var collapseItem = function(){
		$(this).parent().next(".description").slideToggle(300);
		
		// Make bottom border for title disappear ?
//		var border = $(this).parent().css("border-bottom-width");
//		var pos = border.search("px");
//		var borderValue = border.substring(0, pos);
//		
//		if (borderValue != 0)
//		{
//			widthBorder = borderValue;
//			$(this).parent().css("border-bottom-width", "0px");
//		}
//		else
//		{
//			$(this).parent().css("border-bottom-width", widthBorder + "px");
//		}

		var isVisible = $(this).children(".invisible").css('display');
		
		if (isVisible == 'block')
		{
			$(this).children(".invisible").css('display', 'none');
			$(this).children(".visible").css('display', 'block');
		}
		else
		{
			$(this).children(".invisible").css('display', 'block');
			$(this).children(".visible").css('display', 'none');
		}
		
		$.post(	"./home/ajax/block_visibility.php",
				{ block: $(this).parent().parent().attr("id")}//,
				//function(data){alert("Data Loaded: " + data);}
		);
	};
	
	var hoverInItem = function(){
		$(this).children("a:not(.closeEl)").children("img").fadeIn(150);
	};
	
	var hoverOutItem = function(){
		$(this).children("a:not(.closeEl)").children("img").fadeOut(150);
	};
	
	var deleteItem = function(){
		var confirmation = confirm('Are you sure ?');
		if (confirmation)
		{
			var column_id = $(this).parent().parent().parent().attr("id");
			
			$(this).parent().parent().remove();
			$.post(	"./home/ajax/block_delete.php",
					{ block: $(this).parent().parent().attr("id")}//,
					//function(data){alert("Data Loaded: " + data);}					
			);
			
			var order = $("#" + column_id).sortable("serialize");
			$.post(	"./home/ajax/block_sort.php",
					{ column: column_id, order: order }//,
					//function(data){alert("Data Loaded: " + data);}
			);
		}
	};
	
	$(document).ready(function(){
	
		countColumns  = $("div.column").length;

		$("a.closeEl").bind('click', collapseItem);
		$("a.closeEl").css('display', 'block');
		$("a.deleteEl").bind('click', deleteItem);
		
		$("div.title").bind('mouseenter', hoverInItem);
		$("div.title").bind('mouseleave', hoverOutItem);
	
		$("div.column").sortable({
			handle: 'div.title',
			cancel: '.closeEl',
			opacity: 0.8,
			cursor: 'move',
			helper: 'clone',
			placeholder: 'sortHelper',
			revert: true,
			scroll: true,
			connectWith: columns,
			start: sortableStart,
			change: sortableChange,
			update: sortableUpdate
		});
		
		$("div.column").resizable({ 
    		handles: "e",
    		transparent: true,
    		autoHide: true,
    		ghost: true,
    		preventDefault: true,
    		preserveCursor: true,
		    stop: resizableStop
		});

	});

})(jQuery);