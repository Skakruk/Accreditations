<?php
include('init.php');
if(!isset($_SESSION['logined'])){
    header('Location: login.php');
}

if(isset($_GET['delete'])){
    $sql = 'DELETE FROM orgs WHERE id="'.$_GET['delete'].'"';
    $db->query($sql);
    header('Location: organizations.php');
}

 $sqlc = 'SELECT * FROM cities';
    $resc = $db->query($sqlc);
    if($resc->num_rows){
        while($rowco =  $resc->fetch_assoc()){
            $cities[$rowco['id']] = $rowco['name'];
        }

    }

if(isset($_POST['save'])){
	foreach($_POST as $k=>$v){
		$_POST[$k] = $db->real_escape_string($v);
	}
        $sql_data= array(
		'name' => $_POST['name'],
                'city' => $_POST['city']
	);

if(empty($_POST['id'])){
	$sql = "INSERT INTO orgs (`".implode("`,`", array_keys($sql_data))."`) VALUES ('".implode("','", array_values($sql_data))."')";
}else{
    foreach($sql_data as $k=>$v){
            $sql_d[] = "`".$k."`='".$v."'";
        }
	$sql = "UPDATE orgs SET ".implode(',',$sql_d)." WHERE id='".$db->real_escape_string($_POST['id'])."'";
}
	$db->query($sql);
        header('Location: organizations.php');
}

if(isset($_GET['edit'])){
     $sql = 'SELECT * FROM orgs WHERE id='.$_GET['edit'].'';
    $res = $db->query($sql);
    if($res->num_rows){
        $crow = $res->fetch_assoc();
    }

}
include('template/header.php');

?>
<script type="text/javascript">
function del(id){
    if (confirm("Are you sure you want to delete?")) {
	parent.location='organizations.php?delete='+id;
    }
}
    </script>
<div id="header">
    <h3>Organizations</h3>
</div>
<div id="content">
<table id="ctable" class="table table-hover table-striped table-bordered" style="width:400px; margin: 0 auto;">
    <thead><tr><th>Name</th><th>City</th><th style="width: 85px;">Actions</th></tr></thead>
    <tbody>
<?php
    $sql = 'SELECT * FROM orgs';
    $res = $db->query($sql);
    if($res->num_rows){
        while($row = $res->fetch_assoc()){
            echo '<tr>
                    <td>'.$row['name'].'</td>
                    <td>'.$cities[$row['city']].'</td>
                    <td ><a class="btn btn-mini" href="organizations.php?edit='.$row['id'].'#form">Edit</a>
                    <a class="btn btn-mini" href="#" onclick="del('.$row['id'].'); return false;">Delete</a></td>
                </tr>';
        }
    }

?>
    </tbody>
</table>
                      <hr/>
                      <a name="form"></a>
           <div id="addform">
                <form method="POST" action="organizations.php" class="form-horizontal">
                    <fieldset>
                     <?php if(isset($_GET['edit'])):?>
                     <legend>Edit organization "<?php echo $crow['name']; ?>"</legend>
                     <?php else: ?>
                     <legend>Add organization</legend>
                     <?php endif;?>
               <input type="hidden" name="id" value="<?php echo $crow['id'];?>">
                <div class="control-group">
                    <label  class="control-label">Name</label>
                    <div class="controls">
                      <input type="text" name="name" value="<?php echo $crow['name'];?>" class="span5"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">City</label>
                    <div class="controls">
                        <select name="city" autocomplete="off" class="span5">
<?php
    foreach($cities as $k=>$v){
        $act = (($crow['city'] == $k)?'selected="selected" ':'');
        echo '<option '.$act.'value="'.$k.'">'.$v.'</option>';

    }

?>
                        </select>

                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" name="save" value="Save"><i class="icon-ok icon-white"></i> Save</button>
                        <a href="organizations.php" class="btn">Cancel</a>
                   </div>
                </div>
                    </fieldset>
                    </form>
                </div>
                     </div>
<?php
include('template/footer.php');
?>

