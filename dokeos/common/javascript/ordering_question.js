/*global $, document, FCKeditor, renderFckEditor, getPath, getTranslation, getTheme */

$(function ()
{
    var skippedOptions = 0, baseWebPath = getPath('WEB_PATH');
    
    function getDeleteIcon()
    {
		return $('.data_table tbody tr:first td:last .remove_option').attr('src').replace('_na.png', '.png');
    }
    
    function getSelectOptions()
    {
		return $('.data_table tbody tr:first select').html();
    }
    
    function processItems()
    {
    	var deleteImage, deleteField, rows;
    	
		deleteImage = '<img class="remove_option" src="' + getDeleteIcon().replace('.png', '_na.png') + '"/>';
		deleteField = '<input id="$option_number" class="remove_option" type="image" src="' + getDeleteIcon() + '" name="remove[$option_number]" />';
		rows = $('.data_table tbody tr');
	
		if (rows.size() <= 2)
		{
		    deleteField = deleteImage;
		}
	
		rows.each(function ()
		{
			var rankFieldName, id, appendField;
		    
			rankFieldName = $('select[name*="option_rank"]', this).attr('name');
		    id = rankFieldName.substr(12, rankFieldName.length - 13);
		    appendField = deleteField.replace(/\$option_number/g, id);
	
		    $('.remove_option', this).remove();
		    $('td:last', this).append(appendField);
		});
    }

    function removeOption(ev, ui) {
    	ev.preventDefault();

		var tableBody, id, rows, row, response;
	
		tableBody = $(this).parent().parent().parent();
		id = $(this).attr('id');
		$(this).parent().parent().remove();
	
		rows = $('tr', tableBody);
	
		row = 0;
	
		response = $.ajax({
		    type : "POST",
		    url : baseWebPath + "common/javascript/ajax/ordering_question.php",
		    data : {
				action : 'skip_option',
				value : id
		    },
		    async : false
		}).responseText;
	
		rows.each(function () {
		    var rowClass = row % 2 === 0 ? 'row_even' : 'row_odd';
		    $(this).attr('class', rowClass);
		    row++;
		});
	
		skippedOptions++;
	
		processItems();
    }

    function addOption(ev, ui) {
		ev.preventDefault();
		
		var	numberOfOptions, newNumber, response, rowClass, id, fieldAnswer,
			fieldOrder, fieldDelete, string, parameters, editorName;
	
		numberOfOptions = $('#ordering_number_of_options').attr('value');
		newNumber = parseInt(numberOfOptions, 10) + 1;
	
		response = $.ajax({
		    type : "POST",
		    url : baseWebPath + "common/javascript/ajax/memory.php",
		    data : {
				action : 'set',
				variable : 'ordering_number_of_options',
				value : newNumber
		    },
		    async : false
		}).responseText;
	
		$('#ordering_number_of_options').attr('value', newNumber);
	
		rowClass = (numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even' : 'row_odd';
		id = 'correct[' + numberOfOptions + ']';
		
		parameters = { width: '100%', height: '65', toolbarSet: 'RepositoryQuestion', toolbarExpanded: false};
		editorName = 'option[' + numberOfOptions + ']';
	
		fieldAnswer = renderFckEditor(editorName, parameters);
		fieldOrder = '<select name="option_rank[' + numberOfOptions + ']"></select>';
		fieldDelete = '<input id="' + numberOfOptions + '" class="remove_option" type="image" src="' + getDeleteIcon() + '" name="remove[' + numberOfOptions + ']" />';
		string = '<tr class="' + rowClass + '"><td>' + fieldAnswer + '</td><td>' + fieldOrder + '</td><td>' + fieldDelete + '</td></tr>';
	
		$('tbody', $('.data_table')).append(string);
	
		processItems();
    }

    $(document).ready(function ()
    {
		$('.remove_option').live('click', removeOption);
		$('#add_option').live('click', addOption);
		
		alert(getSelectOptions());
    });
    
});