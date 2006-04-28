<?php
interface SearchSource
{
	function search($query);

	static function is_supported();
}
?>