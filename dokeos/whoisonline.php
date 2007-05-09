<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) various contributors

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
==============================================================================
* Who is online list
*
* @todo clean code, do not use variables $t or $p !!!
==============================================================================
*/

$langFile = "index";

include_once("./main/inc/claro_init_global.inc.php");

$track_user_table = Database::get_main_table(MAIN_USER_TABLE);

if ($_GET["chatid"] != '') {

	//send out call request
	$time = time();
	$time = date("Y-m-d H:i:s", $time);
	$chatid = addslashes($_GET['chatid']);
	$sql="update $track_user_table set chatcall_user_id = '".mysql_real_escape_string($_uid)."', chatcall_date = '".mysql_real_escape_string($time)."', chatcall_text = '' where (user_id = ".mysql_real_escape_string($chatid).")";
	$result=api_sql_query($sql,__FILE__,__LINE__);

	//redirect caller to chat
	header("Location: ".$clarolineRepositoryAppend."chat/chat.php?cidReq=".$_cid."&origin=whoisonline&target=$chatid");
	exit();
}

//include (api_get_library_path().'/fileManage.lib.php');

/*
-----------------------------------------------------------
	Header
	include the HTTP, HTML headers plus the top banner
-----------------------------------------------------------
*/

Display::display_header(get_lang('UsersOnLineList'));
/**
*	Display productions in whoisonline
*
*	@param $_uid, User id
*/
function display_productions($_uid)
{
	global $clarolineRepositorySys, $clarolineRepositoryWeb, $disabled_output;
	$sysdir=$clarolineRepositorySys."upload/users/$_uid";
	$webdir=$clarolineRepositoryWeb."upload/users/$_uid";

	if( !is_dir($sysdir))
	{
		mkpath($sysdir);
	}
	$handle = opendir($sysdir);
	$tableout=false;
	while ($file = readdir($handle))
	{
		if ($file == "." || $file == ".." || $file == '.htaccess')
		{
			continue;						// Skip current and parent directories
		}
		$filename=urlencode($file);
		if (!$tableout) {
			echo "<tr><td align=right>".get_lang('Productions')." : </td><td colspan=2><table cellspacing=0 cellpadding=0>";
			echo "<tr><td><a href=\"$webdir/$filename\" target=_blank>".$file."</a></td></tr>";
			$tableout=true;
			$wasfile=true;
		}
		else
		{
		echo "<tr><td><a href=\"$webdir/$filename\" target=_blank>".$file."</a></td></tr>";
		}
	}
	if ($wasfile==true) {
		echo "</table></td></tr>";
	}

}


/*$now=time();

$insql="select * from $track_login_table where ( DAYOFYEAR(login_date) = DAYOFYEAR(FROM_UNIXTIME('$now')) )";
$inresult=api_sql_query($insql);
$loginnumber=mysql_num_rows($inresult);

$outsql="select * from $track_logout_table where ( DAYOFYEAR(logout_date) = DAYOFYEAR(FROM_UNIXTIME('$now')) )";
$outresult=api_sql_query($outsql);
$logoutnumber=mysql_num_rows($outresult);

$number=$loginnumber-$logoutnumber;*/

// Who is logged in?
$MINUTE = 30;

