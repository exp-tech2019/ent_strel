//Отображение категорий
function StockListSelect(Where)
{
	$("#StockMaterialList").find("tr").remove();
	$.post(
		"stockroom/stock.php",
		{"Method":"ListSelect","Where":Where},
		function(data)
		{
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null)
			{
				var EditBtn="<img onclick='StockGroupEdit(this)' src='images/edit.png'>";
				var DeleteBtn="<img onclick='StockGroupDelete(this)' src='images/delete.png'>";
				var AddBtn="<img onclick='StockMaterialAdd(this)' src='images/addButton.png'>";
				$("#StockMaterialList").append("<tr cat><td colspan=6 onclick='StockMaterialReveal(this)'>"+o[i].Name+"</td><td button>"+DeleteBtn+"</td><td button>"+EditBtn+"</td><td button>"+AddBtn+"</td></tr>");
				i++;
			};
		}
	);
}
//отображение материала в категории
function StockMaterialReveal(el)
{
    var elTr=$(el).parent();
	if($(elTr).parent().find("tr[group='"+$(el).text()+"']").length==0)
	{
		$.post(
			"stockroom/stock.php",
			{"Method":"MaterialsTableSelect","GroupName":$(el).text()},
			function(data)
			{
				var o=jQuery.parseJSON(data); var i=0;
				while(o[i]!=null)
				{
					var EditBtn="<img onclick='StockMaterialEditStart(this)' src='images/edit.png'>";
					var DeleteBtn="<img onclick='StockMaterialDelete(this)' src='images/delete.png'>";
					$(elTr).after(
						"<tr group='"+$(el).text()+"' MaterialID="+o[i].id+">"+
							"<td>"+o[i].Name+"</td>"+
							"<td>"+o[i].Unit+"</td>"+
							"<td>0</td>"+
							"<td>0</td>"+
							"<td>0</td>"+
							"<td>0</td>"+
							"<td>"+DeleteBtn+"</td>"+
							"<td>"+EditBtn+"</td>"+
							"<td></td>"+
						"</tr>"
						);
					i++;
				};
			}
		);
	}
	else
	{
		$(elTr).parent().find("tr[group='"+$(el).text()+"']").remove();
	};
}

//---------------------Работа с категориями------------------------
function StockGroupAdd()
{
	var s=prompt("Наименование катгории","");
	if(s!="")
		$.post(
			"stockroom/stock.php",
			{"Method":"GroupAdd","GroupName":s},
			function(data)
			{
				if(data=="ok") 
				{
					var EditBtn="<img onclick='StockGroupEdit(this)' src='images/edit.png'>";
					var DeleteBtn="<img onclick='StockGroupDelete(this)' src='images/delete.png'>";
					var AddBtn="<img onclick='StockMaterialAdd(this)' src='images/addButton.png'>";
					$("#StockMaterialList").append("<tr cat><td colspan=6 onclick='StockMaterialReveal(this)'>"+s+"</td><td button>"+DeleteBtn+"</td><td button>"+EditBtn+"</td><td button>"+AddBtn+"</td></tr>");
				} else alert(data);
			}
		)
}
function StockGroupEdit(el)
{
	var groupOldName=$( $(el).parent().parent().find("td")[0] ).text();
	var groupNewName=prompt("Наименование катгории",groupOldName);
	if(groupNewName!="")
		$.post(
			"stockroom/stock.php",
			{"Method":"GroupEdit","OldName":groupOldName,"NewName":groupNewName},
			function(data)
			{
				if(data=="ok") { $( $(el).parent().parent().find("td")[0] ).text(groupNewName)} else alert(data);
			}
		)
}
function StockGroupDelete(el)
{
	if(confirm("Удалить категорию?"))
		$.post(
			"stockroom/stock.php",
			{"Method":"GroupDelete","GroupName":$( $(el).parent().parent().find("td")[0] ).text()},
			function(data)
			{
				if(data=="ok") { $(el).parent().parent().remove(); } else alert(data);
			}
		);
}

