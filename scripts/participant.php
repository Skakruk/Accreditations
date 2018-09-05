<?php
require_once '../classes/ThumbLib.inc.php';

if (!empty($_FILES)) {
    $tempFile = $_FILES['files']['tmp_name'];

    $ext = '.' . substr(strrchr($_FILES['files']['name'], '.'), 1);
    $targetPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $_REQUEST['folder'] . '/';
    $filename = 'p_' . time() . $ext;
    $targetFile = str_replace('//', '/', $targetPath) . $filename;

    $thumb = PhpThumbFactory::create($tempFile);
    $thumb->resize(500, 500);
    $thumb->save($targetFile);

    echo json_encode(array(
        "files" => array(
            array(
                "name" => $filename,
                "url" => str_replace($_SERVER['DOCUMENT_ROOT'], '', $targetFile),
            )
        )
    ));

    exit();
}
