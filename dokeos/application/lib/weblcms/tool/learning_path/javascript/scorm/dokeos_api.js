var API_1484_11 = new Object();

API_1484_11.Initialize = DokeosInitialize;
API_1484_11.Terminate = DokeosTerminate;
API_1484_11.GetValue = DokeosGetValue;
API_1484_11.SetValue = DokeosSetValue;
API_1484_11.Commit = DokeosCommit;
API_1484_11.GetLastError = DokeosGetLastError;
API_1484_11.GetErrorString = DokeosGetErrorString;
API_1484_11.GetDiagnostic = DokeosGetDiagnostic;
API_1484_11.values = new Array();
API_1484_11.version = "1.0";

var last_error = 0;
var initialized = false;

function DokeosInitialize(params)
{
	if(params && params != "")
	{
		last_error = 201;
		return "false";
	}
	
	if(initialized)
	{
		last_error = 103;
		return "false"
	}
	
	initialized = true;	
	last_error = 0;
	
	return "true";
}

function DokeosTerminate(params)
{
	if(params && params != "")
	{
		last_error = 201;
		return "false";
	}
	
	if(!initialized)
		return "false";

	initialized = false;
	last_error = 0;
	
	check_redirect_conditions();
	
	return "true";
}

function check_redirect_conditions()
{
	var url = null;

	if(this.values['adl.nav.request'] == 'continue')
	{
		url = continue_url;
	}
	
	if(this.values['adl.nav.request'] == 'previous')
	{
		url = previous_url;
	}
	
	/*if(url)
	{
		window.location = url;
	}*/
}

function DokeosGetValue(variable)
{
	if(!initialized)
	{
		last_error = 122;
		return "";
	}
	
	if(variable == "")
	{
		last_error = 301;
		return "";
	}
	
	last_error = 0;
	var value = this.values[variable]; 
	if(!value)
	{ 
		value = jQuery.ajax({
			type: "POST",
			url: "./application/lib/weblcms/tool/learning_path/javascript/scorm/ajax/get_value.php",
			data: { tracker_id: tracker_id, variable: variable},
			async: false
		}).responseText; 
	}

	return value;
}

function DokeosSetValue(variable, value)
{
	if(!initialized)
	{
		last_error = 132;
		return "false";
	}
	
	if(variable == "")
	{
		last_error = 351;
		return "false";
	}
	
	this.values[variable] = value;
	last_error = 0;
	
	return "true";
}

function DokeosCommit(params)
{
	if(params && params != "")
	{
		last_error = 201;
		return "false";
	}
	
	if(!initialized)
	{
		last_error = 142;
		return "false";
	}
	
	last_error = 0;
	
	return "true";
}

function DokeosGetLastError()
{
	return last_error;
}

function DokeosGetErrorString(error_code)
{
	return "";
}

function DokeosGetDiagnostic()
{
	return "";
}