<!------------Работа с материалом-------------------------------->
var StockMaterialCatEl;
function StockMaterialAdd(GroupEl)
{
	StockMaterialCatEl=$(GroupEl).parent().parent();
	$("#StockMaterialDialogID").val("");
	$("#StockMaterialDialogName").val("");
	$("#StockMaterialDialogUnit").val("");
	$("#StockMaterialDialogCount").val("0");
	$("#StockMaterialDialogAttn").removeAttr("checked");
	$("#StockMaterialDialog").dialog("open");
}
function StockMaterialSave()
{
	//Определим имя категории
	var CatName="";
	if($("#StockMaterialDialogID").val()=="")
	{
		CatName=$($(StockMaterialCatEl).find("td")[0]).text();
	};
	var Attn=0; if($("#StockMaterialDialogAttn").is(":checked")) Attn=1;
	$.post(
		"stockroom/stock.php",
		{
			"Method": "MaterialSave",
			"id":$("#StockMaterialDialogID").val(),
			"CatName":CatName,
			"Name":$("#StockMaterialDialogName").val(),
			"Unit":$("#StockMaterialDialogUnit").val(),
			"Count":$("#StockMaterialDialogCount").val(),
			"Attn":Attn,
		},
		function (data)
		{
			if(data.indexOf("error")>-1)
			{
				alert(data);
			}
			else
			{
				$("#StockMaterialDialog").dialog("close");
				if($("#StockMaterialDialogID").val()=="")
				{
					var EditBtn="<img onclick='StockMaterialEditStart(this)' src='images/edit.png'>";
					var DeleteBtn="<img onclick='StockMaterialDelete(this)' src='images/delete.png'>";
					$(elTr).after(
						"<tr group='"+CatName+"' MaterialID="+data+">"+
							"<td>"+$("#StockMaterialDialogName").val()+"</td>"+
							"<td>"+$("#StockMaterialDialogUnit").val()+"</td>"+
							"<td>0</td>"+
							"<td>0</td>"+
							"<td>0</td>"+
							"<td>0</td>"+
							"<td>"+DeleteBtn+"</td>"+
							"<td>"+EditBtn+"</td>"+
							"<td></td>"+
						"</tr>"
					);
				}
				else
				{
					$($(StockMaterialCatEl).find("td")[0]).text($("#StockMaterialDialogName").val());
					$($(StockMaterialCatEl).find("td")[1]).text($("#StockMaterialDialogUnit").val());
				};
			};
		}
	);
}
function StockMaterialEditStart(el)
{
	var id=$(el).parent().parent().attr("MaterialID");
	$.post(
		"stockroom/stock.php",
		{"Method":"MaterialEditStart", "id":id},
		function(data)
		{
			var o=jQuery.parseJSON(data);
			$("#StockMaterialDialogID").val(id);
			$("#StockMaterialDialogName").val(o.Name);
			$("#StockMaterialDialogUnit").val(o.Unit);
			$("#StockMaterialDialogCount").val(o.Count);
			$("#StockMaterialDialogAttn").removeAttr("checked"); if(o.Attn==1){ $("#StockMaterialDialogAttn").prop("checked","true");};
			$("#StockMaterialDialog").dialog("open");
			StockMaterialCatEl=$(el).parent().parent();
		}
	)
}
function StockMaterialDelete(el)
{
	var elTR=$(el).parent().parent();
	if(confirm("Удалить: "+$($(elTR).find("td")[0]).text()+"?"))
		$.post(
			"stockroom/stock.php",
			{"Method":"MaterialDelete", "id":$(elTR).attr("MaterialID")},
			function (data)
			{
				if(data=="ok") {elTR.remove()} else alert(data);
			}
		);
}

