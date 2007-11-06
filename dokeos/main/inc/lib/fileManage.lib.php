<?php # $Id$

/* vim: set expandtab tabstop=4 shiftwidth=4:
===============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	more copyrights held by individual contributors

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
===============================================================================
*/

/**
==============================================================================
*	This is the file manage library for Dokeos.
*	Include/require it in your code to use its functionality.
*
*	@package dokeos.library
==============================================================================
*/

/**
 * Cheks a file or a directory actually exist at this location
 *
 * @author - Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @param  - filePath (string) - path of the presume existing file or dir
 * @return - boolean TRUE if the file or the directory exists
 *           boolean FALSE otherwise.
 */

function check_name_exist($filePath)
{
	clearstatcache();
	chdir ( dirname($filePath) );
	$fileName = basename ($filePath);

	if (file_exists( $fileName ))
	{
		return true;
	}
	else
	{
		return false;
	}
}


//------------------------------------------------------------------------------

/**
 * removes a directory recursively
 *
 * @returns true if OK, otherwise false
 *
 * @author Amary <MasterNES@aol.com> (from Nexen.net)
 * @author Olivier Brouckaert <oli.brouckaert@skynet.be>
 *
 * @param string	$dir		directory to remove
 */

function removeDir($dir)
{
	if(!@$opendir = opendir($dir))
	{
		return false;
	}

	while($readdir = readdir($opendir))
	{
		if($readdir != '..' && $readdir != '.')
		{
			if(is_file($dir.'/'.$readdir))
			{
				if(!@unlink($dir.'/'.$readdir))
				{
					return false;
				}
			}
			elseif(is_dir($dir.'/'.$readdir))
			{
				if(!removeDir($dir.'/'.$readdir))
				{
					return false;
				}
			}
		}
	}

	closedir($opendir);

	if(!@rmdir($dir))
	{
		return false;
	}

	return true;
}
/**
 * Move a file or a directory to an other area
 *
 * @author - Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @param  - $source (String) - the path of file or directory to move
 * @param  - $target (String) - the path of the new area
 * @return - bolean - true if the move succeed
 *           bolean - false otherwise.
 * @see    - move() uses check_name_exist() and copyDirTo() functions
 */


function move($source, $target)
{
	if ( check_name_exist($source) )
	{
		$fileName = basename($source);

		if ( check_name_exist($target."/".$fileName) )
		{
			return false;
		}
		else
		{	/* File case */
			if ( is_file($source) )
			{
				copy($source , $target."/".$fileName);
				unlink($source);
				return true;
			}
			/* Directory case */
			elseif (is_dir($source))
			{
				// check to not copy the directory inside itself
				if (ereg("^".$source."/", $target."/"))
				{
					return false;
				}
				else
				{
					copyDirTo($source, $target);
					return true;
				}
			}
		}
	}
	else
	{
		return false;
	}

}

//------------------------------------------------------------------------------


/**
 * Move a directory and its content to an other area
 *
 * @author - Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @param  - $origDirPath (String) - the path of the directory to move
 * @param  - $destination (String) - the path of the new area
 * @return - no return !!
 */

function copyDirTo($origDirPath, $destination, $move=true)
{
	// extract directory name - create it at destination - update destination trail
	$dirName = basename($origDirPath);
	mkdir ($destination."/".$dirName, 0775);
	$destinationTrail = $destination."/".$dirName;

	chdir ($origDirPath) ;
	$handle = opendir($origDirPath);

	while ($element = readdir($handle) )
	{
		if ( $element == "." || $element == "..")
		{
			continue; // skip the current and parent directories
		}
		elseif ( is_file($element) )
		{
			copy($element, $destinationTrail."/".$element);

			if($move)
			{
				unlink($element) ;
			}
		}
		elseif ( is_dir($element) )
		{
			$dirToCopy[] = $origDirPath."/".$element;
		}
	}

	closedir($handle) ;

	if ( sizeof($dirToCopy) > 0)
	{
		foreach($dirToCopy as $thisDir)
		{
			copyDirTo($thisDir, $destinationTrail, $move);	// recursivity
		}
	}

	if($move)
	{
		rmdir ($origDirPath) ;
	}

}

