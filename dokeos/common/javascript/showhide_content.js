
(function ($)
{
	$(document).ready(function () 
    {
        $("#showhide").click(function()
        {
            $(this).text($(this).text() == '[Hide]' ? '[Show]' : '[Hide]');
            $("#headers").toggle();return false;
        
        });
	});

})(jQuery);

   



