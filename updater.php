<?php
include('init.php');
set_time_limit(0);
if(!isset($_SESSION['logined'])){
    header('Location: login.php');
}

include('template/header.php');

$localfiles = array();

	function backup_tables($host,$user,$pass,$name,$tables = '*')
	{
	  
	  $link = mysql_connect($host,$user,$pass);
	  mysql_select_db($name,$link);
	  mysql_set_charset("utf8");
	  //get all of the tables
	  if($tables == '*')
	  {
	    $tables = array();
	    $result = mysql_query('SHOW TABLES');
	    while($row = mysql_fetch_row($result))
	    {
	      $tables[] = $row[0];
	    }
	  }
	  else
	  {
	    $tables = is_array($tables) ? $tables : explode(',',$tables);
	  }
	  
	  //cycle through
	  foreach($tables as $table)
	  {
	    $result = mysql_query('SELECT * FROM '.$table);
	    $num_fields = mysql_num_fields($result);
	    
	    $return.= 'DROP TABLE '.$table.'; ';
	    $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
	    $return.= $row2[1]."; ";
	    
	    for ($i = 0; $i < $num_fields; $i++) 
	    {
	      while($row = mysql_fetch_row($result))
	      {
	        $return.= 'INSERT INTO '.$table.' VALUES(';
	        for($j=0; $j<$num_fields; $j++) 
	        {
	          $row[$j] = addslashes($row[$j]);
	          if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
	          if ($j<($num_fields-1)) { $return.= ','; }
	        }
	        $return.= "); ";
	      }
	    }
	  }
	  
	  return $return;
	 	
	}

	function getFilesFromDir($dir) { 
		global $localfiles;
		
	  if ($handle = opendir($dir)) { 
	    while (false !== ($file = readdir($handle))) { 
	        if ($file != "." && $file != "..") { 
	            if(is_dir($dir.'/'.$file)) { 
	                $dir2 = $dir.'/'.$file; 
	                getFilesFromDir($dir2);
	            } 
	            else { 
	              $localfiles[] = array('name' => $dir.'/'.$file, 
	              	'modified' => filemtime($dir.'/'.$file)
	              	); 
	            } 
	        } 
	    } 
	    closedir($handle); 
	  } 
	} 

$files = array();

if($_SERVER['HTTP_HOST'] !== 'acc.skakruk.org.ua'){

	if(isset($_POST['update'])){
		$sql = "SELECT * FROM settings WHERE `name`='lastupdate'";
		$res =  $db->query($sql);
		$row = $res->fetch_assoc();

		if(isset($_POST['force_update']))
			$lastupdate = time()-100000000;
		else
			$lastupdate = empty($row['value']) ?  (time()-365*24*60*60) : $row['value'];

		$files = file_get_contents('http://acc.skakruk.org.ua/updater-server.php?getfileslist=1&lastupdate='.$lastupdate);

		$files = json_decode($files, true);
		if(is_array($files))
			foreach($files as $file){
				$fl = file_get_contents('http://acc.skakruk.org.ua/updater-server.php?file='.$file['name']);
				$ft = str_replace('/'.basename($file['name']), '', $file['name']);
				if(!is_dir($ft))
					mkdir($ft);
				if(!is_writable($ft))
					chmod($ft, 0777);
				file_put_contents($file['name'], $fl);
			}

		$lastupdate = time();
		$sql = "UPDATE settings SET `value` = '{$lastupdate}' WHERE `name`='lastupdate'";
		$res =  $db->query($sql);
		if( $db->affected_rows == 0){
		    $sql = "INSERT INTO settings (`name`,`value`) VALUES ('lastupdate','{$lastupdate}')";
		    $db->query($sql);
		}
	}
	
	if(isset($_POST['sync'])){

		$sql = "SELECT * FROM settings WHERE `name`='lastsync'";
		$res =  $db->query($sql);
		$row = $res->fetch_assoc();

		if(isset($_POST['force_update']))
			$lastsync = time()-365*24*60*60;
		else
			$lastsync = empty($row['value']) ?  (time()-365*24*60*60) : $row['value'];


		$includeList = array('./base', './photos', './uploads');
		foreach ($includeList as $dir) {
			getFilesFromDir($dir);
		}
		$i = 0;

		foreach($localfiles as $file){
			if($file['modified'] > $lastsync ){
				$upfiles['file_'.$i] = '@'.realpath($file['name']);
				$upfiles['path_'.$i] = $file['name'];
				$i++; 
			}
		}
		if(count($upfiles) > 0){
			$upfiles = array_chunk($upfiles, 20, true);
			foreach ($upfiles as $arupfile) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $arupfile);
				curl_setopt($ch, CURLOPT_URL,'http://acc.skakruk.org.ua/updater-server.php?syncFiles=1&lastupdate='.$lastsync);
				curl_exec($ch);
				if(!curl_errno($ch)){ 
				  $info = curl_getinfo($ch);
				}
				curl_close($ch);
			}
		}
		$sql = backup_tables('localhost','accs','accs','accreditation');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('db' => $sql));
		curl_setopt($ch, CURLOPT_URL,'http://acc.skakruk.org.ua/updater-server.php?syncDB=1&lastupdate='.$lastsync);
		$data = curl_exec($ch);
		if(!curl_errno($ch)){ 
		  $info = curl_getinfo($ch);
		}
		curl_close($ch);

		$lastsync = time();
		$sql = "UPDATE settings SET `value` = '{$lastsync}' WHERE `name`='lastsync'";
		$res =  $db->query($sql);
		if( $db->affected_rows == 0){
		    $sql = "INSERT INTO settings (`name`,`value`) VALUES ('lastsync','{$lastsync}')";
		    $db->query($sql);
		}
	}
}

?>
<div class="header">
	<h3>Updater</h3>
</div>
<div class="content">
	<?php if(isset($_POST['update'])):?>
		<?php if(count($files) > 0):?>
		<p>Files updated:</p>
		<ul>
			<?foreach($files as $file):?>
				<li><?=$file['name']?> &mdash; <?=date('H:i:s', $file['modified'])?></li>
			<?endforeach;?>
		</ul>
		<?php else:?>
		<p>Everything is up-to-date! <form method="POST"><input type="hidden" name="update" value="true"/><button type="submit" name="force_update" value="true" onclick="return confirm('Are you sure you want to force update? It could take long time and traffic.')">Force update</button></form></p>
		<?php endif;?>
	<?php elseif(isset($_POST['sync'])):?>
		<?php if(count($localfiles) > 0):?>
			<p>Participants are synchronized!</p>
		<?php else:?>
			<p>Everything is up-to-date!</p>
		<?php endif;?>
	<?php endif;?>
	<form method="POST" action="updater.php">
		<div class="form-actions">
		  <button type="submit" name="update" value="1" class="btn">Update program</button>
		  <button type="submit" name="sync" value="1"  class="btn">Sync participants</button>
		</div>
	</form>
</div>	
<?php
include('template/footer.php');

?>
