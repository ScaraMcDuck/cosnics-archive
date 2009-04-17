$(function () {
	
	$(document).ready(function () {
		$("ul.tree-menu > li").css("margin-left", "-0px");
		$("ul.tree-menu ul:first").css("background-image", "none");
		//$("ul.tree-menu > li:first").css("margin-left", "36px");
		$("ul.tree-menu").css("background-image", "none");
		$("ul.tree-menu > li:last").css("background-image", "url(layout/aqua/img/common/treemenu/tree-leaf-end.png)");
		
		$("ul").each(function () {
			$("li:last", $(this)).css("background-image", "url(layout/aqua/img/common/treemenu/tree-leaf-end.png)"); 
		});
	});

});