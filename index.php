<?php 
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Expires: " . date("r"));
	session_start();
    $sHref=(new DateTime())->getTimestamp();
?>
<!doctype html>
<html lang="us">
<head>
	<meta charset="utf-8">
	<meta http-equiv="cache-control" content="no-cache">
	<title>ENT</title>
	<link href="jquery-ui.css" rel="stylesheet">
	<link href="orders/order.css" rel="stylesheet">
	<link href="MainStyle.css" rel="stylesheet">
    <link href="orders/Sp18/style.css" rel="stylesheet">
    <link href="propertes/style.css" rel="stylesheet">
    <link href="css/PayStyle.css" rel="stylesheet">
    <!--<link href="css/icons-all.css" rel="stylesheet">-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
    <link rel="stylesheet" href="skin-material/ui.fancytree.css">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
	<style>
	body{
		font: 62.5% "Trebuchet MS", sans-serif;
		/*margin: 50px;*/
		background-color:#ffffff;
	}
	#OrderTableDoors{
		border-spacing: 0px;
	}
	#OrderTableDoors tr td{
		border: solid 1px #a0a1a0;
	}
	#up
	{
		z-index:1000;
		width:60px;
		height:60px;
		position:fixed;
		bottom:50px;
		left:20px;
		background-color:#000000;
		border-radius:30px; 
		cursor:pointer;
		opacity:0.5;
	}
	#up:hover
	{
		opacity:1;
	}
	.pPageScroll
	{
		color:#FFFFFF;
		font:bold 12pt 'Comic Sans MS';
		text-align:center;
	}
	</style>
</head>

<body>
<script src="external/jquery/jquery.js"></script>
<script src="jquery-2.1.3.min.js"></script>
<script src="jquery.cookie.js"></script>
<script src="jquery-ui.js"></script>
<script src="date.format.js"></script>
<script src="scripts/jquery.maskedinput.js"></script>
<!--<script src="scripts/jquery.json.min.js"></script>-->
<script src="functions.js"></script>
<script src="orders/function.js"></script>
<script src="workers/worker.js"></script>
<script src="enterprise/enterprise.js"></script>
<!--<script src="stockroom/functions.js"></script>-->
<script src="payments/functions.js"></script>
<script src="reportEnt/functions.js?ds=23"></script>
<!--<script src="propertes/prop.js"></script>-->
<script src="propertes/prop.js?sHref=<?php echo $sHref; ?>"></script>
<script src="scripts/JSXml.js"></script>
<script src="jqui-alert.min.js"></script>
<script src="plugins/fancytree/jquery.fancytree.js"></script>
<script>
	//Руссификация календаря
	$.datepicker.regional['ru'] = {
		closeText: 'Закрыть',
		prevText: '&#x3c;Пред',
        nextText: 'След&#x3e;',
        currentText: 'Сегодня',
        monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
        'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
        monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
        'Июл','Авг','Сен','Окт','Ноя','Дек'],
        dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
        dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
        dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
        weekHeader: 'Не',
        dateFormat: 'dd.mm.yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''};
     $.datepicker.setDefaults($.datepicker.regional['ru']);
</script>
<div id="up"><p class="pPageScroll">Вверх</p></div>
<script>
	$(document).ready(function(){
		//Обработка нажатия на кнопку "Вверх"
		$("#up").click(function(){
			//Необходимо прокрутить в начало страницы
			var curPos=$(document).scrollTop();
			var scrollTime=curPos/1.73;
			$("body,html").animate({"scrollTop":0},2);
		});
	});
</script>
<!--Последние изменения-->
<div id=News title="Полседние изменения">
	<p>
		<iframe src="news.html" style="width:100%; height:500px;border:none;"></iframe>
	</p>
</div>
<script>
/*
	$( "#News" ).dialog({
	autoOpen: false,
	modal:true,
	width: 700});
	if($.cookie("EntVersion")!="2.0")
	{
		$( "#News" ).dialog("open");
		$.cookie("EntVersion","3.0", { expires: 360})
	};
	*/
</script>
<!----------------- ФОН ------------------------>
<div class="fon-top"></div>
<div style="width: 90%">&nbsp;</div>
<div id="tabs" class="block">
	<ul>
		<li><a href="#tabs-1">Заказы</a></li>
		<?php
			if(isset($_SESSION["AutorizeType"]))
			{
				$AT=$_SESSION["AutorizeType"];
				if($AT==1 || $AT==2 || $AT==4) echo '<li onclick="IndexTabClick(2)"><a href="#tabs-2">Производство</a></li>';
				if($AT==1 || $AT==2 || $AT==4) echo '<li onclick="IndexTabClick(3)"><a href="#tabs-3">Сотрудники</a></li>';
				if($AT==1 || $AT==2 || $AT==4) echo '<li onclick="IndexTabClick(4)"><a href="#tabs-4">Склад</a></li>';
				if($AT==4) echo '<li onclick="IndexTabClick(7)"><a href="#tabs-7">Отчет производства</a></li>';
				if($AT==1 || $AT==2) echo '<li onclick="IndexTabClick(5)"><a href="#tabs-5">Платежи</a></li>';
				if($AT==1 || $AT==2 || $AT==4) echo '<li onclick="IndexTabClick(6)"><a href="#tabs-6">График</a></li>';
				if($AT==1) echo '<li onclick="IndexTabClick(7)"><a href="#tabs-7">Отчет производства</a></li>';
				if($AT==1) echo '<li onclick="IndexTabClick(8)"><a href="#tabs-8">Настройки</a></li>';
			};
		?>
	</ul>
	<div id="tabs-1">
		<div>
			<?php include 'orders/order.html'; ?>
		</div>
	</div>
	<div id="tabs-2">
	</div>
	<div id="tabs-3">
	</div>
	<div id="tabs-4">
	</div>
	<div id="tabs-5">
	</div>
	<div id="tabs-6">
	</div>
	<div id="tabs-7">
	</div>
	<div id="tabs-8">
	</div>
