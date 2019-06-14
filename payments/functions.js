function PayamentsWorksSelectList(Month, Year, elP)
{
	//Делаем проверку идет обработка
	if($("#PaymentsWorksReportLoader").is(":visible")) {jqUI.alert("Подождите, идет обработка предыдущего запроса"); return;};
	//Выделим месяц рамкой
	$(elP).css("border","solid 1px black");

	var DateWith="01."+Month+"."+Year;
	var DateBy=getLastDayOfMonth(Year , Month)+"."+Month+"."+Year;

	var DNextMonth=new Date(Year,Month,1);

	$("#PaymentsWorksReportLoader").show();
	$("#PaymentsWorksTables").find("tr").remove();
	$.post(
		"payments/functions.php",
		{"Method":"WorksSelectList","DateWith":DateWith, "DateBy":DateBy},
		function(data)
		{
			$("#PaymentsWorksReportLoader").hide();
			var o=jQuery.parseJSON(data);
			var i=0;
			var SumWith=0, SumPlus=0, SumMinus=0, SumEnd=0, SumNoneDate=0;
			while(o[i]!=null)
			{
				$("#PaymentsWorksTables").append("<tr id=PaymentsWorkerTR"+o[i]["idWorker"]+" onclick='var dt=new Date(); var dayLast=new Date().daysInMonth(); PayamentsWorksOneSelect("+o[i]["idWorker"]+" , \""+o[i]["FIO"]+"\",\""+o[i]["Dolgnost"]+"\", \""+DateWith+"\", \""+DateBy+"\")'><td type=FIO style='text-align:left'>"+o[i]["FIO"]+"</td><td type=Dolgnost style='text-align:left'>"+o[i]["Dolgnost"]+"</td><td type=SumWith>"+o[i]["SumWith"]+"</td><td type=SumPlus>"+o[i]["SumPlus"]+"</td><td type=SumMinus>"+o[i]["SumMinus"]+"</td><td type=SumEnd>"+o[i]["SumEnd"]+"</td></tr>");
				SumWith+=parseFloat(o[i]["SumWith"]); SumPlus+=parseFloat(o[i]["SumPlus"]); SumMinus+=parseFloat(o[i]["SumMinus"]); SumEnd+=parseFloat(o[i]["SumEnd"]);
				i++;
			};
			//Выводим итоги
			$("#PaymentsWorksTables").append("<tr style='background-color:lightgray; cursor:default'><td colspan=2>Итог</td><td type=ItogSumWith>"+modRound(SumWith,2)+"</td><td type=ItogSumPlus>"+modRound(SumPlus,2)+"</td><td type=ItogSumMinus>"+modRound(SumMinus,2)+"</td><td type=ItogSumEnd>"+modRound(SumEnd,2)+"</td></tr>");
		}
	)
}
function modRound(value, precision)
{
	// спецчисло для округления
	var precision_number = Math.pow(10, precision);

	// округляем
	return Math.round(value * precision_number) / precision_number;
}

//Фильтр по ФИО или должности из таблицы
function PayamentsWorksSelectListFilter(){
	var SumWith=0;
	var SumPlus=0;
	var SumMinus=0;
	var SumEnd=0;
	for(var i=0;i<$("#PaymentsWorksTables").find("tr").length-1;i++)
	{
		var TR=$("#PaymentsWorksTables tr:eq("+i+")");
		if(TR.find("td[type=FIO]").text().toLowerCase().indexOf($("#PaymentsWorksTablesFilter").val().toLowerCase())>-1 || TR.find("td[type=Dolgnost]").text().toLowerCase().indexOf($("#PaymentsWorksTablesFilter").val().toLowerCase())>-1)
		{
			TR.show();
			SumWith+=parseFloat(TR.find("td[type=SumWith]").text());
			SumPlus+=parseFloat(TR.find("td[type=SumPlus]").text());
			SumMinus+=parseFloat(TR.find("td[type=SumMinus]").text());
			SumEnd+=parseFloat(TR.find("td[type=SumEnd]").text());
		}
		else
			TR.hide();
	};
	console.log(SumWith+" "+SumEnd);
	$("#PaymentsWorksTables tr:last td[type=ItogSumWith]").text(SumWith);
	$("#PaymentsWorksTables tr:last td[type=ItogSumPlus]").text(SumPlus);
	$("#PaymentsWorksTables tr:last td[type=ItogSumMinus]").text(SumMinus);
	$("#PaymentsWorksTables tr:last td[type=ItogSumEnd]").text(SumEnd);
}

