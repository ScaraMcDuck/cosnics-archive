<?php //$Id$
/**
==============================================================================
 *	This script creates the necessary tables in the database. It only works
 *	if you are using the Database data manager, the database is existent and
 *	the user is allowed to create tables in it. Only one statement is allowed
 *	per SQL file; it should be a CREATE TABLE statement that corresponds to
 *	the learning object type and its properties.
 *
 *	@author Tim De Pauw
 * @package repository
==============================================================================
 */

require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../lib/configuration.class.php';
require_once 'DB.php';
?>
<html>
<body>
<h1>Table Creation</h1>
<?php
$conf = Configuration :: get_instance();

if ($conf->get_parameter('general', 'data_manager') != 'Database')
{
	die('Not using Database data manager');
}

/*
 * Establish a connection. Do not use the data manager directly as it is an
 * abstraction layer. Adding a direct query function to it would compromise
 * portability. The last thing we want is people writing proprietary SQL.
 */
$connection = DB :: connect($conf->get_parameter('database', 'connection_string'));
$prefix = $conf->get_parameter('database', 'table_name_prefix');

if (PEAR :: isError($connection))
{
	die('Connection failed: '.$connection->getMessage());
}

$lo_file = dirname(__FILE__).'/learning_object.sql';
if (!file_exists($lo_file))
{
	die('File not found: '.$lo_file);
}
parse_sql_file('learning_object', $lo_file);

$loa_file = dirname(__FILE__).'/learning_object_attachment.sql';
if (!file_exists($loa_file))
{
	die('File not found: '.$loa_file);
}
parse_sql_file('learning_object', $loa_file);

$dir = dirname(__FILE__).'/../lib/learning_object';
if (!($handle = opendir($dir)))
{
	die('Failed to access learning object libraries');
}

while (false !== ($type = readdir($handle)))
{
	$path = $dir.'/'.$type.'/'.$type.'.sql';
	if (file_exists($path))
	{
		parse_sql_file($type, $path);
	}
}

function parse_sql_file($type, $path)
{
	global $connection, $prefix;
	echo '<hr/>'."\n".'<h2>Setting up '.$type.' &hellip;</h2>'."\n";
	$query = str_replace('%prefix%', $prefix, file_get_contents($path));
	echo '<pre>'.htmlentities($query).'</pre>'."\n".'<p>';
	$sth = $connection->prepare($query);
	if (PEAR :: isError($sth))
	{
		echo '<b>FAILED:</b> '.htmlentities($sth->getMessage());
	}
	else
	{
		$res = $connection->execute($sth);
		if (PEAR :: isError($res))
		{
			echo '<b>FAILED:</b> '.htmlentities($res->getMessage());
		}
		else
		{
			echo '<b>SUCCESS:</b> Table created';
		}
	}
	print '</p>'."\n";
}
?>
</body>
</html>