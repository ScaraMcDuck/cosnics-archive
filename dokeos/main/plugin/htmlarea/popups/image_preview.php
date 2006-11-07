<?php
$url=isset($_GET['url'])?$_GET['url']:'';
$width=isset($_GET['width'])?intval($_GET['width']):234;
$height=isset($_GET['height'])?intval($_GET['height']):60;
?>

<html>
<head>
</head>
<body bgcolor="#FFFFFF">
<img src="<?php echo $url; ?>" border="0" width="<?php echo $width; ?>" height="<?php echo $height; ?>">
</body>
</html>