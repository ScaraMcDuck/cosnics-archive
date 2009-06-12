String.prototype.getBytes = function() {
    return encodeURIComponent(this).replace(/%../g, 'x').length;
};


( function($) 
{
	var default_size = 20, tableElement, tableBody;
	
	function imatch(string, regexp, flags, doubleReturn)
	{
		if(typeof(string)!="string" || !regexp)
		{
			return null;
		};
		
		flags = (flags && typeof(flags) == "string") ? flags : "";
		
		var re = (typeof(regexp) == "string") ? new RegExp(regexp, flags) : regexp;
		var matches = string.match(re);
		
		if(!matches)
		{
			return null;
		
		}
		var found = 0;
		var indexes = new Array(matches.length);
		
		for(var m = 0; m < matches.length; m++)
		{
			found = string.substring(0, found).length;
			//alert("Length previous: " + found);
			indexes[m] = found + string.substring(found).search(re);
			//alert(string.substring(found));
			//alert(string.substring(found).search(re));
			//alert("index: " + indexes[m]);
			//alert("match: " + matches[m]);
			//alert("match-length: " + matches[m].length);
			found = indexes[m] + matches[m].length;
		}
		
		return (!doubleReturn)? indexes: [indexes, matches];
		/*keep this comment to use freely
		http://www.fullposter.com/?1 */
	}
	
	
	var answer_changed = function(ev, ui) 
	{   
		var value = $(this).attr('value');
	    var pattern = /\[[a-zA-Z0-9_\s\-]*\]/g;

	    var result = imatch(value, pattern, "", true);
	    
	    alert(encodeURIComponent(value));
	    alert(encodeURIComponent(value).replace(/%../g, 'x'));
	    alert(encodeURIComponent(value).replace(/%../g, 'x').length);
	    exit;
	    
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