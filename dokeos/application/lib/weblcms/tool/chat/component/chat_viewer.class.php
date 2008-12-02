<?php

require_once dirname(__FILE__) . '/../chat_tool.class.php';
require_once dirname(__FILE__) . '/../chat_tool_component.class.php';
require_once Path :: get_plugin_path() . '/phpfreechat/src/phpfreechat.class.php';

class ChatToolViewerComponent extends ChatToolComponent
{
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$params = array();

		$params["data_public_url"] = Path :: get(WEB_PATH)  . 'plugin/phpfreechat/data/public';
		$params["data_public_path"] = Path :: get(SYS_PATH)  . 'plugin/phpfreechat/data/public';
		$params["server_script_url"] = $_SERVER['REQUEST_URI'];
		$params["serverid"] = md5(__FILE__); // used to identify the chat
		$params["isadmin"] = true; // set wether the person is admin or not
		$params["title"] = "Dokeos 2.0 Chat"; // title of the chat
		$params["nick"] = ""; // ask for nick at the user
		$params["frozen_nick"] = true; //forbid the user to change his/her nickname later
		$params["channels"] = array("Dokeos 2.0");
		$params["max_channels"] = 1;
		$params["theme"] = "blune";
		$params["display_pfc_logo"] = false;
		$params["display_ping"] = false;
		$params["displaytabclosebutton"] = false;
		$params["btn_sh_whosonline"] = false;
		$params["btn_sh_smileys"] = false;
		//$params["debug"] = true;
		
		$chat = new phpFreeChat($params);
		
		$this->display_header(new BreadCrumbTrail());
		$chat->printChat();
		$this->display_footer();
	}
	
}
?>