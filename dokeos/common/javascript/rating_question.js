( function($) 
{
	function setValue(e, ui)
	{
		var currentValue = ui.value;
		var sliderId = $(this).attr('id');
		var selectName = sliderId.replace('slider_', '');
		$("select[name=" + selectName + "]").val(currentValue);
		$("#slider_caption_" + selectName).html(currentValue);
	}
	
	function addSlider()
	{
		var id = $(this).attr("name");
		var minValue = (int) $('option:first', this).val();		
		var maxValue = (int) $('option:last', this).val();
		var slider = $('<div class="slider" id="slider_' + id + '"></div>');
		var caption = $('<div class="caption" id="slider_caption_' + id + '"></div>');
		$(this).after(caption).after(slider);
		$(this).toggle();
		
		alert(minValue);
		
		$(slider).slider({
			animate: true,
			min: minValue,
			max: maxValue,
			stop: setValue,
			slide: setValue
			});
	}
	
	$(document).ready( function() 
	{
		$("select.rating_slider").each(addSlider);
	});
	
})(jQuery);