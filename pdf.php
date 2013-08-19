<?php
require_once('classes/html2pdf/html2pdf.class.php');
ini_set("display_errors", "on");
$content = file_get_contents('http://acc.skakruk.org.ua/output.php?ids='.$_GET['ids']);
$html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', array(7, 5, 5, 5));
$html2pdf->pdf->SetDisplayMode('fullpage');
$html2pdf->setDefaultFont('freesans');
$html2pdf->writeHTML($content);
$html2pdf->Output('accreditation_'.time().'.pdf');

// ini_set("pcre.backtrack_limit","1000000");
// include('classes/mpdf/mpdf.php');

// $mpdf = new mPDF('',    // mode - default ''
//  '',    // format - A4, for example, default ''
//  0,     // font size - default 0
//  '',    // default font family
//  15,    // margin_left
//  15,    // margin right
//  16,     // margin top
//  16,    // margin bottom
//  9,     // margin header
//  9,     // margin footer
//  'L');  // L - landscape, P - portrait

// $stylesheet = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/print.css');


// $mpdf->WriteHTML($stylesheet,1);

// $content = file_get_contents('http://acc.skakruk.org.ua/output.php?ids='.$_GET['ids']);

// $mpdf->mirrorMargins = 1;  // Use different Odd/Even headers and footers and mirror margins (1 or 0)
// $mpdf->SetDisplayMode('fullpage','two');

// //print_r($content);

// $mpdf->WriteHTML($content);

// $mpdf->Output();
// exit;

// require_once('classes/wkhtmltopdf.class.php');

// //$html = file_get_contents('http://acc.skakruk.org.ua/output.php?ids='.$_GET['ids']);

// try {
// 	$wkhtmltopdf = new wkhtmltopdf();
// 	$wkhtmltopdf->setHttpUrl('http://acc.skakruk.org.ua/output.php?ids='.$_GET['ids']);
// 	$wkhtmltopdf->output(wkhtmltopdf::MODE_EMBEDDED, '/accreditation_'.time().'.pdf');
// } catch (Exception $e) {
// 	echo $e->getMessage();
// }

?>
