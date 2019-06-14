//Добавление матриала
function StockroomAddMaterialStart()
{
	$("#StockroomMaterialsDialogMaterial").val("");
	$("#StockroomMaterialsDialogMaterialID").val("");
	$("#StockroomMaterialsDialogPrice").val("");
	$("#StockroomMaterialsDialogCount").val("");
	$("#StockroomMaterialsDialogSI").text("");
	$("#StockroomMaterialsDialogBugs").hide();
	$("#StockroomMaterialsDialogType").find("option").remove();
	$("#StockroomMaterialsDialogType").append("<option selected>Лист</option>");
	$("#StockroomMaterialsDialogType").append("<option >Фурнитура</option>");
	$("#StockroomMaterialsDialogType").append("<option >Замок</option>");
	$("#StockroomMaterialsDialogType").append("<option >Петля</option>");
	$("#StockroomMaterialsDialogType").append("<option >Лента ППЖ</option>");
	$("#StockroomMaterialsDialogType").append("<option >Краска</option>");
	$("#StockroomMaterialsDialog").dialog("open");
}
function StockroomMaterialsSave()
{
	var erText="";
	if($("#StockroomMaterialsDialogMaterial").val()=="")
		erText=erText+"Не заполненно поле материал<br>";
	if($("#StockroomMaterialsDialogMaterialID").val()=="")
		erText=erText+"Не заполненно поле идентификатор материала<br>";
	if($("#StockroomMaterialsDialogPrice").val()=="")
		erText=erText+"Не заполненно поле цена<br>";
	if($("#StockroomMaterialsDialogCount").val()=="")
		erText=erText+"Не заполненно поле количество<br>";
	if(erText!="")
	{
		$("#StockroomMaterialsDialogBugs").show(); $("#StockroomMaterialsDialogBugs").html("<hr>"+erText);
	}
	else
	{
		$.post(
			"stockroom/functions.php",
			{"Method":"AddMaterial", "idMaterial":$("#StockroomMaterialsDialogMaterialID").val(), "Price":$("#StockroomMaterialsDialogPrice").val(), "Count":$("#StockroomMaterialsDialogCount").val()},
			function (data){
				if(data=="ok")
				{
					$("#StockroomMaterialsDialog").dialog("close");
				}
				else
				{$("#StockroomMaterialsDialogBugs").show(); $("#StockroomMaterialsDialogBugs").html("<hr>"+data);};
			}
		)
	};
}
function StockroomManualsDialogOpen(){
	//Список констант
	$.post(
		"stockroom/functions.php",
		{"Method":"SelectManual", "Type":$("#StockroomMaterialsDialogType").val()},
		function (data){
			var o=jQuery.parseJSON(data);
			var i=0;
			$("#StockroomManualsList").find("option").remove();
			while(o[i])
			{
				if(i==0)
				{
					$("#StockroomManualsList").append("<option selected value='"+o[i]["Name"]+"' id="+o[i][ 'id' ]+" SI='"+o[i]["SI"]+"'>"+o[i]["Name"]+"</option>");
				}
				else
					$("#StockroomManualsList").append("<option value='"+o[i]["Name"]+"' id="+o[i][ 'id' ]+" SI='"+o[i]["SI"]+"'>"+o[i]["Name"]+"</option>");
				i++;
			};
			$("#StockroomManualsDialog").dialog("open");
		}
	)
}
function StockroomManualsDialogSave(){
	if($("#StockroomManualsList").val()!=null)
	{
		$("#StockroomMaterialsDialogMaterial").val($("#StockroomManualsList").val());
		$("#StockroomMaterialsDialogMaterialID").val($("#StockroomManualsList option:selected").attr("id"));
		$("#StockroomMaterialsDialogSI").text($("#StockroomManualsList option:selected").attr("SI"));
	}
	else
	{
		$("#StockroomMaterialsDialogMaterial").val("");
		$("#StockroomMaterialsDialogMaterialID").val("");
		$("#StockroomMaterialsDialogSI").text("");
	};
	$("#StockroomManualsDialog").dialog("close");
}