function PayamentsWorksSelectBetween()
{
	//Делаем проверку идет обработка
	if($("#PaymentsWorksReportLoader").is(":visible")) {jqUI.alert("Подождите, идет обработка предыдущего запроса"); return;};

	if($("#PaymentsWorkBetweenWith").val()!="" & $("#PaymentsWorkBetweenBy").val()!="")
	{
		//Убираем рамку выбранного месяца
		//PaymentsWorksDate
		$("#PaymentsWorksDate div p").css("border","none");

		$("#PaymentsWorksReportLoader").show();
		DateWith=$("#PaymentsWorkBetweenWith").val();
		DateBy=$("#PaymentsWorkBetweenBy").val();
		$("#PaymentsWorksTables").find("tr").remove();
		$.post(
			"payments/functions.php",
			{"Method":"WorksSelectList","DateWith":DateWith, "DateBy":DateBy},
			function(data)
			{
				$("#PaymentsWorksReportLoader").hide();
				var o=jQuery.parseJSON(data);

				var i=0;
				var SumWith=0, SumPlus=0, SumMinus=0, SumEnd=0, SumNoneDate=0;
				while(o[i]!=null)
				{
					$("#PaymentsWorksTables").append("<tr id=PaymentsWorkerTR"+o[i]["idWorker"]+" onclick='var dt=new Date(); var dayLast=new Date().daysInMonth(); PayamentsWorksOneSelect("+o[i]["idWorker"]+" , \""+o[i]["FIO"]+"\",\""+o[i]["Dolgnost"]+"\", \""+DateWith+"\", \""+DateBy+"\")'><td type=FIO style='text-align:left'>"+o[i]["FIO"]+"</td><td type=Dolgnost style='text-align:left'>"+o[i]["Dolgnost"]+"</td><td type=SumWith>"+o[i]["SumWith"]+"</td><td type=SumPlus>"+o[i]["SumPlus"]+"</td><td type=SumMinus>"+o[i]["SumMinus"]+"</td><td type=SumEnd>"+o[i]["SumEnd"]+"</td></tr>");
					SumWith+=parseFloat(o[i]["SumWith"]); SumPlus+=parseFloat(o[i]["SumPlus"]); SumMinus+=parseFloat(o[i]["SumMinus"]); SumEnd+=parseFloat(o[i]["SumEnd"]);
					i++;
				};
				//Выводим итоги
				$("#PaymentsWorksTables").append("<tr style='background-color:lightgray; cursor:default'><td colspan=2>Итог</td><td type=ItogSumWith>"+SumWith+"</td><td type=ItogSumPlus>"+SumPlus+"</td><td type=ItogSumMinus>"+SumMinus+"</td><td type=ItogSumEnd>"+SumEnd+"</td></tr>");
			}
		)
	};
}

//Добавить выплату
function PayamentsWorksAdd(TypLine, InDialog)
{
	$("#PaymentsWorksDialogPaymentIDPayment").val("");
	$("#PaymentsWorksDialogPaymentSumPlusMinus").hide();
	$("#PaymentsWorksDialogPaymentBalanceLoad").hide();
	$("#PaymentsWorksDialogPaymentLine").val(TypLine);
	var dn=new Date();
	$("#PaymentsWorksDialogPaymentDate").val(dn.format("dd.mm.yyyy"));
	$("#PaymentsWorksDialogPaymentSum").val(0);
	$("#PaymentsWorksDialogPaymentBugs").text("");
	$("#PaymentsWorksDialogPaymentDolgnost").text("");
	$("#PaymentsWorksDialogPaymentWorker").find("option").remove();
	$.post(
		"payments/functions.php",
		{"Method":"SelectWorkers"},
		function (data)
		{
			var o=jQuery.parseJSON(data);
			$("#PaymentsWorksDialogPaymentWorker").append("<option></option>");
			var i=0;
			while(o[i]!=null) {$("#PaymentsWorksDialogPaymentWorker").append("<option>"+o[i]+"</option>"); i++; };
			//Если выплаты/начисления вызываются из диалога
			if(InDialog!==undefined)
			{
				$("#PaymentsWorksDialogPaymentWorker").val($("#PaymentsWorksDialogPaymentOneFIO").text());
				$("#PaymentsWorksDialogPaymentDolgnost").text($("#PaymentsWorksDialogPaymentOneDolgnost").text());
				SelectWorkerBalance();//Расчет остатков за весь период
			};
		}
	);

	$( "#PaymentsWorksDialogPayment" ).dialog("open");
}

//Расчет баланса по всем годам для сотрудника
function SelectWorkerBalance()
{
	if($("#PaymentsWorksDialogPaymentWorker").val()!="")
	{
		$("#PaymentsWorksDialogPaymentBalanceLoad").show();
		$.post(
			"payments/functions.php",
			{
				"Method":"SelectWorkerBalance",
				"FIO":$("#PaymentsWorksDialogPaymentWorker").val()
			},
			function (data)
			{
				$("#PaymentsWorksDialogPaymentBalanceLoad").hide();
				$("#PaymentsWorksDialogPaymentBalance").val(data);
			}
		);
	};
}
//Определение должности выбранного сотрудника
function SelectWorkerDolgnost()
{
	if($("#PaymentsWorksDialogPaymentWorker").val()!="")
	{
		$.post(
			"payments/functions.php",
			{
				"Method":"SelectWorkerDolgnost",
				"FIO":$("#PaymentsWorksDialogPaymentWorker").val()
			},
			function (data)
			{
				$("#PaymentsWorksDialogPaymentDolgnost").text(data);
			}
		);
	};
}

//Сохранение Диалога
function PayamentsWorksSave()
{
	//Проверка на заполненность и корректность
	var flagErr="";
	if($("#PaymentsWorksDialogPaymentLine").val()=="") flagErr=flagErr+"Не заполненно поле Метод<br>";
	if($("#PaymentsWorksDialogPaymentDate").val()=="") flagErr=flagErr+"Не заполненно поле Дата<br>";
	if($("#PaymentsWorksDialogPaymentWorker").val()=="") flagErr=flagErr+"Не заполненно поле Сотрудник<br>";
	if($("#PaymentsWorksDialogPaymentSum").val()=="") flagErr=flagErr+"Не заполненно поле Сумма<br>";
	if($("#PaymentsWorksDialogPaymentBalance").val()=="") flagErr=flagErr+"Баланс неопределен<br>";
	if($("#PaymentsWorksDialogPaymentSum").val()!="" & $("#PaymentsWorksDialogPaymentBalance").val()!="" & $("#PaymentsWorksDialogPaymentLine").val()=="Minus")
	if( ParamGetValue("AllowChangeNegativeBalance")=="0")
		if(parseInt($("#PaymentsWorksDialogPaymentSum").val())>parseInt($("#PaymentsWorksDialogPaymentBalance").val()))
			flagErr=flagErr+"Сумма превышает баланс";

	if(flagErr!="")
	{ $("#PaymentsWorksDialogPaymentBugs").show(); $("#PaymentsWorksDialogPaymentBugs").text("<hr>"+flagErr);	}
	else
	{
		//Определяем поступают или списываются средства
		var sum=0;
		switch($("#PaymentsWorksDialogPaymentLine").val())
		{
			case "Plus": sum=parseInt($("#PaymentsWorksDialogPaymentSum").val())*(+1); break;
			case "Minus": sum=parseInt($("#PaymentsWorksDialogPaymentSum").val())*(-1); break;
		};
		$.post(
			"payments/functions.php",
			{
				"Method":"PayamentsWorksSave",
				"FIO":$("#PaymentsWorksDialogPaymentWorker").val(),
				"DatePayment":$("#PaymentsWorksDialogPaymentDate").val(),
				"Sum":sum,
				"Note":$("#PaymentsWorksDialogPaymentNote").val()
			},
			function (data)
			{
				if(data!="")
				{
					$("#PaymentsWorksDialogPaymentBugs").show(); $("#PaymentsWorksDialogPaymentBugs").text("<hr>"+data);
				}
				else
					$( "#PaymentsWorksDialogPayment" ).dialog("close");
			}
		);
	};
}

