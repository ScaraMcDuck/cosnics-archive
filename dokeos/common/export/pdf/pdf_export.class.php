<?php
/**
 * $Id: filecompression.class.php 13555 2007-10-24 14:15:23Z bmol $
 * @package export
 */
require_once dirname(__FILE__).'/../export.class.php';
require_once Path :: get_plugin_path().'ezpdf/class.ezpdf.php';
/**
 * Exports data to PDF-format
 */
class PdfExport extends Export
{
	public function write_to_file($data)
	{
		$pdf =& new Cezpdf();
		$pdf->selectFont(Path :: get_plugin_path() . 'ezpdf/fonts/Helvetica.afm');
		$pdf->ezTable($data, null, 'Users', array('fontSize' => 7));
		$pdf->ezStream();
	}
}
?>