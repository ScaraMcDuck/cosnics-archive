<?php
/**
 * @package application.searchportal
 */
interface SearchSource
{
	function search($query);

	static function is_supported();
}
?>