//------------------------------------------------------------------------------


/* NOTE: These functions batch is used to automatically build HTML forms
 * with a list of the directories contained on the course Directory.
 *
 * From a thechnical point of view, form_dir_lists calls sort_dir wich calls index_dir
 */

/**
 * Indexes all the directories and subdirectories
 * contented in a given directory
 *
 * @author - Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @param  - path (string) - directory path of the one to index
 * @return - an array containing the path of all the subdirectories
 */

function index_dir($path)
{
	chdir($path);
	$handle = opendir($path);

	// reads directory content end record subdirectoies names in $dir_array
	while ($element = readdir($handle) )
	{
		if ( $element == "." || $element == "..") continue;	// skip the current and parent directories
		if ( is_dir($element) )	 $dirArray[] = $path."/".$element;
	}

	closedir($handle) ;

	// recursive operation if subdirectories exist
	$dirNumber = sizeof($dirArray);
	if ( $dirNumber > 0 )
	{
		for ($i = 0 ; $i < $dirNumber ; $i++ )
		{
			$subDirArray = index_dir( $dirArray[$i] ) ;			    // function recursivity
			$dirArray  =  array_merge( (array)$dirArray , (array)$subDirArray );	// data merge
		}
	}

	chdir("..") ;

	return $dirArray ;

}


/**
 * Indexes all the directories and subdirectories
 * contented in a given directory, and sort them alphabetically
 *
 * @author - Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @param  - path (string) - directory path of the one to index
 * @return - an array containing the path of all the subdirectories sorted
 *           false, if there is no directory
 * @see    - index_and_sort_dir uses the index_dir() function
 */

function index_and_sort_dir($path)
{
	$dir_list = index_dir($path);

	if ($dir_list)
	{
		sort($dir_list);
		return $dir_list;
	}
	else
	{
		return false;
	}
}



//------------------------------------------------------------------------------

/**
 * to create missing directory in a gived path
 *
 * @returns a resource identifier or FALSE if the query was not executed correctly.
 * @author KilerCris@Mail.com original function from  php manual
 * @author Christophe Gesch� gesche@ipm.ucl.ac.be Claroline Team
 * @since  28-Aug-2001 09:12
 * @param 	sting	$path 		wanted path
 * @param 	boolean	$verbose	fix if comments must be printed
 * @param 	string	$mode		fix if chmod is same of parent or default
 * @global 	string  $langCreatedIn string to say "create in"
 */
function mkpath($path, $verbose = false, $mode = "herit")
{
	global $langCreatedIn, $rootSys;

	$path=str_replace("/","\\",$path);
	$dirs=explode("\\",$path);

	$path=$dirs[0];

	if($verbose)
	{
		echo "<UL>";
	}

	for($i=1;$i < sizeof($dirs);$i++)
	{
		$path.='/'.$dirs[$i];

		if(ereg('^'.$path,$rootSys) && strlen($path) < strlen($rootSys))
		{
			continue;
		}

		if(!is_dir($path))
		{
			$ret=mkdir($path,0770);

			if($ret)
			{
				if($verbose)
				{
					echo '<li><strong>'.basename($path).'</strong><br>'.$langCreatedIn.'<br><strong>'.realpath($path.'/..').'</strong></li>';
				}
			}
			else
			{
				if($verbose)
				{
					echo '</UL>error : '.$path.' not created';
				}

				$ret=false;

				break;
			}
		}
	}

	if($verbose)
	{
		echo '</UL>';
	}

	return $ret;
}

/**
 * to extract the extention of the filename
 *
 * @returns array
 * @param 	string	$filename 		filename
 */
function getextension($filename)
{
	$bouts = explode(".", $filename);
	return array(array_pop($bouts), implode(".", $bouts));
}

?>