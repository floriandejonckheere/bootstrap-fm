<?php
/*
 * list.php - directory listing
 *
 * */

require_once('config.php');

header('Content-Type: application/json');

if(isset($_GET['dir']))
{
	$fDir = $_GET['dir'];
} else {
	$fDir = ROOT_DIR;
}

$iFiles = 0;
$sContent = array(
	"path"		=> $fDir,
	"directories"	=> array(),
	"files"		=> array(),
);
if($hDir = opendir($fDir))
{
	while (false !== ($file = readdir($hDir)))
	{
		if($file != '.' && $file != '..')
		{
				array_push($sContent[(is_dir($file) ? 'directories' : 'files')], $file);
		}
	}
} else die('Error opening directory \'' . $fDir . '\'');

echo json_encode($sContent);

?>
