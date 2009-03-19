<?php

$file = dirname(__FILE__) . '/user_import.csv';
$users = parse_csv($file);
//dump($users);

foreach($users as $user)
{
	$action = $user['action'];
	switch($action)
	{
		case 'I': create_user($user); break;
		case 'i': create_user($user); break;
		case 'U': update_user($user); break;
		case 'u': update_user($user); break;
		case 'D': delete_user($user); break;
		case 'd': delete_user($user); break;
	}
}

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
		log("FOUT: Kan het bestand niet openen ($file)");
	}
	
	return $users;
}

function create_user($user)
{
	log_message('Creating user ' . $user['official_code']);
	log_message('Create succesful');
}

function update_user($user)
{
	log_message('Updating user ' . $user['official_code']);
	log_message('Update succesful');
}

function delete_user($user)
{
	log_message('Deleting user: ' . $user['official_code']);
	log_message('Delete succesful');
}

function dump($value)
{
	echo '<pre>';
	print_r($value);
	echo '</pre>';
}

function log_message($text)
{
	echo date('[H:m] ', time()) . $text . '<br />';
}

?>