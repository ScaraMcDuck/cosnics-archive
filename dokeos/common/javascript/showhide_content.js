function showhide()
{

	theBox = document.getElementById('content');
    show = document.getElementById('show');
    hide = document.getElementById('hide');
	
	if (theBox.style.display == "block")
    {
		theBox.style.display = "none";
        show.style.display = "block";
        hide.style.display = "none";

	} else
    {
		theBox.style.display = "block";
        show.style.display = "none";
        hide.style.display = "block";
	}
}

   



