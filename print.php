<?php
include('init.php');

if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}
$users = explode(',', $_GET['ids']);
$sql = "SELECT u.*, c.name AS country_name, c.uk_name AS country_uk_name, c.flag, ac.name AS arc_cat, cat.name AS cat_name, cat.uk_name AS cat_uk_name,
    cat.letter, cat.color
    FROM users u
    LEFT JOIN countries c ON u.country = c.id
    LEFT JOIN category ac ON u.arccat = ac.id
    LEFT JOIN category cat ON u.category = cat.id
    WHERE u.id IN (" . $db->real_escape_string($_GET['ids']) . ")";
$res = $db->query($sql);

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {

       
        $row['permisions'] = json_decode($row['permisions'], true);
        $attr[] = $row;
    }
}

$sql = "SELECT * FROM settings";
$res = $db->query($sql);
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $settings[$row['name']] = $row['value'];
    }
}

if($_GET['action'] == 'view'){
   $font = 'style="font-family: Arial, Helvetica, sans-serif;" ';
}else{
   $font = 'style="font-family:freesans;" ';
}

?>
<html>
    <head>
        <link type="text/css" href="print.css" rel="stylesheet" media="print,projection"/>
        <link type="text/css" href="print.css" rel="stylesheet" media="screen"/>
    </head>
    <body>

<?php
$r=0;
foreach ($attr as $row) {
$r++;
    echo '
                <div '.$font.'class="frontwrap">
<img width="356" height="501" src="base/' . $settings['frontside'] . '" class="background"/>
                    <div class="borderBig" style="border-color:'.$row['color'].'"></div>

                    <span class="name" style="color:'.$row['color'].'">' . $row['name'] . '</span>
                    <span class="surname" style="color:'.$row['color'].'">' . $row['surname'] . '</span>

                     <span class="countryLabel">Nationality</span>
                    <span class="ukcountryLabel" style="color:'.$row['color'].'">Країна</span>
                    <span class="country"><span class="encountry" style="color:'.$row['color'].'">' . $row['country_name'] . '</span> <span class="ukcountry" style="color:'.$row['color'].'">' . $row['country_uk_name'] . '</span></span>
                   <div class="flagborder" style="border-color:'.$row['color'].'">
                    <div class="lt" style="border-color:'.$row['color'].'"></div>
                    </div>
                     <img class="flag" height="56" src="base/flags/' . $row['flag'] . '"/>

                     <span class="categoryLabel" >Category</span>
                    <span class="ukcategoryLabel" style="color:'.$row['color'].'">Категорія</span>
                    <div class="catborder" style="border-color:'.$row['color'].'">
                    <div class="lt" style="border-color:'.$row['color'].'"></div>
                    </div>
                     <span class="cat" style="color:'.$row['color'].'">' . $row['letter'] . '</span>
                        <span class="encat" style="color:'.$row['color'].'">' . $row['cat_name'] . '</span>
                        <span class="ukcat" style="color:'.$row['color'].'">' . $row['cat_uk_name'] . '</span>
                    <span class="arccat" style="color:'.$row['color'].'">' . $row['arc_cat'] . '</span>
 <div class="photo" style="border-color:'.$row['color'].'">   ' . ((!empty($row['photo'])) ? '<img width="124" height="166" src="photos/' . $row['photo'] . '" />' : '<img src="photos/no-image.png" />') . '</div>
                    <div class="borderBottom" style="border-color:'.$row['color'].'"></div>
                    ';

    echo '<div class="permisions"><div class="lcol">';
    $sqlp = 'SELECT * FROM permisions';
    $resp = $db->query($sqlp);

    if ($resp->num_rows) {
        $num = $resp->num_rows;

        $mid = ceil($num / 2);
        $i = 0;

        while ($rowp = $resp->fetch_assoc()) {

            if (in_array($rowp['id'], $row['permisions'])) {

                $i++;
                echo '<div class="permwrap"><img width="33" height="26" src="base/permisions/' . $rowp['flag'] . '" /><div class="p1">' . str_replace(' ', '&nbsp;', $rowp['name']) . '</div><div class="p2">' . $rowp['uk_name'] . '</div></div>';
                if ($i == $mid) {
                    echo ' </div>
                            <div class="rcol">';
                }
            }
        }
    }

    echo '</div>';
    echo'                       </div>

            </div>
            ' . (!empty($settings['backside']) ? '</td><td><div class="backside"><img width="356" height="501" src="photos/' . $settings['backside'] . '"/></div><br class="clear"/>' : '') . '


';

    if(!empty($settings['backside'])){

        if($r % 2 == 0 && $r!= 0 && count($attr)%2 != 0){
          //  echo '</td></tr></table><hr class="pagebreak"/><table><tr><td>';
             echo '<br class="clear"/>';
        }
        //echo '</td></tr><tr><td>';

    }else{
        if($r % 2 == 0){
            //echo '</td></tr><tr><td>';
        }
        if($r % 4 == 0 && $r!= 0 && count($attr)%4 != 0){
           // echo '</td></tr></table><hr class="pagebreak"/><table><tr><td>';
            echo '<br class="clear"/>';
        }
    }
}
?>

</body>
</html>