//----------Список контрагентов---------------------
function StockManualSupliersSelect()
{
	$("#StockManualSupliersTable").find("tr").remove();
	$.post(
		"stockroom/stock.php",
		{"Method":"SupliersSelect"},
		function(data)
		{
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null)
			{
				$("#StockManualSupliersTable").append("<tr idSuplier="+o[i].id+" onclick='StockManualSupliersTRClick(this)'><td>"+o[i].Name+"</td></tr>");
				i++;
			};
			$("#StockManualSupliers").dialog("open");
		}
	);
}
function StockManualSupliersTRClick(el)
{
	$("#StockManualSupliersTable").find("tr").css("background-color","white");
	$(el).css("background-color","red");
}
function StockManualSupliersFind()
{
	for(var i=0; i<$("#StockManualSupliersTable").find("tr").length;i++)
	{
		$($("#StockManualSupliersTable").find("tr")[i]).show();
		if($($($("#StockManualSupliersTable").find("tr")[i]).find("td")[0]).text().toLowerCase().indexOf($("#StockManualSupliersFindBtn").val().toLowerCase())==-1 )
			$($("#StockManualSupliersTable").find("tr")[i]).hide();
	};		
}
function StockManualSupliersSelected()
{
	for(var i=0; i<$("#StockManualSupliersTable").find("tr").length;i++)
		if($($($("#StockManualSupliersTable").find("tr")[i])).css("background-color")=="rgb(255, 0, 0)" )
		{
			$("#StockRecieptDialogSuplier").val( $($($("#StockManualSupliersTable").find("tr")[i]).find("td")[0]).text() );
			$("#StockRecieptDialogSuplierID").val( $("#StockManualSupliersTable tr:eq("+i+")").attr("idSuplier"));
			$("#StockManualSupliers").dialog("close");
			break;
		};
}

//-----------------Справочник материалов----------------------------
function StockManualMaterialsLoad(elTR)
{
	$("#StockManualMaterialsTable").find("tr").remove();
	$("#StockManualMaterialsGuid").val($(elTR).parent().attr("guid"));
	$.post(
		"stockroom/stock.php",
		{"Method":"ListSelect"},
		function(data)
		{
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null)
			{
				$("#StockManualMaterialsTable").append(
					"<tr cat><td colspan=2 onclick='StockManualMaterialsSelect(this)'>"+o[i].Name+"</td></tr>"
				);
				i++;
			};
			$("#StockManualMaterialsDialog").dialog("open");
		}
	);
}

//отображение материала в категории
function StockManualMaterialsSelect(el)
{
    var elTr=$(el).parent();
	if($(elTr).parent().find("tr[group='"+$(el).text()+"']").length==0)
	{
		$.post(
			"stockroom/stock.php",
			{"Method":"MaterialsTableSelect","GroupName":$(el).text()},
			function(data)
			{
				var o=jQuery.parseJSON(data); var i=0;
				while(o[i]!=null)
				{
					$(elTr).after(
						"<tr material=true onclick='StockManualMaterialsSelected(this)' group='"+$(el).text()+"' MaterialID="+o[i].id+">"+
							"<td>"+o[i].Name+"</td>"+
							"<td>"+o[i].Unit+"</td>"+
						"</tr>"
						);
					i++;
				};
			}
		);
	}
	else
	{
		$(elTr).parent().find("tr[group='"+$(el).text()+"']").remove();
	};
}
//Выбран материал (отмечен красным)
function StockManualMaterialsSelected(el)
{
	$("#StockManualMaterialsTable tr[material=true]").css("background-color","white");
	$(el).css("background-color","red");
}
//После выбора материала вернем его параметры в позицию
function StockManualMaterialsOK()
{
	for(var i=0;i<$("#StockManualMaterialsTable tr[material=true]").length;i++)
		if($($("#StockManualMaterialsTable tr[material=true]")[i]).css("background-color")=="rgb(255, 0, 0)" )
		{
			var mID= $($("#StockManualMaterialsTable tr[material=true]")[i]).attr("MaterialID");
			var mName= $($($("#StockManualMaterialsTable tr[material=true]")[i]).find("td")[0]).text();
			var mUnit= $($($("#StockManualMaterialsTable tr[material=true]")[i]).find("td")[1]).text();
			$("#StockRecieptDialogGoodsTable tr[guid='"+$("#StockManualMaterialsGuid").val()+"']").attr("idMaterial",mID);
			$($("#StockRecieptDialogGoodsTable tr[guid='"+$("#StockManualMaterialsGuid").val()+"']").find("td")[0]).text(mName);
			$($("#StockRecieptDialogGoodsTable tr[guid='"+$("#StockManualMaterialsGuid").val()+"']").find("td")[2]).text(mUnit);
			$("#StockManualMaterialsDialog").dialog("close");
			break;
		};
}

