<?php
include('init.php');
if(!isset($_SESSION['logined'])){
    header('Location: login.php');
}
include('template/header.php');
?>
<div id="header">
   
		</div><!-- #header-->
                 <div id="content">
                 </div>
<?php
include('template/footer.php');
?>