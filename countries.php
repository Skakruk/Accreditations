<?php
include('init.php');
if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}

if (isset($_GET['delete'])) {
    $sql = 'DELETE FROM countries WHERE id="' . $_GET['delete'] . '"';
    $db->query($sql);
    header('Location: countries.php');
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
        $sql = "INSERT INTO countries (`" . implode("`,`", array_keys($sql_data)) . "`) VALUES ('" . implode("','", array_values($sql_data)) . "')";
    } else {
        foreach ($sql_data as $k => $v) {
            $sql_d[] = "`" . $k . "`='" . $v . "'";
        }
        $sql = "UPDATE countries SET " . implode(',', $sql_d) . " WHERE id='" . $db->real_escape_string($_POST['id']) . "'";
    }
    $db->query($sql);
    header('Location: countries.php');
}

$crow = array(
    "name" => "",
    "flag" => "",
    "id" => "",
    "uk_name" => ""
);

if (isset($_GET['edit'])) {
    $sql = 'SELECT * FROM countries WHERE id=' . $_GET['edit'] . '';
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
            url: '/scripts/flag.php',
            dataType: 'json',
            formData: {folder: 'base/flags'},
            done: function (e, data) {
                var image = data.result.files[0];
                $("#flag").val(image.name);
                $("#flag_img").attr('src', image.url);
            }
        });
    });

    function del(id) {
        if (confirm("Are you sure you want to delete?")) {
            parent.location = 'countries.php?delete=' + id;
        }
    }
</script>
<div id="header">
    <h3>Countries</h3>
</div>
<div id="content">
    <table id="ctable" class="table table-hover table-striped table-bordered" style="width:600px; margin: 0 auto;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Ukr name</th>
                <th>Flag</th>
                <th style="width: 80px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = 'SELECT * FROM countries';
            $res = $db->query($sql);
            if ($res->num_rows) {
                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                    <td>' . $row['name'] . '</td>
                    <td>' . $row['uk_name'] . '</td>
                    <td ><img src="base/flags/' . $row['flag'] . '" style="height: 18px;" height="18"/></td>
                    <td ><a class="btn btn-mini" href="countries.php?edit=' . $row['id'] . '#form">Edit</a>
                    <a class="btn btn-mini" href="#" onclick="del(' . $row['id'] . '); return false;">Delete</a></td>
                </tr>';
                }
            }

            ?>
        </tbody>
    </table>
    <hr />
    <a name="form"></a>
    <div id="addform">
        <form method="POST" action="countries.php" class="form-horizontal">
            <fieldset>
                <?php if (isset($_GET['edit'])): ?>
                    <legend>Edit country "<?php echo $crow['name']; ?>"</legend>
                <?php else: ?>
                    <legend>Add country</legend>
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
                    <label class="control-label">Flag</label>
                    <div class="controls">
                        <img style="height:18px" id="flag_img" <?= !empty($crow['flag']) ? "src=\"/base/flags/${crow['flag']}\"" : "" ?> /><br />
                        <span class="btn btn-success fileinput-button">
                            <span>Select file...</span>
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
                        <a href="countries.php" class="btn">Cancel</a>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<?php
include('template/footer.php');
?>
