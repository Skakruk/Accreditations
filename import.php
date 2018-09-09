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
        $pathname = './uploads/' . $filename;
        move_uploaded_file($_FILES["file"]["tmp_name"], './uploads/' . $filename);

        ini_set('include_path', ini_get('include_path') . ';../classes/');

        include 'classes/PHPExcel/IOFactory.php';

        $objPHPExcel = PHPExcel_IOFactory::load($pathname);

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        $archery = $db->query('SELECT id FROM category WHERE name="Archer"')->fetch_assoc();

        $archCategoriesIdList = array();
        $res = $db->query("SELECT id, name FROM category WHERE parent = '${archery['id']}'");
        while ($row = $res->fetch_assoc()) {
            $archCategoriesIdList[$row['name']] = $row['id'];
        }

        $countriesIdList = array();
        $res = $db->query('SELECT id, name FROM countries');
        while ($row = $res->fetch_assoc()) {
            $countriesIdList[$row['name']] = $row['id'];
        }

        $citiesIdList = array();
        $citiesToCountriesList = array();
        $res = $db->query('SELECT id, name, country FROM cities');
        while ($row = $res->fetch_assoc()) {
            $citiesIdList[$row['name']] = $row['id'];
            $citiesToCountriesList[$row['id']] = $row['country'];
        }

        $orgsIdList = array();
        $res = $db->query('SELECT id, name, city FROM orgs');
        while ($row = $res->fetch_assoc()) {
            if (!isset($orgsIdList[$row['city']])) $orgsIdList[$row['city']] = array();

            $orgsIdList[$row['city']][$row['name']] = $row['id'];
        }

        if (!($stmt = $db->prepare("INSERT INTO users(name, surname, country, category, arccat, city, org) VALUES (?,?,?,?,?,?,?)"))) {
            echo "Prepare failed: (" . $db->errno . ") " . $db->error;
        }

        if (isset($sheetData) && is_array($sheetData)) {
            $participantsCount = 0;

            foreach ($sheetData as $rowNum => $row) {
                if ($rowNum == 1) continue;
                if (is_null($row["B"]) || empty($row["B"])) continue;

                $participantCategory = array(0 => $row["I"]);

                if (strtolower($row["J"]) !== "senior") {
                    $participantCategory[] = substr($row["J"], 0, 1);
                }

                $participantCategory[] = $row["D"];

                $city = isset($citiesIdList[$row["F"]]) ? $citiesIdList[$row["F"]] : 0;

                if (!empty($row["E"]) && isset($countriesIdList[$row["E"]])) {
                    $country = $countriesIdList[$row["E"]];
                } else if ($city !== 0) {
                    if (isset($citiesToCountriesList[$city])) {
                        $country = $citiesToCountriesList[$city];
                    } else {
                        $country = 0;
                    }
                } else {
                    $country = 0;
                }

                $participant = array(
                    "name" => $row["C"],
                    "surname" => $row["B"],
                    "country" => $country,
                    "category" => $archery['id'],
                    "arccat" => isset($archCategoriesIdList[strtoupper(join("", $participantCategory))]) ? $archCategoriesIdList[strtoupper(join("", $participantCategory))] : 0,
                    "city" => $city,
                    "org" => isset($orgsIdList[$city]) && isset($orgsIdList[$city][$row["G"]]) ? $orgsIdList[$city][$row["G"]] : 0
                );

                $params = array();
                $param_type = 'ssisiii';
                $params[] = & $param_type;
                $values = array_values($participant);

                for ($i = 0; $i < count($values); $i++){
                    $params[] = & $values[$i];
                }

                if (!call_user_func_array(array($stmt, 'bind_param'), $params)) {
                    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;

                    continue;
                } else {
                    $participantsCount++;

                    $stmt->execute();
                }
            }

            $stmt->close();

            echo '
            <div id="alert" class="alert-message success">
                <a href="#" class="close">×</a>
                <p>Successfully imported ' . $participantsCount . ' participants</p>
            </div>
            ';
        }
    }
}

?>
<div class="header">
    <h3>Import list of participants</h3>
</div>

<div class="content">
    <div id="addform">
        <form method="POST" action="import.php" class="form-horizontal" enctype="multipart/form-data">
            <fieldset>
                <legend>Select file (*.xls/*.xlsx)</legend>
                <div class="control-group">
                    <label class="control-label">File</label>
                    <div class="controls">
                        <input type="file" name="file" class="xlarge" />
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="form-actions">
                        <button type="submit"
                                class="btn btn-primary"
                                name="save" value="Save">
                            <i class="icon-ok icon-white"></i> Upload
                        </button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
