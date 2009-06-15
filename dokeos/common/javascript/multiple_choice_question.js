( function($) 
{
	var skipped_options = 0;
	
	function switch_clicked(ev, ui) 
	{   
		var answer_type = $('#mc_answer_type').attr('value');
		var new_type = 'radio';
		var new_label = translation('SwitchToCheckboxes', 'repository');
		
		if(answer_type == 'radio')
		{
			new_type = 'checkbox';
			new_label = translation('SwitchToRadioButtons', 'repository');
		}
		
		var counter = 0;
		
		$('.option').each(function()
		{
			var id = $(this).attr('id');
			
			var correct = 'correct[' + counter + ']';
			var value = 1;
			
			if(new_type == 'radio')
			{
				correct = 'correct';
				value = counter;
			}
			
			var new_field = '<input id="' + id + '" class="option" type="' + new_type + '" value="' + value + '" name="' + correct + '" />';
			var parent = $(this).parent();
			parent.empty();
			parent.append(new_field);
			counter++;
			
		});
		
		$('#mc_answer_type').attr('value', new_type);
		
		var response = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/memory.php",
			data: { action: 'set', variable: 'mc_answer_type', value: new_type },
			async: true
		}).responseText;
		
		$('.switch').attr('value', new_label);
		$('.switch').text(new_label);
		
		return false;
	} 
	
	function remove_option_clicked(ev, ui)
	{
		var table_body = $(this).parent().parent().parent();
		var id = $(this).attr('id');
		$(this).parent().parent().remove();
		
		tr_children = table_body.children();
		
		var row = 0;
		var answer_type = $('#mc_answer_type').attr('value');
		
		var response = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/mc_question.php",
			data: { action: 'skip_option', value: id },
			async: false
		}).responseText;
		
		tr_children.each(function()
		{
			var row_class = row % 2 == 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', row_class);
			
			row++;
		});
		
		skipped_options++;
		
		fix_delete_images();
		
		return false;
	}
	
	function add_fck_editor(name, number, defaultvalue)
	{
		var oFCKeditor = new FCKeditor( name + '[' + number + ']' );
		oFCKeditor.BasePath = "http://localhost/lcms/plugin/html_editor/fckeditor/";
		oFCKeditor.Width = "100%";
		oFCKeditor.Height = 65;
		oFCKeditor.Config[ "FullPage" ] = false;
		oFCKeditor.Config[ "DefaultLanguage" ] = "en" ;
		if(defaultvalue)
		{
			oFCKeditor.Value = defaultvalue;
		}
		else
		{
			oFCKeditor.Value = "";
		}
		oFCKeditor.ToolbarSet = "RepositoryQuestion";
		oFCKeditor.Config[ "SkinPath" ] = oFCKeditor.BasePath + "editor/skins/aqua/";
		oFCKeditor.Config["CustomConfigurationsPath"] = "http://localhost/lcms/common/configuration/html_editor/fckconfig.js";
		oFCKeditor.Config[ "ToolbarStartExpanded" ] = false;
		
		return oFCKeditor.CreateHtml();
	}
	
	function add_option_clicked(ev, ui)
	{
		var number_of_options = $('#mc_number_of_options').attr('value');
		var new_number = (parseInt(number_of_options) + 1);
		
		var response = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/memory.php",
			data: { action: 'set', variable: 'mc_number_of_options', value: new_number },
			async: false
		}).responseText;
		
		$('#mc_number_of_options').attr('value', new_number);
		
		var mc_answer_type = $('#mc_answer_type').attr('value');
		var row_class = (number_of_options - skipped_options) % 2 == 0 ? 'row_even' : 'row_odd';
		
		var name = 'correct[' + number_of_options + ']';
		var id = name;
		var value = 1;
		
		if(mc_answer_type == 'radio')
		{
			name = 'correct';
			value = number_of_options;
		}
		
		var option_field = '<input id="' + id + '" class="option" type="' + mc_answer_type + '" value="' + value + '" name="' + name + '" />';
		var answer_field = add_fck_editor('option', number_of_options, null);
		var comment_field = add_fck_editor('comment', number_of_options, null);
		var score_field = '<input class="input_numeric" type="text" value="1" name="option_weight[' + number_of_options + ']" size="2" />';
		var delete_field = '<input id="' + number_of_options + '" class="remove_option" type="image" src="http://localhost/lcms/layout/aqua/img/common/action_delete.png" name="remove[' + number_of_options + ']" />';
		
		var string = '<tr class="' + row_class + '"><td>' + option_field + '</td><td>' + answer_field + '</td><td>' + comment_field + 
					 '</td><td>' + score_field + '</td><td>' + delete_field + '</td></tr>';
		
		$('tbody', $('.data_table')).append(string);
		
		fix_delete_images();
		
		return false;
	}
	
	function fix_delete_images()
	{
		var na_delete_image = '<img src="http://localhost/lcms/layout/aqua/img/common/action_delete_na.png"/>';
		var delete_field = '<input id="$option_number" class="remove_option" type="image" src="http://localhost/lcms/layout/aqua/img/common/action_delete.png" name="remove[$option_number]" />';
		var body = $('tbody', $('.data_table'));
		var children = body.children();

		if(children.size() <= 2)
			var delete_field = na_delete_image;
		
		children.each(function()
		{
			var id = $('input[name*="option_weight"]', $(this)).attr('name');
			id = id.substr(14, id.length - 15);
			
			var append_field = delete_field.replace(/\$option_number/g, id);
			
			$(this).children().eq(4).empty();
			$(this).children().eq(4).append(append_field);
		});
		
	}

	$(document).ready( function() 
	{
		$('#change_answer_type').live('click', switch_clicked);
		$('.remove_option').live('click', remove_option_clicked);
		$('#add_option').live('click', add_option_clicked);
	});
	
	function translation(string, application) 
	{		
		var translated_string = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/translation.php",
			data: { string: string, application: application },
			async: false
		}).responseText;
		
		return translated_string;
	}
	
})(jQuery);