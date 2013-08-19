<?php
require_once '../classes/ThumbLib.inc.php';

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];

        $ext = '.'.substr(strrchr($_FILES['Filedata']['name'], '.'), 1);
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . 'p_'.time().$ext;
        $thumb = PhpThumbFactory::create($tempFile);
        $thumb->resize(99,78);
        $thumb->save($targetFile,'png');
	echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
}
?>