//-----------------Таблица материалов----------------------------------
function StockroomListSelect(Type)
{
	var Where="1=1";
	switch(Type)
	{
		case "Листы":Where="m.Type='Лист'"; break;
		case "Фурнитура":Where="m.Type='Фурнитура'"; break;
		case "Замки":Where="m.Type='Замок'"; break;
		case "Петли":Where="m.Type='Петля'"; break;
		case "Лента ППЖ":Where="m.Type='Лента ППЖ'"; break;
		case "Краски":Where="m.Type='Краска'"; break;
	};
	$("#StockroomMaterialsTable").find("tr").remove();
	$.post(
		"stockroom/functions.php",
		{"Method":"SelectMaterils","Where":Where},
		function (data)
		{
			var o=jQuery.parseJSON(data);
			var i=0;
			while(o[i]!=null)
			{
				$("#StockroomMaterialsTable").append(
					"<tr>"+
						"<td>"+o[i]["Name"]+"</td>"+
						"<td>"+o[i]["Count"]+ o[i]['SI'] +"</td>"+
						"<td>"+o[i]["Price"]+"</td>"+
					"</tr>"
				);
				i++;
			}
		}
	);
	setTimeout(StockroomListSelect,900000);//Каждые 15 мин
}

//-------------------------------Список справочника справа для фильра--------------


//------------------------------Работа со справочником--------------------------------------
function StockManualGroupDialogShow(Action,id)
{
	switch(Action)
	{
		case "Add":
			$("#StockroomManualGroupBugs").html("<hr>");
			$("#StockroomManualGroupInpName").val("");
			$("#StockroomManualGroupInpId").val("");
			$("#StockroomManualGroupDialog").dialog("open");
		break;
		case "Edit":
			$.post(
				"stockroom/functions.php",
				{"Method":"ManualGroupEditStart","id":id},
				function (data)
				{
					var o=jQuery.parseJSON(data);
					$("#StockroomManualGroupInpName").val(o["Name"]);
					$("#StockroomManualGroupInpId").val(id);
					$("#StockroomManualGroupDialog").dialog("open");
				}
			)
		break;
	}
}
function StockManualGroupAdd()
{
	$.post(
		"stockroom/functions.php",
		{"Method":"ManualGroupAdd","Name":$("#StockroomManualGroupInpName").val()},
		function (data)
		{
			if(data=="ok")
			{
				$("#StockroomManualGroupDialog").dialog("close");
				StockManualGroupSelectt();
			}
			else
			{
				$("#StockroomManualGroupBugs").show();
				$("#StockroomManualGroupBugs").html("<hr>"+data);
			}
		}
	)
}

function StockManualGroupEdit()
{
	$.post(
		"stockroom/functions.php",
		{"Method":"ManualGroupEdit", "id":$("#StockroomManualGroupInpId").val(),"Name":$("#StockroomManualGroupInpName").val()},
		function (data)
		{
			if(data=="ok")
			{
				$("#StockroomManualGroupDialog").dialog("close");
				StockManualGroupSelectt();
			}
			else
			{
				$("#StockroomManualGroupBugs").show();
				$("#StockroomManualGroupBugs").html("<hr>"+data);
			}
		}
	)
}

function StockManualGroupSelectt()
{
	$("#StockManualGroupTable").find("tr").remove();
	$.post(
		"stockroom/functions.php",
		{"Method":"ManualGroupSelect"},
		function (data)
		{
			var o=jQuery.parseJSON(data);
			var i=0;
			while(o[i]!=null)
			{
				$("#StockManualGroupTable").append(
					"<tr id=StockManualGroupTR"+o[i]["id"]+" delete="+o[i]["BlockDelete"]+" onclick='StockManualGroupDialogShow(\"Edit\",\""+o[i]["id"]+"\")'>"+
						"<td>"+o[i]["Name"]+"</td>"+
					"</tr>"
				);
				i++;
			}
		}
	)
}

