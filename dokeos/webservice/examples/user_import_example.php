<?php

$file = dirname(__FILE__) . '/user_import.csv';
$users = parse_csv($file);
dump($users);

function parse_csv($file)
{
	if(file_exists($file) && $fp = fopen($file, "r"))
	{
		$keys = fgetcsv($fp, 1000, ";");
		$users = array();
		
		while($user_data = fgetcsv($fp, 1000, ";"))
		{
			$user = array();
			foreach($keys as $index => $key)
			{
				$user[$key] = trim($user_data[$index]);	
			}
			$users[] = $user;
		}
		fclose($fp);
	}
	else
	{
		echo("FOUT: Kan het bestand niet openen ($file)");
	}
	
	return $users;
}

function create_user($user)
{
	
}

function update_user($user)
{
	
}

function delete_user($user)
{
	
}

function dump($value)
{
	echo '<pre>';
	print_r($value);
	echo '</pre>';
}

?>