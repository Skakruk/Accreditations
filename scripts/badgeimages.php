<?php
require_once '../classes/ThumbLib.inc.php';
include('../init.php');

if (!empty($_FILES)) {
    $tempFile = $_FILES['files']['tmp_name'];

    $ext = '.' . substr(strrchr($_FILES['files']['name'], '.'), 1);
    $targetPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $_REQUEST['folder'] . '/';
    $filename = 'b_' . time() . $ext;
    $targetFile = str_replace('//', '/', $targetPath) . $filename;
    $thumb = PhpThumbFactory::create($tempFile);

    $thumb->resize(712, 1006);
    $thumb->save($targetFile);

    if (isset($_GET['front'])) {
        $name = 'frontside';
    } elseif (isset($_GET['back'])) {
        $name = 'backside';
    }
    $name = $_GET['side'];
    $sql = "UPDATE settings SET `value` = '{$filename}' WHERE `name`='{$name}'";
    $res = $db->query($sql);

    if ($db->affected_rows == 0) {
        $sql = "INSERT INTO settings (`name`,`value`) VALUES ('{$name}','{$filename}')";
        $db->query($sql);
    }

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
