jQuery(document).ready(function($)
{
	$('div.application, div.application_current').css('fontSize', '37%');
	$('div.application, div.application_current').css('width', '40px');
	$('div.application, div.application_current').css('height', '32px');
	$('div.application, div.application_current').css('margin-top', '19px');
	$('div.application, div.application_current').css('margin-bottom', '19px');
	
	$('div.application, div.application_current').mouseover(function()
	{
		$(this).css('fontSize', '75%');
		$(this).css('width', '80px');
		$(this).css('height', '68px');
		$(this).css('margin-top', '0px');
		$(this).css('margin-bottom', '0px');
		$(this).css('background-color', '#EBEBEB');
		$(this).css('border', '1px solid #c0c0c0');
	});
	
	$('div.application').mouseout(function()
	{
		$(this).css('fontSize', '37%');
		$(this).css('width', '40px');
		$(this).css('height', '32px');
		$(this).css('margin-top', '19px');
		$(this).css('margin-bottom', '19px');
		$(this).css('background-color', '#FFFFFF');
		$(this).css('border', '1px solid #EBEBEB');
	});
	
	$('div.application_current').mouseout(function()
	{
		$(this).css('fontSize', '37%');
		$(this).css('width', '40px');
		$(this).css('height', '32px');
		$(this).css('margin-top', '19px');
		$(this).css('margin-bottom', '19px');
		$(this).css('background-color', '#EBEBEB');
		$(this).css('border', '1px solid #c0c0c0');
	});
})