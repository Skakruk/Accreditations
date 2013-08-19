<?php
include('init.php');

if(isset($_GET['logout'])){
    $_SESSION = array();
    unset($_SESSION);
    session_destroy();
    header('Location: index.php');
}

if(isset($_SESSION['logined'])){
    header('Location: index.php');
}
if(isset($_POST['submit'])){

    if($_POST['name'] == 'manager' && $_POST['pass'] == 'LAFUA'){
        $_SESSION['logined'] = true;
        header('Location: index.php');
    }
}
include('template/header.php');
?>

	<div id="header">
		</div><!-- #header-->

	<div id="content">
            <div style="border: none; padding: 40px;" class="well">
        <!-- Modal -->
        <div style="position: relative; top: auto; left: auto; margin: 0 auto; z-index: 1" class="modal">
          <div class="modal-header">
            <h3>Login</h3>

          </div>
             <form method="POST">
              <div class="modal-body">
                    <div class="clearfix">
                        <label>Username</label>
                        <div class="input">
                           <input type="text" name="name" class="xlarge"/>
                        </div>
                    </div>
                     <div class="clearfix">
                        <label>Password</label>
                        <div class="input">
                           <input type="password" name="pass"class="xlarge"/>
                        </div>
                    </div>   
              </div>
              <div class="modal-footer">
                <input type="submit" name="submit" value="Login" class="btn primary"/>
                
              </div>
            </form>
        </div>
      </div>

		</div><!-- #content-->
<?php
include('template/footer.php');
?>