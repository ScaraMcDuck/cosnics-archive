( function($) 
{
	var skipped_options = 0;
	var skipped_matches = 0;
	var labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
	
	function remove_option_clicked(ev, ui)
	{
		var table_body = $(this).parent().parent().parent();
		var id = $(this).attr('id');
		$(this).parent().parent().remove();
		
		tr_children = table_body.children();
		
		var row = 0;
		
		var response = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/matching_question.php",
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
		
		fix_delete_option_images();
		
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
		var number_of_options = $('#mq_number_of_options').attr('value');
		var number_of_matches = $('#mq_number_of_matches').attr('value');
		var new_number = (parseInt(number_of_options) + 1);
		
		var response = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/memory.php",
			data: { action: 'set', variable: 'mq_number_of_options', value: new_number },
			async: false
		}).responseText;
		
		$('#mq_number_of_options').attr('value', new_number);
		
		var row_class = (number_of_options - skipped_options) % 2 == 0 ? 'row_even' : 'row_odd';
		
		var option_field = new_number;
		var answer_field = add_fck_editor('option', number_of_options, null);
		
		var matches_field =  '<select name="matches_to[' + number_of_options + ']">';
			
		var counter = 0;
			
		for(i = 0; i < number_of_matches; i++)
		{
			matches_field += '<option value="' + counter + '">' + labels[i] + '</option>';
			counter++;
		}
			
		matches_field += '</select>';
		
		var comment_field = add_fck_editor('comment', number_of_options, null);
		var score_field = '<input class="input_numeric" type="text" value="1" name="option_weight[' + number_of_options + ']" size="2" />';
		var delete_field = '<input id="' + number_of_options + '" class="remove_option" type="image" src="http://localhost/lcms/layout/aqua/img/common/action_delete.png" name="remove_option[' + number_of_options + ']" />';
		
		var string = '<tr class="' + row_class + '"><td>' + option_field + '</td><td>' + answer_field + '</td><td>'+ matches_field + '</td><td>' 
					 + comment_field + '</td><td>' + score_field + '</td><td>' + delete_field + '</td></tr>';
		
		$('tbody', $('.options')).append(string);
		
		fix_delete_option_images();
		
		return false;
	}
	
	function fix_delete_option_images()
	{
		var na_delete_image = '<img src="http://localhost/lcms/layout/aqua/img/common/action_delete_na.png"/>';
		var delete_field = '<input id="$option_number" class="remove_option" type="image" src="http://localhost/lcms/layout/aqua/img/common/action_delete.png" name="remove_option[$option_number]" />';
		var body = $('tbody', $('.options'));
		var children = body.children();

		if(children.size() <= 2)
			var delete_field = na_delete_image;
		
		var counter = 1;
		
		children.each(function()
		{
			var id = $('input[name*="option_weight"]', $(this)).attr('name');
			id = id.substr(14, id.length - 15);
			
			var append_field = delete_field.replace(/\$option_number/g, id);
			
			$(this).children().eq(5).empty();
			$(this).children().eq(5).append(append_field);
			
			$(this).children().eq(0).empty();
			$(this).children().eq(0).append(counter);
			
			counter++;
		});
		
	}
	
	function remove_match_clicked(ev, ui)
	{
		var table_body = $(this).parent().parent().parent();
		var id = $(this).attr('id');
		$(this).parent().parent().remove();
		
		tr_children = table_body.children();
		
		var row = 0;
		
		var response = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/matching_question.php",
			data: { action: 'skip_match', value: id },
			async: false
		}).responseText;
		
		tr_children.each(function()
		{
			var row_class = row % 2 == 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', row_class);
			
			row++;
		});
		
		var select_box = $('select[name*="matches_to"]');
		$('option[value="' + id + '"]', select_box).remove();
		
		
		select_box.each(function()
		{
			counter = 0;
			$(this).children().each(function()
			{
				$(this).text(labels[counter]);
				counter++;
			});
		});
		
		skipped_matches++;
		
		fix_matches_delete_images();
		
		return false;
	}
	
	function add_match_clicked(ev, ui)
	{
		var number_of_matches = $('#mq_number_of_matches').attr('value');
		var new_number = (parseInt(number_of_matches) + 1);
		
		var response = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/memory.php",
			data: { action: 'set', variable: 'mq_number_of_matches', value: new_number },
			async: false
		}).responseText;
		
		$('#mq_number_of_matches').attr('value', new_number);
		
		var row_class = (number_of_matches - skipped_matches) % 2 == 0 ? 'row_even' : 'row_odd';
		
		var option_field = labels[new_number] + '<input type="hidden" value="' + labels[new_number] + '" name="match_label[' + number_of_matches + ']" />';
		var answer_field = add_fck_editor('match', number_of_matches, null);
		var delete_field = '<input id="' + number_of_matches + '" class="remove_match" type="image" src="http://localhost/lcms/layout/aqua/img/common/action_delete.png" name="remove_match[' + number_of_matches + ']" />';
		
		var string = '<tr class="' + row_class + '"><td>' + option_field + '</td><td>' + answer_field + '</td><td>' + delete_field + '</td></tr>';
		
		$('tbody', $('.matches')).append(string);
		
		var select_box = $('select[name*="matches_to"]');
		select_box.append('<option value="' + number_of_matches + '">' + labels[number_of_matches - skipped_matches] + '</option>');
		
		fix_matches_delete_images();
		
		return false;
	}
	
	function fix_matches_delete_images()
	{
		var na_delete_image = '<img src="http://localhost/lcms/layout/aqua/img/common/action_delete_na.png"/>';
		var delete_field = '<input id="$option_number" class="remove_match" type="image" src="http://localhost/lcms/layout/aqua/img/common/action_delete.png" name="remove_match[$option_number]" />';
		var body = $('tbody', $('.matches'));
		var children = body.children();

		if(children.size() <= 2)
			var delete_field = na_delete_image;

		var counter = 0;
		
		children.each(function()
		{
			var name = $('input[name*="match_label"]', $(this)).attr('name');
			id = name.substr(12, name.length - 13);
	
			var append_field = delete_field.replace(/\$option_number/g, id);
			
			$(this).children().eq(2).empty();
			$(this).children().eq(2).append(append_field);
			
			$(this).children().eq(0).empty();
			$(this).children().eq(0).append(labels[counter] + '<input type="hidden" value="' + labels[counter] + '" name="' + name + '" />');
			
			counter++;
		});
		
	}
	
	function change_matrix_type_clicked()
	{
		var matrix_type = $('#mq_matrix_type').attr('value');
		var new_type = matrix_type == 1 ? 2 : 1 ;
		
		$('#mq_matrix_type').attr('value', new_type);
		
		if(new_type == 2)
		{
			$('.option_matches').attr('multiple', 'multiple');
			var new_label = translation('SwitchToSingleMatch', 'repository');
		}
		else
		{
			var new_label = translation('SwitchToMultipleMatches', 'repository');
			$('.option_matches').attr('multiple', null);
		}
		
		$('.change_matrix_type').attr('value', new_label);
		$('.change_matrix_type').text(new_label);
		
		var response = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/memory.php",
			data: { action: 'set', variable: 'mq_matrix_type', value: new_type },
			async: true
		}).responseText;
		
		return false;
	}

	$(document).ready( function() 
	{
		$('.remove_option').live('click', remove_option_clicked);
		$('#add_option').live('click', add_option_clicked);
		
		$('.remove_match').live('click', remove_match_clicked);
		$('#add_match').live('click', add_match_clicked);
		
		$('.change_matrix_type').live('click', change_matrix_type_clicked)
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