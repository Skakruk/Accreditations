<?php
include('init.php');
if(!isset($_SESSION['logined'])){
    header('Location: login.php');
}
 $sql = "SELECT * FROM settings WHERE name in ('frontside','backside')";
    $res =  $db->query($sql);
    if ($res->num_rows) {
         while($row = $res->fetch_assoc()){
            $images[$row['name']] = $row['value'];
          }
    }

include('template/header.php');
?>
<script type="text/javascript" src="scripts/swfobject.js"></script>
<script type="text/javascript" src="scripts/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
$(function() {

$('#frontphotoup').uploadify({
  'uploader'  : 'scripts/uploadify.swf',
  'script'    : 'scripts/badgeimages.php?side=frontside',
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
    $('input[name=frontside]').val(fname);
    $('#frontphotowrap .curPhoto').show();
    $('#frontphotowrap .curPhoto img').attr('src', response);
    $('#frontphotoup').hide();
      }
});

$('#backphotoup').uploadify({
  'uploader'  : 'scripts/uploadify.swf',
  'script'    : 'scripts/badgeimages.php?side=backside',
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
    $('#backphotowrap .curPhoto').show();
    $('#backphotowrap .curPhoto img').attr('src', response);
    $('#backphotoup').hide();
      }
});
  
  $('#frontphotowrap .deletePhoto').on('click',function(e){
      e.preventDefault();
      $.post('/ajaxfunctions.php?action=delete_badge_photo', {photo_name : $('input[name=frontside]').val(), 'name' : 'frontside'}, function(){
          $('input[name=frontside]').val('');
          $('#frontphotowrap .curPhoto').hide();
          $('#frontphotoupUploader').show();
      });
  });

  $('#backphotowrap .deletePhoto').on('click',function(e){
      e.preventDefault();
      $.post('/ajaxfunctions.php?action=delete_badge_photo', {photo_name : $('input[name=backside]').val(), 'name' : 'backside'}, function(){
          $('input[name=backside]').val('');
          $('#backphotowrap .curPhoto').hide();
          $('#backphotoupUploader').show();
      });
  });

    
});
				</script>
<div id="header">
  <h3>Badge images</h3>
</div><!-- #header-->
<style>
<?php echo (!empty($images['frontside']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/base/'.$images['frontside'])) ? '#frontphotoupUploader{display:none;}' : ''?>
<?php echo (!empty($images['backside']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/base/'.$images['backside'])) ? '#backphotoupUploader{display:none;}' : ''?>
</style>
 <div id="content">
<div id="addform">
  <?php if(!empty($row['value'])){?>
    <div class="clearfix">
       <img width="356" height="501" id="photoshow" src="base/<?php echo $row['value'];?>"/>
    </div>
  <?php } ?>
                <form method="POST" class="form-horizontal">
                    <input type="hidden" name="frontside" value="<?php echo $images['frontside'];?>"/>
                    <input type="hidden" name="backsideside" value="<?php echo $images['backside'];?>"/>
                    <div id="frontphotowrap" class="control-group">
                        <label class="control-label" for="xlInput">Frontside image</label>
                        <div class="controls">
                          <span id="frontphotoup"></span>
                          <div class="curPhoto" <?php echo (!empty($images['frontside']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/base/'.$images['frontside'])) ? '' : 'style="display:none;"'?>>
                              <img width="200" src="/base/<?php echo $images['frontside']?>"/>   
                               <a href="#" class="btn btn-mini btn-danger deletePhoto"><i class="icon-remove icon-white"></i> Delete photo</a>                       
                          </div>
                        </div>
                    </div>

                    <div id="backphotowrap" class="control-group">
                        <label class="control-label" for="xlInput">Backside image</label>
                        <div class="controls">
                          <span id="backphotoup"></span>
                          <div class="curPhoto" <?php echo (!empty($images['backside']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/base/'.$images['backside'])) ? '' : 'style="display:none;"'?>>
                              <img width="200" src="/base/<?php echo $images['backside']?>"/>   
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