<?php
include_once('../../../inc/claro_init_global.inc.php');
?>

<html>

<head>
  <title>Insert Math / LaTeX</title>

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
      document.getElementById("f_vert").value = param["f_vert"];
      document.getElementById("f_horiz").value = param["f_horiz"];
  }

  document.formConfig.latex.focus();
};

function onOK() {
  var required = {
    "f_latex": "You must enter the Math / LaTeX code"
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
  var fields = ["f_horiz", "f_vert", "f_latex", "f_autosize"];
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

<div class="title">Insert Math / LaTeX</div>

<form name="formConfig" action="" method="get">
<p />

<fieldset style="float:left; margin-right: 5px; width:200px;">
<legend><span>Dimensions</span></legend>

<div class="space"></div>

<div class="fr"><nobr><span>Width</span>: <input type="text" name="horiz" id="f_horiz" size="5" value="200" onchange="javascript:window.ipreview.document.location.href='latex_preview.php?rand='+Math.random()+'&width='+document.formConfig.horiz.value+'&height='+document.formConfig.vert.value;" />&nbsp;&nbsp;<span>Height</span>: <input type="text" name="vert" id="f_vert" size="5" value="100" onchange="javascript:window.ipreview.document.location.href='latex_preview.php?rand='+Math.random()+'&width='+document.formConfig.horiz.value+'&height='+document.formConfig.vert.value;" /></nobr>
<input type="hidden" id="f_autosize" name="autosize" value="0"></div>

<div class="space"></div>

</fieldset>
<br clear="all" />
<table width="100%" style="margin-bottom: 0.2em">
 <tr>
  <td valign="bottom">
    <span>Type your Math / LaTeX code here</span>:<br />
    <textarea name="latex" id="f_latex" cols="20" rows="5" wrap="virtual" style="width:300px;height:80px;" onchange="javascript:window.ipreview.document.location.href='latex_preview.php?rand='+Math.random()+'&width='+document.formConfig.horiz.value+'&height='+document.formConfig.vert.value;"></textarea>
  </td>
  <td valign="bottom" style="text-align: right">
    <button type="button" name="preview">Preview</button>
  </td>
 </tr>
 <tr>
  <td valign="bottom">
    <span>Math / LaTeX Preview</span>:<br />
    <iframe name="ipreview" id="ipreview" frameborder="0" style="border : 1px solid gray;" height="150" width="300" src="latex_preview.php"></iframe>
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
