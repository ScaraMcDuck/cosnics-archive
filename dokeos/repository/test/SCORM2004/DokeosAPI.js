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

function DokeosInitialize()
{
	return "true";
}

function DokeosTerminate()
{
	return "true";
}

function DokeosGetValue($variable)
{
	return this.values[$variable];
}

function DokeosSetValue($variable, $value)
{
	this.values[$variable] = $value;
	alert($variable . ' ' . $value);
	return "true";
}

function DokeosCommit()
{
	return "true";
}

function DokeosGetLastError()
{
	return 0;
}

function DokeosGetErrorString()
{
	return "true";
}

function DokeosGetDiagnostic()
{
	return "true";
}