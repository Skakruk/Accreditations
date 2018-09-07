<?php
include('init.php');

if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}

if (isset($_GET['id'])) {
    $paramId = 0;
    $ids = explode(",", $_GET['id']);

    if (!($stmt = $db->prepare("DELETE FROM users WHERE id = ?"))) {
        echo "Prepare failed: (" . $db->errno . ") " . $db->error;
    }

    if (!$stmt->bind_param('s',$id)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    foreach ($ids as $id) {
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
    }

    $stmt->close();

    header('Location: index.php');
    exit();
}