var PayamentsWorksEditParentTR;
function PayamentsWorksEditStart(id, el)
{
	PayamentsWorksEditParentTR=el;
	$("#PaymentsWorksDialogPaymentSumPlusMinus").show();
	$("#PaymentsWorksDialogPaymentIDPayment").val(id);
	$("#PaymentsWorksDialogPaymentBugs").text("");
	$.post(
		"payments/functions.php",
		{"Method":"EditStart", "id":id},
		function(data)
		{
			var o=jQuery.parseJSON(data);
			$("#PaymentsWorksDialogPaymentDate").val(o.DatePayment);
			$("#PaymentsWorksDialogPaymentWorker").find("option").remove();
			$("#PaymentsWorksDialogPaymentWorker").append("<option>"+o.FIO+"</option>");
			$("#PaymentsWorksDialogPaymentDolgnost").text(o.Dolgnost);
			SelectWorkerBalance();
			var Sum=parseFloat(o.Sum);
			$("#PaymentsWorksDialogPaymentSumPlusMinus").find("option").remove();
			if(Sum>=0)
			{
				$("#PaymentsWorksDialogPaymentSumPlusMinus").append("<option selected>Начисления</option>");
				$("#PaymentsWorksDialogPaymentSumPlusMinus").append("<option>Выплаты</option>");
			}
			else
			{
				$("#PaymentsWorksDialogPaymentSumPlusMinus").append("<option selected>Выплаты</option>");
				$("#PaymentsWorksDialogPaymentSumPlusMinus").append("<option>Начисления</option>");
				Sum=(-1)*Sum;
			};
			$("#PaymentsWorksDialogPaymentSum").val(Sum);
			$("#PaymentsWorksDialogPaymentNote").val(o.Note);
			$("#PaymentsWorksDialogPayment").dialog("open");
		}
	);
}

function PayamentsWorksEditSave()
{
	$.post(
		"payments/functions.php",
		{
			"Method":"EditSave",
			"id":$("#PaymentsWorksDialogPaymentIDPayment").val(),
			"DatePayment":$("#PaymentsWorksDialogPaymentDate").val(),
			"PlusMinus":$("#PaymentsWorksDialogPaymentSumPlusMinus").val(),
			"Sum":$("#PaymentsWorksDialogPaymentSum").val(),
			"Note":$("#PaymentsWorksDialogPaymentNote").val()
		},
		function (data)
		{
			if(data=="ok")
			{
				$("#PaymentsWorksDialogPayment").dialog("close");
				var el= $(PayamentsWorksEditParentTR).parent().parent();
				$($(el).find("td")[0]).text($("#PaymentsWorksDialogPaymentDate").val());
				$($(el).find("td")[1]).text($("#PaymentsWorksDialogPaymentNote").val());
				$($(el).find("td")[2]).text("");
				$($(el).find("td")[3]).text("");
				switch($("#PaymentsWorksDialogPaymentSumPlusMinus").val())
				{
					case "Начисления":
						$($(el).find("td")[2]).text($("#PaymentsWorksDialogPaymentSum").val());
					break;
					case "Выплаты":
						$($(el).find("td")[3]).text($("#PaymentsWorksDialogPaymentSum").val());
					break;
				};

			}
			else $("#PaymentsWorksDialogPaymentBugs").text(data);
		}
	);
}

function PayamentsWorksDelete(id,el)
{
	if(confirm("Удалить платеж?"))
		$.post(
			"payments/functions.php",
			{"Method":"Delete","id":id},
			function (data)
			{
				if(data=="ok")
				{
					$(el).parent().parent().remove();
				}
				else $("#PaymentsWorksDialogPaymentBugs").text(data);
			}
		);
}

//Печать платежа
function PayamentsWorksPrint()
{
	if($("#PaymentsWorksDialogPaymentLine").val()=="Minus")
	{
		PayamentsWorksSave();
		if($("#PaymentsWorksDialogPaymentBugs").text()=="")
		{
			$.post(
				"payments/functions.php",
				{
					"Method":"PrintRKO",
					"FIO":$("#PaymentsWorksDialogPaymentWorker").val(),
					"Dolgnost":$("#PaymentsWorksDialogPaymentDolgnost").text(),
					"DatePayment":$("#PaymentsWorksDialogPaymentDate").val(),
					"Sum":$("#PaymentsWorksDialogPaymentSum").val(),
					"Note":$("#PaymentsWorksDialogPaymentNote").val()
				},
				function(data)
				{
					if(data=="ok") window.open("payments/rko.pdf",'_blank');
				}
			);
		};
	}
	else alert("Невозможно распечатать расчетно-кассовый ордер, т.к. выполняется операция начисления!");
}

