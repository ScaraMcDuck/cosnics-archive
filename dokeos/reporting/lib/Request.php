<?php
/*
 * 
 * @author: Michael Kyndt
 */
 require_once('../../common/global.inc.php');
 require_once("reporting.php");
 require_once("reporting_block.php");
 require_once("reporting_block_layout.php");
 
 Display :: header(new BreadCrumbTrail());
 
$blockname = "blockname";
$appname = "TestApplication";
$appurl = "TestApplication.php";
$appfunction = "getActiveInactivePerYearAndMonth";
$width = "500";
$height = "500";

if(isset($_POST['submit']))
{
$blockname = $_POST["blockname"];
$appname = $_POST["appname"];
$appurl = $_POST["appurl"];
$appfunction = $_POST["appfunction"];
$width = $_POST["width"];
$height = $_POST["height"];		
}
?>
<form method="post" action="<?php echo $PHP_SELF;?>">
<label for="blockname">Block name:</label>
	<input type="text" value="<?php echo $blockname; ?>" name="blockname"><BR>
<label for="appname">Application name;</label>
	<input type="text" value="<?php echo $appname; ?>" name="appname"><BR>
<label for="appurl">Application url:</label>
	<input type="text" value="<?php echo $appurl; ?>" name="appurl"><BR>
<label for="appfunction">Application function:</label>
	<input type="text" value="<?php echo $appfunction; ?>" name="appfunction"><BR>
<label for="width">width:</label>
	<input type="text" value="<?php echo $width; ?>" name="width"><BR>
<label for="height">height:</label>
	<input type="text" value="<?php echo $height; ?>" name="height"><BR>
<select name="charttype">
<option value="Text">Text</option>
<option value="Table">Table</option>
<option value="Chart:Pie">Pie</option>
<option value="Chart:Bar">Bar</option>
<option value="Chart:Line">Line</option>
<option value="Chart:FilledCubic">Filled Cubic</option>
</select>
<input type="submit" value="submit" name="submit">
 </form>
 <?php
/*
 * This class sends a request to the reporting class to get the html representing the block & displays it
 */
 if (isset($_POST['submit'])) {
 $Reporting_Block = new ReportingBlock(
 			$_POST["blockname"],
			$_POST["appname"],
			$_POST["appurl"],
			$_POST["appfunction"],
			$_POST["charttype"],
			new ReportingBlockLayout($_POST["width"],$_POST["height"]));
 
  echo Reporting :: generate_block($Reporting_Block);
 }
 Display :: footer();
 ?>
