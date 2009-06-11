( function($) 
{
	var default_size = 20;
	
	var answer_changed = function(ev, ui) 
	{   
		var value = $(this).attr('value');
	    var pattern = /\[[a-zA-Z0-9_-]*\]/g;

	    var result = value.match(pattern);
	    var table = $(".data_table");
	    var body = $("tbody", table);
    	body.empty();
    	 
	    if(result && result.length > 0)
	    {  
	    	table.css('display', null);	    	
	     
		    for(var i = 0; i < result.length; i++)
		    {
		    	add_match_to_table(body, result[i], i);
		    }
	    }
	    else
	    {
	    	table.css('display', 'none');	    	
	    }
	      
	    return true;
	} 
	
	function add_match_to_table(body, match, matchnumber)
	{
		var string =   	'<tr><td>' + match + '<input type="hidden" name="match[' + matchnumber + ']" value="' + match + '" /></td>';
			string +=  	'<td style="padding: 5px; padding-right: 7px;"><div style="display: inline;">';
//			string +=	'<script type="text/javascript">';
//			string +=	'/* <![CDATA[ */';
//			string +=   '';	
//			string +=   'var oFCKeditor = new FCKeditor( \'comment[' + matchnumber + ']\' );';
//			string +=   'oFCKeditor.BasePath = "http://localhost/lcms/plugin/html_editor/fckeditor/";';
//			string +=	'oFCKeditor.Width = "100%";';
//			string +=	'oFCKeditor.Height = 65;';
//			string +=	'oFCKeditor.Config[ "FullPage" ] = false;';
//			string +=	'oFCKeditor.Config[ "DefaultLanguage" ] = "en" ;';
//			string +=	'oFCKeditor.Value = "";';
//			string +=	'oFCKeditor.ToolbarSet = "RepositoryQuestion";';
//			string +=	'oFCKeditor.Config[ "SkinPath" ] = oFCKeditor.BasePath + "editor/skins/aqua/";';
//			string +=	'oFCKeditor.Config["CustomConfigurationsPath"] = "http://localhost/lcms/common/configuration/html_editor/fckconfig.js";';
//			string +=	'oFCKeditor.Config[ "ToolbarStartExpanded" ] = false;';
//			string +=	'oFCKeditor.Create();';
//			string +=	'';	
//			string +=	'/* ]]> */';
//			string +=	'</script>';
//		 	string +=	'<noscript><textarea rows="5" cols="50" name="comment[' + matchnumber + ']"></textarea></noscript>';
			string +=   '<textarea style="width: 100%;" rows="3" name="comment[' + matchnumber + ']"></textarea>';
			string +=	'</div></td>';
			string +=	'<td><input size="2" name="match_weight[' + matchnumber + ']" type="text" value="1" /></td>';
			string +=   '<td><input size="2" name="size[' + matchnumber + ']" type="text" value="' + default_size + '" /></td></tr>';
		
		body.append(string);
		
	}

	$(document).ready( function() 
	{
		$(".answer").live('keyup', answer_changed);
		$(".add_matches").toggle();
	});
	
})(jQuery);