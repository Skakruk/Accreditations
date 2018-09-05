<?php
include('init.php');
if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}

$sql = "SELECT * FROM settings WHERE name ='backnumber'";
$res = $db->query($sql);
if ($res->num_rows) {
    $row = $res->fetch_assoc();
}

include('template/header.php');
?>
<link rel="stylesheet" href="css/jquery.fileupload.css">
<script type="text/javascript" src="js/vendor/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/jquery.fileupload.js"></script>
<script type="text/javascript">
    $(function () {
        $('#photoup').fileupload({
            url: '/scripts/backnumber.php',
            dataType: 'json',
            formData: {folder: 'base'},
            done: function (e, data) {
                var image = data.result.files[0];

                $('input[name=backside]').val(image.name);
                $('#photowrap .curPhoto').show();
                $('#photowrap .curPhoto img').attr('src', image.url);
                $('#photoup').hide();
            }
        });

        $('#photowrap .deletePhoto').on('click', function (e) {
            e.preventDefault();
            $.post('/ajaxfunctions.php?action=delete_badge_photo', {
                photo_name: $('input[name=backside]').val(),
                'name': 'backside'
            }, function () {
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
            <input type="hidden" name="backside" value="<?php echo $row['value']; ?>" />
            <div id="photowrap" class="clearfix">
                <label class="control-label" for="xlInput">Image</label>
                <div class="controls">
                    <span class="btn btn-success fileinput-button">
                        <span>Select files...</span>
                        <input id="photoup" type="file" name="files" />
                    </span>
                    <div class="curPhoto" <?php echo (!empty($row['value']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/base/' . $row['value'])) ? '' : 'style="display:none;"' ?>>
                        <img width="200" src="/base/<?php echo $row['value'] ?>" />
                        <a href="#" class="btn btn-mini btn-danger deletePhoto"><i class="icon-remove icon-white"></i>
                            Delete photo</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
include('template/footer.php');
?>