//-------------------Новое---------------------------------
//Открываем диалог справочника
function StockManualGroupDialogShow()
{
	$.post(
		"stockroom/functions.php",
		{"Method":"ManualSelectGroup"},
		function(data)
		{
			var o=jQuery.parseJSON(data);
			var i=0;
			while(o[i]!=null)
			{
				$("#MainUl").append("<p GroupId="+o[i].id+" onclick='StockManualSelectMainItem(this)' ClickStatus=off>"+o[i].Name+"</p>");
				$("#MainUl").append("<div GroupDivId="+o[i].id+"  ></div>");
				i++;
			};
			$("#StockroomManualsDialogNew").dialog("open");
		}
	);
}
//Диалог справочника - отображение материалов для группы
function StockManualSelectMainItem(el)
	{
		var idGroup=$(el).attr("GroupId");
		if($(el).attr("ClickStatus")=="off")
		{
		$("[GroupDivId="+idGroup+"]").find("p").remove();
		$(el).attr("ClickStatus","on");
		$("[GroupDivId="+idGroup+"]").show();
		//Добавить материал в группу
		$("[GroupDivId="+idGroup+"]").append("<p style='cursor:pointer; -moz-user-select: none; -khtml-user-select: none; -webkit-user-select: none; user-select:none;' GroupParentId="+idGroup+" >Добавить материал</p>");
		
		$.post(
			"stockroom/functions.php",
			{"Method":"ManualSelectMaterial","GroupId":$(el).attr("GroupId")},
			function(data)
			{
				var o=jQuery.parseJSON(data);
				var i=0;
				while(o[i]!=null)
				{
					$("[GroupDivId="+idGroup+"]").append("<p style='cursor:pointer; -moz-user-select: none; -khtml-user-select: none; -webkit-user-select: none; user-select:none;' ondblclick='a("+o[i].id+")' GroupParentId="+idGroup+" MaterialID="+o[i].id+">"+o[i].Name+"</p>");
					i++;
				};
			}
		);
		}
		else
		{
			$("[GroupDivId="+idGroup+"]").find("p").remove();
			$(el).attr("ClickStatus","off");
			$("[GroupDivId="+idGroup+"]").hide();
		};
	}
//Добавление группы в справочника
//--Открытие диалога
function StockManualGroupAddEditStart(id)
{
	$('#StockroomManualsDialogNewGroupID').val(''); 
	$('#StockroomManualsDialogNewGroupName').val(''); 
	$('#StockroomManualsDialogNewGroup').dialog('open');
	$("#StockroomManualsDialogNewGroupBugs").hide();
	$("#StockroomManualsDialogNewGroupBugs").html("");
	if(id!="")
	{
		
	};
}
//--Сохранение резильтатов
function StockManualGroupAddEditSave()
{
	if( $('#StockroomManualsDialogNewGroupID').val()=="" )
	{
		$.post(
			"stockroom/functions.php",
			{"Method":"ManualAddGroup","Name":$("#StockroomManualsDialogNewGroupName").val()},
			function(data)
			{
				if(data!="Ошибка добавления записи")
				{
					$("#MainUl").append("<p GroupId="+data+" onclick='StockManualSelectMainItem(this)' ClickStatus=off>"+$("#StockroomManualsDialogNewGroupName").val()+"</p>");
					$("#MainUl").append("<div GroupDivId="+data+"  ></div>");
					$("#StockroomManualsDialogNewGroup").dialog("close");
				}
				else
				{
					$("#StockroomManualsDialogNewGroupBugs").show();
					$("#StockroomManualsDialogNewGroupBugs").html("<hr>"+data);
				};
			}
		);
	}
	else
	{
		
	};
}