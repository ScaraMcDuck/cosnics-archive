( function($) {

	var columns = $(".column");

	var countColumns = 0;

	var sortableStart = function(e, ui) {
		ui.helper.css("width", ui.item.width());
		ui.helper.css("border", "4px solid #c0c0c0");
	};
	
	var sortableStop = function(e, ui) {
		$("div.title a").fadeOut(150);
	};
	
	function translation(string, application) {
		
		var translated_string = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/translation.php",
			data: { string: string, application: application },
			async: false
		}).responseText;
		
		return translated_string;
	};

	var sortableChange = function(e, ui) {
		if (ui.sender) {
			var w = ui.element.width();
			ui.placeholder.width(w);
			ui.helper.css("width", ui.element.children().width());
		}
	};

	var sortableUpdate = function(e, ui) {
		var column = $(this).attr("id");
		var order = $(this).sortable("serialize");

		$.post("./home/ajax/block_sort.php", {
			column :column,
			order :order
		}// ,
				// function(data){alert("Data Loaded: " + data);}
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

		$("#" + rowId + " div.column").each( function(i) {
			var curWidthBox = $(this).width();
			var curWidthPercentage = (curWidthBox / widthRow) * 100;
			curWidthPercentage = parseInt(curWidthPercentage.toFixed(0));

			widthCurrentTotal = widthCurrentTotal + curWidthPercentage;
		});

		widthCurrentTotal = widthCurrentTotal + countColumns - 1;

		if (widthCurrentTotal > 100) {
			var widthSurplus = widthCurrentTotal - 100;

			widthPercentage = widthPercentage - widthSurplus;
			widthBox = ((widthRow / 100) * widthPercentage) - 1;
		}

		$(this).css('width', widthPercentage + "%");

		$.post("./home/ajax/column_width.php", {
			column :columnId,
			width :widthPercentage
		}// ,
				// function(data){alert("Data Loaded: " + data);}
				);
	};

	var collapseItem = function(e) {
		e.preventDefault();
		$(this).parent().next(".description").slideToggle(300);

		$(this).children(".invisible").toggle();
		$(this).children(".visible").toggle();

		$.post("./home/ajax/block_visibility.php", {
			block :$(this).parent().parent().attr("id")
		}// ,
				// function(data){alert("Data Loaded: " + data);}
				);
	};

	var hoverInItem = function() {
		$(this).children("a").fadeIn(150);
	};

	var hoverOutItem = function() {
		$(this).children("a").fadeOut(150);
	};

	var deleteItem = function(e) {
		e.preventDefault();
		var confirmation = confirm('Are you sure ?');
		if (confirmation) {
			var columnId = $(this).parent().parent().parent().attr("id");

			$(this).parent().parent().remove();
			$.post("./home/ajax/block_delete.php", {
				block :$(this).parent().parent().attr("id")
			}// ,
					// function(data){alert("Data Loaded: " + data);}
					);

			var order = $("#" + columnId).sortable("serialize");
			$.post("./home/ajax/block_sort.php", {
				column :columnId,
				order :order
			}// ,
					// function(data){alert("Data Loaded: " + data);}
					);
		}
	};

	var removeBlockScreen = function(e, ui) {
		$("#main #addBlock").slideToggle(300, function() {
			$("#main #addBlock").remove();
		});

		$("a.addEl").show();
	};

	var showBlockScreen = function(e, ui) {
		e.preventDefault();
		$.post("./home/ajax/block_list.php", function(data) {
			$("#main").prepend(data)
			{
				$("#main #addBlock").slideToggle(300);
			}

			$("a.addEl").hide();
			$("a.closeScreen").bind('click', removeBlockScreen);
			$(".component").bind('click', addBlock);
			$(".component").css('cursor', 'pointer');
			
			$("#applications .application").bind('click', filterComponents);
			$("#applications #show_all").bind('click', showAllComponents);
		});

	};

	var addBlock = function(e, ui) {

		var column = $(".column:first-child");
		var columnId = column.attr("id");
		var order = column.sortable("serialize");

		$.post("./home/ajax/block_add.php", {
			component :$(this).attr("id"),
			column :columnId,
			order :order
		}, function(data) {
			column.prepend(data);
			$("div.title a").css('display', 'none');
			order = column.sortable("serialize");

			bindIcons();

			$.post("./home/ajax/block_sort.php", {
				column :columnId,
				order :order
				},
					function(data)
					{
						
					}
					);
		});
	};
	
	var filterComponents = function(e, ui) {
		var applicationId = $(this).attr("id");

		$("#components #components_" + applicationId).show();
		$("#components").children(":not(#components_" + applicationId + ")").hide();
	};
	
	var showAllComponents = function(e, ui) {
		$("#components").children().show();
	};

	function bindIcons() {
		$("a.closeEl").unbind();
		$("a.closeEl").bind('click', collapseItem);
		$("a.deleteEl").unbind();
		$("a.deleteEl").bind('click', deleteItem);

		$("div.title").unbind();
		$("div.title").bind('mouseenter', hoverInItem);
		$("div.title").bind('mouseleave', hoverOutItem);

		$("a.addEl").unbind();
		$("a.addEl").bind('click', showBlockScreen);
	}
	
	function testModal()
	{
		var a = '<a id="closeModal" href="javascript:void(0)">Sluiten</a>';
		$.modal('<div class="normal-message" style="width: 450px">' + translation('BlockAdded', 'home') + '</div>' + a);
		$("#closeModal").bind('click', function(e, ui) 
		{
			$.modal.close();
		});
	}

	$(document).ready( function() {
		
		$("a.addEl").toggle();
		
		countColumns = $("div.column").length;

		$("div.title a").toggle();
		bindIcons();
		
		//testModal();

		$("div.column").sortable( {
			handle :'div.title',
			cancel :'a',
			opacity :0.8,
			cursor :'move',
			helper :'clone',
			placeholder :'sortHelper',
			revert :true,
			scroll :true,
			connectWith :columns,
			start :sortableStart,
			stop :sortableStop,
			change :sortableChange,
			update :sortableUpdate
		});

		$("div.column").resizable( {
			handles :"e",
			transparent :true,
			autoHide :true,
			ghost :true,
			preventDefault :true,
			preserveCursor :true,
			stop :resizableStop
		});

	});

})(jQuery);