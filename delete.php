<?php
include('init.php');

if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}

if (isset($_GET['id'])) {
    $sql = 'DELETE FROM users WHERE id="' . $db->real_escape_string($_GET['id']) . '"';
    $db->query($sql);
    header('Location: index.php');
    exit();
}
