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
		
		image.attr("class", "rightLoading");
		
		$.post("./rights/ajax/role_right_location.php", {
			rights :$(this).parent().attr('id')
			}, function(result){
					if (result)
					{						
						if (originalClass == "rightTrue")
						{
							image.attr("class", "rightFalse")
						}
						else
						{
							image.attr("class", "rightTrue")
						}
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