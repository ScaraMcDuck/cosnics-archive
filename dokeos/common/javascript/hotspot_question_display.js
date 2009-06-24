/*global $, document, renderFckEditor, getPath, getTranslation, getTheme, setMemory, doAjaxPost, serialize, unserialize */

$(function ()
{
	//var colours = ['#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429', '#f6d7c5', '#7a2893'],
	var colours = ['#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff', '#ff00ff', '#0080ff', '#ff0080', '#00ff80', '#ff8000', '#8000ff'],
		offset,
		currentPolygon = null,
		currentHotspot = null,
		positions = [],
		skippedOptions = 0;
	
	/********************************
	 * Functionality to draw hotspots
	 ********************************/
	
	function calculateSelectionCoordinates(posX, posY)
	{
		var coordinates = {X: [], Y: []};
		
		coordinates.X.push(posX);
		coordinates.X.push(posX + 5);
		coordinates.X.push(posX);
		coordinates.X.push(posX - 5);
		
		coordinates.Y.push(posY - 5);
		coordinates.Y.push(posY);
		coordinates.Y.push(posY + 5);
		coordinates.Y.push(posY);
		
		return coordinates;
	}

	function redrawPolygon()
	{
		$('.polygon_fill_' + currentHotspot + '_' + currentPolygon, $('#hotspot_image_' + currentHotspot)).remove();
		$('.polygon_line_' + currentHotspot + '_' + currentPolygon, $('#hotspot_image_' + currentHotspot)).remove();
		
		var selectionCoordinates = calculateSelectionCoordinates(positions[currentHotspot][currentPolygon].X, positions[currentHotspot][currentPolygon].Y);

		$('#hotspot_image_' + currentHotspot).fillPolygon(selectionCoordinates.X, selectionCoordinates.Y, {clss: 'polygon_fill_' + currentHotspot + '_' + currentPolygon, color: colours[currentPolygon], alpha: 0.5});
		$('#hotspot_image_' + currentHotspot).drawPolygon(selectionCoordinates.X, selectionCoordinates.Y, {clss: 'polygon_line_' + currentHotspot + '_' + currentPolygon, color: colours[currentPolygon], stroke: 1, alpha: 1});
	}
	
	function setCoordinates()
	{
		var coordinatesField = $('input[name="' + currentHotspot + '_' + currentPolygon + '"]'),
			coordinatesData,
			currentCoordinates = positions[currentHotspot][currentPolygon];
		
		coordinatesData = [currentCoordinates.X, currentCoordinates.Y];
		coordinatesField.val((serialize(coordinatesData)));
	}
	
	function resetPolygonObject(question, option)
	{
		currentHotspot = question;
		currentPolygon = option;
		
		positions[currentHotspot] = {};
		positions[currentHotspot][currentPolygon] = {};
		positions[currentHotspot][currentPolygon].X = [];
		positions[currentHotspot][currentPolygon].Y = [];
		
		$('.polygon_fill_' + currentHotspot + '_' + currentPolygon, $('#hotspot_image_' + question)).remove();
		$('.polygon_line_' + currentHotspot + '_' + currentPolygon, $('#hotspot_image_' + question)).remove();
	}

	function getCoordinates(ev, ui)
	{
		if (currentPolygon !== null && currentHotspot !== null)
		{
			var pX, pY;
			
			resetPolygonObject(currentHotspot, currentPolygon);	
			offset = $('#hotspot_image_' + currentHotspot).offset();
			
			pX = ev.pageX - offset.left;
			pY = ev.pageY - offset.top;
			pX = pX.toFixed(0);
			pY = pY.toFixed(0);
			positions[currentHotspot][currentPolygon].X = parseInt(pX, 10);
			positions[currentHotspot][currentPolygon].Y = parseInt(pY, 10);
	
			redrawPolygon();
			setCoordinates();
		}
	}
	
	function resetPolygon(ev, ui)
	{
		ev.preventDefault();
		var ids = $(this).attr('id').replace('reset_', '').split('_'),
			question_id = ids[0],
			option_id = ids[1];
		
		$('#hotspot_marking_' + question_id + ' .colour_box').css('background-color', 'transparent');
		resetPolygonObject(question_id, option_id);
		
		$('tr#' + currentHotspot + '_' + currentPolygon + ' img.hotspot_configured').hide();
		$('tr#' + currentHotspot + '_' + currentPolygon + ' img.edit_option').show();
		
		currentHotspot = null;
		currentPolygon = null;
	}
	
	function editPolygon(ev, ui)
	{
		ev.preventDefault();
		var ids = $(this).attr('id').replace('edit_', '').split('_'),
			question_id = ids[0],
			option_id = ids[1];
		
		$('#hotspot_marking_' + question_id + ' .colour_box').css('background-color', colours[option_id]);
		$('#hotspot_marking_' + question_id + ' .confirm_hotspot').show();
		resetPolygonObject(question_id, option_id);
	}
	
	function setHotspot(ev, ui)
	{
		ev.preventDefault();
		
		var id = $(this).parent().attr('id').replace('hotspot_marking_', '');
		setCoordinates();
		addCheck();
		$(this).hide();
	}
	
	function addCheck()
	{
		$('tr#' + currentHotspot + '_' + currentPolygon + ' img.edit_option').hide();
		$('tr#' + currentHotspot + '_' + currentPolygon + ' img.hotspot_configured').show();
		//var html = '<img class="hotspot_configured" src="' + getPath('WEB_LAYOUT_PATH') + getTheme() + '/img/common/buttons/button_confirm.png" style="float: right;" />';
		//$('tr#' + currentHotspot + '_' + currentPolygon + ' td:nth-child(2)').append(html);
	}

	$(document).ready(function ()
	{
		// Bind clicks on the edit and reset buttons
		$('.edit_option').live('click', editPolygon);
		$('.reset_option').live('click', resetPolygon);

		// Bind clicks on the image
		$('.hotspot_image').click(getCoordinates);
		$('.confirm_hotspot').click(setHotspot);
	});

});