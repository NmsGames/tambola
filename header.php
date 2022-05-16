<?php 
session_start();
if (!isset($_SESSION["user"]))
{
    echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php\">";
    exit;
}

$activePage = basename($_SERVER['PHP_SELF'], ".php");
?>

<!DOCTYPE html>
<html lang="en">
<?php 
function pathUrl($dir = __DIR__){ 
  $root = "";
  $dir = str_replace('\\', '/', realpath($dir)); 
  //HTTPS or HTTP
  $root .= !empty($_SERVER['HTTPS']) ? 'https' : 'http'; 
  //HOST
  $root .= '://' . $_SERVER['HTTP_HOST']; 
  //ALIAS
  if(!empty($_SERVER['CONTEXT_PREFIX'])) {
      $root .= $_SERVER['CONTEXT_PREFIX'];
      $root .= substr($dir, strlen($_SERVER[ 'CONTEXT_DOCUMENT_ROOT' ]));
  } else {
      $root .= substr($dir, strlen($_SERVER[ 'DOCUMENT_ROOT' ]));
  } 
  $root .= '/'; 
  return $root;
}
$host  = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$baseurl = "http://" . $host . $path . "/";?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Category</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="./plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css"> -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./dist//css//adminlte.min.css">
  <link rel="stylesheet" href="./dist/css/datatable.min.css">
  <link rel="stylesheet" href="./dist/css/custom.css">
  <!-- <link rel="stylesheet" href="/pages//style.css"> -->
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
          <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="fas fa-search"></i>
          </a>
          <div class="navbar-search-block">
            <form class="form-inline">

            </form>
          </div>
        </li>
        <!-- Notifications Dropdown Menu -->

        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?= pathUrl(); ?>Api/logout.php" role="button">
            <i class="fas fa-sign-out"></i>
            Logout
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index3.html" class="brand-link">
              <img src="dist/img/AdminLTELogo.png" alt="tambola" class="brand-image img-circle elevation-3"
                style="opacity: .8">
              <span class="brand-text font-weight-light">Tambola</span>
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
              <!-- Sidebar Menu -->
              <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                  data-accordion="false">
                  <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                  <li class="nav-item">
                    <a href="<?php echo $baseurl;?>category.php" class="nav-link <?= ($activePage == 'category') ? 'active':''; ?>">
                      <i class="nav-icon fas fa-th"></i>
                      <p>
                        Add Category
                      </p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= pathUrl();?>ticketsUpload.php" class="nav-link <?= ($activePage == 'ticketsUpload') ? 'active':''; ?>">
                      <i class="nav-icon fas fa-th"></i>
                      <p>
                       Upload Ticket
                      </p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= pathUrl();?>users.php" class="nav-link <?= ($activePage == 'users') ? 'active':''; ?>">
                      <i class="nav-icon fas fa-th"></i>
                      <p>
                       Users
                      </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= pathUrl();?>ticketHistory.php" class="nav-link <?= ($activePage == 'ticketHistory') ? 'active':''; ?>">
                      <i class="nav-icon fas fa-th"></i>
                      <p>
                       Ticket Purchase History
                      </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= pathUrl();?>addUser.php" class="nav-link <?= ($activePage == 'addUser') ? 'active':''; ?>">
                      <i class="nav-icon fas fa-th"></i>
                      <p>
                       Create User
                      </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= pathUrl();?>walletManager.php" class="nav-link <?= ($activePage == 'walletManager') ? 'active':''; ?>">
                      <i class="nav-icon fas fa-th"></i>
                      <p>
                       Wallet Manager
                      </p>
                    </a>
                </li>
            </div>
            <!-- /.sidebar -->
          </aside>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

   