<?php
include('init.php');
if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}

if (isset($_GET['delete'])) {
    $sql = 'DELETE FROM permisions WHERE id="' . $_GET['delete'] . '"';
    $db->query($sql);
    header('Location: permissions.php');
}


if (isset($_POST['save'])) {
    foreach ($_POST as $k => $v) {
        $_POST[$k] = $db->real_escape_string($v);
    }
    $sql_data = array(
        'name' => $_POST['name'],
        'uk_name' => $_POST['uk_name'],
        'flag' => $_POST['flag']
    );

    if (empty($_POST['id'])) {
        $sql = "INSERT INTO permisions (`" . implode("`,`", array_keys($sql_data)) . "`) VALUES ('" . implode("','", array_values($sql_data)) . "')";
    } else {
        $sql_d = array();

        foreach ($sql_data as $k => $v) {
            $sql_d[] = "`" . $k . "`='" . $v . "'";
        }
        $sql = "UPDATE permisions SET " . implode(',', $sql_d) . " WHERE id='" . $db->real_escape_string($_POST['id']) . "'";
    }
    $db->query($sql);
    header('Location: permissions.php');
}

$crow = array(
    "id" => "",
    "name" => "",
    "flag" => "",
    "uk_name" => ""
);

if (isset($_GET['edit'])) {
    $sql = 'SELECT * FROM permisions WHERE id="' . $_GET['edit'] . '"';
    $res = $db->query($sql);
    if ($res->num_rows) {
        $crow = $res->fetch_assoc();
    }

}
include('template/header.php');

?>
<link rel="stylesheet" href="css/jquery.fileupload.css">
<script type="text/javascript" src="js/vendor/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/jquery.fileupload.js"></script>
<script type="text/javascript">
    $(function () {

        $('#photoup').fileupload({
            url: '/scripts/permissions.php',
            dataType: 'json',
            formData: { folder: 'base/permissions' },
            done: function (e, data) {
                var image = data.result.files[0];

                $("#flag").val(image.name);
                $("#icon_img").attr('src', image.url);
            }
        });

    });

    function del(id) {
        if (confirm("Are you sure you want to delete?")) {
            parent.location = 'permissions.php?delete=' + id;
        }
    }
</script>
<div id="header">
    <h3>Permissions</h3>
</div>
<div id="content">
    <br />
    <table id="ctable" class="table table-hover table-striped table-bordered" style="width:600px; margin: 0 auto;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Ukr name</th>
                <th>Image</th>
                <th style="width:85px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = 'SELECT * FROM permisions';
            $res = $db->query($sql);
            if ($res->num_rows) {
                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                    <td>' . $row['name'] . '</td>
                    <td>' . $row['uk_name'] . '</td>
                    <td><img width="33" height="26" src="base/permissions/' . $row['flag'] . '"/></td>
                    <td><a class="btn btn-mini" href="permissions.php?edit=' . $row['id'] . '#form">Edit</a>
                    <a class="btn btn-mini" href="#" onclick="del(' . $row['id'] . '); return false;">Delete</a></td>
                </tr>';
                }
            }

            ?>
        </tbody>
    </table>
    <hr />
    <form method="POST" id="form" class="form-horizontal">
        <fieldset>
            <?php if (isset($_GET['edit'])): ?>
                <legend>Edit permission "<?php echo $crow['name']; ?>"</legend>
            <?php else: ?>
                <legend>Add permission</legend>
            <?php endif; ?>
            <input type="hidden" id="flag" value="<?php echo $crow['flag']; ?>" name="flag" />
            <input type="hidden" name="id" value="<?php echo $crow['id']; ?>">
            <div class="control-group">
                <label class="control-label">Name</label>
                <div class="controls">
                    <input type="text" name="name" value="<?php echo $crow['name']; ?>" class="xlarge" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Uk name</label>
                <div class="controls">
                    <input type="text" name="uk_name" value="<?php echo $crow['uk_name']; ?>" class="xlarge" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Icon</label>
                <div class="controls">
                    <img id="icon_img" <?= !empty($crow['flag']) ? "src=\"/base/permissions/${crow['flag']}\"" : "" ?> /><br />
                    <span class="btn btn-success fileinput-button">
                            <span>Select files...</span>
                            <input id="photoup" type="file" name="files" />
                        </span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">&nbsp;</label>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" name="save" value="Save"><i
                                class="icon-ok icon-white"></i> Save
                    </button>
                    <a href="permissions.php" class="btn">Cancel</a>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<?php
include('template/footer.php');
?>