//Печать РКО после сохранения ВЫПЛАТЫ (из строчки таблицы выплат конкретного сотрудника)
function PayamentsWorksPrintNoSave(FIO, Dolgnost, DatePayment, Sum, Note)
{
	$.post(
				"payments/functions.php",
				{
					"Method":"PrintRKO",
					"FIO":FIO,
					"Dolgnost":Dolgnost,
					"DatePayment":DatePayment,
					"Sum":Sum,
					"Note":Note
				},
				function(data)
				{
					if(data=="ok") window.open("payments/rko.pdf",'_blank');
				}
			);
}
//Печать списка начилсений/выплат сотрудников
function PaymentsPrintWorkersList()
{
	var aFIO=new Array();
	var aDolgnost=new Array();
	var aColumn1=new Array();
	var aColumn2=new Array();
	var aColumn3=new Array();
	var aColumn4=new Array();
	var c=0;
	for(var i=0;i<$("#PaymentsWorksTables tr").length-1;i++)
	{
		var elTR=$("#PaymentsWorksTables tr:eq("+i+")");
		if(elTR.is(":visible"))
		{
			aFIO[c]=elTR.find("td:eq(0)").text();
			aDolgnost[c]=elTR.find("td:eq(1)").text();
			aColumn1[c]=elTR.find("td:eq(2)").text();
			aColumn2[c]=elTR.find("td:eq(3)").text();
			aColumn3[c]=elTR.find("td:eq(4)").text();
			aColumn4[c]=elTR.find("td:eq(5)").text();
			c++;
		};
	};
	//Итог
	var ItogTR= $("#PaymentsWorksTables tr:last");
	var Itog1=ItogTR.find("td:eq(1)").text();
	var Itog2=ItogTR.find("td:eq(2)").text();
	var Itog3=ItogTR.find("td:eq(3)").text();
	var Itog4=ItogTR.find("td:eq(4)").text();
	$.post(
		"payments/functions.php",
		{
			"Method":"PrintWorkersList",
			"aFIO[]":aFIO,
			"aDolgnost[]":aDolgnost,
			"aColumn1[]":aColumn1,
			"aColumn2[]":aColumn2,
			"aColumn3[]":aColumn3,
			"aColumn4[]":aColumn4,
			"Itog1":Itog1,
			"Itog2":Itog2,
			"Itog3":Itog3,
			"Itog4":Itog4
		},
		function(data)
		{
			if(data=="ok") window.open("payments/PaymentsWorkersList.pdf",'_blank');
		}
	);
}

