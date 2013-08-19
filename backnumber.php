<?php
include('init.php');
if(!isset($_SESSION['logined'])){
    header('Location: login.php');
}

 $sql = "SELECT * FROM settings WHERE name ='backnumber'";
    $res = $db->query($sql);
    if ($res->num_rows) {
         $row = $res->fetch_assoc();
    }

include('template/header.php');
?>
<script type="text/javascript" src="scripts/swfobject.js"></script>
<script type="text/javascript" src="scripts/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
$(function() {

$('#photoup').uploadify({
  'uploader'  : 'scripts/uploadify.swf',
  'script'    : 'scripts/backnumber.php',
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
    $('input[name=backside]').val(fname);
    $('#photowrap .curPhoto').show();
    $('#photowrap .curPhoto img').attr('src', response);
    $('#photoup').hide();
      }
});
    $('#photowrap .deletePhoto').on('click',function(e){
      e.preventDefault();
      $.post('/ajaxfunctions.php?action=delete_badge_photo', {photo_name : $('input[name=backside]').val(), 'name' : 'backside'}, function(){
          $('input[name=backside]').val('');
          $('#photowrap .curPhoto').hide();
          $('#photoup').show();
      });
  });
});
				</script>
<div id="header">
  <h3>Back Numbers image</h3>
</div>
<div id="content">  
  <div id="addform">
    <form method="POST" class="form-horizontal">
      <input type="hidden" name="backside" value="<?php echo $row['value'];?>"/>
      <div id="photowrap" class="clearfix">
          <label class="control-label"  for="xlInput">Image</label>
          <div class="controls">
            <span id="photoup"></span>
            <div class="curPhoto" <?php echo (!empty($row['value']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/base/'.$row['value'])) ? '' : 'style="display:none;"'?>>
                <img width="200" src="/base/<?php echo $row['value']?>"/>   
                <a href="#" class="btn btn-mini btn-danger deletePhoto"><i class="icon-remove icon-white"></i> Delete photo</a>                       
            </div>
          </div>
      </div>
    </form>
  </div>
</div>
<?php
include('template/footer.php');
?>
