// in je FORM-tag voor de hotspots:  onSubmit="return validateFlashVar('MINIMUM_AANTAL_CLICKS');

var flashVar = 1;

var lcId = new Date().getTime();
//var flashProxy = new FlashProxy(lcId, "JavaScriptFlashGateway.swf");

function validateFlashVar(counter, lang_1, lang_2)
{
	return true;
	//alert(counter);
	//alert(flashVar);
	
	if(counter != flashVar)
	{
		alert(lang_1 + counter + lang_2);
		
		return false;
	}
	else
	{
		return true;
	}
}

function updateFlashVar()
{
	//alert('updateFlashVar: ' + flashVar);
	flashVar++;
}

function getForm()
{
	form = document.create;
	if (form == null)
		form = document.edit;
	if (form == null)
		form = document.assessment;

	return form;
}

function saveHotspot(question_id, hotspot_id, answer, hotspot_x, hotspot_y)
{
	form = getForm();
	control = question_id+"_"+(hotspot_id-1);
	
	coord = document.getElementById(question_id+"_"+(hotspot_id-1));
	if (coord == null)
		coord = document.assessment[control];
	coord.value = hotspot_x + ";" + hotspot_y + "-" + answer;	
}

function saveDelineationUserAnswer(question_id, hotspot_id, answer, coordinates)
{
	form = getForm();
	control = question_id+"_"+(hotspot_id-1);

	coord = document.getElementById(question_id+"_"+(hotspot_id-1));
	if (coord == null)
		coord = document.assessment[control];
	coord.value = coordinates+"-"+answer;	
}

function saveShapeHotspot(question_id, hotspot_id, type, x, y, w, h)
{
	form = getForm();
	control = "coordinates[" + (hotspot_id - 1) + "]";
	form[control].value = x + ";" + y + "|" + w + "|" + h;
	control = "type[" + (hotspot_id - 1) + "]";
	form[control].value = type;
}

function savePolyHotspot(question_id, hotspot_id, coordinates)
{
	form = getForm();
	control = "coordinates[" + (hotspot_id - 1) + "]";
	form[control].value = coordinates;
	control = "type[" + (hotspot_id - 1) + "]";
	form[control].value = "poly";
}

function saveDelineationHotspot(question_id, hotspot_id, coordinates)
{
	form = getForm();
	control = "coordinates[" + (hotspot_id - 1) + "]";
	form[control].value = coordinates;
	control = "type[" + (hotspot_id - 1) + "]";
	form[control].value = "delineation";
}
function jsdebug(debug_string)
{
	alert(debug_string);
}