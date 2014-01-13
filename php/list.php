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

header('Content-Type: application/json');

$fDir = (isset($_GET['dir']) ? $_GET['dir'] : ROOT_DIR);
$aPath = realpath((substr($fDir, 0, 1) == '/' ? $fDir : getcwd() . '/' . $fDir));
$sContent = array('error'	=>	403);

foreach($w_list as $w_dir){
	if(strpos($aPath, $w_dir) !== 0)
		unset($sContent['error']);
}

if(!isset($sContent['error']))
{
	if(!($hDir = opendir($fDir)))
	{
		$sContent['errors'] = 404;
	} else {
		$sContent['path'] = $aPath;
		$sContent['directories'] = array();
		$sContent['files'] = array();
		
		while (false !== ($file = readdir($hDir)))
		{
			if($file != '.' && $file != '..')
			{
				array_push($sContent[(is_dir(realpath($fDir . '/' . $file)) ? 'directories' : 'files')], $file);
			}
		}
	}
}

echo json_encode($sContent);

?>
