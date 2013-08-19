<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Accreditation</title>
	<meta name="title" content="" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/css/style.css" type="text/css" media="screen, projection" />
    <script type="text/javascript" src="scripts/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
	<!--[if lte IE 6]><link rel="stylesheet" href="/css/style_ie.css" type="text/css" media="screen, projection" /><![endif]-->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>

<body>

<div class="container-fluid" id="wrapper">

	<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
        
          <a class="brand" href="/">Accreditations</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Participants <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li class="<?=($page=='add'?'active':'')?>"><a href="/add.php">Add new</a></li>      
                  <li class="<?=($page=='index'?'active':'')?>"><a href="/">List</a></li>
                  <!--<li class="<?=($page=='targets'?'active':'')?>"><a href="/targets.php">Targets</a></li>     -->    
                  <li class="<?=($page=='backcards'?'active':'')?>"><a href="/backcards.php">Back numbers</a></li>
                </ul>
              </li>
               <li class="dropdown">
               	 <a href="#" class="dropdown-toggle" data-toggle="dropdown">Settings <b class="caret"></b></a>
              	<ul class="dropdown-menu">
					<li class="<?=($page=='frontside'?'active':'')?>"><a href="badgeimages.php">Badge images</a></li>
					<li class="<?=($page=='frontside'?'active':'')?>"><a href="backnumber.php">Back Number images</a></li>
				    <li class="<?=($page=='countries'?'active':'')?>"><a href="countries.php">Countries</a></li>
				    <li class="<?=($page=='cities'?'active':'')?>"><a href="cities.php">Cities</a></li>
				    <li class="<?=($page=='organizations'?'active':'')?>"><a href="organizations.php">Orgs</a></li>
				    <li class="<?=($page=='maincategories'?'active':'')?>"><a href="maincategories.php">Categories</a></li>
				    <li class="<?=($page=='permisions'?'active':'')?>"><a href="permisions.php">Permisions</a></li>
             <li class="<?=($page=='updater'?'active':'')?>"><a href="updater.php">Update</a></li>
    			</ul>
    		</li>
            </ul>
            <div class="navbar-form pull-right">
              <a class="btn btn-danger" href="login.php?logout">Logout</a>
            </div>
          </div><!--/.nav-collapse -->
        </div>
      </div> 
    </div>