<?php
/**
 * $Id: filecompression.class.php 13555 2007-10-24 14:15:23Z bmol $
 * @package export
 */
require_once dirname(__FILE__).'/../export.class.php';
/**
 * Exports data to PDF-format
 */
class PdfExport extends Export
{
	public function write_to_file($data)
	{
        require_once Path :: get_plugin_path().'ezpdf/class.ezpdf.php';
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

    public function write_to_file_html($html)
    {
        require_once Path :: get_plugin_path().'dompdf/dompdf_config.inc.php';
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        $dompdf->stream("sample.pdf");
    }

}
?>