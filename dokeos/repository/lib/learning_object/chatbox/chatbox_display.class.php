<?php
/**
 * @package repository.learningobject
 * @subpackage chatbox
 */
class ChatboxDisplay extends LearningObjectDisplay
{
	public function get_chatbox_html()
	{
		$html[] = '<div id="container" style="height: 200px; overflow: auto; border: 1px solid black;"></div>';
   		$html[] = '<form method="get">';
		$html[] = '<input type="text" name="message" id="message" style="width: 95%;"/>';
   		$html[] = '<input type="submit" id="submit" value="'.get_lang('Ok').'"/>';
   		$html[] = '</form>';
		$html[] =<<<END
<script language="JavaScript" type="text/javascript">
var loadChatContent = function()
{
	$.get('/dokeoslcms/repository/lib/learning_object/chatbox/chatbox_server.php',{}, function(data)
		{
			$('#container').empty()
 			$('#container').append(data);
 			elements = $('#container').get();
 			container = elements[0];
 			container.scrollTop = container.scrollHeight;
     		setTimeout(loadChatContent, 1000);
    	}
    );
}
$(function()
	{
		loadChatContent();
		$('#submit').bind('click',{}, function()
			{
				$.get('/dokeoslcms/repository/lib/learning_object/chatbox/chatbox_server.php?message=' + $('#message').attr('value'));
				$('#message').attr('value','');
				return false;
			}
		);
	}
);
</script>
END;
		return implode("\n",$html);
	}
}
?>