<?php
/*
 * download.php - download file
 *
 * */
 /*
 * Error code	Description
 * ----------	-----------
 * 403		Forbidden
 * 404		File not found (or it is a directory)
 *
 * */

require_once('config.php');
require_once('common.php');

function exception_handler($exception)
{
	$aErr['error']	= intval($exception->getMessage());
	print_array($aErr);
}
set_exception_handler('exception_handler');

if(!isset($_GET['file']) || empty($_GET['file']))
	throw new Exception(404);

$relPath = urldecode($_GET['file']);
$absPath = realpath(ROOT_DIR . '/' . $relPath);

if(!$absPath || !is_file($absPath))
	throw new Exception(404);

if(realpath(ROOT_DIR) != substr($absPath, 0, strlen(realpath(ROOT_DIR))))
	throw new Exception(403);

header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-Length: " . filesize($absPath));
header("Content-disposition: attachment; filename=\"" . basename($absPath) . "\"");
readfile($absPath); // do the double-download-dance (dirty but worky)

?>
