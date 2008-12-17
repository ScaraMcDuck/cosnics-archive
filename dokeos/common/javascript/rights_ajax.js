( function($) {

	var collapseItem = function(e) {
		e.preventDefault();
		alert('Test');
		//$(this).parent().next(".description").slideToggle(300);
	};

	function bindIcons() {
		$("a.setRight").unbind();
		$("a.setRight").bind('click', collapseItem);
	}

	$(document).ready( function() {
		bindIcons();
	});

})(jQuery);