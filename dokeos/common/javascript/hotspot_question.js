$(function ()
{
	var colors = ['#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429', '#f6d7c5', '#7a2893'];

	var offset = $('#hotspot_image').offset();
	var currentPolygon = 0;
	var positions = [];

	function redrawPolygon()
	{
		$('.polygon_fill_' + currentPolygon, $('#hotspot_image')).remove();
		$('.polygon_line_' + currentPolygon, $('#hotspot_image')).remove();

		$('#hotspot_image').fillPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {clss: 'polygon_fill_' + currentPolygon, color: colors[currentPolygon], alpha: .5});
		$('#hotspot_image').drawPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {clss: 'polygon_line_' + currentPolygon, color: colors[currentPolygon], stroke: 1, alpha: .9});
	}

	function getCoordinates(ev, ui)
	{
		var pX, pY;

		pX = ev.pageX - offset.left;
		pY = ev.pageY - offset.top;
		pX = pX.toFixed(0);
		pY = pY.toFixed(0);
		positions[currentPolygon].X.push(parseInt(pX, 10));
		positions[currentPolygon].Y.push(parseInt(pY, 10));

		redrawPolygon();
	}

	function startNewPolygon(ev, ui)
	{
		ev.preventDefault();

		// Get the coordinates, serialize and add them to the page
		var coordinates = new Array(positions[currentPolygon].X, positions[currentPolygon].Y);
		$('#hotspot_image').parent().append('<div style="height: 25px; background-color: #EEEEEE; margin-bottom: 10px;" id="polygon_data_' + currentPolygon + '">' + serialize(coordinates) + '</div')

		// Increment the polygon counter
		currentPolygon += 1;

		// Initialize the newly created polygon
		positions.push(new Object());
		positions[currentPolygon].X = [];
		positions[currentPolygon].Y = [];
	}

//	function drawIntro(svg)
//	{
//		svg.polygon([[10, 10], [112, 10], [112,111], [10,111]], {fill: "lime", stroke: "blue", strokeWidth: 10});
//	}

	$(document).ready(function ()
	{
		// Initialize a polygon
		positions.push(new Object());
		positions[currentPolygon].X = [];
		positions[currentPolygon].Y = [];

		// Bind clicks on the image and the new polygon button
		$('#hotspot_image').click(getCoordinates);
		//$('#new_polygon').click(startNewPolygon);

//		$('#test_area').svg({onLoad: drawIntro});
	});

});