<?php
include('init.php');
if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}

ini_set('include_path', ini_get('include_path') . ';../classes/');

include 'classes/PHPExcel/IOFactory.php';

$objPHPExcel = new PHPExcel();
//$sheet = ;

$sql = 'SELECT u.*, c.name AS country_name, c.uk_name AS country_uk_name, 
		c.flag, ac.name AS arc_cat, cat.name AS cat_name, cat.uk_name AS cat_uk_name,
		    ci.name as city_name,
		    org.name as org_name
		    FROM users u
		    LEFT JOIN countries c ON u.country = c.id
		    LEFT JOIN category ac ON u.arccat = ac.id
		    LEFT JOIN category cat ON u.category = cat.id
		    LEFT JOIN cities ci ON u.city = ci.id
		    LEFT JOIN orgs org ON u.org = org.id            
            WHERE u.arccat > 0
';
$res = $db->query($sql);
if ($res->num_rows > 0) {
    $i = 0;
    while ($row = $res->fetch_assoc()) {
        $parts[] = array(
            $row['surname'],
            $row['name'],
            $row['country_name'],
            $row['country_uk_name'],
            $row['arc_cat'],
            $row['city_name'],
            $row['org_name']
        );
    }
}
//echo '<pre>'.print_r($parts,true).'</pre>';
$objPHPExcel->setActiveSheetIndex(0)->fromArray($parts);

ob_end_clean();
$version = isset($_POST['export']) ? $_POST['export'] : $_GET['export'];
switch ($version) {
    case '2007' :
        $filenameext = '.xlsx';
        $excelversion = '2007';
        break;
    case '2003' :
    default :
        $filenameext = '.xls';
        $excelversion = '5';
        break;
}

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="participants' . $filenameext . '"');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel' . $excelversion);
ob_end_clean();

$objWriter->save('php://output');
$objPHPExcel->disconnectWorksheets();
unset($objPHPExcel);
?>
