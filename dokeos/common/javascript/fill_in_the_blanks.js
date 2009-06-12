( function($) 
{
	var default_size = 20, tableElement, tableBody;
	
	var answer_changed = function(ev, ui) 
	{   
		var value = $(this).attr('value');
	    var pattern = /\[[a-zA-Z0-9_\s\-]*\]/g;

	    var result = value.match(pattern);
	    tableBody.empty();
    	 
	    if(result && result.length > 0)
	    {  
	    	tableElement.css('display', 'block');	    	
	     
		    for(var i = 0; i < result.length; i++)
		    {
		    	add_match_to_table(result[i], i);
		    }
	    }
	    else
	    {
	    	tableElement.css('display', 'none');	    	
	    }
	      
	    return true;
	} 
	
	function add_match_to_table(match, matchnumber)
	{
		var string =   	'<tr><td>' + match + '<input type="hidden" name="match[' + matchnumber + ']" value="' + match + '" /></td>';
			string +=  	'<td><div style="display: inline;">';
			
		var oFCKeditor = new FCKeditor( 'comment[' + matchnumber + ']' );
			oFCKeditor.BasePath = "http://localhost/lcms/plugin/html_editor/fckeditor/";
			oFCKeditor.Width = "100%";
			oFCKeditor.Height = 65;
			oFCKeditor.Config[ "FullPage" ] = false;
			oFCKeditor.Config[ "DefaultLanguage" ] = "en" ;
			oFCKeditor.Value = "";
			oFCKeditor.ToolbarSet = "RepositoryQuestion";
			oFCKeditor.Config[ "SkinPath" ] = oFCKeditor.BasePath + "editor/skins/aqua/";
			oFCKeditor.Config["CustomConfigurationsPath"] = "http://localhost/lcms/common/configuration/html_editor/fckconfig.js";
			oFCKeditor.Config[ "ToolbarStartExpanded" ] = false;
			
			string +=	oFCKeditor.CreateHtml();
			string +=	'</div></td>';
			string +=	'<td><input size="2" name="match_weight[' + matchnumber + ']" type="text" value="1" /></td>';
			string +=   '<td><input size="2" name="size[' + matchnumber + ']" type="text" value="' + default_size + '" /></td></tr>';
		
		tableBody.append(string);
		
	}

	$(document).ready( function() 
	{
	    tableElement = $("#answers_table");
	    tableBody = $("tbody", tableElement);
		$(".answer").live('keyup', answer_changed);
		$(".add_matches").toggle();
	});
	
})(jQuery);