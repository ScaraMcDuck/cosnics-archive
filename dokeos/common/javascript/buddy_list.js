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
		
		$('.buddy_list', $(this).parent()).toggle();
	}
	
	var buddy_dropped = function(event, ui)
	{
		var old_parent = ui.draggable.parent();
		var old_super_parent = old_parent.parent();
		var buddylist = $('.buddy_list', $(this));
		
		if(buddylist.attr('class') != 'buddy_list')
		{
			$(this).append('<ul class="buddy_list"></ul>');
			buddylist = $('.buddy_list', $(this));
		}
		
		buddylist.append(ui.draggable);
		var buddy = ui.draggable.attr('id');
		var new_category = $(this).attr('id');
		
		var is_remove = false;
		var children = $('.buddy_list', old_super_parent).children();
		if(children.size() == 0)
		{
			$('.category_toggle', old_super_parent).css('visibility', 'hidden');
			$('.buddy_list', old_super_parent).remove();
			is_remove = true;
		}
	
		$('.category_toggle', $(this)).css('visibility', 'visible');
	
		var current = $(this);
		
		$.post('index_user.php?go=buddy_category_change',
		{
	    	buddy:  buddy,
	    	new_category: new_category
	    },	function(data)
	    	{
	    		if(data.length > 0)
	    		{ 
	    			if(is_remove)
	    			{
	    				old_super_parent.append('<ul class="buddy_list"></ul>');
	    				old_parent = $('.buddy_list', old_super_parent)
	    			}
	    			
	    			old_parent.append(ui.draggable);
	    			$('.category_toggle', old_super_parent).css('visibility', 'visible');
	    			
	    			var children = $('.buddy_list', current).children();
	    			if(children.size() == 0)
	    			{
	    				$('.category_toggle', current).css('visibility', 'hidden');
	    				buddylist.remove();
	    			}
	    			
	    			alert(translation('CategoryChangeFailed', 'user'));
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