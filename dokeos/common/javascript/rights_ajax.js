( function($) {
	
	function translation(string, application) {		
		var translated_string = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/translation.php",
			data: { string: string, application: application },
			async: false
		}).responseText;
		
		return translated_string;
	};

	var collapseItem = function(e) {
		e.preventDefault();
		
		var image = $("div", this);
		var originalClass = image.attr("class");
		var id = $(this).parent().attr('id');
		
		image.attr("class", "loadingMini");
		
		$.post("./rights/ajax/role_right_location.php", {
			rights : id
			}, function(result){
				  
					if (result)
					{
						var newClass = $.ajax({
							type: "POST",
							url: "./rights/ajax/role_right_location_class.php",
							data: { rights : id },
							async: false
						}).responseText;

						image.attr("class", newClass);
					}
					else
					{
						image.attr("class", originalClass);
						alert(translation('Failure', 'rights'));
					}
			}
		);
	};

	function bindIcons() {
		$("a.setRight").unbind();
		$("a.setRight").bind('click', collapseItem);
	}

	$(document).ready( function() {
		bindIcons();
	});

})(jQuery);