//-----------------Работа с таблицей материалов------------------
function StockMaterialTableAddRow()
{
	$("#StockRecieptDialogGoodsTable").prepend(
		"<tr idRow='' idMaterial='' guid='"+guid()+"' status='Add'>"+
			"<td></td>"+
			"<td onclick='StockManualMaterialsLoad(this)'><img src='images/open.png' width=20></td>"+
			"<td></td>"+
			"<td><input onkeyup='StockMaterialTableCalcSum(this)'></td>"+
			"<td><input onkeyup='StockMaterialTableCalcSum(this)'></td>"+
			"<td><input></td>"+
			"<td><img src='images/delete.png' width=20 onclick='StockMaterialTableDelRow($(this).parent().parent())'></td>"+
		"</tr>"
	);
}

function StockMaterialTableDelRow(tr)
{
	if(confirm("Удалить?"))
	if($(tr).attr("status")=="Add")
	{
		$(tr).remove();
	}
	else
	{
		$(tr).attr("status","Del");
		$(tr).hide();
	}
}

//-------------------------Оприходование материала-------------------------------

//Функция по рсчету стоимости в строке
function StockMaterialTableCalcSum(el)
{
	$("#StockRecieptDialogBugs").text("");
	var tr=$(el).parent().parent();
	var count=parseFloat($($(tr).find("td")[3]).find("input").val());
	//Изменение кол-ва влият на остаток по материалу - для этого расчитаем эти изменения
	if($(el).attr("CountEnd")!=null)
	{
		var CountStart=parseFloat($(el).attr("Count"));
		var CountEnd=parseFloat($(el).attr("CountEnd"));
		var CountEdit=parseFloat($(el).val());
		var CountEndEdit=(CountEdit-CountStart)+CountEnd;
		$(el).attr("CountEnd",CountEndEdit.toString());
		$(el).attr("Count",CountEdit.toString());
		if(CountEndEdit<0)
			$("#StockRecieptDialogBugs").text("Остаток по поступлению отрицателен");
	};
	var prise=parseFloat($($(tr).find("td")[4]).find("input").val());
	if(!isNaN(prise) & !isNaN(count))
	$($(tr).find("td")[5]).find("input").val((count*prise).toString());
	if( $(tr).attr("status")=="Load") $(tr).attr("status","Edit");
}

