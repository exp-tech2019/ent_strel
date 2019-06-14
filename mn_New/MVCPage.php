<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 2 | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <link rel="stylesheet" href="css/my.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="scripts/Global.js"></script>
</head>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<input id="idLogin" type="hidden" value="<?php echo $_SESSION["idLogin"]; ?>">
<div class="wrapper">

  <header class="main-header">
    <a href="index.php" class="logo">
      <span class="logo-mini"><b>M</b>ng</span>
      <span class="logo-lg"><b>Ent</b> маректинг</span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <div class="navbar-custom-menu">

      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
    <?php
        $MVCPage=isset($_GET["MVCPage"]) ? $_GET["MVCPage"] : "OrderList"
    ?>
  <aside class="main-sidebar">
    <section class="sidebar">
      <!--Левое меню-->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header"></li>
        <li onclick="window.location.href='?MVCPage=Import'" class="<?php echo $MVCPage=="Import" ? "active menu-open" : ""; ?> treeview">
          <a href="?MVCPage=StockList">
            <i class="fa fa-plus"></i> <span>Импорт</span>
          </a>
        </li>
        <li onclick="window.location.href='?MVCPage=OrderList'" class="<?php echo $MVCPage=="OrderList" || $MVCPage=="OrderOne" ? "active menu-open" : ""; ?> treeview">
          <a href="?MVCPage=StockList">
            <i class="fa fa-industry"></i> <span>Заказы</span>
          </a>
        </li>
        <li onclick="window.location.href='?MVCPage=ClientList'" class="<?php echo $MVCPage=="CientList" || $MVCPage=="ClientOne" ? "active menu-open" : ""; ?> treeview">
          <a href="?MVCPage=StockList">
            <i class="fa fa-users"></i> <span>Клиенты</span>
          </a>
        </li>
        <li onclick="window.location.href='?MVCPage=PaymentList'" class="<?php echo $MVCPage=="PaymentList" || $MVCPage=="PaymentOne" ? "active menu-open" : ""; ?> treeview">
          <a href="?MVCPage=StockList">
            <i class="fa fa-usd"></i> <span>Платежи</span>
          </a>
        </li>
        <li class="<?php echo $MVCPage=="SettingConstruct" ? "active menu-open" : ""; ?> treeview">
          <a href="#">
            <i class="fa fa-cog"></i> <span>Настройка</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php echo $MVCPage=="SettingConstruct" ? "active" : ""; ?>"><a href="?MVCPage=SettingConstruct"><i class="fa fa-circle-o"></i> Конструктор</a></li>
          </ul>
        </li>
      </ul>
    </section>
  </aside>

  <!-- Контест -->
  <div class="content-wrapper">
    <?php
        include "MVCPages/".$MVCPage.".php";
    ?>
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Версия</b> 1.2.1
    </div>
    <strong>Copyright &copy; 2016-2017 <a href="https://www.exp-tech.com">Exp-Tech</a>.</strong>
  </footer>

  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->


<!-- jQuery UI 1.11.4 -->
<script src="bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="bower_components/raphael/raphael.min.js"></script>
<script src="bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="bower_components/moment/min/moment.min.js"></script>
<script src="bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<script src="scripts/Table.js"></script>
</body>
</html>
