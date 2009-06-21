$(function ()
{
	var colours = ['#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429', '#f6d7c5', '#7a2893'];

	var offset = $('#hotspot_image').offset();
	var currentPolygon = null;
	var positions = [];

	function redrawPolygon()
	{
		$('.polygon_fill_' + currentPolygon, $('#hotspot_image')).remove();
		$('.polygon_line_' + currentPolygon, $('#hotspot_image')).remove();

		$('#hotspot_image').fillPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {clss: 'polygon_fill_' + currentPolygon, color: colours[currentPolygon], alpha: .5});
		$('#hotspot_image').drawPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {clss: 'polygon_line_' + currentPolygon, color: colours[currentPolygon], stroke: 1, alpha: .9});
	}
	
	function setCoordinates()
	{
		var coordinatesField = $('input[name="coordinates[' + currentPolygon + ']"]'),
			coordinatesData = [],
			currentCoordinates = positions[currentPolygon];
		
		$.each(currentCoordinates.X, function(index, item){
			coordinatesData.push([item, currentCoordinates.Y[index]]);
			});
		
		coordinatesField.val((serialize(coordinatesData)));
	}

	function getCoordinates(ev, ui)
	{
		if (currentPolygon != null)
		{
			var pX, pY;
	
			pX = ev.pageX - offset.left;
			pY = ev.pageY - offset.top;
			pX = pX.toFixed(0);
			pY = pY.toFixed(0);
			positions[currentPolygon].X.push(parseInt(pX, 10));
			positions[currentPolygon].Y.push(parseInt(pY, 10));
	
			redrawPolygon();
			setCoordinates();
		}
	}
	
	function resetPolygonObject(id)
	{
		currentPolygon = id;
		
		positions[currentPolygon] = new Object();
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
	
	function editPolygon(ev, ui)
	{
		ev.preventDefault();
		var id = $(this).attr(('id')).replace('edit_', '');
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
			
			if (fieldValue != '')
			{
				fieldValue = unserialize(fieldValue);
				
				currentPolygon = id;
				resetPolygonObject(id)
				
				$.each(fieldValue, function(index, item){
					positions[id].X.push(item[0]);
					positions[id].Y.push(item[1]);
				});
				
				redrawPolygon();
			}
		});
	}

	$(document).ready(function ()
	{
		initializePolygons();
		
		//Bind clicks on the edit and reset buttons
		$('input[name*="edit"]').live('click', editPolygon);
		$('input[name*="reset"]').live('click', resetPolygon);

		//Bind clicks on the image
		$('#hotspot_image').click(getCoordinates);
	});

});