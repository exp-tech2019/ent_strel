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
<body class="hold-transition skin-blue sidebar-mini">
<input id="idLogin" type="hidden" value="<?php echo $_SESSION["idLogin"]; ?>">
<div class="wrapper">

  <header class="main-header">
    <a href="index.php" class="logo">
      <span class="logo-mini"><b>S</b>tock</span>
      <span class="logo-lg"><b>Ent</b> склад</span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul onclick="window.location.href='index.php?SessionLogOut=true'" class="nav navbar-nav">
          <li class="dropdown ">
            <a href="index.php?SessionLogOut=true" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
              <span class="fa fa-home"></span>
              <span class="hidden-xs">Выход</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
    <?php
        $MVCPage=isset($_GET["MVCPage"]) ? $_GET["MVCPage"] : "MainPage"
    ?>
  <aside class="main-sidebar">
    <section class="sidebar">
      <!--Левое меню-->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header"></li>
        <li onclick="window.location.href='?MVCPage=StockList'" class="<?php echo $MVCPage=="StockList" || $MVCPage=="StockList" ? "active menu-open" : ""; ?> treeview">
          <a href="?MVCPage=StockList">
            <i class="fa fa-th"></i> <span>Склад</span>
          </a>
        </li>
        <li class="<?php echo in_array($MVCPage,array("ActArrivalList", "ActArrivalOne", "TransferInEnt_List", "TransferInEnt_One", "TransferInEnt_EditStart", "TransferInStock_List", "TransferInStock_One", "TransferInStock_EditStart", "PackageList", "PackageStart", "PackageEnd", "PackageView")) ? "active menu-open" : ""; ?> treeview">
          <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Документы</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php echo $MVCPage=="ActArrivalList" || $MVCPage=="ActArrivalOne" ? "active" : ""; ?>"><a href="?MVCPage=ActArrivalList"><i class="fa fa-circle-o"></i> Поступление</a></li>
            <li class="<?php echo $MVCPage=="TransferInEnt_List" || $MVCPage=="TransferInEnt_One" || $MVCPage=="TransferInEnt_EditStart" ? "active" : ""; ?>"><a href="?MVCPage=TransferInEnt_One"><i class="fa fa-circle-o"></i> Выдача сотруднику</a></li>
            <li class="<?php echo $MVCPage=="TransferInStock_List" || $MVCPage=="TransferInStock_One" || $MVCPage=="TransferInStock_EditStart" ? "active" : ""; ?>"><a href="?MVCPage=TransferInStock_One"><i class="fa fa-circle-o"></i> Возврат</a></li>
            <li class="<?php echo in_array($MVCPage, array("PackageList", "PackageStart", "PackageEnd", "PackageView")) ? "active" : "" ?>"><a href="?MVCPage=PackageList"><i class="fa fa-circle-o"></i> Копмлектование</a></li>
          </ul>
        </li>
        <li class="<?php echo $MVCPage=="Customer_list" || $MVCPage=="Goods" ? "active menu-open" : ""; ?> treeview">
          <a href="#">
            <i class="fa fa-folder"></i> <span>Справочники</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php echo $MVCPage=="Customer_list" ? "active" : ""; ?>"><a href="?MVCPage=Customer_list"><i class="fa fa-circle-o"></i> Поставщики</a></li>
            <li class="<?php echo $MVCPage=="Goods" ? "active" : ""; ?>"><a href="?MVCPage=Goods"><i class="fa fa-circle-o"></i> Номенклатура</a></li>
          </ul>
        </li>
        <li class="<?php echo $MVCPage=="Spe_Construct" || $MVCPage=="Spe_Orders" || $MVCPage=="Spe_OrderOne" ? "active menu-open" : ""; ?> treeview">
          <a href="#">
            <i class="fa fa-folder"></i> <span>Спецификация</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php echo $MVCPage=="Spe_Construct" ? "active" : ""; ?>"><a href="?MVCPage=Spe_Construct"><i class="fa fa-circle-o"></i> Конструктор</a></li>
            <li class="<?php echo $MVCPage=="Spe_Orders" || $MVCPage=="Spe_OrderOne" ? "active" : ""; ?>"><a href="?MVCPage=Spe_Orders"><i class="fa fa-circle-o"></i> Заказы</a></li>
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
<script src="bower_components/moment/min/moment-with-locales.min.js"></script>
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

<script type="application/javascript">
  document.addEventListener("DOMNodeInserted",function () {
    var inps =document.getElementsByTagName("input");
    for(var i=0;i<inps.length;i++)
      inps[i].addEventListener("input", myFunction);

    function myFunction() {
      this.value=this.value.replace(",",".");
    }
  })
/*
  var insertedNodes = [];
  var observerInp=new MutationObserver(function (mutations) {
    console.log(mutations);
    this.value=this.value.replace(",",".");
  });
  var observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      for (var i = 0; i < mutation.addedNodes.length; i++)
        if (mutation.addedNodes[i].nodeName == "TR")
        {
          var inp=mutation.addedNodes[i].querySelector("input");
          if(inp!==undefined & inp!=null) {
            console.log(inp);
            inp.addEventListener("input",function(){
              console.log("dfdf");
              this.value=this.value.replace(",",".");
            })
          }
        }

    });
  });
  observer.observe(document.body, {subtree: true, attributes: false, characterData: false, childList: true });
*/
</script>