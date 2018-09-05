<?php
include('init.php');
if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}

if (isset($_GET['delete'])) {
    $sql = 'DELETE FROM category WHERE id="' . $_GET['delete'] . '"';
    $db->query($sql);
    header('Location: maincategories.php');
}


if (isset($_POST['save'])) {
    foreach ($_POST as $k => $v) {
        $_POST[$k] = $db->real_escape_string($v);
    }
    $sql_data = array(
        'name' => $_POST['name'],
        'uk_name' => $_POST['uk_name'],
        'letter' => $_POST['letter'],
        'color' => $_POST['color'],
        'parent' => $_POST['parent']
    );

    if (empty($_POST['id'])) {
        $sql = "INSERT INTO category (`" . implode("`,`", array_keys($sql_data)) . "`) VALUES ('" . implode("','", array_values($sql_data)) . "')";
    } else {
        foreach ($sql_data as $k => $v) {
            $sql_d[] = "`" . $k . "`='" . $v . "'";
        }
        $sql = "UPDATE category SET " . implode(',', $sql_d) . " WHERE id='" . $db->real_escape_string($_POST['id']) . "'";
    }
    $db->query($sql);
    header('Location: maincategories.php');
}

$crow = array(
    "name" => "",
    "id" => "",
    "uk_name" => "",
    "parent" => "",
    "letter" => "",
    "color" => ""
);

if (isset($_GET['edit'])) {
    $sql = 'SELECT * FROM category WHERE id=' . $_GET['edit'] . '';
    $res = $db->query($sql);
    if ($res->num_rows) {
        $crow = $res->fetch_assoc();
    }

}
include('template/header.php');

?>
<script type="text/javascript" src="/scripts/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/scripts/jquery.miniColors.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#color").miniColors();
        $('.grow').click(function () {
            if ($(this).find('i').hasClass('icon-plus'))
                $(this).find('i').toggleClass('icon-minus');
            else
                $(this).find('i').toggleClass('icon-plus');
            var chcl = $(this).parents('tr').attr('id');
            $('.' + chcl).toggle();
        })
    })

    function del(id) {
        if (confirm("Are you sure you want to delete?")) {
            parent.location = 'maincategories.php?delete=' + id;
        }
    }
</script>
<div id="header">
    <h3>Categories</h3>
</div>
<div id="content">
    <table id="ctable" class="table table-hover table-striped table-bordered" style="width:700px; margin: 0 auto;">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Ukr name</th>
                <th>Letter</th>
                <th>Color</th>
                <th style="width:85px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $out = '';
            $sql = 'SELECT * FROM category';
            $res = $db->query($sql);
            if ($res->num_rows) {
                while ($row = $res->fetch_assoc()) {
                    $cats[] = $row;
                }
            }
            $ccats = $cats;
            foreach ($ccats as $row) {

                $child = '';
                foreach ($cats as $k => $ocrow) {
                    if ($row['id'] == $ocrow['parent']) {
                        $child .= '<tr class="child p' . $row['id'] . '">
                    <td></td>
                    <td>' . $ocrow['name'] . '</td>
                    <td>' . $ocrow['uk_name'] . '</td>
                    <td>' . $ocrow['letter'] . '</td>
                    <td style="background-color:' . $ocrow['color'] . '">&nbsp;&nbsp;</td>
                    <td><a class="btn btn-mini" href="maincategories.php?edit=' . $ocrow['id'] . '#form">Edit</a>
                    <a href="#" class="btn btn-mini" onclick="del(' . $ocrow['id'] . '); return false;">Delete</a></td>
                </tr>';
                        $skip[] = $ocrow['id'];
                    }

                }
                if (in_array($row['id'], $skip)) continue;
                $parcl = (!empty($child) ? 'id="p' . $row['id'] . '" ' : '');
                $parent = '<tr ' . $parcl . '>
                <td>' . (!empty($child) ? '<span class="grow btn btn-mini"><i class="icon-plus"></i></span>' : '') . '</td>
                    <td>' . $row['name'] . '</td>
                    <td>' . $row['uk_name'] . '</td>
                    <td>' . $row['letter'] . '</td>
                    <td style="background-color:' . $row['color'] . '">&nbsp;&nbsp;</td>
                    <td><a class="btn btn-mini" href="maincategories.php?edit=' . $row['id'] . '#form">Edit</a>
                    <a href="#" class="btn btn-mini" onclick="del(' . $row['id'] . '); return false;">Delete</a></td>
                </tr>';
                $parent .= $child;
                $out .= $parent;
            }

            echo $out;
            ?>
        </tbody>
    </table>
    <hr />

    <a name="form"></a>
    <div id="addform">
        <form method="POST" action="maincategories.php" class="form-horizontal">
            <fieldset>
                <?php if (isset($_GET['edit'])): ?>
                    <legend>Edit category "<?php echo $crow['name']; ?>"</legend>
                <?php else: ?>
                    <legend>Add category</legend>
                <?php endif; ?>
                <input type="hidden" name="id" value="<?php echo $crow['id']; ?>">
                <div class="control-group">
                    <label class="control-label">Name</label>
                    <div class="controls">
                        <input type="text" name="name" value="<?php echo $crow['name']; ?>" class="xlarge" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Ukr. name</label>
                    <div class="controls">
                        <input type="text" name="uk_name" value="<?php echo $crow['uk_name']; ?>" class="xlarge" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Parent cat.</label>
                    <div class="controls">
                        <select name="parent" autocomplete="off">
                            <option value="0">None</option>
                            <?php
                            foreach ($cats as $row) {
                                if ($crow['parent'] == $row['id']) {
                                    $act = 'selected="selected" ';
                                } else {
                                    $act = '';
                                }
                                echo '<option ' . $act . 'value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Letter</label>
                    <div class="controls">
                        <input type="text" name="letter" value="<?php echo $crow['letter']; ?>" class="xlarge" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Color</label>
                    <div class="controls">
                        <input type="text" name="color" id="color" value="<?php echo $crow['color']; ?>"
                               class="xlarge" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" name="save" value="Save"><i
                                    class="icon-ok icon-white"></i> Save
                        </button>
                        <a href="maincategories.php" class="btn">Cancel</a>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<?php
include('template/footer.php');
?>
