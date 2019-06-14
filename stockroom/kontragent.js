function KontragentSelectList()
{
	$("#KontragentTableFind").val("");
	$("#KontragentTable").find("tr").remove();
	$.post(
		"stockroom/kontragent.php",
		{
			"Method":"SelectList"
		},
		function(data)
		{
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null)
			{
				$("#KontragentTable").append(
					"<tr id=KontragentTableTR"+o[i].id+">"+
						"<td onclick='KontragentEditStart("+o[i].id+")'''>"+o[i].Name+"</td>"+
						"<td onclick='KontragentEditStart("+o[i].id+")'''>"+o[i].AdressFact+"</td>"+
						"<td onclick='KontragentEditStart("+o[i].id+")'''>"+o[i].Phone+"</td>"+
						"<td onclick='KontragentEditStart("+o[i].id+")'''>"+o[i].Mail+"</td>"+
						"<td onclick='KontragentEditStart("+o[i].id+")'''>"+o[i].CountTrend+"</td>"+
						"<td onclick='KontragentEditStart("+o[i].id+")'''><img src='images/edit.png' width=22></td>"+
						"<td><img src='images/delete.png' title=Удалить onclick='KontragentDelete("+o[i].id+")' width=22></td>"+
					"</tr>"
				);
				i++;
			};
		}
	);
}

function KontragentAdd()
{
	$("#KontragentDialogAction").text("Add");
	
	$("#KontragentDialogID").val("");
	$("#KontragentDialogName").val("");
	$("#KontragentDialogFullName").val("");
	$("#KontragentDialoginn").val("");
	$("#KontragentDialogkpp").val("");
	$("#KontragentDialogokpo").val("");
	
	$("#KontragentDialogBankName").val("");
	$("#KontragentDialogBankRS").val("");
	$("#KontragentDialogBankKS").val("");
	$("#KontragentDialogBankBik").val("");
	
	$("#KontragentDialogNote").val("");
	
	$("#KontragentDialogAdressFact").val("");
	$("#KontragentDialogAdressUrid").val("");
	$("#KontragentDialogPhone").val("");
	$("#KontragentDialogFax").val("");
	
	$("#KontragentDialogContactTable").find("tr").remove();
	$("#KontragentDialogBugs").text("");
	
	 $('#KontragentDialog').dialog('open');
}

function KontragentSave()
{
	//Создаем массив контакты
	var aContact=new Array();
	for(var i=0;i<$("#KontragentDialogContactTable tr").length;i++)
		aContact[i]={
			FIO:$($( $("#KontragentDialogContactTable tr")[i]) .find("td")[0]) .text(),
			Post:$($( $("#KontragentDialogContactTable tr")[i]) .find("td")[1]) .text(),
			Phone:$($( $("#KontragentDialogContactTable tr")[i]) .find("td")[2]) .text(),
			Phone1:$($( $("#KontragentDialogContactTable tr")[i]) .find("td")[3]) .text(),
			Mail:$($( $("#KontragentDialogContactTable tr")[i]) .find("td")[4]) .text(),
			Note:$($( $("#KontragentDialogContactTable tr")[i]) .find("td")[5]) .text()
		};
	$.post(
		"stockroom/kontragent.php",
		{
			"Method":"Save",
			"Action":$("#KontragentDialogAction").text(),
			"id":$("#KontragentDialogID").val(),
			"Name":$("#KontragentDialogName").val(),
			"FullName":$("#KontragentDialogFullName").val(),
			"LegalForm":$("#KontragentDialogLegalForm").val(),
			"inn":$("#KontragentDialoginn").val(),
			"kpp":$("#KontragentDialogkpp").val(),
			"okpo":$("#KontragentDialogokpo").val(),
			
			"BankName":$("#KontragentDialogBankName").val(),
			"BankRS":$("#KontragentDialogBankRS").val(),
			"BankKS":$("#KontragentDialogBankKS").val(),
			"BankBik":$("#KontragentDialogBankBik").val(),
			
			"Note":$("#KontragentDialogNote").val(),
			
			"AdressFact":$("#KontragentDialogAdressFact").val(),
			"AdressUrid":$("#KontragentDialogAdressUrid").val(),
			"Phone":$("#KontragentDialogPhone").val(),
			"Fax":$("#KontragentDialogFax").val(),
			
			"aContact":aContact
		},
		function (data)
		{
			if(data=="") {$("#KontragentDialog").dialog("close");} else $("#KontragentDialogBugs").text(data);
		}
	);
}

function KontragentDelete(id)
{
	if(confirm("Удалить: "+$($("#KontragentTableTR"+id+" td")[0]).text()+"?") )
		$.post(
			"stockroom/kontragent.php",
			{"Method":"Delete", "id":id},
			function(data)
			{
				if(data=="ok") {$("#KontragentTableTR"+id).remove();} else alert(data);
			}
		);
}

