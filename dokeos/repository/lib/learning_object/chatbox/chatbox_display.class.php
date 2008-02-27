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
   		$html[] = '<form method="get" autocomplete="off">';
		$html[] = '<input type="text" name="message" id="message" style="width: 95%;text-align:left;"/>';
   		$html[] = '<input type="submit" id="submit" value="'.Translation :: get_lang('Ok').'"/>';
   		$html[] = '</form>';
		$html[] = "
<script language=\"JavaScript\" type=\"text/javascript\">
var loadChatContent = function()
{
	$.get('/dokeoslcms/repository/lib/learning_object/chatbox/chatbox_server.php?chatbox=".$this->get_learning_object()->get_id()."',{}, function(data)
		{
			$('#container').empty();
 			$('#container').append(data);
			try
			{
 				elements = $('#container').get();
 				container = elements[0];
 				container.scrollTop = container.scrollHeight;
			}
			catch(error){}
     		setTimeout(loadChatContent, 1000);
    	}
    );
}
$(function()
	{
		loadChatContent();
		$('#submit').bind('click',{}, function()
			{
				$.get('/dokeoslcms/repository/lib/learning_object/chatbox/chatbox_server.php?chatbox=".$this->get_learning_object()->get_id()."&message=' + $('#message').attr('value'));
				$('#message').attr('value','');
				return false;
			}
		);
	}
);
</script>";
		return implode("\n",$html);
	}
}
?>