</div>
<script>
    var hrefStamp=new Date();
	function IndexTabClick(TabIndex){
		switch(TabIndex)
		{
			case 2:
				if($("#EntNaryadTable").length==0)
					$('#tabs-2').load('enterprise/enterprise.html');
			break;
			case 3:
				if($("#WorkerTable").length==0)
					$('#tabs-3').load('workers/index.html');
			break;
			case 4:
				if($("#StockMaterialList").length==0)
					$('#tabs-4').load('stockroom/index.html');
			break;
			case 5:
				if($("#PaymentsWorksTables").length==0)
					$('#tabs-5').load('pay/index.php');
			break;
			case 6:
				if($("#Diagram").length==0)
					$('#tabs-6').load('diagram/index.html');
			break;
			case 7:
				if($("#ReportWorkersTable").length==0)
					$('#tabs-7').load('reportEnt/index.html');
			break;
			case 8:
				if($("#PropAccordionMain").length==0)
                    $('#tabs-8').load('propertes/prop.html?stamp='+(hrefStamp.toISOString()));
			break;
		};
	}
</script>
<div class=blockAutorize>
	Вы вошли как: 
	<span id="MainComineFIO"></span> <script> if(!$("#MainAutorizations").is(":visible")) GlobalSetAutorize( "#MainComineFIO", "FIO")</script>
	<span id="MainComineExit">Выйти</span>
	<script>
		$("#MainComineExit").button();
		$("#MainComineExit").click(
			function ()
			{
				$.post(
					"MainAutorize.php",
					{"Method":"Exit"},
					function (data) { if(data=="ok") document.location.href="index.php"}
				);
			}
		);
	</script>
</div>
<script>
//Возвращаем ФИО входа
</script>

<script>
$('.block').css('width',($(document).width()-120).toString()+'px');
function window_resize(){
	$('.block').css('width',($(document).width()-120).toString()+'px');
}
//alert($('.fon-top').css('height'));
$( "#tabs" ).tabs();
</script>

<!--Аутификация-->
<div style="background-color:#000000; position:fixed; top:0px; left:0px; height:100%; width:100%; z-index:4;" id="BlackScreen">&nbsp;</div>
<div title="Авторизация" id="MainAutorizations">
	<p>
		<table>
			<tr>
				<td>Логин :</td>
				<td><input id="MainAutorizationsLogin"></td>
			</tr>
			<tr>
				<td>Пароль :</td>
				<td><input type=password id="MainAutorizationsPass"></td>
				<script>
					$("#MainAutorizationsPass, #MainAutorizationsLogin").keypress(
						function(e)
						{
							if (e.keyCode == $.ui.keyCode.ENTER) $("#MainAutorizations").parent().find("button:eq(0)").trigger("click");
						}
					)
				</script>
			</tr>
			<p style="color:red" id="MainAutorizationsBugs"></p>
	</p>
</div>

<script>
$("#BlackScreen").hide();
$( "#MainAutorizations" ).dialog({
	autoOpen: false,
	modal:true,
	width: 300,
	buttons: [
	{
		text: "Войти",
		click: function() {
			$.post(
				"MainAutorize.php",
				{"Method":"Autification", "Login":$("#MainAutorizationsLogin").val(), "Pass":$("#MainAutorizationsPass").val()},
				function(data)
				{
					if(data=="ok")
					{
						document.location.href="index.php";
						$("#BlackScreen").hide();
						$( "#MainAutorizations" ).dialog( "close" );
						GlobalSetAutorize( "#MainComineFIO", "FIO");
					}
					else
						$("#MainAutorizationsBugs").text(data);
				}
			);
		}
	}
	]
});

//Нажатие enter на поле авторизации


$("#MainAutorizations").dialog({
  open:function() {
    $(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar-close").remove();
  }
});

<?php
	if(!isset( $_SESSION["AutorizeFIO"]))
	{
		echo "$('#BlackScreen').show(); $('#MainAutorizations').dialog('open');";
	};
?>
</script>
<!--Обработка закрытия страницы-->
<script>
	$(document).ready(function(){
		window.onbeforeunload = function () {
			var DialogVisible=false;
			if($("#orderDialog").is(":visible"))
			{
				//OrderClose();
				$("#orderDialog").dialog("close");
				DialogVisible=true;
			};
			return (DialogVisible ? "Завершить работу с программой?" : null); 
		} 
	});
</script>
</body>
</html>
