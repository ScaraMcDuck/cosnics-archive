<?php
include_once('../../../inc/claro_init_global.inc.php');

include_once(api_get_library_path().'/fileUpload.lib.php');
include_once(api_get_library_path().'/document.lib.php');

$image_url=$_GET['image_url'];
$width=isset($_GET['width'])?intval($_GET['width']):260;
$height=isset($_GET['height'])?intval($_GET['height']):170;
$align=isset($_GET['align'])?$_GET['align']:'baseline';
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

if(!is_dir($doc_path.'images'))
{
	if(is_file($doc_path.'images'))
	{
		@unlink($doc_path.'images');
	}

	@mkdir($doc_path.'images',0777);
	@chmod($doc_path.'images',0777);

	if($_cid)
	{
		$doc_id=add_document($_course,str_replace(api_get_path(SYS_COURSE_PATH).$_course['path'].'/document','',$doc_path).'images','folder',0,'images');

		api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'FolderCreated', $_uid);
	}
}

if($_POST['sent'])
{
	$image_file=$_FILES['image_file'];
	$image_width=$_POST['image_width'];
	$image_height=$_POST['image_height'];
	$image_align=$_POST['image_align'];

	if(strstr($image_file['type'],'image'))
	{
		$image_file['name']=replace_dangerous_char($image_file['name'],'strict');

		$extension=explode('.',$image_file['name']);

		$extension=$extension[sizeof($extension)-1];

		$suffix='';
		$i=0;

		do
		{
			if(file_exists($doc_path.'images/'.str_replace('.'.$extension,$suffix.'.'.$extension,$image_file['name'])))
			{
				$suffix='_'.(++$i);
			}
			else
			{
				break;
			}
		}
		while(1);

		$image_file['name']=str_replace('.'.$extension,$suffix.'.'.$extension,$image_file['name']);

		move_uploaded_file($image_file['tmp_name'],$doc_path.'images/'.$image_file['name']);

		if($_cid)
		{
			$doc_id=add_document($_course,str_replace(api_get_path(SYS_COURSE_PATH).$_course['path'].'/document','',$doc_path).'images/'.$image_file['name'],'file',filesize($doc_path.'images/'.$image_file['name']),$image_file['name']);

			api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'DocumentAdded', $_uid);
		}

		if($size=@getImageSize($doc_path.'images/'.$image_file['name']))
		{
			$image_width=$size[0];
			$image_height=$size[1];
		}

		$image_url=str_replace(api_get_path(SYS_PATH),api_get_path(WEB_PATH),$doc_path).'images/'.$image_file['name'];

		header('Location: insert_image.php?doc_path='.urlencode($doc_path).'&image_url='.urlencode($image_url).'&width='.$image_width.'&height='.$image_height.'&align='.$image_align);
		exit();
	}
	else
	{
		header('Location: insert_image.php?doc_path='.urlencode($doc_path));
		exit();
	}
}
?>

<html>

