<?php
include('init.php');
if(!isset($_SESSION['logined'])){
    header('Location: login.php');
}

if(isset($_POST['delete'])){
    $sql = "DELETE FROM settings WHERE `name`='frontside'";
    $db->query($sql);
    header('Location: frontside.php?deleted');
    unlink($_SERVER['DOCUMENT_ROOT'].'/photos/'.$_POST['frontside']);
}
if(isset($_POST['save'])){
	foreach($_POST as $k=>$v){
		$_POST[$k] =  $db->real_escape_string($v);
	}

	$sql_data= array(
		'name'=>'frontside',
                'value'=>$_POST['frontside']
	);


        foreach($sql_data as $k=>$v){
            $sql_d[] = "`".$k."`='".$v."'";
        }

	$sql = "UPDATE settings SET `value` = '".$_POST['frontside']."' WHERE `name`='frontside'";
        $res =  $db->query($sql);

        if( $db->affected_rows() == 0){

            $sql = "INSERT INTO settings (`".implode("`,`", array_keys($sql_data))."`) VALUES ('".implode("','", array_values($sql_data))."')";
             $db->query($sql);
        }

	header('Location: frontside.php?ok');
}

 $sql = "SELECT * FROM settings WHERE name ='frontside'";
    $res =  $db->query($sql);
    if ($res->num_rows) {
         $row = $res->fetch_assoc();
    }

include('template/header.php');
?>
<script type="text/javascript" src="scripts/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="scripts/swfobject.js"></script>
<script type="text/javascript" src="scripts/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
$(function() {

				$('#photoup').uploadify({
  'uploader'  : 'scripts/uploadify.swf',
  'script'    : 'scripts/frontside.php',
  'cancelImg' : 'images/cancel.png',
  'folder'    : 'base',
  'removeCompleted' : true,
  'auto'      : true,
  'fileExt'     : '*.jpg;*.gif;*.png',
  'fileDesc'    : 'Image Files',
  'onComplete'  : function(event, ID, fileObj, response, data) {
    var fname = response.split('/');
    fname.reverse();
    fname = fname[0];
    $("#photo").val(fname);
    if($("#photoshow").length != 1){
        $("#photowrap").append('<p><img width="356" height="501" id="photoshow" src="base/'+fname+'"/></p>')
    }else{
        $("#photoshow").attr('src','base/'+fname);
    }
      }
});
    setTimeout(function() {
        $("#alert").slideUp();
    }, 2000)
    $("#alert .close").click(function(){
         $("#alert").slideUp();
    })
});
				</script>
<div id="header">
    <?php if(isset($_GET['ok'])){?>
    <div id="alert" class="alert-message success">
        <a href="#" class="close">×</a>
        <p>Frontside image was successfully updated.</p>
      </div>
   <?php }elseif(isset($_GET['deleted'])){?>
    <div id="alert" class="alert-message success">
        <a href="#" class="close">×</a>
        <p>Frontside image was successfully deleted.</p>
      </div>
    <?php } ?>


  <h3>Badge images</h3>

		</div><!-- #header-->



 <div id="content">
<div id="addform">
  <?php if(!empty($row['value'])){?>
    <div class="clearfix">
       <img width="356" height="501" id="photoshow" src="base/<?php echo $row['value'];?>"/>
    </div>
  <?php } ?>
                <form method="POST" class="form-horizontal">
                    <input type="hidden" name="frontside" value="<?php echo $row['value'];?>" id="photo"/>

                    <div id="photowrap" class="control-group">
                        <label class="control-label" for="xlInput">Frontside image</label>
                        <div class="controls">
                          <span id="photoup">
                               </span>
                        </div>
                    </div>
                    
                     <div class="form-actions">
                      <button type="submit" class="btn btn-large btn-primary" name="save" value="Save"/><i class="icon-ok icon-white"></i> Save</button>
                       <input type="submit" class="btn btn-danger" name="delete" value="Delete"/>
                    </div>
                </form>
                </div>
                </div>
<?php
include('template/footer.php');
?>