<?php
include('init.php');

switch ($_GET['action']) {
    case 'add_city':
        $city = $db->real_escape_string($_POST['city']);
        $country = $db->real_escape_string($_POST['country']);
        $sql = "INSERT INTO cities (`name`,`country`) VALUES ('" . $city . "','" . $country . "')";
        $res = $db->query($sql);
        echo json_encode(array('success' => true, 'id' => $db->insert_id));
        break;
    case 'add_org':
        $city = $db->real_escape_string($_POST['city']);
        $org = $db->real_escape_string($_POST['org']);
        $sql = "INSERT INTO orgs (`name`,`city`) VALUES ('" . $org . "','" . $city . "')";
        $res = $db->query($sql);
        echo json_encode(array('success' => true, 'id' => $db->insert_id));
        break;
    case 'delete_photo':
        $photo = $db->real_escape_string($_POST['photo_name']);
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/photos/' . $photo)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/photos/' . $photo);
        }
        $sql = "UPDATE users SET photo='' WHERE photo = '" . $photo . "'";
        $res = $db->query($sql);
        break;
    case 'crop_image' :
        require_once 'classes/ThumbLib.inc.php';

        $thumb = PhpThumbFactory::create($_SERVER['DOCUMENT_ROOT'] . '/photos/' . $_POST['img']);
        $thumb->setOptions(array('jpegQuality' => 100));

        if (!empty($_POST['x'])) {
            $thumb->crop($_POST['x'], $_POST['y'], $_POST['w'], $_POST['h'])->adaptiveResize(124, 166);
        } else {
            $thumb->adaptiveResize(124, 166);
        }

        $thumb->save($_SERVER['DOCUMENT_ROOT'] . '/photos/' . $_POST['img']);
        break;
    case 'delete_badge_photo' :
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/base/' . $_POST['photo_name'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/base/' . $_POST['photo_name']);
        }
        $sql = "UPDATE settings SET value='' WHERE name = '" . $_POST['name'] . "'";
        $res = $db->query($sql);
        break;
    case 'set_target':
        echo $_POST['value'];
        break;
    case 'set_sheet':
        echo $_POST['value'];
        break;
    default:
        return;

}

?>
