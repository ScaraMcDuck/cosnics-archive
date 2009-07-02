$(function ()
{
	//var colours = ['#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429', '#f6d7c5', '#7a2893'],
	var colours = ['#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff', '#ff00ff', '#0080ff', '#ff0080', '#00ff80', '#ff8000', '#8000ff'],
		offset,
		currentPolygon = null,
		positions = [],
		skippedOptions = 0;
	
	/********************************
	 * Functionality to draw hotspots
	 ********************************/

	function redrawPolygon()
	{
		$('.polygon_fill_' + currentPolygon, $('#hotspot_image')).remove();
		$('.polygon_line_' + currentPolygon, $('#hotspot_image')).remove();

		$('#hotspot_image').fillPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {clss: 'polygon_fill_' + currentPolygon, color: colours[currentPolygon], alpha: 0.5});
		$('#hotspot_image').drawPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {clss: 'polygon_line_' + currentPolygon, color: colours[currentPolygon], stroke: 1, alpha: 0.9});
	}
	
	function resetPolygonObject(id)
	{
		currentPolygon = id;
		
		positions[currentPolygon] = {};
		positions[currentPolygon].X = [];
		positions[currentPolygon].Y = [];
		
		$('.polygon_fill_' + currentPolygon, $('#hotspot_image')).remove();
		$('.polygon_line_' + currentPolygon, $('#hotspot_image')).remove();
	}
	
	function resetPolygon(ev, ui)
	{
		ev.preventDefault();
		var id = $(this).attr(('id')).replace('reset_', '');
		$('#hotspot_marking .colour_box').css('background-color', colours[id]);
		resetPolygonObject(id);
	}
	
	function initializePolygons()
	{
		$('input[name*="coordinates"]').each(function (i)
		{
			var fieldName = $(this).attr('name'),
				id = fieldName.substr(12, fieldName.length - 13),
				fieldValue = $(this).val();
			
			if (fieldValue !== '')
			{
				fieldValue = unserialize(fieldValue);
				
				currentPolygon = id;
				resetPolygonObject(id);
				
				$.each(fieldValue, function (index, item)
				{
					positions[id].X.push(item[0]);
					positions[id].Y.push(item[1]);
				});
				
				redrawPolygon();
			}
		});
	}
	
	$(document).ready(function ()
	{
		// Initialize possible existing polygons
		initializePolygons();
	});
	
});