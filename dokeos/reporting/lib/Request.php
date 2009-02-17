<html>
<head>

</head>
<body>
<form method="post" action="<?php echo $PHP_SELF;?>">
<select name="charttype">
<option value="Text">Text</option>
<option value="Table">Table</option>
<option value="Chart:Pie">Pie</option>
<option value="Chart:Bar">Bar</option>
<option value="Chart:Line">Line</option>
</select>
<input type="submit" value="submit" name="submit">
 </form>
 <?php
/*
 * This class sends a request to the reporting class to get the html representing the block & displays it
 */
 if (isset($_POST['submit'])) {
 require_once('../../common/global.inc.php');
 require_once("reporting.php");
 require_once("reporting_block.php");
 
 $Reporting_Block = new ReportingBlock('testblock','TestApplication','TestApplication.php','getActiveInactivePerYearAndMonth',$_POST["charttype"]);
 
  echo Reporting :: generate_block($Reporting_Block);
 }
 ?>
 </body>
 </html>
