(function ($) {
	
	var maxHeight = 0;
	
	$("div.create_block").each(function (i) {
		if ($(this).height() > maxHeight)
		{
			maxHeight = $(this).height();
		}
	});
	
	$("div.create_block").height(maxHeight);
})(jQuery);