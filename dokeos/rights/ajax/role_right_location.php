<?php
$this_section = 'rights';
 
require_once dirname(__FILE__).'/../../common/global.inc.php';
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

$tree = RightsUtilities :: get_tree('admin');

$root = $_REQUEST['root'];

if ($root == 'source' || $root == '')
{
	$root = $tree->getRoot();
	
	echo '[' . "\n";
	echo '	{' . "\n";
	echo '		"text": "'. $root['name'] .'"';
	if ($tree->hasChildren($root['id']))
	{
		echo ', ' . "\n";
		echo '		"id": "'. $root['id'] .'",' . "\n";
		echo '		"hasChildren": true' . "\n";
	}
	echo '	}' . "\n";
	echo ']' . "\n";
}
else
{
	$elements = $tree->getChildren($root);
	
	echo '[' . "\n";
	
	$strings = array();
	
	foreach ($elements as $element)
	{
		$string = '	{' . "\n";
		$string .= '		"text": "<a href=\"blala\">'. $element['name'] .'</a>"';
		if ($tree->hasChildren($element['id']))
		{
			$string .= ', ' . "\n";
			$string .= '		"id": "'. $element['id'] .'",' . "\n";
			$string .= '		"hasChildren": true' . "\n";
		}
		$string .= '	}' . "\n";
		
		$strings[] = $string;
	}
	
	echo implode(', ', $strings);
	
	echo ']' . "\n";
}
?>
