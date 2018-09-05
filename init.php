<?php
if (!@include("./config/config.php")) {
    echo "Please setup 'config/config.php'";
    die();
};

header('Content-Type: text/html; charset=utf-8');
session_id('acc123');
session_start();
ini_set('display_errors', 'on');

$db = @new mysqli($config->db->host, $config->db->user, $config->db->password, $config->db->db);

if ($db->connect_errno) {
    die('Connect Error: ' . $db->connect_errno . ' => '. $db->connect_error);
}

$db->set_charset("utf8");

$page = explode('/', $_SERVER["PHP_SELF"]);
$page = str_replace('.php', '', $page[1]);