//Первичное отображение диалога с платежами первоначальная загрузка
var aPaymentsOneNaryadNum=new Array();
var aPaymentsOneNaryadSum=new Array();
var aPaymentsOneNaryadDate=new Array();
function PayamentsWorksOneSelect (idWorker,FIO, dolgnost, DateWith, DateBy)
{
	$("#PaymentsWorksDialogPaymentOneTable").find("tr").remove();
	$("#PaymentsWorksDialogPaymentOneOrders").find("tr").remove();
	$("#PaymentsWorksDialogPaymentOnePayments").find("tr").remove();
	$("#PaymentsWorksDialogPaymentOneIdWorker").val(idWorker);
	$("#PaymentsWorksDialogPaymentOneFIO").text(FIO);
	$("#PaymentsWorksDialogPaymentOneDolgnost").text(dolgnost);
	$( "#PaymentsWorksDialogPaymentOne" ).dialog("open");
	$( "#PaymentsWorksDialogPaymentOneFilterWith" ).val(DateWith);
	$( "#PaymentsWorksDialogPaymentOneFilterBy" ).val(DateBy);
	$("#PaymentsWorksDialogPaymentOneLoad").show();
	//Очищаем массивы
	aPaymentsOneNaryadNum=new Array();
	aPaymentsOneNaryadSum=new Array();
	aPaymentsOneNaryadDate=new Array();

	$.post(
		"payments/functions.php",
		{
			"Method":"WorkerOneSelect",
			"FIO":FIO,
			"idWorker":idWorker,
			"DateWith":DateWith,
			"DateBy":DateBy
		},
		function (data)
		{
			$("#PaymentsWorksDialogPaymentOneLoad").hide();
			var o=jQuery.parseJSON(data);
			var i=0;
			//Формаирование таблицы Заказы
			var aOrderNum=new Array();
			var aOrderSum=new Array();
			var aOrderDoorCount=new Array();
			var aDoorNum={};
			var aDoorSum={};
			var aDoorCount={};

			var NaryadPos=0;
			var SumWith=0;
			while(o["Lines"][i]!=null)
			{
				console.log(o["Lines"][i]["Note"]+" - "+o["Lines"][i]["Debet"]+" - "+o["Lines"][i]["kredet"]);
				if(o["Lines"][i]["Note"]=="Остаток на начало периода")
				{
					$("#PaymentsWorksDialogPaymentOneSumWith").text(o["Lines"][i]["Debet"]!=""?o["Lines"][i]["Debet"] : o["Lines"][i]["kredet"]);
					SumWith=parseFloat(o["Lines"][i]["Debet"]!=""?o["Lines"][i]["Debet"] : o["Lines"][i]["kredet"]);
				};

				//Формирование скрытой таблицы
				$("#PaymentsWorksDialogPaymentOneTable").append("<tr><td>"+o["Lines"][i]["DatePayment"]+"</td><td>"+o["Lines"][i]["Note"]+"</td><td>"+o["Lines"][i]["Debet"]+"</td><td>"+o["Lines"][i]["kredet"]+"</td><td>"+o["Lines"][i]["Accountant"]+"</td><td></td></tr>");

				if((o["Lines"][i]["Note"].indexOf("Выполнение наряда №")!=-1 & o["Lines"][i]["idPayment"]==null) || o["Lines"][i]["Note"].indexOf("Контроль наряда №")!=-1)
				{
					var s=o["Lines"][i]["Note"].substr(o["Lines"][i]["Note"].indexOf("№")+1);
					aPaymentsOneNaryadNum[NaryadPos]=s;
					s=s.substr(0,s.indexOf("/"));
					var Sum=0;
					if(o["Lines"][i]["Debet"]!=null & o["Lines"][i]["Debet"]!="") Sum+=parseFloat(o["Lines"][i]["Debet"]);
					if(o["Lines"][i]["kredet"]!=null & o["Lines"][i]["kredet"]!="") Sum-=parseFloat(o["Lines"][i]["kredet"]);
					aPaymentsOneNaryadSum[NaryadPos]=Sum;
					aPaymentsOneNaryadDate[NaryadPos]=o["Lines"][i]["DatePayment"];
					NaryadPos++;
					if(!document.getElementById("PaymentsOneOrderTR"+s))
					{
						$("#PaymentsWorksDialogPaymentOneOrders").append(
							"<tr id=PaymentsOneOrderTR"+s+" class=Start>"+
								"<td onclick='PayamentsWorksOneTableDoorShowHide(\""+s+"\")'><img src='images/arrow-turn-left.png'></td>'"+
								"<td>"+s+"</td>"+
								"<td>"+Sum+"</td>"+
								"<td>1</td>"+
							"<tr>"+
							"<tr class=NoneHover>"+
								"<td></td>"+
								"<td colspan=3>"+
									"<table class='Tables'>"+
										"<thead>"+
											"<tr><td><td>Позиция</td><td>Сумма</td><td>Кол-во</td></tr>"+
										"</thead>"+
										"<tbody id=PaymentsOneDoorsTable"+s+"></tbody>"+
									"</table>"+
								"</td>"+
							"</tr>"
						);
						$("#PaymentsOneDoorsTable"+s).parent().parent().hide();
					}
					else
					{
						$($("#PaymentsOneOrderTR"+s+" td")[2]).text( ( parseFloat($($("#PaymentsOneOrderTR"+s+" td")[2]).text())+Sum ).toString() );
						 $($("#PaymentsOneOrderTR"+s+" td")[3]).text( ( parseInt($($("#PaymentsOneOrderTR"+s+" td")[3]).text())+1 ).toString() );
					};
				};

				if(o["Lines"][i]["idPayment"]!=null & o["Lines"][i]["Note"].indexOf("Контроль наряда №")==-1)
				{
					//Для строк ВЫПЛАТ появляеся кнопка ПЕЧАТЬ
					$PrintButton=""; if(o["Lines"][i]["idPayment"]!=null & o["Lines"][i]["kredet"]!="") $PrintButton="<img src='images/Print.png' onclick='PayamentsWorksPrintNoSave(\""+FIO+"\", \""+dolgnost+"\", \""+o["Lines"][i]["DatePayment"]+"\", \""+o["Lines"][i]["kredet"]+"\", \""+o["Lines"][i]["Note"]+"\" )' title='Распечатать расходно-кассовый ордер'>";

					var PaymentEditButton="<img src='images/edit.png' onclick=' PayamentsWorksEditStart("+o["Lines"][i]["idPayment"]+", this) ' >";
					var PaymentDelButton="<img src='images/delete.png' width=16 onclick='PayamentsWorksDelete("+o["Lines"][i]["idPayment"]+", this)'>"
					$("#PaymentsWorksDialogPaymentOnePayments").append(
						"<tr>"+
							"<td>"+o["Lines"][i]["DatePayment"]+"</td>"+
							"<td>"+o["Lines"][i]["Note"]+"</td>"+
							"<td>"+o["Lines"][i]["Debet"]+"</td>"+
							"<td>"+o["Lines"][i]["kredet"]+"</td>"+
							"<td>"+o["Lines"][i]["Accountant"]+"</td>"+
							"<td>"+PaymentEditButton+"</td>"+
							"<td>"+PaymentDelButton+"</td>"+
							"<td>"+$PrintButton+"</td>"+
						"</tr>"
					);
				};
				i++;
			};

			//Расчитаем вкладку Основное
			$("#PaymentsWorksDialogPaymentOneSumWith").text(o.SumWith);
			$("#PaymentsWorksDialogPaymentOneSumPlus").text(o.Earned);
			$("#PaymentsWorksDialogPaymentOneSumMinus").text(o.Paid);
			$("#PaymentsWorksDialogPaymentOneSumEnd").text(o.SumEnd);

			$("#PaymentsWorksDialogPaymentOneTable").append("<tr><td></td><td><b>Итого (тек. период)</b></td><td>"+$("#PaymentsWorksDialogPaymentOneSumPlus").text()+"</td><td>"+$("#PaymentsWorksDialogPaymentOneSumMinus").text()+"</td></tr>");
			$("#PaymentsWorksDialogPaymentOneTable").append("<tr><td></td><td><b>Остаток на конец периода</b></td><td>"+$("#PaymentsWorksDialogPaymentOneSumEnd").text()+"</td></tr>");
		}
	);
}


