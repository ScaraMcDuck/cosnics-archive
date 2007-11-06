<?php
/**
 * $Id: filecompression.class.php 13555 2007-10-24 14:15:23Z bmol $
 * @package imagemanipulation
 */
 /**
 * This class provide image manipulation using php's GD-extension
 */
class GdImageManipulation extends ImageManipulation
{
    function scale($width,$height,$type = ImageManipulation::SCALE_INSIDE)
    {
    }
 	function crop($width,$height,$offset_x = ImageManipulation::OFFSET_CENTER,$offset_y = ImageManipulation::OFFSET_CENTER)
 	{
 	}
	function resize($width,$height)
	{
	}
	function write_to_file($file = null)
	{
	}
}
?>