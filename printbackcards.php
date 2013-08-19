<?php
include('init.php');
if(!isset($_SESSION['logined'])){
    header('Location: login.php');
}


$sql = "SELECT * FROM settings WHERE name = 'backnumbers_us'";
$res =  $db->query($sql);
$row = $res->fetch_assoc();

$inputFileName = './uploads/'.$row['value'];
if(file_exists($inputFileName)){

	ini_set('include_path', ini_get('include_path').';../classes/');

	include 'classes/PHPExcel/IOFactory.php';

	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);

	$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
}

$sql = "SELECT * FROM settings";
$res = $db->query($sql);
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $settings[$row['name']] = $row['value'];
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<link type="text/css" href="/css/print.css" rel="stylesheet" />
</head>
<body>
    
    <div class="page">
<?php
$r=0;
$ids = explode(',',$_GET['ids']);

foreach ($sheetData as $key => $row) {
	if(in_array($key,$ids)){
		$us[$key] = $row;
	}
}
	foreach($us as $key=>$row){
		$r++;
		$names = explode(' ', preg_replace('/\s+/', ' ',$row['D']));
		?>
			<div class="card">
				<img  width="712" src="/base/<?=$settings['backnumber']?>">
				<span class="number"><?=$row['A']?><?=$row['B']?></span>
				<div class="names">
					<span class="name"><?=$names[1]?></span>
					<span class="surname"><?=mb_convert_case($names[0], MB_CASE_TITLE, "UTF-8")?></span>
				</div>
				<span class="country"><?=$row['F']?></span>
			</div>	
		<?php
		if(count($us) != $r)
			if($r%2==0 && $r != 0){
				echo '</div><div class="page">';
			}
	}
?>

	</div>
	</body>
</html>