function PayamentsWorksOneTableDoorShowHide(idTR)
{
	if( $( $("#PaymentsOneOrderTR"+idTR+" td")[0] ).find("img").attr("src")== "images/arrow-turn-left.png")
		//Раскрыть таблицу
	{
		$("#PaymentsOneDoorsTable"+idTR).parent().parent().show();
		$( $("#PaymentsOneOrderTR"+idTR+" td")[0] ).find("img").attr("src","images/arrow_skip.png");
		//Пропарсим массив нарядов и выведем Позиции в текущую таблицу заказа
		for(var i=0; i<aPaymentsOneNaryadNum.length;i++)
			if(aPaymentsOneNaryadNum[i].indexOf(idTR+"/")==0)
			{
				var sD=aPaymentsOneNaryadNum[i];
				sD=sD.substr(sD.indexOf("/")+1,sD.lastIndexOf("/")-sD.indexOf("/")-1);
				if( !document.getElementById("PaymentsOnePositionTR"+idTR+"_"+sD) )
				{

					$("#PaymentsOneDoorsTable"+idTR).append(
						"<tr Class=Start id=PaymentsOnePositionTR"+idTR+"_"+sD+">"+
							"<td onclick='PayamentsWorksOneTablePositionShowHide(\""+idTR+"\" , \""+sD+"\")'><img src='images/arrow-turn-left.png'></td> "+
							"<td>"+sD+"</td>"+
							"<td>"+aPaymentsOneNaryadSum[i]+"</td>"+
							"<td>1</td>"+
						"</tr>"+
						"<tr class=NoneHover>"+
							"<td>"+
							"<td colspan=3>"+
								"<table class=Tables>"+
									"<thead>"+
										"<tr><td>Наряд</td><td>Сумма</td><td>Выполненн</td></tr>"+
									"</thead>"+
									"<tbody id=PaymentsOnePositionTable"+idTR+"_"+sD+"></tbody>"+
								"</table>"+
							"</td>"+
						"</tr>"
					);
					$("#PaymentsOnePositionTable"+idTR+"_"+sD).parent().parent().hide();
				}
				else
				{
					var SumOld=parseFloat( $( $("#PaymentsOnePositionTR"+idTR+"_"+sD+" td")[2] ).text() );
					$( $("#PaymentsOnePositionTR"+idTR+"_"+sD+" td")[2] ).text( SumOld+ parseFloat(aPaymentsOneNaryadSum[i]));
					var CountOld=parseInt( $( $("#PaymentsOnePositionTR"+idTR+"_"+sD+" td")[3] ).text() );
					CountOld++;
					$( $("#PaymentsOnePositionTR"+idTR+"_"+sD+" td")[3] ).text(CountOld);
				};
			};
	}
	else
		//Скрыть и очистить таблицу Позиций
	{
		$( $("#PaymentsOneOrderTR"+idTR+" td")[0] ).find("img").attr("src","images/arrow-turn-left.png");
		$("#PaymentsOneDoorsTable"+idTR).parent().parent().hide();
		$("#PaymentsOneDoorsTable"+idTR).find("tr").remove();
	}
}

function PayamentsWorksOneTablePositionShowHide(idTR, sD)
{
	if( $( $("#PaymentsOnePositionTR"+idTR+"_"+sD+" td")[0] ).find("img").attr("src")== "images/arrow-turn-left.png")
		//Раскрыть таблицу
	{
		$("#PaymentsOnePositionTable"+idTR+"_"+sD).parent().parent().show();
		$( $("#PaymentsOnePositionTR"+idTR+"_"+sD+" td")[0] ).find("img").attr("src","images/arrow_skip.png");
		//Пропарсим массив нарядов и выведем Позиции в текущую таблицу заказа
		for(var i=0; i<aPaymentsOneNaryadNum.length;i++)
			if(aPaymentsOneNaryadNum[i].indexOf(idTR+"/"+sD+"/")==0)
			{
				var sN=aPaymentsOneNaryadNum[i];
				sN=sN.substr(sN.lastIndexOf("/")+1);
				$("#PaymentsOnePositionTable"+idTR+"_"+sD).append(
						"<tr id=PaymentsOneNaryadTR"+idTR+"_"+sD+"_"+sN+">"+
							"<td>"+idTR+"/"+sD+"/"+sN+"</td>"+
							"<td>"+aPaymentsOneNaryadSum[i]+"</td>"+
							"<td>"+aPaymentsOneNaryadDate[i]+"</td>"+
						"</tr>"
					);
			};
	}
	else
		//Скрыть и очистить таблицу Позиций
	{
		$( $("#PaymentsOnePositionTR"+idTR+"_"+sD+" td")[0] ).find("img").attr("src","images/arrow-turn-left.png");
		$("#PaymentsOnePositionTable"+idTR+"_"+sD).parent().parent().hide();
		$("#PaymentsOnePositionTable"+idTR+"_"+sD).find("tr").remove();
	}
}

//Печать таблицы из диалога
function PayamentsWorksOnePrint()
{
	var aDate=new Array();
	var aNote=new Array();
	var aDebet=new Array();
	var aKredet=new Array();
	var aAttantion=new Array();
	for(var i=0; i<$("#PaymentsWorksDialogPaymentOneTable tr").length; i++)
		if($($( $("#PaymentsWorksDialogPaymentOneTable tr")[i]).find("td")[0]).text()!="Итого")
		{
			aDate[i]= $($( $("#PaymentsWorksDialogPaymentOneTable tr")[i]).find("td")[0]).text();
			aNote[i]= $($( $("#PaymentsWorksDialogPaymentOneTable tr")[i]).find("td")[1]).text();
			aDebet[i]= $($( $("#PaymentsWorksDialogPaymentOneTable tr")[i]).find("td")[2]).text();
			aKredet[i]= $($( $("#PaymentsWorksDialogPaymentOneTable tr")[i]).find("td")[3]).text();
			aAttantion[i]= $($( $("#PaymentsWorksDialogPaymentOneTable tr")[i]).find("td")[4]).text();
		}
		else
		{
			aDate[i]= $($( $("#PaymentsWorksDialogPaymentOneTable tr")[i]).find("td")[0]).text();
			aNote[i]= "";
			aDebet[i]= $($( $("#PaymentsWorksDialogPaymentOneTable tr")[i]).find("td")[1]).text();
			aKredet[i]= $($( $("#PaymentsWorksDialogPaymentOneTable tr")[i]).find("td")[2]).text();
			aAttantion[i]= $($( $("#PaymentsWorksDialogPaymentOneTable tr")[i]).find("td")[3]).text();
		};
	$.post(
		"payments/functions.php",
		{
			"Method":"WorkerOnePrint",
			"FIO":$("#PaymentsWorksDialogPaymentOneFIO").text(),
			"Dolgnost":$("#PaymentsWorksDialogPaymentOneDolgnost").text(),
			"With":$("#PaymentsWorksDialogPaymentOneFilterWith").val(),
			"By":$("#PaymentsWorksDialogPaymentOneFilterBy").val(),
			"Date[]":aDate,
			"Note[]":aNote,
			"Debet[]":aDebet,
			"Kredet[]":aKredet,
			"Attantion[]":aAttantion
		},
		function(data)
		{
			if(data=="ok") window.open("payments/OnePrint.pdf",'_blank');
		}
	);
}

