<?php
include_once('../../../inc/claro_init_global.inc.php');

include_once(api_get_library_path().'/fileUpload.lib.php');
include_once(api_get_library_path().'/document.lib.php');

$audio_url=$_GET['audio_url'];
$doc_path=$_GET['doc_path'];

if(!ereg('^'.api_get_path(SYS_PATH),$doc_path) || !is_dir($doc_path))
{
	if(!empty($_course['path']))
	{
		$doc_path=api_get_path(SYS_COURSE_PATH).$_course['path'].'/document/';
	}
	else
	{
		$doc_path=api_get_path(SYS_CODE_PATH).'upload/';
	}
}

if(!is_dir($doc_path.'audio'))
{
	if(is_file($doc_path.'audio'))
	{
		@unlink($doc_path.'audio');
	}

	@mkdir($doc_path.'audio',0777);
	@chmod($doc_path.'audio',0777);

	if($_cid)
	{
		$doc_id=add_document($_course,str_replace(api_get_path(SYS_COURSE_PATH).$_course['path'].'/document','',$doc_path).'audio','folder',0,'audio');

		api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'FolderCreated', $_uid);
	}
}

if($_POST['sent'])
{
	$audio_file=$_FILES['audio_file'];

	if(strstr($audio_file['type'],'audio') && stristr($audio_file['name'],'.mp3'))
	{
		$audio_file['name']=replace_dangerous_char($audio_file['name'],'strict');

		$extension=explode('.',$audio_file['name']);

		$extension=$extension[sizeof($extension)-1];

		$suffix='';
		$i=0;

		do
		{
			if(file_exists($doc_path.'audio/'.str_replace('.'.$extension,$suffix.'.'.$extension,$audio_file['name'])))
			{
				$suffix='_'.(++$i);
			}
			else
			{
				break;
			}
		}
		while(1);

		$audio_file['name']=str_replace('.'.$extension,$suffix.'.'.$extension,$audio_file['name']);

		move_uploaded_file($audio_file['tmp_name'],$doc_path.'audio/'.$audio_file['name']);

		@copy(api_get_path(SYS_CODE_PATH).'plugin/mp3player/mp3player.swf',$doc_path.'mp3player.swf');

		if($_cid)
		{
			$doc_id=add_document($_course,str_replace(api_get_path(SYS_COURSE_PATH).$_course['path'].'/document','',$doc_path).'audio/'.$audio_file['name'],'file',filesize($doc_path.'audio/'.$audio_file['name']),$audio_file['name']);

			api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'DocumentAdded', $_uid);

			// this is not needed since the mp3player.swf does not have to be stored in the database nor does it has to be visible as a document)
			// see also http://www.dokeos.com/forum/viewtopic.php?p=19152#19152
			//$doc_id=add_document($_course,str_replace(api_get_path(SYS_COURSE_PATH).$_course['path'].'/document','',$doc_path).'mp3player.swf','file',filesize($doc_path.'mp3player.swf'),'mp3player.swf');
			//api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'DocumentAdded', $_uid);
		}

		$audio_url=str_replace(api_get_path(SYS_PATH),api_get_path(WEB_PATH),$doc_path).'audio/'.$audio_file['name'];

		header('Location: insert_audio.php?doc_path='.urlencode($doc_path).'&audio_url='.urlencode($audio_url));
		exit();
	}
	else
	{
		header('Location: insert_audio.php?doc_path='.urlencode($doc_path));
		exit();
	}
}
?>

<html>

<head>
  <title>Insert MP3 file</title>

<script type="text/javascript" src="popup.js"></script>

<script type="text/javascript">

window.resizeTo(400, 100);

I18N = window.opener.HTMLArea.I18N.dialogs;

function i18n(str) {
  return (I18N[str] || str);
};

function Init() {
  __dlg_translate(I18N);
  __dlg_init();
  var param = window.dialogArguments;
  if (param) {
      document.getElementById("f_url").value = param["f_url"];
  }
};

function onOK() {
  var required = {
    "f_url": "You must enter the URL"
  };
  for (var i in required) {
    var el = document.getElementById(i);
    if (!el.value) {
      alert(required[i]);
      el.focus();
      return false;
    }
  }
  // pass data back to the calling window
  var fields = ["f_url"];
  var param = new Object();
  for (var i in fields) {
    var id = fields[i];
    var el = document.getElementById(id);
    param[id] = el.value;
  }
  __dlg_close(param);
  return false;
};

function onCancel() {
  __dlg_close(null);
  return false;
};
</script>

<style type="text/css">
html, body {
  background: ButtonFace;
  color: ButtonText;
  font: 11px Tahoma,Verdana,sans-serif;
  margin: 0px;
  padding: 0px;
}
body { padding: 5px; }
table {
  font: 11px Tahoma,Verdana,sans-serif;
}
form p {
  margin-top: 5px;
  margin-bottom: 5px;
}
.fl { width: 5em; float: left; padding: 2px 5px; text-align: right; }
.fr { float: left; padding: 2px 5px; text-align: right; }
fieldset { padding: 0px 10px 5px 5px; }
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
button { width: 70px; }
.space { padding: 2px; }

.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px;
}
form { padding: 0px; margin: 0px; }
</style>

</head>

<body onload="Init()">

<form name="formUpload" method="post" action="insert_audio.php?doc_path=<?php echo urlencode($doc_path); ?>" enctype="multipart/form-data" onsubmit="javascript:if(document.getElementById) { document.getElementById('loading').style.visibility='visible'; } document.formUpload.upload.value='Wait...'; document.formUpload.upload.disabled=true;">
<input type="hidden" name="sent" value="1">
<div class="title"><span>Insert MP3 file</span> <img id="loading" src="<?php echo api_get_path(WEB_CODE_PATH); ?>img/loading.gif" border="0" style="visibility: hidden;"></div>
<!--- new stuff --->
<table border="0" cellpadding="2" cellspacing="0" align="center">
  <tbody>

  <tr>
    <td><span>MP3 file</span> :</td>
    <td nowrap="nowrap"><input type="file" name="audio_file" size="25" value="" /><input type="submit" name="upload" value="Upload"></td>
  </tr>

  </tbody>
</table>
</form>

<form name="formConfig" action="" method="get">
<p />

<input type="hidden" name="url" id="f_url" value="<?php echo $audio_url; ?>">

<table width="100%" style="margin-bottom: <?php if(!strstr($_SERVER['HTTP_USER_AGENT'],'MSIE')) echo '62'; else echo '10'; ?>px;">
 <tr>
  <td valign="bottom" align="center">
    <button type="button" name="ok" onclick="return onOK();" <?php if(empty($audio_url)) echo 'disabled="disabled"'; ?> >OK</button>
    &nbsp;<button type="button" name="cancel" onclick="return onCancel();">Cancel</button>
  </td>
 </tr>
</table>
</form>
</body>
</html>
