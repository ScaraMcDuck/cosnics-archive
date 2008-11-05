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
		/*$file = Filesystem::create_unique_name($this->get_path(SYS_ARCHIVE_PATH),$this->get_filename());
		$handle = fopen($file, 'a+');
		foreach ($data as $index => $row)
		{
			fwrite($handle, '"'.implode('";"', $row).'"'."\n");
		}
		fclose($handle);
		Filesystem :: file_send_for_download($file, true, $file);
		exit;*/
		
		$pdf =& new Cezpdf();
		$pdf->selectFont('./fonts/Helvetica.afm');
		$pdf->ezTable($data, null, 'Users', array('fontSize' => 12));
		$pdf->ezStream();
	}
}
?>