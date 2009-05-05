function showhide()
{

	theBox = document.getElementById('content');
    show = document.getElementById('show');
    hide = document.getElementById('hide');
	
	if (theBox.style.display == "inline")
    {
		theBox.style.display = "none";
        show.style.display = "inline";
        hide.style.display = "none";

	} else
    {
		theBox.style.display = "inline";
        show.style.display = "none";
        hide.style.display = "inline";
	}
}

   



