<?php
$width=isset($_GET['width'])?intval($_GET['width']):200;
$height=isset($_GET['height'])?intval($_GET['height']):100;
?>

<html>
<head>
</head>
<body bgcolor="#FFFFFF">

<script type="text/javascript">
<!--
if(parent.document.formConfig.latex.value != '')
{
	document.write('<embed width="<?php echo $width; ?>" height="<?php echo $height; ?>" type="application/x-techexplorer" autosize="false" texdata="'+parent.document.formConfig.latex.value+'" pluginspage="http://www.integretechpub.com/techexplorer/" />');
}
//-->
</script>

</body>
</html>