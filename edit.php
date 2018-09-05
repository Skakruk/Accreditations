<?php
include('init.php');
if (!isset($_SESSION['logined'])) {
    header('Location: login.php');
}


if (isset($_POST['save'])) {


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
    foreach ($sql_data as $k => $v) {
        $sql_d[] = "`" . $k . "`='" . $v . "'";
    }
    $sql = "UPDATE users SET " . implode(',', $sql_d) . " WHERE id='" . $db->real_escape_string($_GET['id']) . "'";
    $db->query($sql);
    header('Location: index.php');
}

if (isset($_GET['id'])) {
    $sql = "SELECT * FROM users WHERE id =" . $db->real_escape_string($_GET['id']) . "";
    $res = $db->query($sql);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
    }
    $row['permisions'] = json_decode($row['permisions'], true);
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
    if (!empty($row['category'])) {
        echo 'var selCat = "' . $row['category'] . '";' . "\n\t";
    } else {
        echo 'var selCat = false;' . "\n\t";
    }

    if (!empty($row['country'])) {
        echo 'var selCountry = "' . $row['country'] . '";' . "\n\t";
    } else {
        echo 'var selCountry = false;' . "\n\t";
    }

    if (!empty($row['arccat'])) {
        echo 'var selSubCat = "' . $row['arccat'] . '";' . "\n";
    } else {
        echo 'var selSubCat = false;' . "\n\t";
    }

    if (!empty($row['city'])) {
        echo 'var selCity = "' . $row['city'] . '";' . "\n";
    } else {
        echo 'var selCity = false;' . "\n\t";
    }

    if (!empty($row['org'])) {
        echo 'var selOrg = "' . $row['org'] . '";' . "\n";
    } else {
        echo 'var selOrg = false;' . "\n\t";
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
        $('#photoup').fileupload({
            url: '/scripts/participant.php',
            dataType: 'json',
            formData: {folder: 'photos'},
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
                    aspectRatio: 0.75
                }, function () {
                    jcrop_api = this;
                });
                //
                $('.modal-footer a').on('click', function (e) {
                    e.preventDefault();
                    var data = jcrop_api.tellSelect();
                    data.img = photo.name;
                    $.post('/ajaxfunctions.php?action=crop_image', data, function () {
                        $('#addPhotoModal').modal('hide');
                        $(".curPhoto img").attr("src", photo.url + "?" + e.timeStamp);
                        $(".curPhoto").show();
                    }, 'json');
                    $(this).off('click');
                    jcrop_api.destroy();
                });
            }
        });

        if (subcats && selCat) {
            var itmtr = false;
            var ech = '<option disabled="disabled">Select</option>';
            $.each(subcats, function (index, item) {
                if (item.parent == selCat) {
                    itmtr = true;
                    var sel = ((selSubCat == item.id) ? 'selected="selected" ' : '');
                    ech += '<option ' + sel + 'value="' + item.id + '">' + item.name + '</option>';
                }
            })
            if (itmtr) {
                $('#subcat').show();
                $('#subcat select').html(ech);
            } else {
                $('#subcat').hide();
                $('#subcat select').html(' ');
            }
        }
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
        })


        if (cities) {
            var opt = '<option disabled="disabled" selected="selected">Select</option>';
            $.each(cities, function (index, item) {
                if (item.country == selCountry) {
                    var act = ((item.id == selCity) ? 'selected="selected" ' : '');
                    opt += '<option ' + act + 'value="' + item.id + '">' + item.name + '</option>';
                }
            });
            opt += '<option value="_add">&nbsp;+ Add</option>';
            $('select[name=city]').html(opt);
        }


        $('select[name=country]').change(function () {
            var me = this;
            var opt = '<option disabled="disabled" selected="selected">Select</option>';
            if (cities) {
                $.each(cities, function (index, item) {
                    if (item.country == $(me).val()) {
                        var act = ((selCity && item.id == selCity) ? 'selected="selected" ' : '');
                        opt += '<option ' + act + 'value="' + item.id + '">' + item.name + '</option>';
                    }
                })
            }
            opt += '<option value="_add">&nbsp;+ Add</option>';
            $('#city_wrap').show();
            $('select[name=city]').html(opt);
            $('#org_wrap').hide();
            $('select[name=org]').html('');
        });


        if (orgs) {
            var opt = '<option disabled="disabled" selected="selected">Select</option>';
            if (orgs) {
                $.each(orgs, function (index, item) {
                    if (item.city == selCity) {
                        var act = ((item.id == selOrg) ? 'selected="selected" ' : '');
                        opt += '<option ' + act + 'value="' + item.id + '">' + item.name + '</option>';
                    }
                })
            }
            opt += '<option value="_add">&nbsp;+ Add</option>';
            $('select[name=org]').html(opt);
        }

        $('select[name=city]').change(function () {
            var me = this;
            var opt = '<option disabled="disabled" selected="selected">Select</option>';
            if (orgs) {
                $.each(orgs, function (index, item) {
                    if (item.city == $(me).val()) {
                        var act = ((selOrg && item.id == selOrg) ? 'selected="selected" ' : '');
                        opt += '<option ' + act + 'value="' + item.id + '">' + item.name + '</option>';
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
        })
    });
</script>
<?php echo(!empty($row['photo']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/photos/' . $row['photo']) ? '<style> #photoupUploader{display:none;} </style>' : '') ?>
<div id="header">
</div><!-- #header-->


<div id="content">
    <div id="addform">
        <form method="POST" class="form-horizontal">
            <input type="hidden" name="photo" id="photo" value="<?php echo $row['photo']; ?>" autocomplete="off" />
            <div class="control-group">
                <label class="control-label">Name</label>
                <div class="controls">
                    <input type="text" name="name"
                           value="<?php echo htmlentities($row['name'], ENT_QUOTES, 'UTF-8'); ?>" class="span5" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Surname</label>
                <div class="controls">
                    <input type="text" name="surname"
                           value="<?php echo htmlentities($row['surname'], ENT_QUOTES, 'UTF-8'); ?>" class="span5" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Country</label>
                <div class="controls">

                    <select type="text" name="country" class="span5">
                        <option disabled="disabled">Select</option>
                        <?php
                        $sql = 'SELECT * FROM countries';
                        $res = $db->query($sql);
                        if ($res->num_rows) {
                            while ($rowco = $res->fetch_assoc()) {
                                $act = ($row['country'] == $rowco['id']) ? 'selected="selected" ' : '';
                                echo '<option ' . $act . 'value="' . $rowco['id'] . '">' . $rowco['name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="control-group" id="city_wrap">
                <label class="control-label">City</label>
                <div class="controls">
                    <select type="text" name="city" class="span5" autocomplete="off">
                        <option disabled="disabled" selected="selected">Select</option>
                        <option value="_add">&nbsp;+ Add</option>
                    </select>
                </div>
            </div>
            <div id="city_add" style="display:none" class="control-group">
                <label class="control-label">&nbsp;</label>
                <div class="controls">
                    <input type="text" name="city_add" class="span5" />
                    <a class="btn small primary add" href="#">Add</a>
                    <a class="btn small cancel" href="#">Cancel</a>
                </div>
            </div>
            <div class="control-group" id="org_wrap">
                <label class="control-label">Organization</label>
                <div class="controls">
                    <select type="text" name="org" class="span5" autocomplete="off">
                        <option disabled="disabled" selected="selected">Select</option>
                        <option value="_add">&nbsp;+ Add</option>
                    </select>
                </div>
            </div>
            <div id="org_add" style="display:none" class="control-group">
                <label class="control-label">&nbsp;</label>
                <div class="controls">
                    <input type="text" name="org_add" class="span5" />
                    <a class="btn small primary add" href="#">Add</a>
                    <a class="btn small cancel" href="#">Cancel</a>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Category</label>
                <div class="controls">
                    <select type="text" id="category" name="category" class="span5">
                        <option>Select</option>
                        <?php
                        $sqlc = 'SELECT * FROM category WHERE parent = "0"';
                        $resc = $db->query($sqlc);
                        if ($resc->num_rows) {
                            while ($rowcc = $resc->fetch_assoc()) {
                                $actc = ($row['category'] == $rowcc['id']) ? 'selected="selected" ' : '';
                                echo '<option ' . $actc . 'value="' . $rowcc['id'] . '">' . $rowcc['name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div id="subcat" class="control-group" style="display:none;">
                <label class="control-label">Sub category</label>
                <div class="controls">
                    <select type="text" name="arccat" class="span5">
                        <option disabled="disabled">Select</option>

                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="xlInput">Photo</label>
                <div class="controls">
                     <span class="btn btn-success fileinput-button">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Select files...</span>
                        <input id="photoup" type="file" name="files" />
                    </span>
                    <div class="curPhoto" <?php echo (!empty($row['photo']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/photos/' . $row['photo'])) ? '' : 'style="display:none;"' ?>>
                        <img src="/photos/<?php echo $row['photo'] ?>" />
                        <a href="#" class="btn btn-mini btn-danger deletePhoto"><i class="icon-remove icon-white"></i>
                            Delete photo</a>
                    </div>
                    <div id="addPhotoModal" class="modal hide fade">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div id="imgwrap">
                                <img class="imgholder" id="jcrop_target" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-primary">Save changes</a>
                        </div>
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
                            if (in_array($rowp['id'], $row['permisions'])) {
                                $act = 'checked="checked" ';
                            } else {
                                $act = '';
                            }
                            echo '
													<label class="checkbox">
														<input type="checkbox" ' . $act . 'value="' . $rowp['id'] . '" name="permisions[]">
														<span>' . $rowp['name'] . '</span>
													</label>
												';
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="control-group">
                <label for="xlInput">&nbsp;</label>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" name="save" value="Save"><i
                                class="icon-ok icon-white"></i> Save changes
                    </button>
                    <a href="index.php" class="btn" />Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div><!-- #content-->

<?php
include('template/footer.php');
?>