//Создаем новый приход
function StockRecieptAdd()
{
	$("#StockRecieptDialogID").val("");
	$("#StockRecieptDialogBugs").text("");
	$.post(
			"stockroom/stock.php",
			{ "Method":"RecieptMaxNum"},
			function (data) {$("#StockRecieptDialogNum").val(data);}
	);
	$("#StockRecieptDialogDate").val("");
	$("#StockRecieptDialogSuplier").val("");
	$("#StockRecieptDialogSuplierID").val("");
	$("#StockRecieptDialogGoodsTable").find("tr").remove();
	$('#StockRecieptDialog').dialog('open');
}
function StockRecieptEditStart(elimg)
{
	//Определим Tr
	var elTr=$(elimg).parent().parent();
	var id=$(elTr).attr("idTrade");
	$("#StockRecieptDialogBugs").text("");
	//Определим оприходование или списание
	switch($(elTr).attr("TypeTrade"))
	{
		case "R":
			$.post(
				"stockroom/stock.php",
				{
					"Method":"RecieptEditStart",
					"id":id
				},
				function (data)
				{
					var o=jQuery.parseJSON(data); var i=0;
					$("#StockRecieptDialogID").val(o.id);
					$("#StockRecieptDialogNum").val(o.Num);
					$("#StockRecieptDialogDate").val(o.Date);
					$("#StockRecieptDialogSuplier").val(o.SupplierName);
					$("#StockRecieptDialogSuplierID").val(o.idSupplier);
					$("#StockRecieptDialogNote").val(o.Note);
					$("#StockRecieptDialogGoodsTable").find("tr").remove();
					while(o.Materials[i]!=null)
					{
						var Material=o.Materials[i];
						$("#StockRecieptDialogGoodsTable").prepend(
							"<tr idRow='"+Material.id+"' idMaterial='"+Material.idMaterial+"' guid='"+guid()+"' status='Load'>"+
								"<td>"+Material.MaterialName+"</td>"+
								"<td onclick='StockManualMaterialsLoad(this)'><img src='images/open.png' width=20></td>"+
								"<td>"+Material.MaterialUnit+"</td>"+
								"<td><input onkeyup='StockMaterialTableCalcSum(this)' Count='"+Material.Count+"' CountEnd='"+Material.CountEnd+"' value='"+Material.Count+"'></td>"+
								"<td><input onkeyup='StockMaterialTableCalcSum(this)' value='"+Material.Price+"'></td>"+
								"<td><input value='"+Material.Sum+"'></td>"+
								"<td><img src='images/delete.png' width=20 onclick='StockMaterialTableDelRow($(this).parent().parent())'></td>"+
							"</tr>"
						);
						i++;
					};
					$('#StockRecieptDialog').dialog('open');
				}
			);
		break;
	};
}
//Сохраняем новый/редактированный приход
function StockRecieptSave()
{
	$("#StockRecieptDialogBugs").text("");
	//Сделаем проверку на заполненность
	var erFlag=false;
	$("#StockRecieptDialogNum").css("background-color","white");
	$("#StockRecieptDialogDate").css("background-color","white");
	$("#StockRecieptDialogSuplier").css("background-color","white");
	if($("#StockRecieptDialogNum").val()=="") {erFlag=true; $("#StockRecieptDialogNum").css("background-color","#F5A0A0")};
	if($("#StockRecieptDialogDate").val()=="") {erFlag=true; $("#StockRecieptDialogDate").css("background-color","#F5A0A0")};
	if($("#StockRecieptDialogSuplierID").val()=="") {erFlag=true; $("#StockRecieptDialogSuplier").css("background-color","#F5A0A0")};
	for(var i=0;i<$("#StockRecieptDialogGoodsTable").find("tr").length-1;i++)
	{
		$("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(0)").css("background-color","white");
		$("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(3) input").css("background-color","white");
		$("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(4) input").css("background-color","white");
		$("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(5) input").css("background-color","white");
		if($("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(0)").text()=="") {erFlag=true; $("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(0)").css("background-color","#F5A0A0")};
		if($("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(3) input").val()=="") {erFlag=true; $("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(3) input").css("background-color","#F5A0A0")};
		if($("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(4) input").val()=="") {erFlag=true; $("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(4) input").css("background-color","#F5A0A0")};
		if($("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(5) input").val()=="") {erFlag=true; $("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(5) input").css("background-color","#F5A0A0")};
	};
	if(!erFlag)
	{
		//Создадим массивы строк
		var aMaterial=new Array();
		var amidRow=new Array();
		var amidMaterial=new Array();
		var amStatus=new Array();
		var amCount=new Array();
		var amCountEnd=new Array();
		var amCountEndEdit=new Array();
		var amPrise=new Array();
		var amSum=new Array();
		for(var i=0;i<$("#StockRecieptDialogGoodsTable").find("tr").length;i++)
		{
			amidRow[i]=$("#StockRecieptDialogGoodsTable tr:eq("+i+")").attr("idRow");
			amidMaterial[i]=$("#StockRecieptDialogGoodsTable tr:eq("+i+")").attr("idMaterial");
			amStatus[i]=$("#StockRecieptDialogGoodsTable tr:eq("+i+")").attr("status");
			amCount[i]=$("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(3) input").val();
			amCountEnd[i]=$("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(3) input").val();
			amCountEndEdit[i]=$("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(3) input").attr("CountEnd");
			amPrise[i]=$("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(4) input").val();
			amSum[i]=$("#StockRecieptDialogGoodsTable tr:eq("+i+") td:eq(5) input").val();
		};
		$.post(
			"stockroom/stock.php",
			{
				"Method":"RecieptSave",
				"idReciept":$("#StockRecieptDialogID").val(),
				"Num":$("#StockRecieptDialogNum").val(),
				"Date":$("#StockRecieptDialogDate").val(),
				"SuplierID":$("#StockRecieptDialogSuplierID").val(),
				"Note":$("#StockRecieptDialogNote").val(),
				"idRow[]":amidRow,
				"idMaterial[]":amidMaterial,
				"Status[]":amStatus,
				"Count[]":amCount,
				"CountEnd[]":amCountEnd,
				"CountEndEdit[]":amCountEndEdit,
				"Prise[]":amPrise,
				"Sum[]":amSum,
			},
			function(data)
			{
				if(data!="") { $("#StockRecieptDialogBugs").text(data); } else $('#StockRecieptDialog').dialog('close');
			}
		);
	};
}

