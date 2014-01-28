<?php
/*
 * common.php - common functions
 *
 * */

function print_array($array)
{
	header('Content-Type: application/json');
	echo json_encode($array);
}

?>
