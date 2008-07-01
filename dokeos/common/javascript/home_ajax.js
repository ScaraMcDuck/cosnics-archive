(function($){

	var columns = $(".column");
	
	var borderWidth = 1;
	
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
	
	var collapseItem = function(){
		$(this).parent().next(".description").slideToggle(300);
		
//		var border = $(this).parent().css("border-bottom-width");
//		var pos = border.search("px");
//		var borderValue = border.substring(0, pos);
//		
//		if (borderValue != 0)
//		{
//			borderWidth = borderValue;
//			$(this).parent().css("border-bottom-width", "0px");
//		}
//		else
//		{
//			$(this).parent().css("border-bottom-width", borderWidth + "px");
//		}
		
		$.post(	"./home/ajax/block_visibility.php",
				{ block: $(this).parent().parent().attr("id")}//,
				//function(data){alert("Data Loaded: " + data);}
		);
	};
	
	$(document).ready(function(){

		$("a.closeEl").bind('click', collapseItem);
		$("a.closeEl").css('display', "block");
	
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
			start: function(e,ui) {
				ui.helper.css("width", ui.item.width());
				ui.helper.css("border", "4px solid #c0c0c0");
			},
			change: sortableChange,
			update: sortableUpdate
		});

	});

})(jQuery);