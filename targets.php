<?php
include('init.php');
if(!isset($_SESSION['logined'])){
    header('Location: login.php');
}


include('template/header.php');
?>

<script type="text/javascript" src="scripts/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="scripts/paging.js"></script>
<script type="text/javascript" src="scripts/jquery.dataTables.columnFilter.js"></script>
<script type="text/javascript" src="scripts/jquery.jeditable.mini.js"></script>
<script type="text/javascript">
 
$(document).ready(function(){
   
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
                null,
                { type: "select" },
                { type: "select" },
                { type: "text" },
                { type: "text" },
                { type: "select" },
                { type: "select" },
                null
            ],
            sPlaceHolder: "tfoot"
        }
    );
    $("#members_filter label input", "#members_wrapper").addClass('input-medium search-query');

    $('.sheet').editable('/ajaxfunctions.php?action=set_sheet',{
     	submit : 'OK'
    });

    $('.target').editable('/ajaxfunctions.php?action=set_target',{
    	data   : " {'A':'A','B':'B','C':'C','D':'D'}",
     	type   : 'select',
     	submit : 'OK'
    });
});
</script>
<div class="header">
	<h3>Targets</h3>
</div>
<div class="content">
	<table id="members" class="table table-hover table-striped table-bordered">
                <thead>
                    <th>#</th>
                    <th>Sheet</th>
                    <th>Target</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Arch. category</th>
                    <th>Country</th>
                    <th width="55" >Print <input type="checkbox" class="checkall"/></th>
                </thead>
                <tfoot>
                    <th></th>
                    <th>Sheet</th>
                    <th>Target</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Arch. category</th>
                    <th>Country</th>
                    <th></th>
                </tfoot>
                <tbody>
<?php
	$sql = 'SELECT u.*, c.name AS country_name, ac.name AS cat, av.name AS arc_cat,
            org.name as org_name
            FROM users u
            LEFT JOIN countries c ON u.country = c.id
            LEFT JOIN category ac ON u.category = ac.id
            LEFT JOIN category av ON u.arccat = av.id
            LEFT JOIN orgs org ON u.org = org.id
            WHERE u.arccat > 0';

	$res = $db->query($sql);
	if($res->num_rows >0){
		$i = 0;
		while($row = $res->fetch_assoc()){
		$i++;
			$cla = (($i%2 == 0)? 'odd' : 'even'); 
			echo '<tr class="'.$cla.'">
					<td>'.$row['id'].'</td>
					<td id="us_'.$row['id'].'" class="sheet">1</td>
					<td id="qus_'.$row['id'].'" class="target">A</td>
					<td>'.$row['name'].'</td>
					<td>'.$row['surname'].'</td>
					<td>'.$row['arc_cat'].'</td>
					<td>'.$row['country_name'].'</td>
					<td class="printtd"><label><input type="checkbox" name="print"  class="toprint" value="'.$row['id'].'"/></label></td>
				</tr>';
		}
	}
    $res->close();
?>                    
                    </tbody>
                </table>
</div>
<?php
include('template/footer.php');
?>