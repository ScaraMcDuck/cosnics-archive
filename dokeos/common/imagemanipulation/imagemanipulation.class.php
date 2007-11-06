<?php
/**
 * $Id: filecompression.class.php 13555 2007-10-24 14:15:23Z bmol $
 * @package imagemanipulation
 */
 /**
 * An abstract class for handling image manipulations. Impement new image
 * manipulation methods by creating a class which extends this abstract class.
 */
abstract class ImageManipulation
{
	/**
	 * When cropping an image, use this offset value to get the exacte center of
	 * the image
	 */
	const CROP_CENTER = -1;
	/**
	 * Final dimensions will be less than or equal to the entered width and
	 * height. Useful for ensuring a maximum height and/or width.
	 */
	const SCALE_INSIDE = 0;
	/**
	 * Final dimensions will be greater than or equal to the entered width and
	 * height. Ideal for cropping the result to a square.
	 */
	const SCALE_OUTSIDE = 1;
	/**
	 * The file on which the manipulations will be done
	 */
	private $file;
	/**
	 * Constructor
	 * @param string $file Full path of the image file on which the
	 * manipulations should be done
	 */
    function ImageManipulation($file)
    {
    	$this->file = $file;
    }
    /**
     * Resize an image maintaining the original aspect-ratio
     * @param int $width
     * @param int $height
     * @param int $type
     */
    abstract function scale($width,$height,$type = ImageManipulation::SCALE_INSIDE);
	/**
 	 * Crop an image to the rectangle specified by the given offsets and
 	 * dimensions.
 	 * @param int $width The width of the image after cropping
 	 * @param int $height The height of the image after cropping
 	 * @param int $offset_x
 	 * @param int $offset_y
 	 */
 	abstract function crop($width,$height,$offset_x = ImageManipulation::OFFSET_CENTER,$offset_y = ImageManipulation::OFFSET_CENTER);
	/**
	 * Resize an image to an exact set of dimensions, ignoring aspect ratio.
	 * @param int $width The width of the image after resizing
	 * @param int $height The height of the image after resizing
	 */
	abstract function resize($width,$height);
	/**
	 * Write the resulting image (after some manipulations to a file)
	 * @param string $file Full path of the file to which the image should be
	 * written. If null, the original image will be overwritten.
	 */
	abstract function write_to_file($file = null);
    /**
     * Create an imagemanipulation instance
     * @todo At the moment this returns the class using GD. The class to return
     * should be configurable
     * @param string $file Full path of the image file on which the
     * manipulations should be done
     */
    public static function factory($file)
    {
		require_once dirname(__FILE__).'/gd/gdimagemanipulation.class.php';
		return new GdImageManipulation($file);
    }
}
?>