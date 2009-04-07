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
		foreach ($data as $datapair)
		{
			$title = $datapair['key'];
			$table_data = $datapair['data'];
			$pdf->ezTable($table_data, null, $title, array('fontSize' => 5));
		}
		$pdf->ezStream();
	}

    public function write_to_file_table($data)
	{
		$pdf =& new Cezpdf();
		$pdf->selectFont(Path :: get_plugin_path() . 'ezpdf/fonts/Helvetica.afm');
		foreach ($data as $datapair)
		{
			$title = $datapair['key'];
			$table_data = $datapair['data'];
			$pdf->ezTable($table_data, null, $title, array('fontSize' => 5));
		}
		$pdf->ezStream();
	}

    public function write_to_file_image($image,$padding,$width,$resize='none',$justification,$border)
    {
        $pdf =& new Cezpdf();
		$pdf->selectFont(Path :: get_plugin_path() . 'ezpdf/fonts/Helvetica.afm');
        foreach ($image as $datapair)
		{
			$title = $datapair['key'];
			$table_data = $datapair['data'];
			$pdf->ezImage($table_data,$padding,$width,$resize,$justification,$border);
            //$pdf->ezText($table_data);
		}
        //$pdf->ezImage($image,$padding,$width,$resize,$justification,$border);
        //$pdf->ezText($image);
		$pdf->ezStream();
    }

    public function write_to_file_text($data)
    {
        $pdf =& new Cezpdf();
		$pdf->selectFont(Path :: get_plugin_path() . 'ezpdf/fonts/Helvetica.afm');
        $pdf->ezText($data);
		$pdf->ezStream();
    }
}
?>