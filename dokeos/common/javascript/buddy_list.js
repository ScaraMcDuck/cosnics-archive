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
		//Variable initialisation
		var old_parent = ui.draggable.parent();
		var old_super_parent = old_parent.parent();
		var buddylist = $('.buddy_list', $(this));
		
		//Check if buddylist exists in new parent or add it
		if(buddylist.attr('class') != 'buddy_list')
		{
			$(this).append('<ul class="buddy_list"></ul>');
			buddylist = $('.buddy_list', $(this));
		}
		
		//Add item to buddylist
		buddylist.append(ui.draggable);
		
		//Determine postback variables
		var buddy = ui.draggable.attr('id');
		var new_category = $(this).attr('id');
		
		//Check wheter the buddylist from old parent may be removed
		var is_remove = false;
		var children = $('.buddy_list', old_super_parent).children();
		if(children.size() == 0)
		{
			$('.category_toggle', old_super_parent).css('visibility', 'hidden');
			$('.buddy_list', old_super_parent).remove();
			is_remove = true;
		}
		
		//Toggle the visibility of new parent's icon
		$('.category_toggle', $(this)).css('visibility', 'visible');
	
		var current = $(this);
		bind_icons();
		
		$.post('index_user.php?go=buddy_category_change',
		{
	    	buddy:  buddy,
	    	new_category: new_category
	    },	function(data)
	    	{
	    		if(data.length > 0)
	    		{ 
	    			//Turn back actions
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
	    			
	    			bind_icons();
	    			alert(translation('CategoryChangeFailed', 'user'));
	    		}
	    	}
	    );
	}
	
	var delete_category_clicked = function(ev, ui) 
	{ 		
		var id = $(this).attr('id');
		var object = $(this).parent().parent().parent().parent();
		var object_parent = object.parent();
		
		var children = $('.buddy_list', object).children();
		
		var normal_category = $('.category_list_item[id="0"]', object_parent);
		var normal_buddy_list = $('.buddy_list', normal_category);
		
		if(normal_buddy_list.attr('class') != 'buddy_list')
		{
			normal_category.append('<ul class="buddy_list"></ul>');
			normal_buddy_list = $('.buddy_list', normal_category);
			$('.category_toggle', normal_category).css('visibility', 'visible');
		}
		
		normal_buddy_list.append(children);
		
		object.remove();
		bind_icons();
		
		$.get('index_user.php?go=buddy_delete_category',
		{
			buddylist_category: id,
			ajax: 1
	    },  function(data)
	    	{
	    		if(data.length > 0)
	    		{ 
	    			alert(data);
	    			object_parent.prepend(object);
	    			$('.buddy_list', object).append(children);
	    			
	    			if(normal_buddy_list.children().size() == 0)
	    			{
	    				normal_buddy_list.remove();
	    				$('.category_toggle', normal_category).css('visibility', 'hidden');
	    			}
	    			bind_icons();
	    		}
	    	}
		);
		
		return false;
	}
	
	var delete_item_clicked = function(ev, ui) 
	{ 
		var id = $(this).attr('id');
		var object = $(this).parent().parent().parent().parent();
		var object_parent = object.parent();
		
		object.remove();
		bind_icons();
		
		$.get('index_user.php?go=buddy_delete_item',
		{
			buddylist_item:  id,
			ajax: 1
	    },  function(data)
	    	{
	    		if(data.length > 0)
	    		{ 
	    			alert(data);
	    			object_parent.prepend(object);
	    			bind_icons();
	    		}
	    	}
		);
		
		return false;
	}
	
	$(document).ready( function() 
	{	
		$(".buddy_list_item").draggable({
			revert: true
		});
		
		$(".category_list_item").droppable({
			accept: '.buddy_list_item',
			hoverClass: 'buddyDrop',
			drop: buddy_dropped
		});
		
		bind_icons();
	});
	
	function bind_icons()
	{
		$(".category_toggle").bind('click', item_clicked);
		$(".delete_category").bind('click', delete_category_clicked);
		$(".delete_item").bind('click', delete_item_clicked);
		
		var total = 0;
		
		$('.category_list_item').each(function()
		{
			var size = $('.buddy_list', $(this)).children().size();
			total += size;
			$('.userscount', $(this)).text(size);
		});
		
		$('.totalusers').text(total);
	}
	
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