function KontragentEditStart(id)
{
	$("#KontragentDialogBugs").text("");
	$("#KontragentDialogContactTable").find("tr").remove();
	$.post(
		"stockroom/kontragent.php",
		{"Method":"EditStart", "id":id},
		function(data)
		{
			var o=jQuery.parseJSON(data);
			$("#KontragentDialogAction").text("Edit");
			$("#KontragentDialogID").val(o.id);
			$("#KontragentDialogName").val(o.Name);
			$("#KontragentDialogLegalForm").val(o.LegalForm);
			$("#KontragentDialogFullName").val(o.FullName);
			$("#KontragentDialoginn").val(o.inn);
			$("#KontragentDialogkpp").val(o.kpp);
			$("#KontragentDialogokpo").val(o.okpo);
			$("#KontragentDialogBankName").val(o.BankName);
			$("#KontragentDialogBankRS").val(o.BankRS);
			$("#KontragentDialogBankKS").val(o.BankKS);
			$("#KontragentDialogBankBik").val(o.BankBik);
			$("#KontragentDialogAdressFact").val(o.AdressFact);
			$("#KontragentDialogAdressUrid").val(o.AdressUrid);
			$("#KontragentDialogPhone").val(o.Phone);
			$("#KontragentDialogFax").val(o.Fax);
			$("#KontragentDialogNote").val(o.Note);
			var i=0;
			while(o.Contacts[i]!=null)
			{
				$("#KontragentDialogContactTable").append(
					"<tr>"+
						"<td>"+o.Contacts[i].FIO+"</td>"+
						"<td>"+o.Contacts[i].Post+"</td>"+
						"<td>"+o.Contacts[i].Phone+"</td>"+
						"<td>"+o.Contacts[i].Phone1+"</td>"+
						"<td>"+o.Contacts[i].Mail+"</td>"+
						"<td>"+o.Contacts[i].Note+"</td>"+
						"<td><img onclick='KontragentContactEditStart(this)' src='images/edit.png'></td>"+
						"<td><img onclick='KontragentContactDelete(this)' src='images/delete.png' width=16></td>"+
						"<td></td>"+
					"</tr>"
				);
				i++;
			};
			$("#KontragentDialog").dialog("open");
		}
	);
}

//Поиск по таблице контрагентов
function KontragentFind()
{
	for(var i=0;i<$("#KontragentTable").find("tr").length;i++)
	{
		var tr=$("#KontragentTable").find("tr")[i];
		var el=$(tr).find("td");
		var showTR=false;
		var sSearch=$("#KontragentTableFind").val().toLowerCase();
		if ($(el[0]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if ($(el[1]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if ($(el[2]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if ($(el[3]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if ($(el[4]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if(showTR)
		{
			$(tr).show();
		}
		else
			$(tr).hide();
	}
}

var KontragentContactEditTR;
function KontragentContactEditStart(elImg)
{
	$("#KontragentContactDialogStatus").val("Edit");
	var el=$(elImg).parent().parent();
	$("#KontragentContactDialogFIO").val( $($(el).find("td")[0]).text() );
	$("#KontragentContactDialogDolgnost").val( $($(el).find("td")[1]).text() );
	$("#KontragentContactDialogPhone").val( $($(el).find("td")[2]).text() );
	$("#KontragentContactDialogPhone1").val( $($(el).find("td")[3]).text() );
	$("#KontragentContactDialogMail").val( $($(el).find("td")[4]).text() );
	$("#KontragentContactDialogNote").val( $($(el).find("td")[5]).text() );
	KontragentContactEditTR=el;
	$("#KontragentContactDialog").dialog("open");
}
function KontragentContactEditSave()
{
	$(KontragentContactEditTR).find("td").remove();
	$(KontragentContactEditTR).append(
		"<td>"+$("#KontragentContactDialogFIO").val()+"</td>"+
		"<td>"+$("#KontragentContactDialogDolgnost").val()+"</td>"+
		"<td>"+$("#KontragentContactDialogPhone").val()+"</td>"+
		"<td>"+$("#KontragentContactDialogPhone1").val()+"</td>"+
		"<td>"+$("#KontragentContactDialogMail").val()+"</td>"+
		"<td>"+$("#KontragentContactDialogNote").val()+"</td>"
	);
	$("#KontragentContactDialog").dialog("close");
}

function KontragentContactAdd()
{
	$("#KontragentContactDialogStatus").val("Add");
	$("#KontragentContactDialogFIO").val("");
	$("#KontragentContactDialogDolgnost").val("");
	$("#KontragentContactDialogPhone").val("");
	$("#KontragentContactDialogPhone1").val("");
	$("#KontragentContactDialogMail").val("");
	$("#KontragentContactDialogNote").val("");
	$("#KontragentContactDialog").dialog("open");
}
function KontragentContactAddSave()
{
	$("#KontragentDialogContactTable").append(
		"<tr>"+
			"<td>"+$("#KontragentContactDialogFIO").val()+"</td>"+
			"<td>"+$("#KontragentContactDialogDolgnost").val()+"</td>"+
			"<td>"+$("#KontragentContactDialogPhone").val()+"</td>"+
			"<td>"+$("#KontragentContactDialogPhone1").val()+"</td>"+
			"<td>"+$("#KontragentContactDialogMail").val()+"</td>"+
			"<td>"+$("#KontragentContactDialogNote").val()+"</td>"+
			"<td><img onclick='KontragentContactEditStart(this)' src='images/edit.png'></td>"+
			"<td><img onclick='KontragentContactDelete(this)' src='images/delete.png' width=16></td>"+
			"<td></td>"+
		"</tr>"
	);
	$("#KontragentContactDialog").dialog("close");
}

function KontragentContactDelete(el)
{
	if(confirm("Удалить контакт?")) $(el).parent().parent().remove();
}

function KontragentContactFind()
{
	for(var i=0;i<$("#KontragentDialogContactTable").find("tr").length;i++)
	{
		var el=$($("#KontragentDialogContactTable").find("tr")[i]).find("td");
		var showTR=false;
		var sSearch=$("#KontragentDialogContactFindInput").val().toLowerCase();
		if ($(el[0]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if ($(el[1]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if ($(el[2]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if ($(el[3]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if ($(el[4]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if ($(el[5]).text().toLowerCase().indexOf(sSearch)>-1) showTR=true;
		if(showTR)
		{
			$($("#KontragentDialogContactTable").find("tr")[i]).show();
		}
		else
			$($("#KontragentDialogContactTable").find("tr")[i]).hide();
	}
}