//Обнуление платежей
function PaymentsSumNull()
{
	if(confirm("Выполнить обнуление по не распределенным сотрудникам?"))
		$.post(
			"payments/functions.php",
			{"Method":"SumNull"},
			function(data)
			{
				if(data=="ok") {alert("Платежи обнулены, обновите список")} else alert("При выполнении произошла ошибка: "+data);
			}
		)
}

//------------ Обнуление --------------------
//История
function PaymentsSumNullHistory()
{
	$("#PaymentsSumNullViewTable").find("tr").remove();
	$.post(
		"payments/functions.php",
		{"Method":"SumNullHistory", "DateWith":$("#PaymentsSumNullViewWith").val(), "DateBy":$("#PaymentsSumNullViewBy").val()},
		function (data)
		{
			console.log(data);
			var i=0; var o=jQuery.parseJSON(data);
			while(o[i]!=null)
			{
				var Count=0; if(o[i].Count!=null) Count=o[i].Count;
				var Sum=0; if(o[i].Sum!=null) Sum=o[i].Sum;
				$("#PaymentsSumNullViewTable").append(
					"<tr idHistory="+o[i].id+" onclick='PaymentsSumNullHistoryList(this)'>"+
						"<td><img src='images/arrow-turn-left.png' width=20></td>"+
						"<td>"+o[i].Date+"</td>"+
						"<td>"+Count+"</td>"+
						"<td>"+Sum+"</td>"+
						"<td>"+o[i].Manager+"</td>"+
					"</tr>"+
					"<tr idHistoryList="+o[i].id+">"+
						"<td colspan=5></td>"+
					"<tr>"
				);
				i++;
			};
		}
	);
}

function PaymentsSumNullHistoryList(el)
{
	var elTR=$(el);
	if($("#PaymentsSumNullViewTable tr[idHistoryList="+elTR.attr("idHistory")+"] td table").length==0)
	{
		elTR.find("td:eq(0) img").attr("src","images/arrow_skip.png");
		$.post(
			"payments/functions.php",
			{"Method":"SumNullHistoryList", "idHistory":elTR.attr("idHistory")},
			function (data) {
				var html="";
				var i=0; var o=jQuery.parseJSON(data);
				while (o[i]!=null)
				{
					var BtnComplite="<img onclick='PaymentsSumNullEditLoad(this)' src='images/edit.png' width=20>"; if(o[i].Complite==1) BtnComplite="<img src='images/done.png' width=20>";
					html=html+
						"<tr idList="+o[i].id+" idNaryad="+o[i].idNaryad+">"+
							"<td>"+o[i].NaryadNum+"</td>"+
							"<td>"+o[i].Step+"</td>"+
							"<td>"+o[i].Summ+"</td>"+
							"<td>"+BtnComplite+"</td>"+
						"</tr>";
					i++;
				};
				$("#PaymentsSumNullViewTable tr[idHistoryList="+elTR.attr("idHistory")+"] td").html(
					"<table class='TablesHeight Tables'>"+
						"<thead class=BorderTablesThead>"+
							"<tr>"+
								"<td>Наряд</td>"+
								"<td>Стадия</td>"+
								"<td>Сумма</td>"+
								"<td></td>"+
							"<tr>"+
						"</thead>"+
						"<tbody idHistoryList="+elTR.attr("idHistory")+">"+html+"</tbody>"+
					"</table>"
				);
			}
		);
	}
	else
	{
		elTR.find("td:eq(0) img").attr("src","images/arrow-turn-left.png");
		$("#PaymentsSumNullViewTable tr[idHistoryList="+elTR.attr("idHistory")+"] td").find("table").remove();
	};
}
function PaymentsSumNullEditLoad (el){
	var elTR=$(el).parent().parent();
	$("#PaymentsSumNullEditIDNaryad").val(elTR.attr("idNaryad"));
	$("#PaymentsSumNullEditIDHistoryList").val(elTR.attr("idList"));
	$("#PaymentsSumNullEditNaryad").text(elTR.find("td:eq(0)").text());
	$("#PaymentsSumNullEditStep").text(elTR.find("td:eq(1)").text());
	$("#PaymentsSumNullEditSumm").text(elTR.find("td:eq(2)").text());
	$("#PaymentsSumNullEditWorker").find("option").remove();
	$("#PaymentsSumNullEditDate").val("");
	$("#PaymentsSumNullEditBus").text("");
	$.post(
		"payments/functions.php",
		{"Method":"SumNullEditWorker", "Step":elTR.find("td:eq(1)").text()},
		function(data){
			var i=0; var o=jQuery.parseJSON(data);
			while(o[i]!=null)
			{
				$("#PaymentsSumNullEditWorker").append("<option>"+o[i]+"</option>");
				i++;
			};
		}
	);
	$( "#PaymentsSumNullEditDialog" ).dialog("open");
}
function PaymentsSumNullEditSave (){
	$.post(
		"payments/functions.php",
		{
			"Method":"SumNullEditSave",
			"NaryadID":$("#PaymentsSumNullEditIDNaryad").val(),
			"HistoryListID":$("#PaymentsSumNullEditIDHistoryList").val(),
			"Step":$("#PaymentsSumNullEditStep").text(),
			"Summ":$("#PaymentsSumNullEditSumm").text(),
			"Worker":$("#PaymentsSumNullEditWorker").val(),
			"Date":$("#PaymentsSumNullEditDate").val()
		},
		function(data){
			if(data=="ok") {
				$( "#PaymentsSumNullEditDialog" ).dialog("close");
				//#PaymentsSumNullViewTable tr td table tbody
				$("#PaymentsSumNullViewTable tr td table tbody tr[idList="+$("#PaymentsSumNullEditIDHistoryList").val()+"] td:eq(3) img").attr("src","images/done.png");
				$("#PaymentsSumNullViewTable tr td table tbody tr[idList="+$("#PaymentsSumNullEditIDHistoryList").val()+"] td:eq(3) img").attr("onclick","");
			} else {$("#PaymentsSumNullEditBus").text(data);};
		}
	)
}

