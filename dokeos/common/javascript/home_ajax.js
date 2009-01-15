(function ($) {

	/*global addBlock, bindIcons, columnsResizable, columnsSortable, confirm, document, editTab, filterComponents, getLoadingBox, getMessageBox, handleLoadingBox, jQuery, showAllComponents, tabsSortable */
	
	var columns = $(".column");
	
	function translation(string, application) {		
		var translated_string = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/translation.php",
			data: { string: string, application: application },
			async: false
		}).responseText;
		
		return translated_string;
	}
	
	function checkForEmptyColumns() {
		var emptyBlock  = '<div class="empty_column">';
		emptyBlock += translation('EmptyColumnText', 'home');
		emptyBlock += '<div class="deleteColumn"></div>';
		emptyBlock += '</div>';
		
		$("div.tab div.column").each(function (i) {
			var numberOfBlocks = $(".block", this).length;
			var emptyBlockExists = $(".empty_column", this).length;
			
			if (numberOfBlocks === 0 && emptyBlockExists === 0)
			{
				$(this).append(emptyBlock);
			}
			else if (numberOfBlocks > 0 && emptyBlockExists >= 1)
			{
				$(".empty_column", this).remove();
			}
		});
		
		bindIcons();
	}

	function sortableStart(e, ui) {
		ui.helper.css("width", ui.item.width());
		ui.helper.css("border", "4px solid #c0c0c0");
	}
	
	function sortableStop(e, ui) {
		// Fade the action links / images
		$("div.title a").fadeOut(150);
		checkForEmptyColumns();
	}
	
	function showTab(e, ui) {
		e.preventDefault();
		var tabId = $(this).attr('id');
		var tab = tabId.split("_");
		
		$("div.tab:not(#tab_" + tab[2] + ")").css('display', 'none');
		$("div #tab_" + tab[2]).css('display', 'block');
		
		$("#tab_menu li").attr('class', 'normal');
		$("#tab_select_" + tab[2]).attr('class', 'current');
		
		$("li.current a.deleteTab").css('display', 'inline');
		$("li.normal a.deleteTab").css('display', 'none');
		
		$("#tab_menu li").unbind();
		$("#tab_menu li:not(.current)").bind('click', showTab);
		$("#tab_menu li.current").bind('click', editTab);
	}

	function sortableChange(e, ui) {
		if (ui.sender) {
			var w = ui.element.width();
			ui.placeholder.width(w);
			ui.helper.css("width", ui.element.children().width());
		}
	}

	function sortableUpdate(e, ui) {
		var column = $(this).attr("id");
		var order = $(this).sortable("serialize");

		$.post("./home/ajax/block_sort.php", {
			column : column,
			order : order
		}// ,
				// function(data){alert("Data Loaded: " + data);}
				);
	}
	
	function tabsSortableUpdate(e, ui) {
		var order = $(this).sortable("serialize");
		
		$.post("./home/ajax/tab_sort.php", {
			order : order
		} //,
				 //function(data){alert("Data Loaded: " + data);}
				);
	}

	function resizableStop(e, ui) {
		var columnId = $(this).attr("id");
		var rowId = $(this).parent().attr("id");
		var countColumns = $("div.column" , $(this).parent()).length;

		var widthBox = $(this).width();
		var widthRow = $(this).parent().width();
		var widthPercentage = (widthBox / widthRow) * 100;
		widthPercentage = widthPercentage.toFixed(0);

		var widthCurrentTotal = 0;

		$("#" + rowId + " div.column").each(function (i) {
			var curWidthBox = $(this).width();
			var curWidthPercentage = (curWidthBox / widthRow) * 100;
			curWidthPercentage = parseInt(curWidthPercentage.toFixed(0), 10);

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
			column : columnId,
			width : widthPercentage
		}// ,
				// function(data){alert("Data Loaded: " + data);}
				);
	}

	function collapseItem(e) {
		e.preventDefault();
		$(this).parent().next(".description").slideToggle(300);

		$(this).children(".invisible").toggle();
		$(this).children(".visible").toggle();

		$.post("./home/ajax/block_visibility.php", {
			block : $(this).parent().parent().attr("id")
		}// ,
				// function(data){alert("Data Loaded: " + data);}
				);
	}

	function hoverInItem() {
		$(this).children("a").fadeIn(150);
	}

	function hoverOutItem() {
		$(this).children("a").fadeOut(150);
	}

	function deleteItem(e) {
		e.preventDefault();
		var confirmation = confirm('Are you sure ?');
		if (confirmation) {
			var columnId = $(this).parent().parent().parent().attr("id");

			$(this).parent().parent().remove();
			$.post("./home/ajax/block_delete.php", {
				block : $(this).parent().parent().attr("id")
			}// ,
					// function(data){alert("Data Loaded: " + data);}
					);

			var order = $("#" + columnId).sortable("serialize");
			$.post("./home/ajax/block_sort.php", {
				column : columnId,
				order : order
			}// ,
					// function(data){alert("Data Loaded: " + data);}
					);
		}
		
		checkForEmptyColumns();
	}

	function removeBlockScreen(e, ui) {
		$("#addBlock").slideToggle(300, function () {
			$("#addBlock").remove();
		});

		$("a.addEl").show();
	}

	function showBlockScreen(e, ui) {
		e.preventDefault();
		$.post("./home/ajax/block_list.php", function (data) {
			$("#tab_menu").after(data);
			$("#addBlock").slideToggle(300);

			$("a.addEl").hide();
			$("a.closeScreen").bind('click', removeBlockScreen);
			$(".component").bind('click', addBlock);
			$(".component").css('cursor', 'pointer');
			
			$("#applications .application").bind('click', filterComponents);
			$("#applications #show_all").bind('click', showAllComponents);
		});
	}

	function addBlock(e, ui) {
		var column = $(".tab:visible .column:first-child");
		var columnId = column.attr("id");
		var order = column.sortable("serialize");
		
		var loadingMessage = 'YourBlockIsBeingAdded';

		var loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId : 'homeOverlay',
			containerId : 'homeContainer',
			opacity: 75,
			close: false
		});

		$.post("./home/ajax/block_add.php", {
			component : $(this).attr("id"),
			column : columnId,
			order : order
		}, function (data) {
			column.prepend(data);
			$("div.title a").css('display', 'none');
			order = column.sortable("serialize");

			bindIcons();
			blocksDraggable();

			$.post("./home/ajax/block_sort.php", {
				column : columnId,
				order : order
			},
			function (data)
			{
				$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
				handleLoadingBox(loading);
			}, "json");
		});
	}
	
	function filterComponents(e, ui) {
		var applicationId = $(this).attr("id");

		$("#components #components_" + applicationId).show();
		$("#components").children(":not(#components_" + applicationId + ")").hide();
	}
	
	function showAllComponents(e, ui) {
		$("#components").children().show();
	}
	
	function addTab(e, ui) {
		e.preventDefault();
			
		var loadingMessage = 'YourTabIsBeingAdded';

		var loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId : 'homeOverlay',
			containerId : 'homeContainer',
			opacity : 75,
			close: false
		});
		
		$.post("./home/ajax/tab_add.php", {}, function (data) {
			$("#main .tab:last").after(data.html);
			$("#tab_menu ul").append(data.title);
			bindIcons();
			tabsSortable();
			columnsSortable();
			columnsResizable();
			tabsDroppable();
			
			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
			handleLoadingBox(loading);
		}, "json");
	}
	
	function addColumn(e, ui) {
		e.preventDefault();
		var row = $(".tab:visible .row:first");
		var rowId = row.attr('id');
		
		var loadingMessage = 'YourColumnIsBeingAdded';

		var loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId: 'homeOverlay',
			containerId: 'homeContainer',
			opacity: 75,
			close: false
		});
		
		$.post("./home/ajax/column_add.php", {row: rowId}, function (data) {
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
			
			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
			handleLoadingBox(loading);
		}, "json");
	}
	
	function getMessageBox(isError, message)
	{
		var messageClass;
		
		if (isError === '0')
		{
			messageClass = 'statusError';
		}
		else
		{
			messageClass = 'statusConfirmation';
		}
		
		var successMessage  = '<div class="' + messageClass + '" style="margin-bottom: 15px;">';
		successMessage += '</div>';
		successMessage += '<div>';
		successMessage += '<h3>' + message + '</h3>';
		successMessage += '</div>';
			
		return successMessage;
	}
	
	function getLoadingBox(message)
	{
		var loadingHTML  = '<div class="loadingBox">';
		loadingHTML += '<div class="loadingHuge" style="margin-bottom: 15px;">';
		loadingHTML += '</div>';
		loadingHTML += '<div>';
		loadingHTML += '<h3>' + translation(message, 'home') + '</h3>';
		loadingHTML += '</div>';
		loadingHTML += '</div>';
			
		return loadingHTML;
	}
	
	function handleLoadingBox(loading)
	{
		loading.dialog.container.append($(loading.opts.closeHTML).addClass(loading.opts.closeClass));
		loading.bindEvents();
		$.timeout(function () { 
			loading.close();
		}, 3000);
	}
	
	function deleteTab(e, ui)
	{
		e.preventDefault();
		var tab = $(this).parent().attr('id');
		tab = tab.split("_");
		
		var tabId = tab[2];
		
		var loadingMessage = 'YourTabIsBeingDeleted';
	
		var loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId: 'homeOverlay',
			containerId: 'homeContainer',
			opacity: 75,
			close: false
		});
		
		$.post("./home/ajax/tab_delete.php", {tab: tabId}, function (data) {
			if (data.success === '1')
			{
				$('#tab_' + tabId).remove();
				$('#tab_select_' + tabId).remove();
				
				// Show the first existing tab			
				$("#tab_menu ul li:first").attr('class', 'current');
				var newTabId = $("#tab_menu ul li:first").attr('id');
				newTabId = newTabId.split("_");
				newTabId = newTabId[2];
				$("#tab_" + newTabId).css('display', 'block');
				
				$("li.current a.deleteTab").css('display', 'inline');
				$("li.normal a.deleteTab").css('display', 'none');
				
				$("#tab_menu li").unbind();
				$("#tab_menu li:not(.current)").bind('click', showTab);
				$("#tab_menu li.current").bind('click', editTab);
			}
			
			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
			handleLoadingBox(loading);
		}, "json");
	}
	
	function saveTabTitle(e)
	{
		e.preventDefault();
		var tab = e.data.tab.parent().attr('id');
		tab = tab.split("_");
		
		var tabId = tab[2];
		var newTitle = $('#tabTitle').attr('value');
		
		$.post("./home/ajax/tab_edit.php", {tab: tabId, title: newTitle}, function (data) {
			if (data.success === '1')
			{
				e.data.tab.html(newTitle);
				e.data.loading.close();
				
				$('#tabSave').unbind();
				$('#tabTitle').unbind();
			}
		}, "json");
	}
	
	function editTab(e, ui)
	{
		e.preventDefault();
		
		var editTabHTML  = '<div id="editTab"><h3>Edit tab name</h3>';
		editTabHTML += '<input id="tabTitle" type="text" value="' + $('.tabTitle', this).html() + '"/>&nbsp;';
		editTabHTML += '<input id="tabSave" type="submit" class="button" value="' + translation('Save') + '"/>';
		editTabHTML += '</div>';
		
		var loading = $.modal(editTabHTML, {
			overlayId : 'homeOverlay',
			containerId : 'homeEditContainer',
			opacity: 75
		});
		
		$("#tabTitle").bind('keypress', {loading : loading, tab: $('.tabTitle', this)}, function (e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			// If ENTER is pressed we save the new tab title
			if (code === 13) {
				saveTabTitle(e);
			}
			else if (code === 27)
			{
				loading.close();
				$('#tabSave').unbind();
				$('#tabTitle').unbind();
			}
		});
		
		$('#tabSave').bind('click', {loading: loading, tab: $('.tabTitle', this)}, saveTabTitle);
	}
	
	function deleteColumn(e, ui) {
		var column = $(this).parent().parent();
		var columnId = column.attr("id").split("_");
		columnId = columnId[1];
		
		var loadingMessage = 'YourColumnIsBeingDeleted';
		
		var loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId: 'homeOverlay',
			containerId: 'homeContainer',
			opacity: 75,
			close: false
		});
		
		$.post("./home/ajax/column_delete.php", {column: columnId}, function (data) {
			if (data.success === '1')
			{
				// Get the deleted column's width
				var columnWidth = column.css('width');
				columnWidth = parseInt(columnWidth.replace('%', ''), 10);
				column.remove();
				
				// Get the last column's width 
				var otherColumn = $(".tab:visible .column:last");
				var otherColumnWidth = otherColumn.css('width');
				otherColumnWidth = parseInt(otherColumnWidth.replace('%', ''), 10);
				
				// Calculate the new width
				var newColumnWidth =  columnWidth + otherColumnWidth + 1;
				
				// Set the new width + postback
				otherColumn.css('margin-right', '0px');
				otherColumn.css('width', newColumnWidth + '%');
				
				$.post("./home/ajax/column_width.php", {
					column : otherColumn.attr('id'),
					width : newColumnWidth
				}// ,
						// function(data){alert("Data Loaded: " + data);}
						);
			}
			
			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
			handleLoadingBox(loading);
		}, "json");
	}

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
		$("#tab_menu li:not(.current)").bind('click', showTab);
		$("#tab_menu li.current").bind('click', editTab);
		
		$("a.addTab").unbind();
		$("a.addTab").bind('click', addTab);
		
		$("a.addColumn").unbind();
		$("a.addColumn").bind('click', addColumn);
		
		$("a.deleteTab").unbind();
		$("a.deleteTab").bind('click', deleteTab);
		
		$(".deleteColumn").unbind();
		$(".deleteColumn").bind('click', deleteColumn);	
	}
	
	function getDraggableParent(e, ui) {
		return $(this).parent().parent().html();
	}
	
	function beginDraggable() {
		$("div.title").unbind();
	}
	
	function endDraggable() {
		bindIcons();
	}
	
	function blocksDraggable() {
		$("a.dragEl").draggable("destroy");
		$("a.dragEl").draggable({
			//helper: getDraggableParent,
			revert : true,
			scroll : true,
			cursor : 'move',
			start : beginDraggable,
			stop : endDraggable,
			//helper : getDraggableParent,
			placeholder : 'blockSortHelper'
		});
	}
	
	function processDroppedBlock(e, ui) {
		// Retrieving some variables
		var newTab = $(this).attr('id');
		var	newTabSplit = newTab.split("_");
		var newTabId = newTabSplit[2];
		
		var block = ui.draggable.attr('id');
		var blockSplit = block.split("_");
		var blockId = blockSplit[2];
		
		var newColumn = $("#tab_" + newTabId + " .row:first .column:first").attr('id');
		var newColumnSplit = newColumn.split("_");
		var newColumnId = newColumnSplit[1];
		
		var theBlock = ui.draggable.parent().parent();
		
		// Show the processing modal
		var loadingMessage = 'YourBlockIsBeingMoved';
		
		var loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId : 'homeOverlay',
			containerId : 'homeContainer',
			opacity : 75,
			close : false
		});
		
		// Do the actual move + postback		
		$.post("./home/ajax/block_move.php", {block: blockId, column: newColumnId}, function (data) {
			if (data.success === '1')
			{
				//Does the column have blocks
				var blockCount = $("#" + newColumn + " .block").length;
				if (blockCount > 0)
				{
					$("#" + newColumn + " .block:last").after(theBlock);
				}
				else
				{
					$("#" + newColumn).append(theBlock);
				}
				
				checkForEmptyColumns();
			}
			
			// Now we can get rid of the modal as well
			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
			handleLoadingBox(loading);
		}, "json");
	}
	
	function tabsDroppable() {
		$("#tab_elements li").droppable("destroy");
		$("#tab_elements li.normal").droppable({
			accept : "a.dragEl",
			drop : processDroppedBlock
		});
	}
	
	function columnsSortable() {
		$("div.column").sortable("destroy");
		$("div.column").sortable({
			handle : 'div.title',
			cancel : 'a',
			opacity : 0.8,
			cursor : 'move',
			helper : 'clone',
			placeholder : 'blockSortHelper',
			revert : true,
			scroll : true,
			connectWith : columns,
			start : sortableStart,
			stop : sortableStop,
			change : sortableChange,
			update : sortableUpdate
		});
	}
	
	function tabsSortable() {
		$("#tab_menu #tab_elements").sortable("destroy");
		$("#tab_menu #tab_elements").sortable({
			cancel : 'a.deleteTab',
			opacity : 0.8,
			cursor : 'move',
			helper : 'clone',
			placeholder : 'tabSortHelper',
			revert : true,
			scroll : true,
			start : sortableStart,
			update : tabsSortableUpdate
		});
	}
	
	function columnsResizable() {
		$("div.column").resizable("destroy");
		$("div.column").resizable({
			handles : "e",
			transparent : true,
			autoHide : true,
			ghost : true,
			preventDefault : true,
			preserveCursor : true,
			stop : resizableStop
		});
	}

	// Extension to jQuery selectors which only returns visible elements
	$.extend($.expr[':'], {
	    visible: function (a) {
	        return $(a).css('display') !== 'none';
	    }
	});

	$(document).ready(function () {
		$("a.addEl").toggle();
		$("li.current a.deleteTab").css('display', 'inline');

		bindIcons();
		
		tabsSortable();
		
		blocksDraggable();
		tabsDroppable();
		
		columnsSortable();
		columnsResizable();

	});

})(jQuery);