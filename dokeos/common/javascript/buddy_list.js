( function($) 
{
	var item_clicked = function(ev, ui) 
	{ 
		//alert('test');
		
		var visible_img = 'bullet_toggle_plus.png';
		var invisible_img = 'bullet_toggle_minus.png';
		var current = $(this).attr('src');
		
		if(current.indexOf(visible_img) > -1)
			current = current.replace(visible_img, invisible_img)
		else
			current = current.replace(invisible_img, visible_img);
	
		$(this).attr('src', current);
		
		$(".buddy_list", $(this).parent()).toggle();
	}
	
	var buddy_dropped = function(event, ui)
	{
		var old_parent = ui.draggable.parent();
		
		$(".buddy_list", $(this)).append(ui.draggable);
		var buddy = ui.draggable.attr("id");
		var new_category = $(this).attr("id");
		
		$.post("index_user.php?go=buddy_category_change",
		{
	    	buddy:  buddy,
	    	new_category: new_category
	    },	function(data)
	    	{
	    		if(data.length > 0)
	    		{
	    			alert(translation('CategoryChangeFailed', 'user'));
	    			old_parent.append(ui.draggable);
	    		}
	    	}
	    );
	}
	
	$(document).ready( function() 
	{
		$(".category_toggle").bind('click', item_clicked);
		
		$(".buddy_list_item").draggable({
			revert: true,
		});
		
		$(".category_list_item").droppable({
			accept: '.buddy_list_item',
			hoverClass: 'buddyDrop',
			drop: buddy_dropped
		});
	});
	
	function translation(string, application) {
		
		var translated_string = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/translation.php",
			data: { string: string, application: application },
			async: false
		}).responseText;
		
		return translated_string;
	};
	
})(jQuery);