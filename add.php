<?php
include('init.php');
if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}
if (isset($_POST['save'])) {

    if ($_POST['category'] != 'Archer') {
        $_POST['arccat'] = ' ';
    }

    $sql_data = array(
        'name' => $db->real_escape_string($_POST['name']),
        'surname' => $db->real_escape_string($_POST['surname']),
        'country' => $db->real_escape_string($_POST['country']),
        'category' => $db->real_escape_string($_POST['category']),
        'arccat' => $db->real_escape_string($_POST['arccat']),
        'photo' => $db->real_escape_string($_POST['photo']),
        'city' => $db->real_escape_string($_POST['city']),
        'org' => $db->real_escape_string($_POST['org']),
        'permisions' => json_encode($_POST['permisions'])
    );
    $sql = "INSERT INTO users (`" . implode("`,`", array_keys($sql_data)) . "`) VALUES ('" . implode("','", array_values($sql_data)) . "')";

    $db->query($sql);
    header('Location: index.php');
}

include('template/header.php');
?>
<link rel="stylesheet" href="css/jquery.fileupload.css">
<link rel="stylesheet" href="css/jquery.Jcrop.min.css">
<script type="text/javascript" src="js/vendor/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/jquery.fileupload.js"></script>
<script type="text/javascript" src="scripts/jquery.Jcrop.min.js"></script>
<script type="text/javascript">
    <?php
    $sqlc = 'SELECT * FROM category WHERE parent != "0"';
    $resc = $db->query($sqlc);
    if ($resc->num_rows) {
        while ($rowcc = $resc->fetch_assoc()) {
            $subcats[] = array('id' => $rowcc['id'], 'parent' => $rowcc['parent'], 'name' => $rowcc['name']);
        }
        echo 'var subcats = ' . json_encode($subcats) . ';' . "\n\t";
    }


    $sqlcity = 'SELECT * FROM cities';
    $rescity = $db->query($sqlcity);
    if ($rescity->num_rows) {
        while ($rowccity = $rescity->fetch_assoc()) {
            $cities[] = $rowccity;
        }
        echo 'var cities = ' . json_encode($cities) . ';' . "\n\t";
    }

    $sqlorg = 'SELECT * FROM orgs';
    $resorg = $db->query($sqlorg);
    if ($resorg->num_rows) {
        while ($roworg = $resorg->fetch_assoc()) {
            $org[] = $roworg;
        }
        echo 'var orgs = ' . json_encode($org) . ';' . "\n\t";
    }
    ?>
    $(function () {
        var jcrop_api = null;

        $('#addPhotoModal')
            .bind('hidden', function () {
                $('.modal-placeholder').hide();
            })
            .bind('show', function () {
                $('.modal-placeholder').show();
            });

        $('#photoup').fileupload({
            url: '/scripts/participant.php',
            formData: { folder: 'photos' },
            dataType: 'json',
            done: function (e, data) {
                var photo = data.result.files[0];
                $("#photo").val(photo.name);
                $("#photoupUploader").hide();
                $('#addPhotoModal img').attr('src', photo.url);
                $('#addPhotoModal').modal('show');
                //
                $('#jcrop_target').Jcrop({
                    addClass: 'custom',
                    bgColor: 'yellow',
                    bgOpacity: .8,
                    sideHandles: true,
                    aspectRatio: 0.75,
                    boxHeight: document.body.clientHeight - 300,
                }, function () {
                    jcrop_api = this;
                });
                //
            }
        });

        $('.modal-footer .save-changes').on('click', function (e) {
            e.preventDefault();
            var data = jcrop_api.tellSelect();
            data.img = $("#photo").val();

            $.post('/ajaxfunctions.php?action=crop_image', data, function () {
                $('#addPhotoModal').modal('hide');
                $(".curPhoto img").attr("src", '/photos/' + data.img + "?" + e.timeStamp);
                $(".curPhoto").show();
            }, 'json');

            jcrop_api.destroy();
        });

        $("#category").change(function () {
            var me = this, itmtr = false;
            var ech = '<option disabled="disabled">Select</option>';
            $.each(subcats, function (index, item) {
                if (item.parent == $(me).val()) {
                    itmtr = true;
                    ech += '<option value="' + item.id + '">' + item.name + '</option>';
                }
            })
            if (itmtr) {
                $('#subcat').show();
                $('#subcat select').html(ech);
            } else {
                $('#subcat').hide();
                $('#subcat select').html(' ');
            }
        });

        $('select[name=country]').change(function () {
            var me = this;
            var opt = '<option disabled="disabled" selected="selected">Select</option>';
            if (cities) {
                $.each(cities, function (index, item) {
                    if (item.country == $(me).val()) {
                        opt += '<option value="' + item.id + '">' + item.name + '</option>';
                    }
                })
            }
            opt += '<option value="_add">&nbsp;+ Add</option>';
            $('#city_wrap').show();
            $('select[name=city]').html(opt);
            $('#org_wrap').hide();
            $('select[name=org]').html('');
        });

        $('select[name=city]').change(function () {
            var me = this;
            var opt = '<option disabled="disabled" selected="selected">Select</option>';
            if (orgs) {
                $.each(orgs, function (index, item) {
                    if (item.city == $(me).val()) {
                        opt += '<option value="' + item.id + '">' + item.name + '</option>';
                    }
                })
            }
            opt += '<option value="_add">&nbsp;+ Add</option>';
            $('#org_wrap').show();
            $('select[name=org]').html(opt);
        });

        $('select[name=city]').change(function () {
            if ($(this).val() == '_add') {
                $('#city_add').slideDown();
            } else {
                $('#city_add').slideUp();
            }
        });

        $('#city_add .cancel').click(function (e) {
            e.preventDefault();
            $('#city_add').slideUp();
            $('input[name=city_add]').val('');
            $('select[name=city] option:eq(0)').attr('selected', true);
            $('#org_wrap').hide();
        });

        $('#city_add .add').click(function (e) {
            e.preventDefault();
            $('#city_add').slideUp();
            var inp = $('input[name=city_add]');
            $.post('/ajaxfunctions.php?action=add_city', {
                city: $(inp).val(),
                country: $('select[name=country]').val()
            }, function (data) {
                $('select[name=city] option').removeAttr('selected')
                var len = $('select[name=city] option').length;
                len = len - 1;
                $('select[name=city] option:eq(' + len + ')').before('<option selected="selected" value="' + data.id + '">' + $(inp).val() + '</option>');
                $(inp).val('');

            }, "json");
        })


        $('select[name=org]').change(function () {
            if ($(this).val() == '_add') {
                $('#org_add').slideDown();
            } else {
                $('#org_add').slideUp();
            }
        });

        $('#org_add .cancel').click(function (e) {
            e.preventDefault();
            $('#org_add').slideUp();
            $('input[name=org_add]').val('');
            $('select[name=org] option:eq(0)').attr('selected', true);
        });

        $('#org_add .add').click(function (e) {
            e.preventDefault();
            $('#org_add').slideUp();
            var inp = $('input[name=org_add]');

            $.post('/ajaxfunctions.php?action=add_org', {
                org: $(inp).val(),
                city: $('select[name=city]').val()
            }, function (data) {
                $('select[name=org] option').removeAttr('selected')
                var len = $('select[name=org] option').length;
                len = len - 1;
                $('select[name=org] option:eq(' + len + ')').before('<option selected="selected" value="' + data.id + '">' + $(inp).val() + '</option>');
                $(inp).val('');
            }, "json");

        });

        $('.deletePhoto').on('click', function (e) {
            e.preventDefault();
            $.post('/ajaxfunctions.php?action=delete_photo', { photo_name: $('#photo').val() }, function () {
                $('#photo').val('');
                $('.curPhoto').hide();
                $("#photoupUploader").show();
            });
        });

    });
