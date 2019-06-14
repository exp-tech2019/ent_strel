var RaportSelectTabIndex=1;
function  RaportSelect(){
	switch(RaportSelectTabIndex){
		case 1:
			//Суммирование з/п/ сотрудникам
			$.post(
				"reportEnt/functions.php",
				{
					"Method":"SelectGeneralPayments",
					"RangeWith":$("#ReportRangeWith").val(),
					"RangeBy":$("#ReportRangeBy").val()
				},
				function(data){
					var o=jQuery.parseJSON(data);
					$("#ReportGeneralGroupPaymentsTable tr:eq(0) td:eq(0)").text(o.Naryad);
					$("#ReportGeneralGroupPaymentsTable tr:eq(0) td:eq(1)").text(o.Payment);
					$("#ReportGeneralGroupPaymentsTable tr:eq(0) td:eq(2)").text(o.Naryad-o.Payment);
				}
			);
			//Кол-во выполненных дверей
			$.post(
				"reportEnt/functions.php",
				{
					"Method":"SelectGeneralDoorCount",
					"RangeWith":$("#ReportRangeWith").val(),
					"RangeBy":$("#ReportRangeBy").val()
				},
				function(data){
					var o=jQuery.parseJSON(data);
					$("#ReportGeneralGroupEntDoorCount").text(o.DoorCount);
					$("#ReportGeneralGroupEntNaryadCount").text(o.NaryadCount);
					var i=0;
					while(o.NaryadStep[i]!=null)
					{
						$("#ReportGeneralGroupEntNaryadStep tr:eq(0) td:eq("+i+")").text(o.NaryadStep[i]);
						i++;
					};
				}
			);
			//Сотрудники
			$("#ReportGeneralGroupWorkerProductionTeamTable").find("tr").remove();
			$.post(
				"reportEnt/functions.php",
				{
					"Method":"SelectGeneralWorkers",
					"RangeWith":$("#ReportRangeWith").val(),
					"RangeBy":$("#ReportRangeBy").val()
				},
				function(data){
					var o=jQuery.parseJSON(data);
					$("#ReportGeneralGroupWorkerCount").text(o.WorkerCount);
					$("#ReportGeneralGroupWorkerProductionTeam").text(o.ProductionTeamCount);
					var i=0;
					while(o.ProductionTeam[i]!=null)
					{
						$("#ReportGeneralGroupWorkerProductionTeamTable").append(
							"<tr>"+
								"<td>"+o.ProductionTeam[i].Num+"</td>"+
								"<td>"+o.ProductionTeam[i].FIO+"</td>"+
								"<td>"+o.ProductionTeam[i].Dolgnost+"</td>"+
							"</tr>"
						);
						i++;
					};
				}
			);
		break;
		case 2:
			$("#ReportEntTable1").find("tr").remove();
			$.post(
				"reportEnt/functions.php",
				{
					"Method":"SelectEntTypeDoors",
					"RangeWith":$("#ReportRangeWith").val(),
					"RangeBy":$("#ReportRangeBy").val()
				},
				function(data){
					var o=jQuery.parseJSON(data);
					var i=0;
					while(o[i]!=null)
					{
						$("#ReportEntTable1").append(
							"<tr>"+
								"<td>"+o[i].Name+"</td>"+
								"<td>"+o[i].Step.Laser+"</td>"+
								"<td>"+o[i].Step.Sgibka+"</td>"+
								"<td>"+o[i].Step.Mdf+"</td>"+
								"<td>"+o[i].Step.Svarka+"</td>"+
								"<td>"+o[i].Step.Frame+"</td>"+
								"<td>"+o[i].Step.Sborka+"</td>"+
								"<td>"+o[i].Step.Color+"</td>"+
								"<td>"+o[i].Step.SborkaMdf+"</td>"+
								"<td>"+o[i].Step.Upak+"</td>"+
								"<td>"+o[i].Step.Shpt+"</td>"+
							"</tr>"
						);
						i++;
					};
				}
			);
			$("#ReportEntTable2").find("tr").remove();
			$.post(
				"reportEnt/functions.php",
				{
					"Method":"SelectEntOrders",
					"RangeWith":$("#ReportRangeWith").val(),
					"RangeBy":$("#ReportRangeBy").val()
				},
				function(data){
					var o=jQuery.parseJSON(data);
					var i=0;
					while(o[i]!=null)
					{
						$("#ReportEntTable2").append(
							"<tr>"+
								"<td>"+o[i].Blank+"</td>"+
								"<td>"+o[i].Shet+"</td>"+
								"<td>"+o[i].Step.Laser+"</td>"+
								"<td>"+o[i].Step.Sgibka+"</td>"+
								"<td>"+o[i].Step.Mdf+"</td>"+
								"<td>"+o[i].Step.Svarka+"</td>"+
								"<td>"+o[i].Step.Frame+"</td>"+
								"<td>"+o[i].Step.Sborka+"</td>"+
								"<td>"+o[i].Step.Color+"</td>"+
								"<td>"+o[i].Step.SborkaMdf+"</td>"+
								"<td>"+o[i].Step.Upak+"</td>"+
								"<td>"+o[i].Step.Shpt+"</td>"+
							"</tr>"
						);
						i++;
					};
				}
			);
		break;
		//Платежи
		case 3:
			$("#ReportPaymentsTable1").find("tr").remove();
			$.post(
				"reportEnt/functions.php",
				{
					"Method":"SelectPaymentsWorkers",
					"RangeWith":$("#ReportRangeWith").val(),
					"RangeBy":$("#ReportRangeBy").val()
				},
				function(data){
					var o=jQuery.parseJSON(data);
					var i=0;
					while(o[i]!=null)
					{
						$("#ReportPaymentsTable1").append(
							"<tr>"+
								"<td>"+o[i].Dolgnost+"</td>"+
								"<td>"+parseFloat(o[i].SumWith)+"</td>"+
								"<td>"+(parseFloat(o[i].Cost)+parseFloat(o[i].SumPlus))+"</td>"+
								"<td>"+parseFloat(o[i].SumMinus)+"</td>"+
								"<td>"+(parseFloat(o[i].SumWith)+parseFloat(o[i].Cost)+parseFloat(o[i].SumPlus)+parseFloat(o[i].SumMinus))+"</td>"+
							"</tr>"
						);
						i++;
					};
				}
			);

			$("#ReportPaymentsTable2").find("tr").remove();
			$("#ReportPaymentsTable3").find("tr").remove();
			$.post(
				"reportEnt/functions.php",
				{
					"Method":"SelectPaymentsOrders",
					"RangeWith":$("#ReportRangeWith").val(),
					"RangeBy":$("#ReportRangeBy").val()
				},
				function(data){
					console.log(data);
					var o=jQuery.parseJSON(data);
					var i=0;
					while(o.TypeDoors[i]!=null)
					{
						$("#ReportPaymentsTable2").append(
							"<tr>"+
								"<td>"+o.TypeDoors[i].Name+"</td>"+
								"<td>"+o.TypeDoors[i].Step.Laser+"</td>"+
								"<td>"+o.TypeDoors[i].Step.Sgibka+"</td>"+
								"<td>"+o.TypeDoors[i].Step.Mdf+"</td>"+
								"<td>"+o.TypeDoors[i].Step.Svarka+"</td>"+
								"<td>"+o.TypeDoors[i].Step.Frame+"</td>"+
								"<td>"+o.TypeDoors[i].Step.Sborka+"</td>"+
								"<td>"+o.TypeDoors[i].Step.Color+"</td>"+
								"<td>"+o.TypeDoors[i].Step.SborkaMdf+"</td>"+
								"<td>"+o.TypeDoors[i].Step.Upak+"</td>"+
								"<td>"+o.TypeDoors[i].Step.Shpt+"</td>"+
								"<td>"+(
									o.TypeDoors[i].Step.Laser+
									o.TypeDoors[i].Step.Sgibka+
									o.TypeDoors[i].Step.Mdf+
									o.TypeDoors[i].Step.Svarka+
									o.TypeDoors[i].Step.Frame+
									o.TypeDoors[i].Step.Sborka+
									o.TypeDoors[i].Step.Color+
									o.TypeDoors[i].Step.SborkaMdf+
									o.TypeDoors[i].Step.Upak+
									o.TypeDoors[i].Step.Shpt
								)+"</td>"+
							"</tr>"
						);
						i++;
					};
					i=0;
					while(o.Orders[i]!=null)
					{
						$("#ReportPaymentsTable3").append(
							"<tr>"+
								"<td>"+o.Orders[i].Blank+"</td>"+
								"<td>"+o.Orders[i].Shet+"</td>"+
								"<td>"+o.Orders[i].Step.Laser+"</td>"+
								"<td>"+o.Orders[i].Step.Sgibka+"</td>"+
								"<td>"+o.Orders[i].Step.Mdf+"</td>"+
								"<td>"+o.Orders[i].Step.Svarka+"</td>"+
								"<td>"+o.Orders[i].Step.Frame+"</td>"+
								"<td>"+o.Orders[i].Step.Sborka+"</td>"+
								"<td>"+o.Orders[i].Step.Color+"</td>"+
								"<td>"+o.Orders[i].Step.SborkaMdf+"</td>"+
								"<td>"+o.Orders[i].Step.Upak+"</td>"+
								"<td>"+o.Orders[i].Step.Shpt+"</td>"+
								"<td>"+(
									o.Orders[i].Step.Laser+
									o.Orders[i].Step.Sgibka+
									o.Orders[i].Step.Mdf+
									o.Orders[i].Step.Svarka+
									o.Orders[i].Step.Frame+
									o.Orders[i].Step.Sborka+
									o.Orders[i].Step.Color+
									o.Orders[i].Step.SborkaMdf+
									o.Orders[i].Step.Upak+
									o.Orders[i].Step.Shpt
								)+"</td>"+
							"</tr>"
						);
						i++;
					};
				}
			);
		break;
		//Сотрудники
		case 4:
			if($("#ReportWorkers_Table").length==0)
			{
				$("#ReportTab-4").load(
					"/reportEnt/TabWorkers/index.php?ds="+(new Date().getTime().toString()),
					function () {
						ReportWorkers.Select($("#ReportRangeWith").val(), $("#ReportRangeBy").val());
					}
				);
				//Подгрузим наш function.js
				/*
				var scriptElem = document.createElement('script');
				scriptElem.setAttribute('src',"/reportEnt/TabWorkers/function.js");
				scriptElem.setAttribute('type','text/javascript');
				document.getElementsByTagName('head')[0].appendChild(scriptElem);
				*/
			}
			else
				ReportWorkers.Select($("#ReportRangeWith").val(), $("#ReportRangeBy").val());
			break;
	}
}