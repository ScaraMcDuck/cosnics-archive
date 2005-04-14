<?php
$url=isset($_GET['url'])?$_GET['url']:'';
$width=isset($_GET['width'])?intval($_GET['width']):234;
$height=isset($_GET['height'])?intval($_GET['height']):60;
$quality=isset($_GET['quality'])?$_GET['quality']:'high';
?>

<html>
<head>
</head>
<body bgcolor="#FFFFFF">
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,2,0" width="<?php echo $width; ?>" height="<?php echo $height; ?>">
<param name="movie" value="<?php echo $url; ?>">
<param name="quality" value="<?php echo $quality; ?>">
<embed src="<?php echo $url; ?>" quality="<?php echo $quality; ?>" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></embed>
</object>
</body>
</html>