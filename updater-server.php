<?php
include('init.php');

$r = 0;
$files = array();
function getFilesFromDir($dir) { 
	global $files;
	$excludeList = array('./base','./photos', './uploads');
  //$files = array(); 

  if ($handle = opendir($dir)) { 
    while (false !== ($file = readdir($handle))) { 
        if ($file != "." && $file != "..") { 
            if(is_dir($dir.'/'.$file) && !in_array($dir.'/'.$file, $excludeList)) { 
                $dir2 = $dir.'/'.$file; 
                getFilesFromDir($dir2);
            } 
            else { 
              $files[] = array('name' => $dir.'/'.$file, 
              	'modified' => filemtime($dir.'/'.$file)
              	); 
            } 
        } 
    } 
    closedir($handle); 
  } 
} 

if(isset($_GET['getfileslist'])){
  getFilesFromDir('.'); 
  foreach ($files as $key => $file) {
      if($file['modified'] > $_GET['lastupdate']){        
          $output[] = $file;
      }
   } 
  echo json_encode($output);
  exit();
}

if(isset($_GET['file'])){
  $file = $_GET['file'];
  if (file_exists($file)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename='.basename($file));
      header('Content-Transfer-Encoding: binary');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      header('Content-Length: ' . filesize($file));
      ob_clean();
      flush();
      readfile($file);
      exit;
  }
}


if(isset($_GET['syncDB'])){
    $sql = $_POST['db'];
    $sql = explode(';',$sql);
    foreach ($sql as $command) {
      $res = $db->query($command);
    }    
}

if(isset($_GET['syncFiles'])){
  function getNormalizedFILES(){ 
      $newfiles = array(); 
      foreach($_FILES as $fieldname => $fieldvalue) 
          foreach($fieldvalue as $paramname => $paramvalue) 
              foreach((array)$paramvalue as $index => $value) 
                  $newfiles[$fieldname][$index][$paramname] = $value; 
      return $newfiles; 
  } 
  $upfiles = getNormalizedFILES();

 // echo 'POST <br/><pre>'. print_r($_POST, true) . '</pre><br/>';

  //echo 'Files <br/><pre>'. print_r($upfiles, true) . '</pre><br/>';
  
  foreach($upfiles as $key => $file){
    $file = $file[0];
    $ind = str_replace('file_', '', $key);
    $path = $_POST['path_'.$ind];
    //echo $_SERVER['DOCUMENT_ROOT'].$path.'<br/>';
    move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$path);
  }
  
}

?>