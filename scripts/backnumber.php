<?php
require_once '../classes/ThumbLib.inc.php';
include('../init.php');
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
    $ext = '.'.substr(strrchr($_FILES['Filedata']['name'], '.'), 1);
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$filename = 'bn_'.time().$ext;
	$targetFile =  str_replace('//','/',$targetPath) . $filename;
    $thumb = PhpThumbFactory::create($tempFile);
    $thumb->resize(1424,1006);
    $thumb->save($targetFile);
    $sql = "UPDATE settings SET `value` = '{$filename}' WHERE `name`='backnumber'";
    $res =  $db->query($sql);
    if( $db->affected_rows == 0){
        $sql = "INSERT INTO settings (`name`,`value`) VALUES ('backnumber','{$filename}')";
        $db->query($sql);
    }
	echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
}

?>
