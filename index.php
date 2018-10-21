<?php
define("SUBREAD", TRUE);
require_once(__DIR__."/config.php");
$t = json_decode(file_get_contents(__DIR__."/lang/".$config["lang"].".json"),true);
//User timezone detection
$ip     = $_SERVER['REMOTE_ADDR'];
$json   = file_get_contents( 'http://ip-api.com/json/' . $ip);
$ipData = json_decode( $json, true);
if ($ipData['timezone']) {
    $tz = new DateTimeZone($ipData['timezone']);
    date_default_timezone_set($ipData['timezone']);
    $now = new DateTime( 'now', $tz); // DateTime object corellated to user's timezone
} else {
   // we can't determine a timezone - do something else...
}
body();

function head(){
  global $t;
  global $config;
	echo '';
	?>
<!DOCTYPE html>
<html lang="<?= $config["lang"] ?>">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= $t["html.title"] ?></title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/iconfonts/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/favicon.png" />
</head>
	<?php
}

function body(){
	global $config;
	global $t;
	$subreddit = "";
	if($_GET['r']){
		$subreddit = strtolower($_GET['r']);
	} else {
		$subreddit = $config['defaultsubreddit'];
	}
	
	head();
	echo '';
	?>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
        <a class="navbar-brand brand-logo" href="./">
          <img class="img-responsive" src="images/logo.png" alt="logo" />
        </a>
        <a class="navbar-brand brand-logo-mini" href="./">
          <img class="img-responsive" src="images/logo.png" alt="logo" />
        </a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center">
        <ul class="navbar-nav navbar-nav-left header-links d-none d-md-flex">
          <!--<li class="nav-item">
            <a href="#" class="nav-link">Schedule
              <span class="badge badge-primary ml-1">New</span>
            </a>
          </li>-->
          <?= sprintf($t["currentlyBrowsing"],$subreddit) ?>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
			
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item nav-profile">
            <div class="nav-link">
              <div class="user-wrapper">
                <div class="text-wrapper">
                  <p class="profile-name"><?= $t["welcome"] ?></p>
                </div>
              </div>
            </div>
          </li>
          <li class="nav-item">
            <div class="container-fluid">
            	<p><?= $t["welcome.desc"] ?>
            </div>
          </li>
          <li class="nav-item">
          	<a class="nav-link" href="?r=<?php echo $subreddit; ?>&force">
              <i class="menu-icon mdi mdi-refresh"></i>
              <span class="menu-title"><?= $t["ForceRefresh"] ?></span>
			</a>
          </li>
          <li class="nav-item">
          	<a class="nav-link" href="#" data-toggle="modal" data-target="#subreddit">
              <i class="menu-icon mdi mdi-link"></i>
              <span class="menu-title"><?= $t["SelectSubreddit"] ?></span>
			</a>
          </li>
          <br>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#subreddits" aria-expanded="false" aria-controls="subreddits">
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title"><?= $t["CachedSubreddits"] ?></span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="subreddits">
              <ul class="nav flex-column sub-menu">
              	
              	<?php
              	$files = glob(__DIR__."/json/*.json");
              	foreach($files as $file){
              		$subr = str_replace(".json","",basename($file));
              		
              		?>
              		<li class="nav-item">
                		<a class="nav-link" href="?r=<?php echo $subr;?>"><?php echo $subr; ?></a>
                	</li>
              		<?php
              	}
              	?>
                
              </ul>
            </div>
			</li>
        </ul>
      </nav>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <?php
          //Body here
          //Parse and display body here
          include(__DIR__."/pages/home.php");
          ?>
        </div>
        <!-- content-wrapper ends -->
        <?php foot(); ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="vendors/js/vendor.bundle.addons.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/misc.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="js/dashboard.js"></script>
  <!-- End custom js for this page-->
 <div class="modal" id="subreddit">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?= $t["SelectSubreddit"] ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form class="form">
        	<input class="form-control" type="text" placeholder="wallpapers" name="r" id="r">
        </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><?= $t["close"] ?></button>
      </div>

    </div>
  </div>
</div>
</body>
</html>
	<?php
}


function foot(){
	echo '';
	?>
	<!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div class="container-fluid clearfix">
            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © 2018
              <a href="https://urgero.org" target="_blank">Mitchell Urgero</a>. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Made with <i class="mdi mdi-heart text-danger"></i></span>
          </div>
        </footer>
	<?php
}


?>