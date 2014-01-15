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

$relPath = ((isset($_GET['dir']) && !empty($_GET['dir'])) ? urldecode($_GET['dir']) : '.');
$absPath = realpath(ROOT_DIR . '/' . $relPath);
//$fDir = realpath((isset($_GET['dir']) && !empty($_GET['dir'])) ? ROOT_DIR . '/' . urldecode($_GET['dir']) : ROOT_DIR);

if(!$absPath)
	throw new Exception(404);

if(realpath(ROOT_DIR) != substr($absPath, 0, strlen(realpath(ROOT_DIR))))
	throw new Exception(403);


$sContent['path'] = array_pop(explode('/', realpath(ROOT_DIR))) . ($relPath != '.' ? '/' . $relPath : '');
$sContent['entries'] = array();

$aList	= scandir($absPath, SCANDIR_SORT_ASCENDING);
$iStart	= (isset($_GET['start']) ? intval($_GET['start']) : 0);
$iCount	= (isset($_GET['count']) ? min($iStart + intval($_GET['count']), count($aList)) : count($aList));

for($i = $iStart ; $i < $iCount ; ++$i)
{
	$sFile = stat($absPath . '/' . $aList[$i]);

	$aFile = array();
	$aFile['name'] = $aList[$i];
	$aFile['owner'] = posix_getpwuid($sFile['uid'])['name'];
	$aFile['group'] = posix_getgrgid($sFile['gid'])['name'];
	$aFile['permissions'] = getHumanPerms($absPath . '/' . $aList[$i]);
	$aFile['size'] = $sFile['size'];
	$aFile['timestamp'] = $sFile['mtime'];
	
	array_push($sContent['entries'], $aFile);
}
print_array($sContent);

function print_array($array)
{
	header('Content-Type: application/json');
	echo json_encode($array);
}

?>
