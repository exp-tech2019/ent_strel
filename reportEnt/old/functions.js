function ReportSelect()
{
	$("#ReportWith").css("background-color","white");
	$("#ReportBy").css("background-color","white");
	var er=false;
	if($("#ReportWith").val()=="") {er=true; $("#ReportWith").css("background-color","pink");};
	if($("#ReportBy").val()=="") {er=true; $("#ReportBy").css("background-color","pink");};
	if(!er)
	{
		$("#ReportWorkersTable").find("tr").remove();
		$.post(
			"reportEnt/functions.php",
			{"Method":"Select","DateWith":$("#ReportWith").val(),"DateBy":$("#ReportBy").val()},
			function(data)
			{
				var o=jQuery.parseJSON(data), i=1;
				var CostSum=0, PaymentPlusSum=0, PaymentMinusSum=0, OstatokSum=0;
				while(o[i]!=null){
					$("#ReportWorkersTable").append(
						"<tr>"+
							"<td>"+o[i].Step+"</td>"+
							"<td>"+o[i].CountComplite+"</td>"+
							"<td>"+o[i].Cost+"</td>"+
							"<td>"+o[i].PaymentPlus+"</td>"+
							"<td>"+o[i].PaymentMinus+"</td>"+
							"<td>"+o[i].Ostatok+"</td>"+
						"</tr>"
						);
					CostSum+=o[i].Cost;
					PaymentPlusSum+=o[i].PaymentPlus;
					PaymentMinusSum+=o[i].PaymentMinus;
					OstatokSum+=o[i].Ostatok;
					i++;
				};
				$("#ReportWorkersTable").append(
						"<tr style='background-color:lightgray'>"+
							"<td colspan=2>Итого</td>"+
							"<td>"+CostSum+"</td>"+
							"<td>"+PaymentPlusSum+"</td>"+
							"<td>"+PaymentMinusSum+"</td>"+
							"<td>"+OstatokSum+"</td>"+
						"</tr>"
						);
			}
		)
	};
}