function getTranslation(string, application)
{
	return getUtilities('translation', { string: string, application: application }).translation;
}

function getPath(path)
{
	return getUtilities('path', { path: path }).path;
}

function getTheme()
{
	return getUtilities('theme').theme;
}

function getUtilities(type, parameters)
{
	if (typeof parameters == "undefined")
	{
		parameters = new Object();
	}
	
	parameters.type = type;
	
	var response = $.ajax({
		type: "POST",
		url: "./common/javascript/ajax/utilities.php",
		data: parameters,
		async: false
	}).responseText;
	
	return eval('(' + response + ')');
}

function renderFckEditor(name, options)
{
	var defaults = {
			width: '100%',
			height: '100',
			fullPage: false,
			toolbarSet: 'Basic',
			toolbarExpanded: true,
			value: ''
	};
	
	var options = $.extend(defaults, options);
	
	var oFCKeditor = new FCKeditor(name);
	oFCKeditor.BasePath = getPath('WEB_PLUGIN_PATH') + 'html_editor/fckeditor/';
	oFCKeditor.Width = options.width;
	oFCKeditor.Height = options.height;
	oFCKeditor.Config[ "FullPage" ] = options.fullPage;
	oFCKeditor.Config[ "DefaultLanguage" ] = options.language ;
	if(options.value)
	{
		oFCKeditor.Value = options.value;
	}
	else
	{
		oFCKeditor.Value = "";
	}
	oFCKeditor.ToolbarSet = options.toolbarSet;
	oFCKeditor.Config[ "SkinPath" ] = oFCKeditor.BasePath + 'editor/skins/' + getTheme() + '/';
	oFCKeditor.Config["CustomConfigurationsPath"] = getPath('WEB_LIB_PATH') + 'configuration/html_editor/fckconfig.js';
	oFCKeditor.Config[ "ToolbarStartExpanded" ] = options.toolbarExpanded;
	
	return oFCKeditor.CreateHtml();
}