// This if statement prevents users accessing the who's online feature when it has been disabled.
if ((get_setting('showonline','world') == "true" AND !$_uid) OR (get_setting('showonline','users') == "true" AND $_uid))
{
	$userlist = WhoIsOnline($_uid,$statsDbName,$MINUTE);

	$total=count($userlist);

	if (isset($_GET["id"]) && !IsValidUser($_GET["id"]))
	{
		api_display_tool_title(get_lang('UsersOnLineList'));
		echo "<table width=100%>";
		echo "<tr><td align=left>&nbsp;<b>".get_lang('TotalOnLine')." : $total</b></td>";
		echo "<td align=right>";
		if ($_GET["id"]=='')
		{
			echo "<A HREF=\"javascript:window.location.reload()\">".get_lang('Refresh')."</A>";
		}
		else
		{
			if(0) // if ($_uid && $_GET["id"] != $_uid)
			{
				echo "<A HREF=\"".$_SERVER['PHP_SELF']."?chatid=".$_GET["id"]."\">".get_lang('SendChatRequest')."</A>";
			}
		}
		echo "&nbsp;</td></tr></table><br />";
	}

	if ($userlist!=false)
	{
		//$imgurl = ClearURL(getURL(api_get_path(WEB_PATH)).$REQUEST_URI);
		echo "<TABLE border=0 width=80% align=center cellspacing=3 cellpadding=3>";
		$online=1;
	//	if (!IsValidUser($_GET["id"]))
		if ($_GET["id"]=='')
		{
				foreach($userlist as $row)
					//$row[0] - the user_id ; $row[1] - last login date
				{

					$uid=$row[0];
					$name=GetFullUserName($row[0]).($_uid==$row[0]?("&nbsp;<b>(".get_lang('Me').")</b>"):(""));

					//$row[1] is the last click timestamp
					
					$udm = UsersDataManager :: get_instance();
					$user = $udm->retrieve_user($uid);
					
					if ($user->get_status()==1) { $status=get_lang('Teacher'); }
					else { $status=get_lang('Student'); }

					$fullurl=api_get_path(WEB_CODE_PATH)."upload/users/".$user->get_picture_uri();
					$system_image_path=api_get_path(SYS_CODE_PATH)."upload/users/".$user->get_picture_uri();
					list($width, $height, $type, $attr) = @getimagesize($system_image_path);
					$height+=30;
					$width+=30;
					$windowname="window".$height;
					$window="$windowname=window.open(\"$fullurl\",\"$windowname\",\"alwaysRaised=yes, alwaysLowered=no,alwaysOnTop=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=$width,height=$height,left=200,top=20\"); return false;";
					$name="<a href=\"".$_SERVER['PHP_SELF']."?id=$row[0]\">".$name."</a>";

					echo "<tr><td>".$online."</td><td>".$name."</td><td>". Display::encrypted_mailto_link($user->get_email(),$user->get_email()) ."</td><td>".$status."</td></tr>";

					$online++;
				}
		}
		else   //individual list
		{

					$name=GetFullUserName($_GET["id"]).($_uid==$_GET["id"]?("&nbsp;<b>(".get_lang('Me').")</b>"):(""));
					$alt=GetFullUserName($_GET["id"]).($_uid==$_GET["id"]?("&nbsp;(".get_lang('Me').")"):(""));
					//$row[1] is the last click timestamp
					
					$udm = UsersDataManager :: get_instance();
					$user = $udm->retrieve_user($_GET["id"]);
					
					if ($user->get_status()==1) { $status=get_lang('Teacher'); }
					else { $status=get_lang('Student'); }
					if ($user->get_picture_uri()<>'')
					{
						$fullurl=api_get_path(WEB_CODE_PATH)."upload/users/".$user->get_picture_uri();
						$system_image_path=api_get_path(SYS_CODE_PATH)."upload/users/".$user->get_picture_uri();

						list($width, $height, $type, $attr) = getimagesize($system_image_path);
						$resizing = (($height > 200) ? 'height="200"' : '');
						$height+=30;
						$width+=30;
						$windowname="window".$height;
						$window="$windowname=window.open(\"$fullurl\",\"$windowname\",\"alwaysRaised=yes, alwaysLowered=no,alwaysOnTop=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=$width,height=$height,left=200,top=20\"); return false;";
						//$name="<a href='#' onClick='".$window."'>".$name."</a>";
						echo "<tr><td colspan=3 align=center><img src=$fullurl $resizing alt=\"".$alt."\"></td></tr><tr><td>&nbsp;</td></tr>";
					}
					echo "<tr><td>".$name."</td><td align=center>". Display::encrypted_mailto_link($user->get_email(),$user->get_email()) ."</td><td align=right>".$status."</td></tr>";

					$online++;

		}
		echo "</TABLE><BR>";
	}
} // if ((get_setting('showonline','world') == "true" AND !$_uid) OR (get_setting('showonline','users') == "true" AND $_uid))
else
{
	Display::display_error_message(get_lang('AccessNotAllowed'));
}
$referer=empty($_GET['referer'])?'index.php':urlencode($_GET['referer']);

echo "<table width=100%>";
echo "<tr><td align=left>&nbsp;<a href=\"".($_GET['id']?"javascript:window.history.back();":$referer)."\">< ".get_lang('Back')."</a></td>";
echo "<td align=right><a href=\"javascript:window.location.reload()\">".get_lang('Refresh')."</a>&nbsp;</td></tr></table><br />";

/*echo "<table width=65% align=center>";
$online=0;
while ($inrow=mysql_fetch_array($inresult)) { //we take the today logins one by one

	$id=$inrow['login_user_id'];
	$checksql="select * from $track_logout_table where ( logout_user_id = $id )";
	//echo $checksql;
	$checkresult=api_sql_query($checksql);
	$out=false;
	if (mysql_num_rows($checkresult)>0) { //we take the logouts of the given user
		for ($i=1; $i<(mysql_num_rows($checkresult)+1); $i++) { $checkrow=mysql_fetch_array($checkresult); }
			//we take the last logout of this user and check if that is newer than the current login or not
		if ( ($checkrow['logout_date']) > ($inrow['login_date']) ) { $out=true; } else { $out=false; }
	}

	if (!$out) {
		$online++;
		$sql="select * from $track_user_table where ( user_id = $id )";
		$result=api_sql_query($sql,__FILE__,__LINE__);
		$row=mysql_fetch_array($result);
		if ($row['status']==1) { $status='teacher'; } else { $status='student'; }
		if ($row['picture_uri']<>'') { $img="<img src='main/upload/users/".$row['picture_uri']."'>"; } else { $img=''; }
		echo "<tr><td>$online.</td><td align=right>{$row['lastname']}</td><td>{$row['firstname']}</td><td align=center><a href=mailto:{$row['email']}>{$row['email']}</a></td><td>$status</td><td>$img</td></tr>";

	}

}

echo "</table>";*/

/*
==============================================================================
		FOOTER
==============================================================================
*/

Display::display_footer();
?>
