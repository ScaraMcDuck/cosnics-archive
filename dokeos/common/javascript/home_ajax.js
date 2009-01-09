( function($) {

	var columns = $(".column");

	function sortableStart (e, ui) {
		ui.helper.css("width", ui.item.width());
		ui.helper.css("border", "4px solid #c0c0c0");
	};
	
	function sortableStop(e, ui) {
		$("div.title a").fadeOut(150);
	};
	
	function showTab(e, ui) {
		e.preventDefault();
		var tabId = $(this).attr('id');
		var tab = tabId.split("_");
		
		$("div.tab:not(#tab_"+ tab[2] +")").css('display', 'none');
		$("div #tab_" + tab[2]).css('display', 'block');
		
		$("#tab_menu li").attr('class', 'normal');
		$("#tab_select_"+ tab[2]).attr('class', 'current');
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

	function sortableChange (e, ui) {
		if (ui.sender) {
			var w = ui.element.width();
			ui.placeholder.width(w);
			ui.helper.css("width", ui.element.children().width());
		}
	};

	function sortableUpdate (e, ui) {
		var column = $(this).attr("id");
		var order = $(this).sortable("serialize");

		$.post("./home/ajax/block_sort.php", {
			column :column,
			order :order
		}// ,
				// function(data){alert("Data Loaded: " + data);}
				);
	};
	
	function tabsSortableUpdate (e, ui) {
		var order = $(this).sortable("serialize");
		
		$.post("./home/ajax/tab_sort.php", {
			order :order
		} //,
				 //function(data){alert("Data Loaded: " + data);}
				);
	};

	function resizableStop (e, ui) {
		var columnId = $(this).attr("id");
		var rowId = $(this).parent().attr("id");
		var countColumns = $("div.column" ,$(this).parent()).length;

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

	function collapseItem (e) {
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

	function hoverInItem () {
		$(this).children("a").fadeIn(150);
	};

	function hoverOutItem () {
		$(this).children("a").fadeOut(150);
	};

	function deleteItem (e) {
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

	function removeBlockScreen (e, ui) {
		$("#addBlock").slideToggle(300, function() {
			$("#addBlock").remove();
		});

		$("a.addEl").show();
	};

	function showBlockScreen (e, ui) {
		e.preventDefault();
		$.post("./home/ajax/block_list.php", function(data) {
			$("#tab_menu").after(data)
			{
				$("#addBlock").slideToggle(300);
			}

			$("a.addEl").hide();
			$("a.closeScreen").bind('click', removeBlockScreen);
			$(".component").bind('click', addBlock);
			$(".component").css('cursor', 'pointer');
			
			$("#applications .application").bind('click', filterComponents);
			$("#applications #show_all").bind('click', showAllComponents);
		});

	};

	function addBlock (e, ui) {
		var column = $(".tab:visible .column:first-child");
		var columnId = column.attr("id");
		var order = column.sortable("serialize");
		
		var loadingHTML  = '<div class="loadingBox">';
			loadingHTML += '<div class="loadingHuge" style="margin-bottom: 15px;">';
			loadingHTML += '</div>';
			loadingHTML += '<div>';
			loadingHTML += '<h3>' + translation('YourBlockIsBeingAdded', 'home') + '</h3>';
			loadingHTML += '</div>';
			loadingHTML += '</div>';
		
		var loading = $.modal(loadingHTML, {
			overlayId: 'homeOverlay',
		  	containerId: 'homeContainer',
		  	opacity: 75,
		  	close: false
			});

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
						var successMessage  = '<div class="statusConfirmation" style="margin-bottom: 15px;">';
							successMessage += '</div>';
							successMessage += '<div>';
							successMessage += '<h3>' + translation('BlockAdded', 'home') + '</h3>';
							successMessage += '</div>';
						
						$(".loadingBox", loading.dialog.container).html(successMessage);
						loading.dialog.container.append($(loading.opts.closeHTML).addClass(loading.opts.closeClass));
						loading.bindEvents();
					}
					);
		});
	};
	
	function filterComponents (e, ui) {
		var applicationId = $(this).attr("id");

		$("#components #components_" + applicationId).show();
		$("#components").children(":not(#components_" + applicationId + ")").hide();
	};
	
	function showAllComponents (e, ui) {
		$("#components").children().show();
	};
	
	function addTab (e, ui) {
		e.preventDefault();
		
		var loadingHTML  = '<div class="loadingBox">';
			loadingHTML += '<div class="loadingHuge" style="margin-bottom: 15px;">';
			loadingHTML += '</div>';
			loadingHTML += '<div>';
			loadingHTML += '<h3>' + translation('YourTabIsBeingAdded', 'home') + '</h3>';
			loadingHTML += '</div>';
			loadingHTML += '</div>';
	
		var loading = $.modal(loadingHTML, {
			overlayId: 'homeOverlay',
		  	containerId: 'homeContainer',
		  	opacity: 75,
		  	close: false
			});
		
		$.post("./home/ajax/tab_add.php", {}, function(data) {
			$("#main .tab:last").after(data);
			var tabId = $("#main .tab:last").attr("id");
			var id = tabId.split("_");
			var tabSelectId = 'tab_select_' + id[1];
			$("#tab_menu ul").append("<li class=\"normal\" id=\""+ tabSelectId +"\"><strong>"+ translation('NewTab', 'home') +"</strong></li>");
			bindIcons();
			tabsSortable();
			columnsSortable();
			columnsResizable();
			
			var successMessage  = '<div class="statusConfirmation" style="margin-bottom: 15px;">';
			successMessage += '</div>';
			successMessage += '<div>';
			successMessage += '<h3>' + translation('TabAdded', 'home') + '</h3>';
			successMessage += '</div>';
		
			$(".loadingBox", loading.dialog.container).html(successMessage);
			loading.dialog.container.append($(loading.opts.closeHTML).addClass(loading.opts.closeClass));
			loading.bindEvents();
		});
	};
	
	function addColumn (e, ui) {
		e.preventDefault();
		var row = $(".tab:visible .row:first");
		var rowId = row.attr('id');
		
		var loadingHTML  = '<div class="loadingBox">';
			loadingHTML += '<div class="loadingHuge" style="margin-bottom: 15px;">';
			loadingHTML += '</div>';
			loadingHTML += '<div>';
			loadingHTML += '<h3>' + translation('YourColumnIsBeingAdded', 'home') + '</h3>';
			loadingHTML += '</div>';
			loadingHTML += '</div>';

		var loading = $.modal(loadingHTML, {
			overlayId: 'homeOverlay',
		  	containerId: 'homeContainer',
		  	opacity: 75,
		  	close: false
			});
		
		$.post("./home/ajax/column_add.php", {row: rowId}, function(data) {
			var columnHtml = data.html;
			var newWidths = data.width;
			
			
			
			var lastColumn = $("div.column:last", row);
			lastColumn.css('margin-right', '1%');
			
			$("div.column", row).each(function (i) {
				var newWidth = newWidths[this.id] + '%'; 
				this.style.width = newWidth;
			});
			
			$("div.column:last", row).after(columnHtml);
			
			bindIcons();
			columnsSortable();
			columnsResizable();
			
			var successMessage  = '<div class="statusConfirmation" style="margin-bottom: 15px;">';
			successMessage += '</div>';
			successMessage += '<div>';
			successMessage += '<h3>' + data.message + '</h3>';
			successMessage += '</div>';
		
			$(".loadingBox", loading.dialog.container).html(successMessage);
			loading.dialog.container.append($(loading.opts.closeHTML).addClass(loading.opts.closeClass));
			loading.bindEvents();
			$.timeout(function() { 
				loading.close();
				}, 5000);
		}, "json");
	};

	function bindIcons() {
		$("div.title a").hide();
		$("a.closeEl").unbind();
		$("a.closeEl").bind('click', collapseItem);
		$("a.deleteEl").unbind();
		$("a.deleteEl").bind('click', deleteItem);

		$("div.title").unbind();
		$("div.title").bind('mouseenter', hoverInItem);
		$("div.title").bind('mouseleave', hoverOutItem);

		$("a.addEl").unbind();
		$("a.addEl").bind('click', showBlockScreen);
		
		$("#tab_menu li").unbind();
		$("#tab_menu li").bind('click', showTab);
		
		$("a.addTab").unbind();
		$("a.addTab").bind('click', addTab);
		
		$("a.addColumn").unbind();
		$("a.addColumn").bind('click', addColumn);
	}
	
	function columnsSortable() {
		$("div.column").sortable("destroy");
		$("div.column").sortable( {
			handle :'div.title',
			cancel :'a',
			opacity :0.8,
			cursor :'move',
			helper :'clone',
			placeholder :'blockSortHelper',
			revert :true,
			scroll :true,
			connectWith :columns,
			start :sortableStart,
			stop :sortableStop,
			change :sortableChange,
			update :sortableUpdate
		});
	}
	
	function tabsSortable() {
		$("#tab_menu #tab_elements").sortable("destroy");
		$("#tab_menu #tab_elements").sortable( {
			opacity :0.8,
			cursor :'move',
			helper :'clone',
			placeholder :'tabSortHelper',
			revert :true,
			scroll :true,
			start :sortableStart,
			//change :sortableChange,
			update :tabsSortableUpdate
		});
	}
	
	function columnsResizable() {
		$("div.column").resizable("destroy");
		$("div.column").resizable( {
			handles :"e",
			transparent :true,
			autoHide :true,
			ghost :true,
			preventDefault :true,
			preserveCursor :true,
			stop :resizableStop
		});
	}

	// Extension to jQuery selectors which only returns visible elements
	$.extend($.expr[':'],{
	    visible: function(a) {
	        return $(a).css('display') !== 'none';
	    }
	});

	$(document).ready( function() {
		
		$("a.addEl").toggle();

		bindIcons();
		
//		$(".moveEl").draggable({helper: function(e, ui) {
//			var clone = $(this).parent().clone();
//			clone.css('opacity', '0.5');
//			return clone;
//		}});
//		$("#tab_menu ul li").droppable({
//			activeClass: 'droppable-active',
//			hoverClass: 'droppable-hover',
//			drop: function(ev, ui) {
//				alert('Woohoo !');
//				//$(this).append("<br>Dropped!");
//			}
//		});
		
		tabsSortable();
		
		columnsSortable();
		columnsResizable();

	});

})(jQuery);