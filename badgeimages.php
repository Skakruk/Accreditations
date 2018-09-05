<?php
include('init.php');
if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}
$sql = "SELECT * FROM settings WHERE name in ('frontside','backside')";
$res = $db->query($sql);
if ($res->num_rows) {
    while ($row = $res->fetch_assoc()) {
        $images[$row['name']] = $row['value'];
    }
}

include('template/header.php');
?>
    <link rel="stylesheet" href="css/jquery.fileupload.css">
    <script type="text/javascript" src="js/vendor/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="js/jquery.fileupload.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#frontphotoup').fileupload({
                url: '/scripts/badgeimages.php?side=frontside',
                dataType: 'json',
                formData: {folder: 'base'},
                done: function (e, data) {
                    var image = data.result.files[0];

                    $('input[name=frontside]').val(image.name);
                    $('#frontphotowrap .curPhoto').show();
                    $('#frontphotowrap .curPhoto img').attr('src', image.url);
                    $('#frontphotoup').hide();
                }
            });
            $('#backphotoup').fileupload({
                'url': '/scripts/badgeimages.php?side=backside',
                dataType: 'json',
                formData: {folder: 'base'},
                done: function (e, data) {
                    var image = data.result.files[0];

                    $('input[name=backside]').val(image.name);
                    $('#backphotowrap .curPhoto').show();
                    $('#backphotowrap .curPhoto img').attr('src', image.url);
                    $('#backphotoup').hide();
                }
            });

            $('#frontphotowrap .deletePhoto').on('click', function (e) {
                e.preventDefault();
                $.post('/ajaxfunctions.php?action=delete_badge_photo', {
                    photo_name: $('input[name=frontside]').val(),
                    'name': 'frontside'
                }, function () {
                    $('input[name=frontside]').val('');
                    $('#frontphotowrap .curPhoto').hide();
                    $('#frontphotoupUploader').show();
                });
            });

            $('#backphotowrap .deletePhoto').on('click', function (e) {
                e.preventDefault();
                $.post('/ajaxfunctions.php?action=delete_badge_photo', {
                    photo_name: $('input[name=backside]').val(),
                    'name': 'backside'
                }, function () {
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
            <?php if (!empty($row['value'])) { ?>
                <div class="clearfix">
                    <img width="356" height="501" id="photoshow" src="base/<?php echo $row['value']; ?>" />
                </div>
            <?php } ?>
            <form method="POST" class="form-horizontal">
                <input type="hidden" name="frontside" value="<?php echo $images['frontside']; ?>" />
                <input type="hidden" name="backsideside" value="<?php echo $images['backside']; ?>" />
                <div id="frontphotowrap" class="control-group">
                    <label class="control-label" for="xlInput">Frontside image</label>
                    <div class="controls">
                        <span class="btn btn-success fileinput-button">
                            <span>Select files...</span>
                            <input id="frontphotoup" type="file" name="files" />
                        </span>
                        <?

                        ?>
                        <div class="curPhoto" <?php echo (!empty($images['frontside']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/base/' . $images['frontside'])) ? '' : 'style="display:none;"' ?>>
                            <img width="200" src="/base/<?php echo $images['frontside'] ?>" />
                            <a href="#" class="btn btn-mini btn-danger deletePhoto"><i
                                        class="icon-remove icon-white"></i> Delete photo</a>
                        </div>
                    </div>
                </div>

                <div id="backphotowrap" class="control-group">
                    <label class="control-label" for="xlInput">Backside image</label>
                    <div class="controls">
                        <span class="btn btn-success fileinput-button">
                            <span>Select files...</span>
                            <input id="backphotoup" type="file" name="files" />
                        </span>
                        <div class="curPhoto" <?php echo (!empty($images['backside']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/base/' . $images['backside'])) ? '' : 'style="display:none;"' ?>>
                            <img width="200" src="/base/<?php echo $images['backside'] ?>" />
                            <a href="#" class="btn btn-mini btn-danger deletePhoto"><i
                                        class="icon-remove icon-white"></i> Delete photo</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php
include('template/footer.php');
?>