<head>
  <title>Insert Image</title>

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
  	<?php if(empty($image_url)): ?>
      document.getElementById("f_align").value = param["f_align"];

      param["f_url"]=param["f_url"].replace(/^<?php echo str_replace('/','\/',$urlAppend); ?>\//,'<?php echo addslashes(api_get_path(WEB_PATH)); ?>');
      param["f_url"]=(param["f_url"].indexOf('://') == -1)?'<?php echo addslashes(api_get_path(WEB_COURSE_PATH).$_course['path'].'/'); ?>'+param["f_url"]:param["f_url"];

      document.getElementById("f_url").value = param["f_url"];
      document.getElementById("f_vert").value = param["f_vert"];
      document.getElementById("f_horiz").value = param["f_horiz"];
    <?php endif; ?>

      string_url='image_preview.php?url='+document.formConfig.f_url.value+'&width='+document.formConfig.f_horiz.value+'&height='+document.formConfig.f_vert.value;

      window.ipreview.location.replace(string_url);
  }
  if(document.formConfig.f_url.value != '')
  {
	  document.formConfig.ok.disabled=false;
  }
  else
  {
	  document.formConfig.ok.disabled=true;
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
  var fields = ["f_align", "f_url", "f_horiz", "f_vert"];
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
  window.ipreview.location.replace('image_preview.php?url='+document.formConfig.f_url+'&width='+document.formConfig.f_horiz+'&height='+document.formConfig.f_vert);
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

<form name="formUpload" method="post" action="insert_image.php?doc_path=<?php echo urlencode($doc_path); ?>" enctype="multipart/form-data" onsubmit="javascript:if(document.getElementById) { document.getElementById('loading').style.visibility='visible'; } document.formUpload.image_width.value=document.formConfig.horiz.value; document.formUpload.image_height.value=document.formConfig.vert.value; document.formUpload.image_align.value=document.formConfig.align.value; document.formUpload.upload.value='Wait...'; document.formUpload.upload.disabled=true;">
<input type="hidden" name="sent" value="1">
<input type="hidden" name="image_width" value="260">
<input type="hidden" name="image_height" value="170">
<input type="hidden" name="image_align" value="baseline">
<div class="title"><span>Insert Image</span> <img id="loading" src="<?php echo api_get_path(WEB_CODE_PATH); ?>img/loading.gif" border="0" style="visibility: hidden;"></div>
<!--- new stuff --->
<table border="0" cellpadding="2" cellspacing="0" align="center">
  <tbody>

  <tr>
    <td><span>Image file</span> :</td>
    <td nowrap="nowrap"><input type="file" name="image_file" size="25" value="" /><input type="submit" name="upload" value="Upload"></td>
  </tr>

  </tbody>
</table>
</form>

<form name="formConfig" action="" method="get">
<p />

<input type="hidden" name="url" id="f_url" value="<?php echo $image_url; ?>">

<fieldset style="float: left; margin-left: 5px; width:120px;">
<legend><span>Layout</span></legend>

<p />

<div class="fl"><nobr><span>Align</span>:
<select size="1" name="align" id="f_align">
  <option value="left" <?php if($align == 'left') echo 'selected="selected"'; ?> >Left</option>
  <option value="right" <?php if($align == 'right') echo 'selected="selected"'; ?> >Right</option>
  <option value="texttop" <?php if($align == 'texttop') echo 'selected="selected"'; ?> >Texttop</option>
  <option value="absmiddle" <?php if($align == 'absmiddle') echo 'selected="selected"'; ?> >Absmiddle</option>
  <option value="baseline" <?php if($align == 'baseline') echo 'selected="selected"'; ?> >Baseline</option>
  <option value="absbottom" <?php if($align == 'absbottom') echo 'selected="selected"'; ?> >Absbottom</option>
  <option value="bottom" <?php if($align == 'bottom') echo 'selected="selected"'; ?> >Bottom</option>
  <option value="middle" <?php if($align == 'middle') echo 'selected="selected"'; ?> >Middle</option>
  <option value="top" <?php if($align == 'top') echo 'selected="selected"'; ?> >Top</option>
</select>
</nobr></div>

<p />

<div class="space"></div>

</fieldset>

<fieldset style="float:right; margin-right: 5px; width:200px;">
<legend><span>Dimensions</span></legend>

<div class="space"></div>

<div class="fr"><nobr><span>Width</span>: <input type="text" name="horiz" id="f_horiz" size="5" value="<?php echo $width; ?>" onchange="javascript:if(document.formConfig.f_url.value != '') { window.ipreview.document.location.href='image_preview.php?url='+document.formConfig.f_url.value+'&width='+document.formConfig.horiz.value+'&height='+document.formConfig.vert.value; }" />&nbsp;&nbsp;<span>Height</span>: <input type="text" name="vert" id="f_vert" size="5" value="<?php echo $height; ?>" onchange="javascript:if(document.formConfig.f_url.value != '') { window.ipreview.document.location.href='image_preview.php?url='+document.formConfig.f_url.value+'&width='+document.formConfig.horiz.value+'&height='+document.formConfig.vert.value; }" /></nobr></div>

<div class="space"></div>

</fieldset>
<br clear="all" />
<table width="100%" style="margin-bottom: 0.2em">
 <tr>
  <td valign="bottom">
    <span>Image Preview</span>:<br />
    <iframe name="ipreview" id="ipreview" frameborder="0" style="border : 1px solid gray;" height="200" width="300" src="<?php if(!empty($image_url)) echo 'image_preview.php?url='.urlencode($image_url).'&width='.$width.'&height='.$height; ?>"></iframe>
  </td>
  <td valign="bottom" style="text-align: right">
    <button type="button" name="ok" onclick="return onOK();">OK</button><br>
    <button type="button" name="cancel" onclick="return onCancel();">Cancel</button>
  </td>
 </tr>
</table>
</form>
</body>
</html>
