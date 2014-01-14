<?php
/*
 * list.php - directory listing
 *
 * */
 /*
 * Error code	Description
 * ----------	-----------
 * 403		Directory not white-listed
 * 404		File or directory not found
 *
 * */

require_once('config.php');
require_once('perms.php');

function exception_handler($exception)
{
	$aErr['error']	= intval($exception->getMessage());
	print_array($aErr);
}
set_exception_handler('exception_handler');

// Request string
$fDir = ((isset($_GET['dir']) && !empty($_GET['dir'])) ? urldecode($_GET['dir']) : ROOT_DIR);
// Absolute path
$aPath = realpath((substr($fDir, 0, 1) == '/' ? $fDir : getcwd() . '/' . $fDir));
if($aPath == false)
	throw new Exception(404);

$legal = false;
foreach($w_list as $w_dir){
	if(realpath($w_dir) == substr($aPath, 0, strlen(realpath($w_dir))))
		$legal = true;
}
if($legal == false)
	throw new Exception(403);

if(!isset($sContent['error']))
{
	$sContent['path'] = $aPath;
	if(!($hDir = opendir($fDir)))
	{
		throw new Exception(404);
	} else {
		$sContent['path'] = $aPath;
		$sContent['entries'] = array();
		
		while (false !== ($file = readdir($hDir)))
		{
			if($file != '.' && $file != '..')
			{
				$sFile = stat($aPath . '/' . $file);

				$aFile = array();
				$aFile['name'] = $file;
				$aFile['owner'] = posix_getpwuid($sFile['uid'])['name'];
				$aFile['group'] = posix_getgrgid($sFile['gid'])['name'];
				$aFile['permissions'] = getHumanPerms($aPath . '/' . $file);
				$aFile['size'] = $sFile['size'];
				$aFile['timestamp'] = $sFile['mtime'];
				
				array_push($sContent['entries'], $aFile);
			}
		}
		array_multisort($sContent['entries']);
	}
}
print_array($sContent);

function print_array($array)
{
	header('Content-Type: application/json');
	echo json_encode($array);
}

?>
