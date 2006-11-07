<?php
include_once('../../../inc/claro_init_global.inc.php');

include_once(api_get_library_path().'/fileUpload.lib.php');
include_once(api_get_library_path().'/document.lib.php');

$link_url=$_GET['link_url'];
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

if(!is_dir($doc_path.'linked_files'))
{
   if(is_file($doc_path.'linked_files'))
   {
      @unlink($doc_path.'linked_files');
   }

   @mkdir($doc_path.'linked_files',0777);
   @chmod($doc_path.'linked_files',0777);

   if($_cid)
   {
		$doc_id=add_document($_course,str_replace(api_get_path(SYS_COURSE_PATH).$_course['path'].'/document','',$doc_path).'linked_files','folder',0,'linked_files', 'These are documents that are uploaded through htmlArea');
		api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'FolderCreated', $_uid);
   }
}

if($_POST['sent'])
{
	$file=$_FILES['file'];

	if($file['size'])
	{
		$file['name']=php2phps(replace_dangerous_char($file['name'],'strict'));

		$extension=explode('.',$file['name']);

		$extension=$extension[sizeof($extension)-1];

		$suffix='';
		$i=0;

		do
		{
			if(file_exists($doc_path.'linked_files/'.str_replace('.'.$extension,$suffix.'.'.$extension,$file['name'])))
			{
				$suffix='_'.(++$i);
			}
			else
			{
				break;
			}
		}
		while(1);

		$file['name']=str_replace('.'.$extension,$suffix.'.'.$extension,$file['name']);

		move_uploaded_file($file['tmp_name'],$doc_path.'linked_files/'.$file['name']);
		
		if($_cid)
		{
			$doc_id=add_document($_course,str_replace(api_get_path(SYS_COURSE_PATH).$_course['path'].'/document','',$doc_path).'linked_files/'.$file['name'],'file',filesize($doc_path.'linked_files/'.$file['name']),$file['name']);
			api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'DocumentAdded', $_uid);         
	    }

		$link_url=str_replace($rootSys,$rootWeb,$doc_path).'linked_files/'.$file['name'];

		header('Location: link.php?link_url='.urlencode($link_url));
		exit();
	}
}
?>

<html>

<head>
  <title>Insert/Modify Link</title>
  <script type="text/javascript" src="popup.js"></script>
  <script type="text/javascript">
    window.resizeTo(400, 200);

I18N = window.opener.HTMLArea.I18N.dialogs;

function i18n(str) {
  return (I18N[str] || str);
};

function onTargetChanged() {
  var f = document.getElementById("f_other_target");
  if (this.value == "_other") {
    f.style.visibility = "visible";
    f.select();
    f.focus();
  } else f.style.visibility = "hidden";
};

function Init() {
  __dlg_translate(I18N);
  __dlg_init();
  var param = window.dialogArguments;
  var target_select = document.getElementById("f_target");
  if (param) {
  	<?php if(empty($link_url)): ?>
      document.getElementById("f_href").value = param["f_href"];
    <?php endif; ?>
      document.getElementById("f_title").value = param["f_title"];
      comboSelectValue(target_select, param["f_target"]);
      if (target_select.value != param.f_target) {
        var opt = document.createElement("option");
        opt.value = param.f_target;
        opt.innerHTML = opt.value;
        target_select.appendChild(opt);
        opt.selected = true;
      }
  }
  var opt = document.createElement("option");
  opt.value = "_other";
  opt.innerHTML = i18n("Other");
  target_select.appendChild(opt);
  target_select.onchange = onTargetChanged;
  document.getElementById("f_href").focus();
  document.getElementById("f_href").select();
};

function onOK() {
  var required = {
    // f_href shouldn't be required or otherwise removing the link by entering an empty
    // url isn't possible anymore.
    // "f_href": i18n("You must enter the URL where this link points to")
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
  var fields = ["f_href", "f_title", "f_target" ];
  var param = new Object();
  for (var i in fields) {
    var id = fields[i];
    var el = document.getElementById(id);
    param[id] = el.value;
  }
  if (param.f_target == "_other")
    param.f_target = document.getElementById("f_other_target").value;
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
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
button { width: 70px; }
table .label { text-align: right; width: 8em; }

.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px;
}

#buttons {
      margin-top: 1em; border-top: 1px solid #999;
      padding: 2px; text-align: right;
}
</style>

</head>

<body onload="Init()">
<div class="title"><span>Insert/Modify Link</span> <img id="loading" src="<?php echo api_get_path(WEB_CODE_PATH); ?>img/loading.gif" border="0" style="visibility: hidden;"></div>

<form name="formConfig" method="post" action="link.php?doc_path=<?php echo urlencode($doc_path); ?>" enctype="multipart/form-data" onsubmit="javascript:if(document.getElementById) { document.getElementById('loading').style.visibility='visible'; } document.formConfig.upload.value='Wait...'; document.formConfig.upload.disabled=true;" style="margin: 0px;">
<input type="hidden" name="sent" value="1">

<table border="0" style="width: 100%;">
  <tr>
    <td class="label">URL:</td>
    <td><input type="text" id="f_href" style="width: 250px;" value="<?php echo $link_url; ?>" onfocus="javascript:document.formConfig.ok.disabled=false;" onblur="javascript:if(this.value != '') { document.formConfig.ok.disabled=false; } else { document.formConfig.ok.disabled=true; document.formConfig.upload.disabled=false; document.formConfig.file.disabled=false; }" /></td>
  </tr>
  <tr>
    <td class="label"><span>or File</span>:</td>
    <td><input type="file" id="f_file" name="file" style="width: 250px" <?php if(!empty($link_url)) echo 'disabled="disabled"'; ?> /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="upload" value="Upload" <?php if(!empty($link_url)) echo 'disabled="disabled"'; ?> /></td>
  </tr>
  <tr>
    <td class="label">Title (tooltip):</td>
    <td><input type="text" id="f_title" style="width: 250px" /></td>
  </tr>
  <tr>
    <td class="label">Target:</td>
    <td><select id="f_target">
      <option value="">None (use implicit)</option>
      <option value="_blank">New window (_blank)</option>
      <option value="_self">Same frame (_self)</option>
      <option value="_top">Top frame (_top)</option>
    </select>
    <input type="text" name="f_other_target" id="f_other_target" size="10" style="visibility: hidden" />
    </td>
  </tr>
</table>

<div id="buttons">
  <button type="button" name="ok" onclick="return onOK();" <?php if(empty($link_url)) echo 'disabled="disabled"'; ?> >OK</button>
  <button type="button" name="cancel" onclick="return onCancel();">Cancel</button>
</div>

</form>

</body>
</html>
