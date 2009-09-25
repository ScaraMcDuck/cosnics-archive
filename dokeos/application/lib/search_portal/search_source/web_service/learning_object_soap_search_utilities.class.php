<?php
/**
 * @package application.searchportal
 * @subpackage webservice
 */

ini_set('soap.wsdl_cache_enabled', '0');

class ContentObjectSoapSearchUtilities
{
	private static $temp_dir;

	static function get_wsdl_file_path($url)
	{
		$file = tempnam(null, 'wsdl');
		$template = file_get_contents(dirname(__FILE__).'/content_object_search.template.wsdl');
		$url = preg_replace('|/+$|', '', $url);
		$contents = str_replace('%url%', $url, $template);
		file_put_contents($file, $contents);
		return $file;
	}

	static function soap_fault_to_exception($fault)
	{
		return new Exception($fault->faultstring.'('.$fault->faultcode.')');
	}
}
?>