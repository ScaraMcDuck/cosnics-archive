<?php
/**
 * $Id: filecompression.class.php 13555 2007-10-24 14:15:23Z bmol $
 * @package export
 */
require_once dirname(__FILE__).'/../export.class.php';
/**
 * Exports data to CSV-format
 */
class CsvExport extends Export
{
	public function write_to_file($data)
	{
		$file = Filesystem::create_unique_name(api_get_path(SYS_ARCHIVE_PATH),$this->get_filename());
		$handle = fopen($file, 'a+');
		$key_array = array_keys($data[0]);
		fwrite($handle, '"'.implode('";"', $key_array).'"'."\n");
		foreach ($data as $index => $row)
		{
			fwrite($handle, '"'.implode('";"', $row).'"'."\n");
		}
		fclose($handle);
		DocumentManager :: file_send_for_download($file, true, $file);
		exit;
	}
}
?>