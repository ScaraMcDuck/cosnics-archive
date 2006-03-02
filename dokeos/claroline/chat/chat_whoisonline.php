<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert

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
*	Shows the list of connected users
*
*	@author Olivier Brouckaert
*	@package dokeos.chat
==============================================================================
*/

define('FRAME','online');

$langFile='chat';

include('../inc/claro_init_global.inc.php');

$showPic=intval($_GET['showPic']);

$tbl_course_user	= Database::get_main_table(MAIN_COURSE_USER_TABLE);
$tbl_user		= Database::get_main_table(MAIN_USER_TABLE);
$tbl_chat_connected	= Database::get_course_chat_connected_table();

$query="SELECT username FROM $tbl_user WHERE user_id='$_uid'";
$result=api_sql_query($query,__FILE__,__LINE__);

list($pseudoUser)=mysql_fetch_row($result);

$isAllowed=(empty($pseudoUser) || !$_cid)?false:true;
$isMaster=$is_courseAdmin?true:false;

if(!$isAllowed)
{
	exit();
}

$pictureURL=api_get_path(WEB_CODE_PATH).'upload/users/';

$query="SELECT t1.user_id,username,firstname,lastname,picture_uri,t3.status FROM $tbl_user t1,$tbl_chat_connected t2,$tbl_course_user t3 WHERE t1.user_id=t2.user_id AND t3.user_id=t2.user_id AND t3.course_code = '".$_course[sysCode]."' AND t2.last_connection>'".date('Y-m-d H:i:s',time()-60*5)."' ORDER BY username";
$result=api_sql_query($query,__FILE__,__LINE__);

$Users=api_store_result($result);

include('header_frame.inc.php');
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">

<?php
foreach($Users as $enreg)
{
?>

<tr>
  <td width="1%" rowspan="2" valign="middle"><img src="../img/whoisonline.png" border="0" alt="" style="margin-right: 3px;"></td>
  <td width="99%"><a <?php if($enreg['status'] == 1) echo 'class="master"'; ?> name="user_<?php echo $enreg['user_id']; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?showPic=<?php if($showPic == $enreg['user_id']) echo '0'; else echo $enreg['user_id']; ?>#user_<?php echo $enreg['user_id']; ?>"><b><?php echo ucfirst($enreg['lastname']).' '.ucfirst($enreg['firstname']); ?></b></a></td>
</tr>

<?php if($showPic == $enreg['user_id']): ?>
<tr>
  <td colspan="2" align="center"><img src="<?php if(empty($enreg['picture_uri'])) echo '../img/unknown.jpg'; else echo $pictureURL.$enreg['picture_uri']; ?>" border="0" width="100" alt="" style="margin-top: 5px;"></td>
</tr>
<?php endif; ?>

<tr>
  <td height="5"></td>
</tr>

<?php
}

unset($Users);
?>

</table>

<?php
include('footer_frame.inc.php');
?>
