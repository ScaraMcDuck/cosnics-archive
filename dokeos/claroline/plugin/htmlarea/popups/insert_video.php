<?php
include_once('../../../inc/claro_init_global.inc.php');

include_once(api_get_library_path().'/fileUpload.lib.php');
include_once(api_get_library_path().'/document.lib.php');

$video_url=$_GET['video_url'];
$width=isset($_GET['width'])?intval($_GET['width']):260;
$height=isset($_GET['height'])?intval($_GET['height']):170;
$video_type=isset($_GET['video_type'])?$_GET['video_type']:'quicktime';
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

if(!is_dir($doc_path.'videos'))
{
	if(is_file($doc_path.'videos'))
	{
		@unlink($doc_path.'videos');
	}

	@mkdir($doc_path.'videos',0777);
	@chmod($doc_path.'videos',0777);

	if($_cid)
	{
		$doc_id=add_document($_course,str_replace(api_get_path(SYS_COURSE_PATH).$_course['path'].'/document','',$doc_path).'videos','folder',0,'videos');

		api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'FolderCreated', $_uid);
	}
}

if($_POST['sent'])
{
	$video_file=$_FILES['video_file'];
	$video_width=$_POST['video_width'];
	$video_height=$_POST['video_height'];

	if(strstr($video_file['type'],'video') || strstr($video_file['type'],'real'))
	{
		$video_file['name']=replace_dangerous_char($video_file['name'],'strict');

		$extension=explode('.',$video_file['name']);

		$extension=$extension[sizeof($extension)-1];

		$suffix='';
		$i=0;

		do
		{
			if(file_exists($doc_path.'videos/'.str_replace('.'.$extension,$suffix.'.'.$extension,$video_file['name'])))
			{
				$suffix='_'.(++$i);
			}
			else
			{
				break;
			}
		}
		while(1);

		$video_file['name']=str_replace('.'.$extension,$suffix.'.'.$extension,$video_file['name']);

		move_uploaded_file($video_file['tmp_name'],$doc_path.'videos/'.$video_file['name']);

		if($_cid)
		{
			$doc_id=add_document($_course,str_replace(api_get_path(SYS_COURSE_PATH).$_course['path'].'/document','',$doc_path).'videos/'.$video_file['name'],'file',filesize($doc_path.'videos/'.$video_file['name']),$video_file['name']);

			api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'DocumentAdded', $_uid);
		}

		$video_url=str_replace(api_get_path(SYS_PATH),api_get_path(WEB_PATH),$doc_path).'videos/'.$video_file['name'];

		$ext=explode('.',$video_url);
		$ext=$ext[sizeof($ext)-1];

		if($ext == 'rm' || $ext == 'ram')
		{
			$video_type='real';
		}
		else
		{
			$video_type='quicktime';
		}

		header('Location: insert_video.php?doc_path='.urlencode($doc_path).'&video_url='.urlencode($video_url).'&video_type='.$video_type.'&width='.$video_width.'&height='.$video_height);
		exit();
	}
	else
	{
		header('Location: insert_video.php?doc_path='.urlencode($doc_path));
		exit();
	}
}
?>

<html>

<head>
  <title>Insert Video</title>

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
      document.getElementById("f_vert").value = param["f_vert"];
      document.getElementById("f_horiz").value = param["f_horiz"];
      window.ipreview.location.replace(param.f_url);
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
  var fields = ["f_url", "f_horiz", "f_vert", "f_type"];
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

function onPreview() {
  var f_url = document.getElementById("f_url");
  var url = f_url.value;
  if (!url) {
    alert("You have to enter an URL first");
    f_url.focus();
    return false;
  }
  window.ipreview.location.replace(url);
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

<form name="formUpload" method="post" action="insert_video.php?doc_path=<?php echo urlencode($doc_path); ?>" enctype="multipart/form-data" onsubmit="javascript:if(document.getElementById) { document.getElementById('loading').style.visibility='visible'; } document.formUpload.video_width.value=document.formConfig.horiz.value; document.formUpload.video_height.value=document.formConfig.vert.value; document.formUpload.upload.value='Wait...'; document.formUpload.upload.disabled=true;">
<input type="hidden" name="sent" value="1">
<input type="hidden" name="video_width" value="260">
<input type="hidden" name="video_height" value="170">
<div class="title"><span>Insert Video</span> <img id="loading" src="<?php echo api_get_path(WEB_CODE_PATH); ?>img/loading.gif" border="0" style="visibility: hidden;"></div>
<!--- new stuff --->
<table border="0" cellpadding="2" cellspacing="0" align="center">
  <tbody>

  <tr>
    <td><span>Video file</span> :</td>
    <td nowrap="nowrap"><input type="file" name="video_file" size="25" value="" /><input type="submit" name="upload" value="Upload"></td>
  </tr>

  </tbody>
</table>
</form>

<form name="formConfig" action="" method="get">
<p />

<input type="hidden" name="url" id="f_url" value="<?php echo $video_url; ?>">
<input type="hidden" name="type" id="f_type" value="<?php echo $video_type; ?>">

<fieldset style="float:left; margin-right: 5px; width:200px;">
<legend><span>Dimensions</span></legend>

<div class="space"></div>

<div class="fr"><nobr><span>Width</span>: <input type="text" name="horiz" id="f_horiz" size="5" value="<?php echo $width; ?>" <?php if(!empty($video_url)) echo 'onchange="javascript:window.ipreview.document.location.href=\'video_preview.php?url='.urlencode($video_url).'&type=\'+document.formConfig.type.value+\'&width=\'+document.formConfig.horiz.value+\'&height=\'+document.formConfig.vert.value;"'; ?> />&nbsp;&nbsp;<span>Height</span>: <input type="text" name="vert" id="f_vert" size="5" value="<?php echo $height; ?>" <?php if(!empty($video_url)) echo 'onchange="javascript:window.ipreview.document.location.href=\'video_preview.php?url='.urlencode($video_url).'&type=\'+document.formConfig.type.value+\'&width=\'+document.formConfig.horiz.value+\'&height=\'+document.formConfig.vert.value;"'; ?> /></nobr></div>

<div class="space"></div>

</fieldset>
<br clear="all" />
<table width="100%" style="margin-bottom: 0.2em">
 <tr>
  <td valign="bottom">
    <span>Video Preview</span>:<br />
    <iframe name="ipreview" id="ipreview" frameborder="0" style="border : 1px solid gray;" height="200" width="300" src="<?php if(!empty($video_url)) echo 'video_preview.php?url='.urlencode($video_url).'&type='.$video_type.'&width='.$width.'&height='.$height; ?>"></iframe>
  </td>
  <td valign="bottom" style="text-align: right">
    <button type="button" name="ok" onclick="return onOK();" <?php if(empty($video_url)) echo 'disabled="disabled"'; ?> >OK</button><br>
    <button type="button" name="cancel" onclick="return onCancel();">Cancel</button>
  </td>
 </tr>
</table>
</form>
</body>
</html>
