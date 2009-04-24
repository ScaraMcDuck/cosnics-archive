(function ($) {
	
	var maxBlockHeight = 0, maxComplexBlockHeight = 0;
	
//	function autoFilter() {
//		alert('Test');
//	}
	
	$(document).ready(function () {
		
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
		
		$("div.create_block").height(maxBlockHeight);
		$("div.create_complex_block").height(maxComplexBlockHeight);
		
		//$("select[name=filter_type] option").bind('click', autoFilter);
	});
	
})(jQuery);