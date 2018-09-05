<?php
if (isset($_POST['saveimg'])) {
    require_once 'classes/ThumbLib.inc.php';
    $thumb = PhpThumbFactory::create($_SERVER['DOCUMENT_ROOT'] . $_POST['img']);
    $thumb->setOptions(array('jpegQuality' => 100));
    if (!empty($_POST['x1'])) {
        $thumb->crop($_POST['x1'], $_POST['y1'], $_POST['w'], $_POST['h'])->adaptiveResize(124, 166);
    } else {
        $thumb->adaptiveResize(124, 166);
    }
    $thumb->save($_SERVER['DOCUMENT_ROOT'] . $_POST['img']);

}
echo $_REQUEST['folder'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title></title>
    <meta name="title" content="" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" href="style.css" type="text/css" media="screen, projection" />
    <!--[if lte IE 6]>
    <link rel="stylesheet" href="style_ie.css" type="text/css" media="screen, projection" /><![endif]-->
    <script type="text/javascript" src="scripts/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="scripts/jquery.Jcrop.min.js"></script>
    <script language="Javascript">
        $(function () {
            <?php
            if (isset($_POST['saveimg'])) {
                echo '
     $(".curPhoto img", window.parent.document).attr("src", "' . $_POST['img'] . '");
     $(".curPhoto", window.parent.document).show();
     parent.$.colorbox.close();
     ';
            }
            ?>
            $('#jcrop_target').Jcrop({

                addClass: 'custom',
                bgColor: 'yellow',
                bgOpacity: .8,
                sideHandles: true,
                aspectRatio: 0.75,
                onChange: showCoords,
                onSelect: showCoords
            });

        });

        function showCoords(c) {
            jQuery('#x1').val(c.x);
            jQuery('#y1').val(c.y);
            jQuery('#x2').val(c.x2);
            jQuery('#y2').val(c.y2);
            jQuery('#w').val(c.w);
            jQuery('#h').val(c.h);
        };
    </script>
</head>

<body>
    <form id="cropimgform" method="post">
        <input type="hidden" size="4" id="x1" name="x1" />
        <input type="hidden" size="4" id="y1" name="y1" />
        <input type="hidden" size="4" id="x2" name="x2" />
        <input type="hidden" size="4" id="y2" name="y2" />
        <input type="hidden" size="4" id="w" name="w" />
        <input type="hidden" size="4" id="h" name="h" />
        <input type="hidden" value="<?php echo $_GET['img']; ?>" name="img" />
        <div id="imgwrap">
            <img src="<?php echo $_GET['img']; ?>" id="jcrop_target" />
            <br clear="all" />
        </div>
        <input type="submit" name="saveimg" value="Save">
    </form>
</body>
</html>

