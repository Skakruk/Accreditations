<?php
include('init.php');
if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}
include('template/header.php');
?>

<script type="text/javascript" src="scripts/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="scripts/paging.js"></script>
<script type="text/javascript" src="scripts/jquery.dataTables.columnFilter.js"></script>
<script type="text/javascript">

    $(document).ready(function () {

        $("table#members").dataTable({
            "aaSorting": [[ 1, "desc" ]],
            "aoColumns": [
                { "bSortable": false },
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                { "bSortable": false },
                { "bSortable": false }
            ],
            "sDom": "<'row-fluid'<'span4'l><'span8'f>r>t<'row-fluid'<'span4'i><'span8'p>>",
            "sPaginationType": "bootstrap"
        }).columnFilter(
            {
                aoColumns: [
                    null,
                    null,
                    { type: "text" },
                    { type: "text" },
                    { type: "select" },
                    { type: "select" },
                    { type: "select" },
                    { type: "select" },
                    { type: "select" },
                    null,
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
        $("#viewlink").click(function (e) {
            e.preventDefault();
            var toprint = [];
            $('.toprint:checked').each(function () {
                toprint.push($(this).val());
            });
            if (toprint.length > 0)
                window.open($(this).attr('href') + '&ids=' + toprint.join(','));
        });

        $('.checkallusers').click(function () {
            $('.user:visible').attr('checked', $(this).is(':checked'));
        });

        $('.checkall').click(function () {
            $('.toprint:visible').attr('checked', $(this).is(':checked'));
        });
    });

    function del(id) {
        if (confirm("Are you sure you want to delete?")) {
            parent.location = 'delete.php?id=' + id;
        }
    }

    function delAll (e) {
        e.preventDefault();

        if (confirm("Are you sure you want to delete all selected participants?")) {
            var users = $('.user:checked').toArray().map(function(el) {
               return el.value;
            });

            parent.location = 'delete.php?id=' + users.join(',');
        }
    }
</script>
<div id="header">
    <a href="add.php" class="btn btn-large btn-success" id="addnew">+ Add new</a>
    <div id="linkswrap">
        <a id="printlink" class="btn btn-large btn-primary" href="pdf.php">Print</a>
        <a id="viewlink" class="btn btn-primary" href="output.php?action=view">View</a>
    </div>
</div><!-- #header-->

<div id="content">
    <table id="members" class="table table-hover table-striped table-bordered">
        <thead>
            <th><input type="checkbox" class="checkallusers" /></th>
            <th>#</th>
            <th>Surname</th>
            <th>Name</th>
            <th>Category</th>
            <th>Arch. category</th>
            <th>Country</th>
            <th>City</th>
            <th>Club</th>
            <th width="55">Print <input type="checkbox" class="checkall" /></th>
            <th width="85" style="text-align:center;">Actions</th>
        </thead>
        <tfoot>
            <th><a href="#" class="btn btn-mini btn-danger" onclick="delAll(event)">Delete</a></th>
            <th></th>
            <th>Surname</th>
            <th>Name</th>
            <th>Category</th>
            <th>Arch. category</th>
            <th>Country</th>
            <th>City</th>
            <th></th>
            <th></th>
            <th></th>
        </tfoot>
        <tbody>
            <?php
            $sql = 'SELECT u.*, c.name AS country_name, ac.name AS cat, av.name AS arc_cat,
            ci.name as city_name, org.name as org_name
            FROM users u
            LEFT JOIN countries c ON u.country = c.id
            LEFT JOIN category ac ON u.category = ac.id
            LEFT JOIN category av ON u.arccat = av.id
            LEFT JOIN cities ci ON u.city = ci.id
            LEFT JOIN orgs org ON u.org = org.id';

            $res = $db->query($sql);
            if ($res->num_rows > 0) {
                $i = 0;
                while ($row = $res->fetch_assoc()) {
                    $i++;
                    $cla = (($i % 2 == 0) ? 'odd' : 'even');
                    echo '<tr class="' . $cla . '">
					<td><input type="checkbox" name="id" class="user" value="' . $row['id'] . '"/></td>
					<td>' . $row['id'] . '</td>
					<td>' . $row['surname'] . '</td>
					<td>' . $row['name'] . '</td>
					<td>' . $row['cat'] . '</td>
					<td>' . $row['arc_cat'] . '</td>
					<td>' . $row['country_name'] . '</td>
                    <td>' . $row['city_name'] . '</td>
                    <td>' . $row['org_name'] . '</td>
					<td class="printtd"><label><input type="checkbox" name="print"  class="toprint" value="' . $row['id'] . '"/></label></td>
					<td  style="white-space: nowrap;padding:5px;"><a class="btn btn-mini" href="edit.php?id=' . $row['id'] . '">Edit</a>&nbsp;&nbsp;&nbsp;<a href="#" class="btn btn-mini" onclick="del(' . $row['id'] . '); return false;">Delete</a></td>
				</tr>';
                }
            }
            $res->close();
            ?>
        </tbody>
    </table>
</div><!-- #content-->
<form method="POST" action="/export.php" class="form-horizontal">
    <div class="form-actions">
        <label class="control-label">Export to </label>
        <div class="controls">
            <a href="/export.php?export=2003" target="_blank" class="btn">Excel 2003</a>
            <a href="/export.php?export=2007" target="_blank" class="btn">Excel 2007</a>
            <!-- <button type="submit" name="export" value="2003" class="btn">Excel 2003</button>
              <button type="submit" name="export" value="2007" class="btn">Excel 2007</button> -->
        </div>
    </div>
</form>
<?php
include('template/footer.php');
?>
