(function ($) {
	
	var maxBlockHeight = 0, maxComplexBlockHeight = 0;
	
	$("div.create_block").each(function (i) {
		if ($(this).height() > maxBlockHeight)
		{
			maxBlockHeight = $(this).height();
		}
	});
	
	$("div.create_complex_block").each(function (i) {
		if ($(this).height() > maxComplexBlockHeight)
		{
			maxComplexBlockHeight = $(this).height();
		}
	});
	
	$(document).ready(function () {
		$("div.create_block").height(maxBlockHeight);
		$("div.create_complex_block").height(maxComplexBlockHeight);
	});
	
})(jQuery);