</script>
<div id="header">

</div><!-- #header-->
<div id="content">
    <div id="addform">
        <form method="POST" class="form-horizontal">
            <input type="hidden" name="photo" id="photo" value="" autocomplete="off" />
            <div class="clearfix control-group">
                <label class="control-label">Surname</label>
                <div class="controls">
                    <input type="text" name="surname" class="input-xlarge" />
                </div>
            </div>

            <div class="clearfix control-group">
                <label class="control-label">Name</label>
                <div class="controls">
                    <input type="text" name="name" class="input-xlarge" />
                </div>
            </div>

            <div class="clearfix control-group">
                <label class="control-label">Country</label>
                <div class="controls">

                    <select type="text" name="country" class="input-xlarge">
                        <option disabled="disabled" selected="selected">Select</option>
                        <?php
                        $sql = 'SELECT * FROM countries';
                        $res = $db->query($sql);
                        if ($res->num_rows) {
                            while ($rowco = $res->fetch_assoc()) {
                                echo '<option value="' . $rowco['id'] . '">' . $rowco['name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="clearfix control-group" id="city_wrap" style="display:none">
                <label class="control-label">City</label>
                <div class="controls">

                    <select type="text" name="city" class="input-xlarge" autocomplete="off">
                        <option disabled="disabled" selected="selected">Select</option>
                        <option value="_add">&nbsp;+ Add</option>
                    </select>
                </div>
            </div>
            <div id="city_add" style="display:none" class="clearfix control-group">
                <label>&nbsp;</label>
                <div class="controls">
                    <input type="text" name="city_add" class="input-xlarge" />
                    <a class="btn small primary add" href="#">Add</a>
                    <a class="btn small cancel" href="#">Cancel</a>
                </div>
            </div>
            <div class="clearfix control-group" id="org_wrap" style="display:none">
                <label class="control-label">Organization</label>
                <div class="controls">
                    <select type="text" name="org" class="input-xlarge" autocomplete="off">
                        <option disabled="disabled" selected="selected">Select</option>
                        <option value="_add">&nbsp;+ Add</option>
                    </select>
                </div>
            </div>
            <div id="org_add" style="display:none" class="clearfix control-group">
                <label class="control-label">&nbsp;</label>
                <div class="controls">
                    <input type="text" name="org_add" class="input-xlarge" />
                    <a class="btn small primary add" href="#">Add</a>
                    <a class="btn small cancel" href="#">Cancel</a>
                </div>
            </div>

            <div class="clearfix control-group">
                <label class="control-label">Category</label>
                <div class="controls">
                    <select type="text" id="category" name="category" class="input-xlarge" autocomplete="off">
                        <option>Select</option>
                        <?php
                        $sqlc = 'SELECT * FROM category WHERE parent = "0"';
                        $resc = $db->query($sqlc);
                        if ($resc->num_rows) {
                            while ($rowcc = $resc->fetch_assoc()) {
                                echo '<option value="' . $rowcc['id'] . '">' . $rowcc['name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div id="subcat" class="clearfix control-group" style="display:none;">
                <label class="control-label">Sub. category</label>
                <div class="controls">
                    <select type="text" name="arccat" class="input-xlarge">
                        <option disabled="disabled">Select</option>

                    </select>
                </div>
            </div>


            <div class="clearfix control-group">
                <label class="control-label" for="xlInput">Photo</label>
                <div class="controls">
                        <span class="btn btn-success fileinput-button">
                            <span>Select files...</span>
                            <input id="photoup" type="file" name="files" />
                        </span>
                    <div class="curPhoto" style="display:none;">
                        <img src="" />
                        <a href="#" class="btn btn-mini btn-danger deletePhoto"><i
                                    class="icon-remove icon-white"></i> Delete photo</a>
                    </div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Pemisions</label>
                <div class="controls">
                    <?php
                    $sqlp = 'SELECT * FROM permisions';
                    $resp = $db->query($sqlp);
                    if ($resp->num_rows) {
                        while ($rowp = $resp->fetch_assoc()) {
                            echo '
                                  <label class="checkbox">
                                    <input type="checkbox" value="' . $rowp['id'] . '" name="permisions[]">
                                    ' . $rowp['name'] . '
                                  </label>
                                ';
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="clearfix control-group">
                <label for="xlInput">&nbsp;</label>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" name="save" value="Save"><i
                                class="icon-ok icon-white"></i> Save
                    </button>
                    <button type="submit" class="btn btn-primary" name="savencontinue" value="Save"><i
                                class="icon-ok icon-white"></i> Save &amp; continue
                    </button>
                    <a href="index.php" class="btn" />Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div><!-- #content-->
<div class="modal-placeholder" style="display: none;">
    <div id="addPhotoModal" class="modal hide fade">
        <div class="modal-body">
            <div id="imgwrap">
                <img class="imgholder" id="jcrop_target" />
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default" type="button" onclick="cancelUploadPhoto()">Cancel</button>
            <a href="#" class="btn btn-primary save-changes">Save changes</a>
        </div>
    </div>
</div>
<?php
include('template/footer.php');
?>
