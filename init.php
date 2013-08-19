<?php
header('Content-Type: text/html; charset=utf-8');
session_id('acc123');
session_start();
ini_set('display_errors', 'on');
$db = new mysqli('localhost','accs','accs','accreditation');

if(mysqli_connect_errno()){
 echo mysqli_connect_error();
}

$db->set_charset("utf8");

$page = explode('/',$_SERVER["PHP_SELF"]);
$page = str_replace('.php', '', $page[1]);

?>
