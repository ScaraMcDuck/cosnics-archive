( function($) 
{
	$(document).ready(
		function()
		{
			var tree = $('.myTree');
			$('li', tree.get(0)).each(
				function()
				{
					subbranch = $('ul', $(this));
					if (subbranch.size() > 0) {
						if (subbranch.eq(0).css('display') == 'none') {
							$(this).prepend('<img src="layout/aqua/img/common/treemenu/bullet_toggle_plus.png" width="16" height="16" class="expandImage" />');
						} else {
							$(this).prepend('<img src="layout/aqua/img/common/treemenu/bullet_toggle_minus.png" width="16" height="16" class="expandImage" />');
						}
					} else {
						$(this).prepend('<img src="layout/aqua/img/common/treemenu/spacer.gif" width="16" height="16" class="expandImage" />');
					}
				}
			);
			$('img.expandImage', tree.get(0)).click(
				function()
				{
					if (this.src.indexOf('spacer') == -1) {
						subbranch = $('ul', this.parentNode).eq(0);
						if (subbranch.css('display') == 'none') {
							subbranch.show();
							this.src = 'layout/aqua/img/common/treemenu/bullet_toggle_minus.png';
						} else {
							subbranch.hide();
							this.src = 'layout/aqua/img/common/treemenu/bullet_toggle_plus.png';
						}
					}
				}
			);
			$('span.textHolder').Droppable(
				{
					accept			: 'treeItem',
					hoverclass		: 'dropOver',
					activeclass		: 'fakeClass',
					tollerance		: 'pointer',
					onhover			: function(dragged)
					{
						if (!this.expanded) {
							subbranches = $('ul', this.parentNode);
							if (subbranches.size() > 0) {
								subbranch = subbranches.eq(0);
								this.expanded = true;
								if (subbranch.css('display') == 'none') {
									var targetBranch = subbranch.get(0);
									this.expanderTime = window.setTimeout(
										function()
										{
											$(targetBranch).show();
											$('img.expandImage', targetBranch.parentNode).eq(0).attr('src', 'layout/aqua/img/common/treemenu/bullet_toggle_minus.png');
											$.recallDroppables();
										},
										500
									);
								}
							}
						}
					},
					onout			: function()
					{
						if (this.expanderTime){
							window.clearTimeout(this.expanderTime);
							this.expanded = false;
						}
					},
					ondrop			: function(dropped)
					{
						id = $(this).parents(".myTree").attr("id");

						$.post("common/html/menu/ajax/" + id + ".php", 
					    {
							target : this.id,
							source : dropped.childNodes[2].id
						},  function(data) 
							{
	    						//alert(data);
	    					}
	    				);
	    				
						if(this.parentNode == dropped)
							return;
						if (this.expanderTime){
							window.clearTimeout(this.expanderTime);
							this.expanded = false;
						}
						subbranch = $('ul', this.parentNode);
						if (subbranch.size() == 0) {
							$(this).after('<ul></ul>');
							subbranch = $('ul', this.parentNode);
						}
						oldParent = dropped.parentNode;
						subbranch.eq(0).append(dropped);
						oldBranches = $('li', oldParent);
						if (oldBranches.size() == 0) {
							$('img.expandImage', oldParent.parentNode).attr('src', 'layout/aqua/img/common/treemenu/spacer.gif');
							$(oldParent).remove();
						}
						expander = $('img.expandImage', this.parentNode);
						if (expander.get(0).src.indexOf('spacer') > -1)
							expander.get(0).src = 'layout/aqua/img/common/treemenu/bullet_toggle_minus.png';
					}
				}
			);
			$('li.treeItem').Draggable(
				{
					revert		: true,
					autoSize		: true,
					ghosting			: true/*,
					onStop		: function()
					{
						$('span.textHolder').each(
							function()
							{
								this.expanded = false;
							}
						);
					}*/
				}
			);
			
			$('#deletediv').toggle();
			
			$('#deleter').Droppable(
				{
					accept			: 'treeItem',
					hoverclass		: 'dropOver',
					activeclass		: 'fakeClass',
					tollerance		: 'pointer',
					ondrop			: function(dropped)
					{
						id = $(this).parents(".myTree").attr("id");
						
						$.post("common/html/menu/ajax/" + id + "_remover.php", 
					    {
							item : dropped.childNodes[2].id
						},  function(data) 
							{
	    						if(data == "true")
	    						{
	    							oldParent = dropped.parentNode;
									oldBranches = $('li', oldParent);
									if (oldBranches.size() == 0) {
										$('img.expandImage', oldParent.parentNode).attr('src', 'layout/aqua/img/common/treemenu/spacer.gif');
										$(oldParent).remove();
									}
									
									$(dropped).remove();
	    						}
	    						else
	    							//alert(data);
	    							alert(translation('CouldNotRemoveItem', 'admin'));
	    					}
	    				);
						
					}
				}
			);
		}
	);
	
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