//------------------------------------Вкладка ДВИЖЕНИЕ----------------------------------------------
function StockTradeSelect()
{
	$("#StockTradeList").find("tr").remove();
	$.post(
		"stockroom/stock.php",
		{
			"Method":"TradeSelect",
			"DateWith":$("#StockMenuTableInpDateWith").val(),
			"DateBy":$("#StockMenuTableInpDateBy").val(),
		},
		function (data)
		{
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null)
			{
				var img="<img src='images/log_in.png' style='width:15px; float:left'>";
				if(o[i].TypeTrade!="R") img="<img src='images/log_out.png' style='width:15px; float:left'>";
				$("#StockTradeList").append(
					"<tr cat idTrade="+o[i].id+" TypeTrade="+o[i].TypeTrade+" style='vertical-align:middle'>"+
						"<td colspan=2 onclick='StockTradeMaterialsSelect(this)'>"+img+" "+o[i].Num+" - "+o[i].Date+" </td><td colspan=3 onclick='StockTradeMaterialsSelect(this)'>"+o[i].Supplier+"</td><td>"+o[i].Sum+" <img onclick='StockRecieptEditStart(this)' src='images/edit.png' style='float:right; cursor:pointer'></td>"+
					"</tr>"
				);
				i++;
			};
		}
	);
}
function StockTradeMaterialsSelect(elTd)
{
	//Определим родительскую Tr
	var elTr=$(elTd).parent();
	var idTrade=$(elTr).attr("idTrade");
	if($("#StockTradeList").find("tr[group="+idTrade+"]").length==0)
	{
		
		$.post(
			"stockroom/stock.php",
			{
				"Method":"TradeMaterialsSelect",
				"idReciept":idTrade,
				"TypeTrade":$(elTr).attr("TypeTrade")
			},
			function(data)
			{
				var o=jQuery.parseJSON(data); var i=0;
				while(o[i]!=null)
				{
					$(elTr).after(
						"<tr group="+idTrade+">"+
							"<td style='border-right:solid 1px gray; padding-left:7px;'>"+o[i].Name+"</td>"+
							"<td style='border-right:solid 1px gray; padding-left:7px;'>"+o[i].Unit+"</td>"+
							"<td style='border-right:solid 1px gray; padding-left:7px;'>"+o[i].Price+"</td>"+
							"<td style='border-right:solid 1px gray; padding-left:7px;'>"+o[i].Count+"</td>"+
							"<td style='border-right:solid 1px gray; padding-left:7px;'>"+o[i].CountEnd+"</td>"+
							"<td style='border-right:solid 1px gray; padding-left:7px;'>"+o[i].Sum+"</td>"+
						"</tr>"
					);
					i++;
				};
			}
		);
	}
	else $("#StockTradeList").find("tr[group="+idTrade+"]").remove();
}