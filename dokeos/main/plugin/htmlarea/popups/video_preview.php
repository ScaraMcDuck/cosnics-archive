<?php
$url=isset($_GET['url'])?$_GET['url']:'';
$width=isset($_GET['width'])?intval($_GET['width']):234;
$height=isset($_GET['height'])?intval($_GET['height']):60;
$type=isset($_GET['type'])?$_GET['type']:'quicktime';

if($type == 'quicktime')
{
	$type='video/quicktime';
	$pluginspage='http://www.apple.com/quicktime/download/';
}
else
{
	$type='audio/x-pn-realaudio-plugin';
	$pluginspage='http://www.real.com/player/index.html?src=000629realhome';
}
?>

<html>
<head>
</head>
<body bgcolor="#FFFFFF">
<embed src="<?php echo $url; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" autoplay="true" autostart="true" loop="false" cache="false" type="<?php echo $type; ?>" pluginspage="<?php echo $pluginspage; ?>" controls="ImageWindow"></embed>
</body>
</html>