<?php
include('init.php');
if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}
include('template/header.php');


if (isset($_POST['save'])) {
    if ($_FILES["file"]["error"] > 0) {
        echo "Error: " . $_FILES["file"]["error"] . "<br />";
    } else {
        $ext = '.' . substr(strrchr($_FILES['file']['name'], '.'), 1);
        $filename = 'cl_' . time() . $ext;
        move_uploaded_file($_FILES["file"]["tmp_name"], './uploads/' . $filename);
        $sql = "UPDATE settings SET `value` = '{$filename}' WHERE `name`='backnumbers_us'";
        $res = $db->query($sql);
        if ($db->affected_rows == 0) {
            $sql = "INSERT INTO settings (`name`,`value`) VALUES ('backnumbers_us','{$filename}')";
            $db->query($sql);
        }
    }
}

$sql = "SELECT * FROM settings WHERE name = 'backnumbers_us'";
$res = $db->query($sql);
$row = $res->fetch_assoc();

$inputFileName = './uploads/' . $row['value'];
if (file_exists($inputFileName)) {

    ini_set('include_path', ini_get('include_path') . ';../classes/');

    include 'classes/PHPExcel/IOFactory.php';

    $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);

    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
}
?>
    <script type="text/javascript" src="scripts/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="scripts/paging.js"></script>
    <script type="text/javascript" src="scripts/jquery.dataTables.columnFilter.js"></script>
    <script type="text/javascript" src="scripts/jquery.jeditable.mini.js"></script>
    <script type="text/javascript">

        $(document).ready(function () {

            $("table#members").dataTable({
                "aoColumns": [
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    { "bSortable": false }
                ],
                "sDom": "<'row-fluid'<'span4'l><'span8'f>r>t<'row-fluid'<'span4'i><'span8'p>>",
                "sPaginationType": "bootstrap"
            }).columnFilter(
                {
                    aoColumns: [
                        { type: "select" },
                        { type: "select" },
                        { type: "text" },
                        { type: "text" },
                        { type: "select" },
                        { type: "select" },
                        { type: "select" },
                        null
                    ],
                    sPlaceHolder: "tfoot"
                }
            );
            $("#members_filter label input", "#members_wrapper").addClass('input-medium search-query');
            $("#printlink").click(function (e) {
                e.preventDefault();
                var toprint = new Array();
                $('.toprint:checked').each(function () {
                    toprint.push($(this).val());
                });
                if (toprint.length > 0)
                    window.open($(this).attr('href') + '?ids=' + toprint.join(','));
            });

            $('.checkall').click(function () {
                if ($(this).is(':checked')) {
                    $('.toprint:visible').attr('checked', true);
                } else {
                    $('.toprint:visible').attr('checked', false);
                }
            })
        });
    </script>
    <div class="header">
        <h3>Back numbers
            <div id="linkswrap">
                <a id="printlink" class="btn btn-large btn-primary" href="printbackcards.php">Print</a>
            </div>
        </h3>

    </div>
    <div class="content">
        <?php if (isset($sheetData) && is_array($sheetData)) : ?>
            <table id="members" class="table table-hover table-striped table-bordered">
                <thead>
                    <th>Sheet</th>
                    <th>Target</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Region</th>
                    <th>Organization</th>
                    <th></th>
                    <th width="55">Print <input type="checkbox" class="checkall" /></th>
                </thead>
                <tfoot>
                <th>Sheet</th>
                <th>Target</th>
                <th>Name</th>
                <th>Surname</th>
                <th>Region</th>
                <th>Organization</th>
                <th></th>
                <th></th>
                </tfoot>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($sheetData as $rowNum => $row) {
                        if (empty($row['A'])) continue;
                        $i++;
                        $cla = (($i % 2 == 0) ? 'odd' : 'even');
                        $names = explode(' ', preg_replace('/\s+/', ' ', $row['D']));
                        echo '<tr class="' . $cla . '">
					<td >' . $row['A'] . '</td>
					<td>' . $row['B'] . '</td>
					<td>' . $names[1] . '</td>
					<td>' . mb_convert_case($names[0], MB_CASE_TITLE, "UTF-8") . '</td>
					<td>' . $row['F'] . '</td>
					<td>' . $row['G'] . '</td>
					<td>' . $row['H'] . '</td>
					<td class="printtd"><label><input type="checkbox" name="print"  class="toprint" value="' . $rowNum . '"/></label></td>
				</tr>';
                    }
                    ?>
                </tbody>
            </table>

        <?php endif; ?>


        <div id="addform">
            <form method="POST" action="backcards.php" class="form-horizontal" enctype="multipart/form-data">
                <fieldset>
                    <legend>Add/change file</legend>
                    <div class="control-group">
                        <label class="control-label">File</label>
                        <div class="controls">
                            <input type="file" name="file" class="xlarge" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">&nbsp;</label>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" name="save" value="Save"><i
                                        class="icon-ok icon-white"></i> Save
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

<?php
include('template/footer.php');
?>