//-------------------------------Заказы------------------------------------
function PaymentsOrderSelect(DateWith, DateBy){
	$("#PaymentsOrderTable").find("tr").remove();
	$.post(
		"payments/functions.php",
		{
			"Method":"OrderSelect",
			"DateWith":DateWith,
			"DateBy":DateBy
		},
		function(data){
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null){
				$("#PaymentsOrderTable").append(
					"<tr idOrder='"+o[i].id+"'>"+
						"<td>"+o[i].Blank+"</td>"+
						"<td>"+o[i].BlankDate+"</td>"+
						"<td>"+o[i].Shet+"</td>"+
						"<td>"+o[i].CostPlain+"</td>"+
						"<td>"+o[i].CostNaryadComplite+"</td>"+
					"</tr>"+
					"<tr More>"+
						"<td colspan=5>"+
						"<table>"+
							"<tr>"+
								"<td>Стадия</td>"+
								"<td>Планируется</td>"+
								"<td>Зараюотанно</td>"+
							"</tr>"+
							"<tr>"+
								"<td>Лазер</td>"+
								"<td>"+o[i].CostLaser+"</td>"+
								"<td></td>"+
							"</tr>"+
							"<tr>"+
								"<td>Гибка</td>"+
								"<td>"+o[i].CostSgibka+"</td>"+
								"<td></td>"+
							"</tr>"+
							"<tr>"+
								"<td>Сварка</td>"+
								"<td>"+o[i].CostSvarka+"</td>"+
								"<td></td>"+
							"</tr>"+
							"<tr>"+
								"<td>Рамка</td>"+
								"<td>"+o[i].CostFrame+"</td>"+
								"<td></td>"+
							"</tr>"+
							"<tr>"+
								"<td>Сборка</td>"+
								"<td>"+o[i].CostSborka+"</td>"+
								"<td></td>"+
							"</tr>"+
							"<tr>"+
								"<td>Покраска</td>"+
								"<td>"+o[i].CostColor+"</td>"+
								"<td></td>"+
							"</tr>"+
							"<tr>"+
								"<td>Упаковка</td>"+
								"<td>"+o[i].CostUpak+"</td>"+
								"<td></td>"+
							"</tr>"+
							"<tr>"+
								"<td>Погрузка</td>"+
								"<td>"+o[i].CostShpt+"</td>"+
								"<td></td>"+
							"</tr>"+
							"<tr>"+
								"<td>Сборка МДФ</td>"+
								"<td>"+o[i].CostSborkaMdf+"</td>"+
								"<td></td>"+
							"</tr>"+
							"<tr>"+
								"<td>МДФ цех</td>"+
								"<td>"+o[i].CostMdf+"</td>"+
								"<td></td>"+
							"</tr>"+
						"</table>"+
						"</td>"+
					"</tr>"
					);
				i++;
			};
			$("#PaymentsOrderTable tr[More]").hide();
			$("#PaymentsOrderTable tr[idOrder]").click(function(){
				if($(this).next().is(":visible"))
				{
					$(this).next().hide();
				}
				else
				{
					var el=$(this).next().find("td table");
					$.post(
						"payments/functions.php",
						{
							"Method":"OrderSelectMore",
							"idOrder":$(this).attr("idOrder")
						},
						function(data){
							var o=jQuery.parseJSON(data);
							el.find("tr:eq(1) td:eq(2)").text(o.CostLaser);
							el.find("tr:eq(2) td:eq(2)").text(o.CostSgibka);
							el.find("tr:eq(3) td:eq(2)").text(o.CostSvarka);
							el.find("tr:eq(4) td:eq(2)").text(o.CostFrame);
							el.find("tr:eq(5) td:eq(2)").text(o.CostSborka);
							el.find("tr:eq(6) td:eq(2)").text(o.CostColor);
							el.find("tr:eq(7) td:eq(2)").text(o.CostUpak);
							el.find("tr:eq(8) td:eq(2)").text(o.CostShpt);
							el.find("tr:eq(9) td:eq(2)").text(o.CostSborkaMdf);
							el.find("tr:eq(10) td:eq(2)").text(o.CostMdf);
						}
					)
					$(this).next().show();
				};
			})
		}
	);
}
function PaymentsOrderSelectFromMonth(Month,el){
	$(el).parent().find("p").css("border","0");
	$(el).css("border","solid 1px black");
	var DateWith="01."+Month+"."+$("#PaymentsOrderFilterYear").val();
	var DateBy=getLastDayOfMonth($("#PaymentsOrderFilterYear").val() , Month)+"."+Month+"."+$("#PaymentsOrderFilterYear").val();
	PaymentsOrderSelect(DateWith, DateBy);
}