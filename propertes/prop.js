function PropNalogTableSave()
{
	$("#PropNalogBugs").text("");
	//Сделаем проверку
	var flag=false;
	//Делаем проверку заполненности
	for(var i=0;i<$("#PropNalogTableValues").find("tr").length;i++)
	{
		//Пропускаем пустые строчки
		var flag1=false;
		for(var j=0;j<5;j++)
			if(j!=2)
				if(!flag1) flag1= $("#PropNalogTableValues tr:eq("+i+") td:eq("+j+") input").val()!=""?true:false;
		//Если не пустая строка тогда проверим ее заполненность
		if(flag1)
			for(var j=0;j<5;j++)
				if(j!=2)
					if($("#PropNalogTableValues tr:eq("+i+") td:eq("+j+") input").val()=="")
					{
						flag=true;
						$("#PropNalogTableValues tr:eq("+i+") td:eq("+j+") input").css("background-color","pink");
					}
					else
						$("#PropNalogTableValues tr:eq("+i+") td:eq("+j+") input").css("background-color","white");
	};
	//Теперь сохраним в бд
	if(!flag)
	{
		var MaterialArr=new Array();
		var UnitArr=new Array();
		var CalcTypeArr=new Array();
		var CountArr=new Array();
		var PriceArr=new Array();
		for(var i=0;i<$("#PropNalogTableValues").find("tr").length;i++)
		{
			MaterialArr[i]=$("#PropNalogTableValues tr:eq("+i+") td:eq(0) input").val();
			UnitArr[i]=$("#PropNalogTableValues tr:eq("+i+") td:eq(1) input").val();
			CalcTypeArr[i]=$("#PropNalogTableValues tr:eq("+i+") td:eq(2) select").val();
			CountArr[i]=$("#PropNalogTableValues tr:eq("+i+") td:eq(3) input").val();
			PriceArr[i]=$("#PropNalogTableValues tr:eq("+i+") td:eq(4) input").val();
		};
		$.post(
			"propertes/prop.php",
			{
				"Method":"PropNalogSave",
				"DoorType":$("#PropNalogTypeDoor").val(),
				"MaterialArr[]":MaterialArr,
				"UnitArr[]":UnitArr,
				"CalcTypeArr[]":CalcTypeArr,
				"CountArr[]":CountArr,
				"PriceArr[]":PriceArr
			},
			function(data){
				if(data=="ok") {} 
				else{ 
					$("#PropNalogBugs").text(data);
				};
			}
		);
	};
}

//-------------Зарплата------------------------
var PropPrl={
	Load:function () {
        $("#PropPrlDoorsTable tr").remove();
        $("#PropPrlConstTable tr").remove();
        if($("#PropPrlTypeDoor").val()=="" || ($("#PropPrlStep").val()=="" & $("#PropPrlDolgnost").val()=="")) return false;

        $.post(
            "propertes/Payrolls/Load.php",
			{
                TypeDoor:$("#PropPrlTypeDoor").val(),
                StepName:PropPrlDiv_action=="Step" ? $("#PropPrlStep").val() : $("#PropPrlDolgnost").val(),
                Action:PropPrlDiv_action
			},
			function (o) {
            	if(o.DoorSize.length>0)
            		o.DoorSize.forEach(function(d){
                        $("#PropPrlDoorsTable").append(
                            "<tr>"+
								"<td>с <input value='"+d.HWith+"' TypeField='HeightWith' style='width: 40px'></td>"+
								"<td>по <input value='"+d.HBy+"' TypeField='HeightBy' style='width: 40px'></td>"+
								"<td>с <input value='"+d.WWith+"' TypeField='WidthWith' style='width: 40px'></td>"+
								"<td>по <input value='"+d.WBy+"' TypeField='WidthBy' style='width: 40px'></td>"+
								"<td>"+
									"<select TypeField='StvorkaSelect'>"+
										"<option value='' "+(d.S=="" ? "selected" : "")+"></option>"+
										"<option value='1' "+(d.S=="1" ? "selected" : "")+">Одностворчатая</option>"+
										"<option value='2' "+(d.S=="2" ? "selected" : "")+">Двухстворчатая</option>"+
									"</select>"+
									"с <input value='"+d.SWith+"' "+(d.S==2 ? "" : "disabled")+"  TypeField='StvorkaWith' style='width: 40px'>"+
									"по <input value='"+d.SBy+"' "+(d.S==2 ? "" : "disabled")+" TypeField='StvorkaBy' style='width: 40px'>"+
								"</td>"+
								"<td><input "+(d.Framug==0 ? "" : "checked")+" TypeField='Framuga' type='checkbox'></td>"+
								"<td><input value='"+d.Sum+"' TypeField='Sum' style='width: 50px'></td>"+
								"<td><img onclick='PropPrl.DeletePropSize(this)' src='images/delete.png' width=15></td>"+
                            "</tr>"
                        );
					});
                $("#PropPrlDoorsTable [TypeField=StvorkaSelect]").unbind("change");
                $("#PropPrlDoorsTable [TypeField=StvorkaSelect]").change(function () {
                    var inp=$(this).parent().find("input");
                    switch ($(this).val()){
                        case "2":
                            inp.prop("disabled",false);
                            break;
                        default:
                            inp.val("");
                            inp.prop("disabled",true);
                            break;
                    }
                });

                //Константы
				o.Const.forEach(function(con){
                    $("#PropPrlConstTable").append(
                        "<tr>" +
							"<td><input value='"+con.Note+"' TypeField='Note' style='width: 100%'></td>"+
							"<td><input value='"+con.Sum+"' TypeField='Sum' style='width: 100%'></td>"+
							"<td><img onclick='PropPrl.DeletePropConst(this)' src='images/delete.png' width=15></td>"+
                        "</tr>"
                    );
				});

				//Конструкция двери
                if(o.Construct!=null)
                {
                    //Рамка
                    if(o.Construct.Frame==1)
                    {$("#PropPrlConstruct tr:eq(0) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(0) td:eq(0) input").removeAttr("checked");
                    if(o.Construct.FrameCount==1)
                    {$("#PropPrlConstruct tr:eq(0) td:eq(1) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(0) td:eq(1) input").removeAttr("checked");
                    $("#PropPrlConstruct tr:eq(0) td:eq(3) input").val(o.Construct.FrameSum);
                    //Доводчик
                    if(o.Construct.Dovod==1)
                    {$("#PropPrlConstruct tr:eq(1) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(1) td:eq(0) input").removeAttr("checked");
                    if(o.Construct.DovodPreparation==1)
                    {$("#PropPrlConstruct tr:eq(1) td:eq(1) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(1) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(1) td:eq(3) input").val(o.Construct.DovodSum);
                    //Наличник
                    if(o.Construct.Nalichnik==1)
                    {$("#PropPrlConstruct tr:eq(2) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(2) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(2) td:eq(3) input").val(o.Construct.NalichnikSum);
                    //Окно
                    if(o.Construct.Window==1)
                    {$("#PropPrlConstruct tr:eq(3) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(3) td:eq(0) input").prop("checked",false);
                    if(o.Construct.WindowCount==1)
                    {$("#PropPrlConstruct tr:eq(3) td:eq(1) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(3) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(3) td:eq(2) input").val(o.Construct.WindowMore);
                    $("#PropPrlConstruct tr:eq(3) td:eq(3) input").val(o.Construct.WindowSum);
                    //Фрамуга
                    if(o.Construct.Framuga==1)
                    {$("#PropPrlConstruct tr:eq(4) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(4) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(4) td:eq(3) input").val(o.Construct.FramugaSum);
                    //Навесы
                    if(o.Construct.Petlya==1)
                    {$("#PropPrlConstruct tr:eq(5) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(5) td:eq(0) input").prop("checked",false);
                    if(o.Construct.PetlyaCount==1)
                    {$("#PropPrlConstruct tr:eq(5) td:eq(1) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(5) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(5) td:eq(2) input").val(o.Construct.PetlyaMore);
                    $("#PropPrlConstruct tr:eq(5) td:eq(3) input").val(o.Construct.PetlyaSum);
                    //Навесы рабочая створка
                    if(o.Construct.PetlyaWork==1)
                    {$("#PropPrlConstruct tr:eq(6) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(6) td:eq(0) input").prop("checked",false);
                    if(o.Construct.PetlyaWorkCount==1)
                    {$("#PropPrlConstruct tr:eq(6) td:eq(1) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(6) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(6) td:eq(2) input").val(o.Construct.PetlyaWorkMore);
                    $("#PropPrlConstruct tr:eq(6) td:eq(3) input").val(o.Construct.PetlyaWorkSum);
                    //Навесы вторая створка
                    if(o.Construct.PetlyaStvorka==1)
                    {$("#PropPrlConstruct tr:eq(7) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(7) td:eq(0) input").prop("checked",false);
                    if(o.Construct.PetlyaStvorkaCount==1)
                    {$("#PropPrlConstruct tr:eq(7) td:eq(1) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(7) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(7) td:eq(2) input").val(o.Construct.PetlyaStvorkaMore);
                    $("#PropPrlConstruct tr:eq(7) td:eq(3) input").val(o.Construct.PetlyaStvorkaSum);
                    //Ребра жесткости
                    if(o.Construct.Stiffener==1)
                    {$("#PropPrlConstruct tr:eq(8) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(8) td:eq(0) input").prop("checked",false);
                    if(o.Construct.StiffenerW==1)
                    {$("#PropPrlConstruct tr:eq(8) td:eq(1) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(8) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(8) td:eq(3) input").val(o.Construct.StiffenerSum);
                    //Площадь двери
                    if(o.Construct.M2==1)
                    {$("#PropPrlConstruct tr:eq(9) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(9) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(9) td:eq(3) input").val(o.Construct.M2Sum);
                    //Антипаника
                    if(o.Construct.Antipanik==1)
                    {$("#PropPrlConstruct tr:eq(10) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(10) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(10) td:eq(3) input").val(o.Construct.AntipanikSum);
                    //Отбойник
                    if(o.Construct.Otboynik==1)
                    {$("#PropPrlConstruct tr:eq(11) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(11) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(11) td:eq(3) input").val(o.Construct.OtboynikSum);
                    //Калитка
                    if(o.Construct.Wicket==1)
                    {$("#PropPrlConstruct tr:eq(12) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(12) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(12) td:eq(3) input").val(o.Construct.WicketSum);
                    //Врезка замка
                    if(o.Construct.BoxLock==1)
                    {$("#PropPrlConstruct tr:eq(13) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(13) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(13) td:eq(3) input").val(o.Construct.BoxLockSum);
                    //Ответка
                    if(o.Construct.Otvetka==1)
                    {$("#PropPrlConstruct tr:eq(14) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(14) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(14) td:eq(3) input").val(o.Construct.OtvetkaSum);
                    //Утепление
                    if(o.Construct.Isolation==1)
                    {$("#PropPrlConstruct tr:eq(15) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(15) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(15) td:eq(3) input").val(o.Construct.IsolationSum);
                    //Вент. решетка
                    if(o.Construct.Grid==1)
                    {$("#PropPrlConstruct tr:eq(16) td:eq(0) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(16) td:eq(0) input").removeAttr("checked");
                    if(o.Construct.GridCount==1)
                    {$("#PropPrlConstruct tr:eq(16) td:eq(1) input").prop("checked",true);}
                    else
                        $("#PropPrlConstruct tr:eq(16) td:eq(1) input").removeAttr("checked");
                    $("#PropPrlConstruct tr:eq(16) td:eq(3) input").val(o.Construct.GridSum);
                }
                else//очищаем поля
                {
                    $("#PropPrlConstruct tr:eq(0) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(0) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(0) td:eq(3) input").val("");

                    $("#PropPrlConstruct tr:eq(1) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(1) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(1) td:eq(3) input").val("");

                    $("#PropPrlConstruct tr:eq(2) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(2) td:eq(3) input").val("");

                    $("#PropPrlConstruct tr:eq(3) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(3) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(3) td:eq(2) input").val("");
                    $("#PropPrlConstruct tr:eq(3) td:eq(3) input").val("");

                    $("#PropPrlConstruct tr:eq(4) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(4) td:eq(3) input").val("");
                    //Навесы
                    $("#PropPrlConstruct tr:eq(5) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(5) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(5) td:eq(2) input").val("");
                    $("#PropPrlConstruct tr:eq(5) td:eq(3) input").val("");
                    //Навесы рабочая створка
                    $("#PropPrlConstruct tr:eq(6) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(6) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(6) td:eq(2) input").val("");
                    $("#PropPrlConstruct tr:eq(6) td:eq(3) input").val("");
                    //Навесы 2ая створка
                    $("#PropPrlConstruct tr:eq(7) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(7) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(7) td:eq(2) input").val("");
                    $("#PropPrlConstruct tr:eq(7) td:eq(3) input").val("");
                    //Ребра жесткости
                    $("#PropPrlConstruct tr:eq(8) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(8) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(8) td:eq(3) input").val("");
                    //Площадь двери
                    $("#PropPrlConstruct tr:eq(9) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(9) td:eq(3) input").val("");
                    //Антипаника
                    $("#PropPrlConstruct tr:eq(10) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(10) td:eq(3) input").val("");
                    //Отбойник
                    $("#PropPrlConstruct tr:eq(11) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(11) td:eq(3) input").val("");
                    //Калитка
                    $("#PropPrlConstruct tr:eq(12) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(12) td:eq(3) input").val("");
                    //Врезка замка
                    $("#PropPrlConstruct tr:eq(13) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(13) td:eq(3) input").val("");
                    //Отвветка
                    $("#PropPrlConstruct tr:eq(14) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(14) td:eq(3) input").val("");
                    //Утепление
                    $("#PropPrlConstruct tr:eq(15) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(15) td:eq(3) input").val("");
                    //Вент. решетка
                    $("#PropPrlConstruct tr:eq(16) td:eq(0) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(16) td:eq(1) input").prop("checked",false);
                    $("#PropPrlConstruct tr:eq(16) td:eq(3) input").val("");
                };
            }
		)
    },
	AddPropSize:function(){
		if($("#PropPrlTypeDoor").val()=="" || ($("#PropPrlStep").val()=="" & $("#PropPrlDolgnost").val()=="")) return false;
		$("#PropPrlDoorsTable").append(
			"<tr>"+
				"<td>с <input TypeField='HeightWith' style='width: 40px'></td>"+
            	"<td>по <input TypeField='HeightBy' style='width: 40px'></td>"+
				"<td>с <input TypeField='WidthWith' style='width: 40px'></td>"+
				"<td>по <input TypeField='WidthBy' style='width: 40px'></td>"+
				"<td>"+
					"<select TypeField='StvorkaSelect'><option value=''></option><option value='1'>Одностворчатая</option><option value='2'>Двухстворчатая</option></select>"+
					"с <input  TypeField='StvorkaWith' style='width: 40px'>"+
					"по <input TypeField='StvorkaBy' style='width: 40px'>"+
				"</td>"+
				"<td><input TypeField='Framuga' type='checkbox'></td>"+
				"<td><input TypeField='Sum' style='width: 50px'></td>"+
            	"<td><img onclick='PropPrl.DeletePropSize(this)' src='images/delete.png' width=15></td>"+
			"</tr>"
		);
        $("#PropPrlDoorsTable [TypeField=StvorkaSelect]").unbind("change");
		$("#PropPrlDoorsTable [TypeField=StvorkaSelect]").change(function () {
            var inp=$(this).parent().find("input");
			switch ($(this).val()){
				case "2":
                    inp.prop("disabled",false);
					break;
				default:
					inp.val("");
					inp.prop("disabled",true);
					break;
			}
        });
	},
	DeletePropSize:function(el){
		if(confirm("Удалить?"))
			$(el).parent().parent().remove();
	},
	AddPropStatic:function () {
        if($("#PropPrlTypeDoor").val()=="" || $("#PropPrlStep").val()=="") return false;
        $("#PropPrlConstTable").append(
        	"<tr>" +
				"<td><input TypeField='Note' style='width: 100%'></td>"+
				"<td><input TypeField='Sum' style='width: 100%'></td>"+
            	"<td><img onclick='PropPrl.DeletePropConst(this)' src='images/delete.png' width=15></td>"+
			"</tr>"
		);
    },
	DeletePropConst:function (el) {
        if(confirm("Удалить?"))
            $(el).parent().parent().remove();
    },
	Save:function(){
        if($("#PropPrlTypeDoor").val()=="" || ($("#PropPrlStep").val()=="" & $("#PropPrlDolgnost").val()=="")) return false;

        var flagErr=false;
        var arrDoor=new Array();
        $("#PropPrlDoorsTable tr").each(function () {
        	var tr=$(this);
			var HeightWith=tr.find("input[TypeField=HeightWith]").val();
            var HeightBy=tr.find("input[TypeField=HeightBy]").val();

            var WidthWith=tr.find("input[TypeField=WidthWith]").val();
            var WidthBy=tr.find("input[TypeField=WidthBy]").val();

            var StvorkaSelect=tr.find("select[TypeField=StvorkaSelect]").val();
            var StvorkaWith=tr.find("input[TypeField=StvorkaWith]").val();
            var StvorkaBy=tr.find("input[TypeField=StvorkaBy]").val();

            var Framuga=tr.find("input[TypeField=Framuga]").prop("checked") ? 1 : 0;

            var Sum=tr.find("input[TypeField=Sum]").val();

            // if((HeightWith=="" & HeightBy=="") & (WidthWith=="" & WidthBy=="")) flagErr=true;
            if(Sum=="") flagErr=true;

            arrDoor.push({
                HeightWith:HeightWith,
                HeightBy:HeightBy,

                WidthWith:WidthWith,
                WidthBy:WidthBy,

                StvorkaSelect:StvorkaSelect,
                StvorkaWith:StvorkaWith,
                StvorkaBy:StvorkaBy,

                Framuga:Framuga,

                Sum:Sum
			});
        });
        if(flagErr) return false;

        //Константы
		var arrConst=new Array();
        $("#PropPrlConstTable tr").each(function () {
			var tr=$(this);
			var Note=tr.find("input[TypeField=Note]").val();
			var Sum=tr.find("input[TypeField=Sum]").val();
			if(Sum=="") flagErr=true;
			arrConst.push({Note:Note, Sum:Sum});
        });

		$.post(
			"propertes/Payrolls/Save.php",
			{
				TypeDoor:$("#PropPrlTypeDoor").val(),
                StepName:PropPrlDiv_action=="Step" ? $("#PropPrlStep").val() : $("#PropPrlDolgnost").val(),
                Action:PropPrlDiv_action,


				DoorSize:arrDoor,
				Const:arrConst,

                ConstrFrame:($("#PropPrlConstruct tr:eq(0) td:eq(0) input").is(":checked")?"1":"0"),
                ConstrFrameCount:($("#PropPrlConstruct tr:eq(0) td:eq(1) input").is(":checked")?"1":"0"),
                ConstrFrameSum:($("#PropPrlConstruct tr:eq(0) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(0) td:eq(3) input").val():"NULL"),

                ConstrDovod:$("#PropPrlConstruct tr:eq(1) td:eq(0) input").is(":checked")?"1":"0",
                ConstrDovodPreparation:$("#PropPrlConstruct tr:eq(1) td:eq(1) input").is(":checked")?"1":"0",
                ConstrDovodSum:($("#PropPrlConstruct tr:eq(1) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(1) td:eq(3) input").val():"NULL"),

                ConstrNalichnik:$("#PropPrlConstruct tr:eq(2) td:eq(0) input").is(":checked")?"1":"0",
                ConstrNalichnikSum:($("#PropPrlConstruct tr:eq(2) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(2) td:eq(3) input").val():"NULL"),

                ConstrWindow:$("#PropPrlConstruct tr:eq(3) td:eq(0) input").is(":checked")?"1":"0",
                ConstrWindowCount:$("#PropPrlConstruct tr:eq(3) td:eq(1) input").is(":checked")?"1":"0",
                ConstrWindowMore:$("#PropPrlConstruct tr:eq(3) td:eq(2) input").val()!=""?$("#PropPrlConstruct tr:eq(3) td:eq(2) input").val():"NULL",
                ConstrWindowSum:($("#PropPrlConstruct tr:eq(3) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(3) td:eq(3) input").val():"NULL"),

                ConstrFramuga:$("#PropPrlConstruct tr:eq(4) td:eq(0) input").is(":checked")?"1":"0",
                ConstrFramugaSum:($("#PropPrlConstruct tr:eq(4) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(4) td:eq(3) input").val():"NULL"),
                //Навесы
                ConstrPetlya:$("#PropPrlConstruct tr:eq(5) td:eq(0) input").is(":checked")?"1":"0",
                ConstrPetlyaCount:$("#PropPrlConstruct tr:eq(5) td:eq(1) input").is(":checked")?"1":"0",
                ConstrPetlyaMore:$("#PropPrlConstruct tr:eq(5) td:eq(2) input").val()!=""?$("#PropPrlConstruct tr:eq(5) td:eq(2) input").val():"NULL",
                ConstrPetlyaSum:($("#PropPrlConstruct tr:eq(5) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(5) td:eq(3) input").val():"NULL"),
                //Навесы на рабочей створке
                ConstrWorkPetlya:$("#PropPrlConstruct tr:eq(6) td:eq(0) input").is(":checked")?"1":"0",
                ConstrWorkPetlyaCount:$("#PropPrlConstruct tr:eq(6) td:eq(1) input").is(":checked")?"1":"0",
                ConstrWorkPetlyaMore:$("#PropPrlConstruct tr:eq(6) td:eq(2) input").val()!=""?$("#PropPrlConstruct tr:eq(6) td:eq(2) input").val():"NULL",
                ConstrWorkPetlyaSum:($("#PropPrlConstruct tr:eq(6) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(6) td:eq(3) input").val():"NULL"),
                //Навесы на второй створке
                ConstrStvorkaPetlya:$("#PropPrlConstruct tr:eq(7) td:eq(0) input").is(":checked")?"1":"0",
                ConstrStvorkaPetlyaCount:$("#PropPrlConstruct tr:eq(7) td:eq(1) input").is(":checked")?"1":"0",
                ConstrStvorkaPetlyaMore:$("#PropPrlConstruct tr:eq(7) td:eq(2) input").val()!=""?$("#PropPrlConstruct tr:eq(7) td:eq(2) input").val():"NULL",
                ConstrStvorkaPetlyaSum:($("#PropPrlConstruct tr:eq(7) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(7) td:eq(3) input").val():"NULL"),
                //Ребра жесткости
                ConstrStiffener:$("#PropPrlConstruct tr:eq(8) td:eq(0) input").is(":checked")?"1":"0",
                ConstrStiffenerW:$("#PropPrlConstruct tr:eq(8) td:eq(1) input").is(":checked")?"1":"0",
                ConstrStiffenerSum:($("#PropPrlConstruct tr:eq(8) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(8) td:eq(3) input").val():"NULL"),
                //Площадь двери
                ConstructM2:$("#PropPrlConstruct tr:eq(9) td:eq(0) input").is(":checked")?"1":"0",
                ConstructM2Sum:($("#PropPrlConstruct tr:eq(9) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(9) td:eq(3) input").val():"NULL"),
                //Антипаника
                Antipanik:$("#PropPrlConstruct tr:eq(10) td:eq(0) input").is(":checked")?"1":"0",
                AntipanikSum:($("#PropPrlConstruct tr:eq(10) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(10) td:eq(3) input").val():"NULL"),
                //Отбойник
                Otboynik:$("#PropPrlConstruct tr:eq(11) td:eq(0) input").is(":checked")?"1":"0",
                OtboynikSum:($("#PropPrlConstruct tr:eq(11) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(11) td:eq(3) input").val():"NULL"),
                //Калитка
                Wicket:$("#PropPrlConstruct tr:eq(12) td:eq(0) input").is(":checked")?"1":"0",
                WicketSum:($("#PropPrlConstruct tr:eq(12) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(12) td:eq(3) input").val():"NULL"),
                //Врезка замка
                BoxLock:$("#PropPrlConstruct tr:eq(13) td:eq(0) input").is(":checked")?"1":"0",
                BoxLockSum:($("#PropPrlConstruct tr:eq(13) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(13) td:eq(3) input").val():"NULL"),
                //Отвветка
                Otvetka:$("#PropPrlConstruct tr:eq(14) td:eq(0) input").is(":checked")?"1":"0",
                OtvetkaSum:($("#PropPrlConstruct tr:eq(14) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(14) td:eq(3) input").val():"NULL"),
                //Утепление
                Isolation:$("#PropPrlConstruct tr:eq(15) td:eq(0) input").is(":checked")?"1":"0",
                IsolationSum:($("#PropPrlConstruct tr:eq(15) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(15) td:eq(3) input").val():"NULL"),
                //Вент решетка
                Grid:($("#PropPrlConstruct tr:eq(16) td:eq(0) input").is(":checked")?"1":"0"),
                GridCount:($("#PropPrlConstruct tr:eq(16) td:eq(1) input").is(":checked")?"1":"0"),
                GridSum:($("#PropPrlConstruct tr:eq(16) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(16) td:eq(3) input").val():"NULL")
			}
		)
	},
    LoadXML:function(){
        var formData = new FormData();

        formData.append("username", "Groucho");
        formData.append("accountnum", 123456); // number 123456 is immediately converted to a string "123456"

// HTML file input, chosen by user
        formData.append("userfile", document.getElementById("PropPrlLoadXML_btn").files[0]);

// JavaScript file-like object
        var content = '<a id="a"><b id="b">hey!</b></a>'; // the body of the new file...
        var blob = new Blob([content], { type: "text/xml"});

        formData.append("webmasterfile", blob);

        var request = new XMLHttpRequest();
        request.open("POST", "Propertes/Payrolls/LoadXML.php");
        request.send(formData);
    },
    CopyProp:function () {
        switch ($("#PropPrlTypeDoor").val()!="" & $("#PropPrlCopy_TypeDoor").val()!="" & $("#PropPrlTypeDoor").val()!=$("#PropPrlCopy_TypeDoor").val()){
            case true: case 1:
                console.log("fdf");
                $.post(
                    "propertes/Payrolls/CopyProp.php",
                    {
                        OldDoorType:$("#PropPrlTypeDoor").val(),
                        NewDoorType:$("#PropPrlCopy_TypeDoor").val(),
                        Step:$("#PropPrlStep").val(),
                        StepCh:$("#PropPrlCopy_StepCh").prop("checked") ? 1 : 0
                    },
                    function (o) {
                        $("#PropPrlCopy_span").hide();
                        $("#PropPrlCopy_span").show();
                    }
                );
                break;
            case false: case 0:
                alert("Не удалость скопировать");
                break;
        }
    }
};

var PropPrlDolgnost={
    Save:function () {
        var arrDolgnost=new Array();
        $("#PropPrlDolgnost_table tr").each(function () {
            arrDolgnost.push({
                idDolgnost:$(this).attr("idDolgnost"),
                Action:$(this).find("select").val()
            })
        });
        $.post(
            "propertes/Payrolls/SaveDolgnost.php",
            {
                arrDolgnost:arrDolgnost
            },
            function (o) {
                if(o!="") alert("Ошибка сохранения");
            }
        )
    }
}

function PropPrlBtnChange(el)
{
	switch($(el).text()){
		case "n/a":$(el).text("<"); $(el).next().removeAttr("disabled"); break;
		case "<":$(el).text(">"); break;
		case ">":$(el).text("="); break;
		case "=":$(el).text("n/a"); $(el).next().val(""); $(el).next().attr("disabled","disabled"); break;
	};
	PropPrlStatusSave=false;
	$("#PropPrlBtnSave").show();
}

function PropPrlAddRow(el)
{
	if($(el).val()!="" & $(el).parent().parent().next().length==0)
	{
		switch($(el).parent().parent().parent().attr("id"))
		{
			case "PropPrlDoorsTable":
				var Opens="<option></option>"; for(var i in OrderGlobalOpenDoor) Opens=Opens+"<option>"+OrderGlobalOpenDoor[i]+"</option>";
				$("#PropPrlDoorsTable").append(
					"<tr>"+
						"<td><button onclick='PropPrlBtnChange(this)'>n/a</button><input style='width:50px' onkeyup='PropPrlAddRow(this)' disabled='disabled' ></td>"+
						"<td><button onclick='PropPrlBtnChange(this)'>n/a</button><input style='width:50px' onkeyup='PropPrlAddRow(this)' disabled='disabled' ></td>"+
						"<td>"+
							"<select><option></option><option>Одностворчатая</option><option>Двухстворчатая</option></select>"+
							"<button onclick='PropPrlBtnChange(this)'>n/a</button><input style='width:50px' onkeyup='PropPrlAddRow(this)' disabled='disabled'>"+
						"</td>"+
						"<td><select>"+Opens+"</select></td>"+
						"<td><input type=checkbox></td>"+
						"<td><input onkeyup='PropPrlAddRow(this)' style='width:50px'></td>"+
						"<td><img onclick='PropPrlDelRow(this)' src='images/delete.png' width=15></td>"+
					"</tr>"
				);
			break;
			case "PropPrlConstTable":
				$("#PropPrlConstTable").append(
					"<tr>"+
						"<td><input style='width:90%; float:left;' onkeyup='PropPrlAddRow(this)'></td>"+
						"<td><input style='width:50px' onkeyup='PropPrlAddRow(this)'></td>"+
						"<td><img onclick='PropPrlDelRow(this)' src='images/delete.png' width=15></td>"+
					"</tr>"
				);
			break;
		};
		PropPrlStatusSave=false;
	};
	$("#PropPrlBtnSave").show();
}
function PropPrlDelRow(el)
{
	//Делаем проверку на кол-во оставшихся строк
	if($(el).parent().parent().parent().find("tr").length>1)
	{
		$(el).parent().parent().remove();
	}
	else //В случае если в таблице эта последняя строка то ее не удаляем а очищаем содержимое
	{
		var elTR=$(el).parent().parent();
		elTR.find("td:eq(0) button").text("n/a");
		elTR.find("td:eq(0) input").val("");
		elTR.find("td:eq(0) input").attr("disabled","disabled");
		elTR.find("td:eq(1) button").text("n/a");
		elTR.find("td:eq(1) input").val("");
		elTR.find("td:eq(1) input").attr("disabled","disabled");
		elTR.find("td:eq(2) select").val("");
		elTR.find("td:eq(3) select").val("");
		elTR.find("td:eq(4) input").removeAttr("checked");
		elTR.find("td:eq(5) input").val("");
	};
	PropPrlStatusSave=false;
	$("#PropPrlBtnSave").show();
}
function PropPrlSave(){
	$("#PropPrlDebug").text("");
	//Проверим приделы (0,max)
	var ZeroComplite=false; var MaxComplite=false;
	for (var i = 0; i <$("#PropPrlDoorsTable tr").length; i++)
		if($("#PropPrlDoorsTable tr:eq("+i+") td:eq(5) input").val()!="")
		{
			var TempHSign=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(0) button").text();
			var TempHVal=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(0) input").val();
			var TempWSign=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(1) button").text();
			var TempWVal=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(1) input").val();
			var TempS=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(2) input").is(":checked");
			var TempSSign=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(1) button").text();
			var TempWVal=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(1) input").val();
			var TempOpen=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(3) select").val();
			var TempFramug=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(4) input").is(":checked");
			if(TempHSign=="<" & 0<TempHVal & TempWSign=="n/a" & !TempS & TempOpen=="" & !TempFramug) ZeroComplite=true;
			if(TempHSign=="<" & 0<TempHVal & TempWSign=="<" & 0<TempWVal & !TempS & TempOpen=="" & !TempFramug) ZeroComplite=true;
			if(TempHSign==">" & TempHVal<100000 & TempWSign=="n/a" & !TempS & TempOpen=="" & !TempFramug) MaxComplite=true;
			if(TempHSign==">" & TempHVal<100000 & TempWSign==">" & TempWVal<100000 & !TempS & TempOpen=="" & !TempFramug) MaxComplite=true;
			if(TempHSign=="n/a" & TempWSign=="n/a" & TempWVal<100000 & !TempS & TempOpen=="" & !TempFramug) {ZeroComplite=true; MaxComplite=true;};
		};
	$("#PropPrlDebug").text("");
	//if(!ZeroComplite || !MaxComplite) $("#PropPrlDebug").text("Не правильно указан диапазон");
	
	//Проверим заполнение сумм в таблице конструкция двери
	var ConstructTableNull=true;
	for(var i=0; i<$("#PropPrlConstruct tr").length;i++)
	{
		$("#PropPrlConstruct tr:eq("+i+") td:eq(3) input").css("background-color","white");
		if($("#PropPrlConstruct tr:eq("+i+") td:eq(0) input").is(":checked") & $("#PropPrlConstruct tr:eq("+i+") td:eq(3) input").val()=="")
		{
			$("#PropPrlConstruct tr:eq("+i+") td:eq(3) input").css("background-color","pink");
			ConstructTableNull=false;
		};
	};

	//Сохранение
	if(ConstructTableNull)
	{
		var HSign=new Array();
		var HVal=new Array();
		var WSign=new Array();
		var WVal=new Array();
		var S=new Array();
		var SSign=new Array();
		var SVal=new Array();
		var Open=new Array();
		var Framug=new Array();
		var Sum=new Array();
		for (var i = 0; i <$("#PropPrlDoorsTable tr").length; i++) 
			if($("#PropPrlDoorsTable tr:eq("+i+") td:eq(5) input").val()!="")
			{
				HSign[i]=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(0) button").text();
				HVal[i]=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(0) input").val();
				WSign[i]=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(1) button").text();
				WVal[i]=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(1) input").val();
				switch($("#PropPrlDoorsTable tr:eq("+i+") td:eq(2) select").val())
				{
					case "Одностворчатая": S[i]=1; break;
					case "Двухстворчатая": S[i]=2; break;
					default: S[i]=0; break;
				};
				SSign[i]=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(2) button").text();
				SVal[i]=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(2) input").val();
				Open[i]=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(3) select").val();
				Framug[i]=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(4) input").is(":checked")?1:0;
				Sum[i]=$("#PropPrlDoorsTable tr:eq("+i+") td:eq(5) input").val();
			};
		//Запись таблицы постоянных значений
		var ConstName=new Array();
		var ConstSum=new Array();
		for (var i = 0; i <$("#PropPrlConstTable tr").length; i++) 
			if($("#PropPrlConstTable tr:eq("+i+") td:eq(1) input").val()!="")
			{
				ConstName[i]=$("#PropPrlConstTable tr:eq("+i+") td:eq(0) input").val();
				ConstSum[i]=$("#PropPrlConstTable tr:eq("+i+") td:eq(1) input").val();
			};
		console.log($("#PropPrlConstruct tr:eq(6) td:eq(0) input").is(":checked"));
		$.post(
			"propertes/prop.php",
			{
				"Method":"PropPrlSave",
				"DoorType":$("#PropPrlTypeDoor").val(),
				"Step":$("#PropPrlStep").val(),
				"HSign[]":HSign,
				"HVal[]":HVal,
				"WSign[]":WSign,
				"WVal[]":WVal,
				"S[]":S,
				"SSign[]":SSign,
				"SVal[]":SVal,
				"Open[]":Open,
				"Framug[]":Framug,
				"Sum[]":Sum,
				"ConstName[]":ConstName,
				"ConstSum[]":ConstSum,
				//Сохранение таблицы Конструкция двери
				"ConstrFrame":($("#PropPrlConstruct tr:eq(0) td:eq(0) input").is(":checked")?"1":"0"),
				"ConstrFrameCount":($("#PropPrlConstruct tr:eq(0) td:eq(1) input").is(":checked")?"1":"0"),
				"ConstrFrameSum":($("#PropPrlConstruct tr:eq(0) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(0) td:eq(3) input").val():"NULL"),

				"ConstrDovod":$("#PropPrlConstruct tr:eq(1) td:eq(0) input").is(":checked")?"1":"0",
				"ConstrDovodPreparation":$("#PropPrlConstruct tr:eq(1) td:eq(1) input").is(":checked")?"1":"0",
				"ConstrDovodSum":($("#PropPrlConstruct tr:eq(1) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(1) td:eq(3) input").val():"NULL"),

				"ConstrNalichnik":$("#PropPrlConstruct tr:eq(2) td:eq(0) input").is(":checked")?"1":"0",
				"ConstrNalichnikSum":($("#PropPrlConstruct tr:eq(2) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(2) td:eq(3) input").val():"NULL"),

				"ConstrWindow":$("#PropPrlConstruct tr:eq(3) td:eq(0) input").is(":checked")?"1":"0",
				"ConstrWindowCount":$("#PropPrlConstruct tr:eq(3) td:eq(1) input").is(":checked")?"1":"0",
				"ConstrWindowMore":$("#PropPrlConstruct tr:eq(3) td:eq(2) input").val()!=""?$("#PropPrlConstruct tr:eq(3) td:eq(2) input").val():"NULL",
				"ConstrWindowSum":($("#PropPrlConstruct tr:eq(3) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(3) td:eq(3) input").val():"NULL"),

				"ConstrFramuga":$("#PropPrlConstruct tr:eq(4) td:eq(0) input").is(":checked")?"1":"0",
				"ConstrFramugaSum":($("#PropPrlConstruct tr:eq(4) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(4) td:eq(3) input").val():"NULL"),
				//Навесы
				"ConstrPetlya":$("#PropPrlConstruct tr:eq(5) td:eq(0) input").is(":checked")?"1":"0",
				"ConstrPetlyaCount":$("#PropPrlConstruct tr:eq(5) td:eq(1) input").is(":checked")?"1":"0",
				"ConstrPetlyaMore":$("#PropPrlConstruct tr:eq(5) td:eq(2) input").val()!=""?$("#PropPrlConstruct tr:eq(5) td:eq(2) input").val():"NULL",
				"ConstrPetlyaSum":($("#PropPrlConstruct tr:eq(5) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(5) td:eq(3) input").val():"NULL"),
				//Навесы на рабочей створке
				"ConstrWorkPetlya":$("#PropPrlConstruct tr:eq(6) td:eq(0) input").is(":checked")?"1":"0",
				"ConstrWorkPetlyaCount":$("#PropPrlConstruct tr:eq(6) td:eq(1) input").is(":checked")?"1":"0",
				"ConstrWorkPetlyaMore":$("#PropPrlConstruct tr:eq(6) td:eq(2) input").val()!=""?$("#PropPrlConstruct tr:eq(6) td:eq(2) input").val():"NULL",
				"ConstrWorkPetlyaSum":($("#PropPrlConstruct tr:eq(6) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(6) td:eq(3) input").val():"NULL"),
				//Навесы на второй створке
				"ConstrStvorkaPetlya":$("#PropPrlConstruct tr:eq(7) td:eq(0) input").is(":checked")?"1":"0",
				"ConstrStvorkaPetlyaCount":$("#PropPrlConstruct tr:eq(7) td:eq(1) input").is(":checked")?"1":"0",
				"ConstrStvorkaPetlyaMore":$("#PropPrlConstruct tr:eq(7) td:eq(2) input").val()!=""?$("#PropPrlConstruct tr:eq(7) td:eq(2) input").val():"NULL",
				"ConstrStvorkaPetlyaSum":($("#PropPrlConstruct tr:eq(7) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(7) td:eq(3) input").val():"NULL"),
				//Ребра жесткости
				"ConstrStiffener":$("#PropPrlConstruct tr:eq(8) td:eq(0) input").is(":checked")?"1":"0",
				"ConstrStiffenerW":$("#PropPrlConstruct tr:eq(8) td:eq(1) input").is(":checked")?"1":"0",
				"ConstrStiffenerSum":($("#PropPrlConstruct tr:eq(8) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(8) td:eq(3) input").val():"NULL"),
				//Площадь двери
				"ConstructM2":$("#PropPrlConstruct tr:eq(9) td:eq(0) input").is(":checked")?"1":"0",
				"ConstructM2Sum":($("#PropPrlConstruct tr:eq(9) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(9) td:eq(3) input").val():"NULL"),
				//Антипаника
				"Antipanik":$("#PropPrlConstruct tr:eq(10) td:eq(0) input").is(":checked")?"1":"0",
				"AntipanikSum":($("#PropPrlConstruct tr:eq(10) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(10) td:eq(3) input").val():"NULL"),
				//Отбойник
				"Otboynik":$("#PropPrlConstruct tr:eq(11) td:eq(0) input").is(":checked")?"1":"0",
				"OtboynikSum":($("#PropPrlConstruct tr:eq(11) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(11) td:eq(3) input").val():"NULL"),
				//Калитка
				"Wicket":$("#PropPrlConstruct tr:eq(12) td:eq(0) input").is(":checked")?"1":"0",
				"WicketSum":($("#PropPrlConstruct tr:eq(12) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(12) td:eq(3) input").val():"NULL"),
				//Врезка замка
				"BoxLock":$("#PropPrlConstruct tr:eq(13) td:eq(0) input").is(":checked")?"1":"0",
				"BoxLockSum":($("#PropPrlConstruct tr:eq(13) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(13) td:eq(3) input").val():"NULL"),
				//Отвветка
				"Otvetka":$("#PropPrlConstruct tr:eq(14) td:eq(0) input").is(":checked")?"1":"0",
				"OtvetkaSum":($("#PropPrlConstruct tr:eq(14) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(14) td:eq(3) input").val():"NULL"),
				//Утепление
				"Isolation":$("#PropPrlConstruct tr:eq(15) td:eq(0) input").is(":checked")?"1":"0",
				"IsolationSum":($("#PropPrlConstruct tr:eq(15) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(15) td:eq(3) input").val():"NULL"),
				//Вент решетка
				"Grid":($("#PropPrlConstruct tr:eq(16) td:eq(0) input").is(":checked")?"1":"0"),
				"GridCount":($("#PropPrlConstruct tr:eq(16) td:eq(1) input").is(":checked")?"1":"0"),
				"GridSum":($("#PropPrlConstruct tr:eq(16) td:eq(3) input").val()!=""?$("#PropPrlConstruct tr:eq(16) td:eq(3) input").val():"NULL")
			},
			function (data){if(data=="") {PropPrlStatusSave=true; $("#PropPrlBtnSave").hide();} else $("#PropPrlDebug").text(data);}
			);
	};
}

//----Задания по зарплате-----
function PropPrlTasksAdd(){
	$("#PropPrlTasksAddDialogCh1").removeAttr("checked");
	$("#PropPrlTasksAddDialogCh1Inp").val("");
	$("#PropPrlTasksAddDialogCh2").removeAttr("checked");
	$("#PropPrlTasksAddDialogCh3").removeAttr("checked");
	$("#PropPrlTasksAddDialogCh4").removeAttr("checked");
	$("#PropPrlTasksAddDialogCh4Inp").val("");
	$("#PropPrlTasksAddDialogNote").val("");
	$("#PropPrlTasksAddDialogTable").find("tr").remove();
	//Подгрузим список должностей
	$.post(
		"propertes/prop.php",
		{"Method":"ManDolgnostSelect"},
		function(data){
			var o=jQuery.parseJSON(data);
			var i=0;

			while(o[i]!=null){
				$("#PropPrlTasksAddDialogTable").append(
					"<tr style='text-align:left; background-color:lightgray' idDolgnost='"+o[i].id+"'>"+
						"<td style='width:22px;' onclick='PropPrlTasksGrpClick(this)'><img src='images/arrow-turn-left.png'></td>"+
						"<td style='width:500px;' onclick='PropPrlTasksGrpClick(this)'>"+o[i].Dolgnost+"</td>"+
						"<td onclick=''><input></td>"+
					"<tr>"
				);
				i++;
			}
		}
	);
	$("#PropPrlTasksAddDialog").dialog("open");
}
//Подгрузка списка сотрудников в группу
function PropPrlTasksGrpClick(el){
	var elTR=$(el).parent();
	var idDolgnost=elTR.attr("idDolgnost");
	if(elTR.find("td:eq(0) img").attr("src")=="images/arrow-turn-left.png")
	{
		$.post(
			"propertes/prop.php",
			{"Method":"PrlTasksWorkersLoad", "idDolgnost":idDolgnost},
			function(data){
				var o =jQuery.parseJSON(data); var i=0;
				while(o[i]!=null){
					elTR.after(
						"<tr style='text-align:left;' DolgnostID='"+idDolgnost+"' idWorker='"+o[i].id+"'>"+
							"<td style='width:22px;'></td>"+
							"<td style='width:500px;' onclick=''>"+o[i].FIO+"</td>"+
							"<td onclick=''><input></td>"+
						"<tr>"
					);
					i++;
				};
				elTR.find("td:eq(0) img").attr("src","images/arrow_skip.png");
			}
		);
	}
	else
	{
		$("#PropPrlTasksAddDialogTable").find("tr[DolgnostID="+idDolgnost+"]").remove();
		elTR.find("td:eq(0) img").attr("src","images/arrow-turn-left.png");
	};
}
function PropPrlTasksSave(){
	//Делаем проверку на заполненность
	var flagErr=false;
	$("#PropPrlTasksAddDialogNote").css("background-color","white");
	if($("#PropPrlTasksAddDialogNote").val()==""){
		$("#PropPrlTasksAddDialogNote").css("background-color","lightpink");
		flagErr=true;
	};
	var Ch1=$("#PropPrlTasksAddDialogCh1").is(":checked");
	var Ch2=$("#PropPrlTasksAddDialogCh2").is(":checked");
	var Ch3=$("#PropPrlTasksAddDialogCh3").is(":checked");
	var Ch4=$("#PropPrlTasksAddDialogCh4").is(":checked");
	if(!Ch1 & !Ch2 & !Ch3 & !Ch4) flagErr=true;

	$("#PropPrlTasksAddDialogCh1Inp").css("background-color","white");
	if(Ch1 & $("#PropPrlTasksAddDialogCh1Inp").val()==""){
		$("#PropPrlTasksAddDialogCh1Inp").css("background-color","lightpink");
		flagErr=true;
	};

	$("#PropPrlTasksAddDialogCh4Inp").css("background-color","white");
	if(Ch4 & $("#PropPrlTasksAddDialogCh4Inp").val()==""){
		$("#PropPrlTasksAddDialogCh4Inp").css("background-color","lightpink");
		flagErr=true;
	};
	//Сформируем массив назначенных выплат
	var idDolgnost=new Array();
	var idWorker=new Array();
	var Cost=new Array();
	var c=0;
	for(var i=0;i<$("#PropPrlTasksAddDialogTable").find("tr").length; i++)
	{
		var elTR=$("#PropPrlTasksAddDialogTable tr:eq("+i+")");
		if(elTR.find("td:eq(2) input").val()!==undefined & elTR.find("td:eq(2) input").val()!="")
		{
			idDolgnost[c]=elTR.attr("idDolgnost")!==undefined ? elTR.attr("idDolgnost") : elTR.attr("DolgnostID");
			idWorker[c]=elTR.attr("idDolgnost")!==undefined ? "NULL" : elTR.attr("idWorker");
			Cost[c]=elTR.find("td:eq(2) input").val();
			c++;
		};
	};

	//Сохраняем
	if(!flagErr)
		$.post(
			"propertes/prop.php",
			{
				"Method":"PrlTasksSave", 
				"idTask":$("#PropPrlTasksAddDialogIdTask").val(),
				"Note":$("#PropPrlTasksAddDialogNote").val(),
				"Ch1":Ch1,
				"Ch1Inp":$("#PropPrlTasksAddDialogCh1Inp").val(),
				"Ch2":Ch2,
				"Ch3":Ch3,
				"Ch4":Ch4,
				"Ch4Inp":$("#PropPrlTasksAddDialogCh4Inp").val(),

				"idDolgnost[]":idDolgnost,
				"idWorker[]":idWorker,
				"Cost[]":Cost
			},
			function(data){
				if(data=="ok")
				{
					$("#PropPrlTasksAddDialog").dialog("close");
				}
				else
					console.log("Error: "+data);
			}
		);
}
function PropPrlTasksSelect(){
	$("#PropPrlTasksTable").find("div").remove();
	$.post(
		"propertes/prop.php",
		{"Method":"PrlTasksSelect"},
		function(data){
			var o=jQuery.parseJSON(data);
			var i=0; var idTask=""; var WorkersList=""; var ZadanieText="";
			while(o[i]!=null){
				if(idTask!=o[i].idTask & idTask!="")
				{
					$("#PropPrlTasksTable").append(
						"<div class='PrlTaskDiv' idTask='"+idTask+"'>"+
							"<div onclick='PropPrlTasksTableDivA(this)'>Задание: "+ZadanieText+"<img src='images/edit.png'><img src='images/delete.png'></div>"+
							"<p>"+WorkersList+"</p>"+
						"</div>"
					);
					WorkersList="";
					idTask=o[i].idTask;
				};
				WorkersList+=(o[i].Dolgnost!=null ? o[i].Dolgnost+" " : "")+(o[i].FIO!=null ? o[i].FIO+" ": "")+"= "+o[i].Cost+";<br>";
				//ZadanieText=(o[i].Ch1==1? "Каждое "+o[i].Ch1Inp+" число; ":"")+(o[i].Ch2==1? "Последний день мес.; ": "")+(o[i].Ch3==1?"Каждый день; ":"")+(o[i].Ch4==1?"Дата "+oi[i].Ch4Inp:"");
				ZadanieText=o[i].Note;

				if(idTask=="") idTask=o[i].idTask;
				i++;
			};
			//Выведем последнюю запись
			if(i!=0)
				$("#PropPrlTasksTable").append(
					"<div class='PrlTaskDiv' idTask='"+idTask+"'>"+
						"<div onclick='$(this).next().slideToggle()'>Задание: "+ZadanieText+"<img src='images/edit.png'><img src='images/delete.png'></div>"+
						"<p>"+WorkersList+"</p>"+
					"</div>"
				);
			$("#PropPrlTasksTable div p").hide();
			$("#PropPrlTasksTable div div img").click(function(){
				PropPrlTasksTableDivB(this);
			})
		}
	)
}
function PropPrlTasksTableDivA(t){
	$(t).next().slideToggle();
}
function PropPrlTasksTableDivB(t){
	var oldF = t.parentElement.onclick;
	switch($(t).attr("src")){
		case "images/edit.png": PropPrlTasksEditStart(t); break;
		case "images/delete.png": PropPrlTasksDelete(t); break;
	};
	t.parentElement.onclick = function () {
		t.parentElement.onclick = oldF;
	};
}
function PropPrlTasksEditStart(el){
	$("#PropPrlTasksAddDialogTable").find("tr").remove();
	var WorkersList=new Array();
	$.post(
		"propertes/prop.php",
		{"Method":"PrlTasksWorkersLoad"},
		function(data){
			var w=jQuery.parseJSON(data); var j=0;
			while(w[j]!=null){
				WorkersList[j]={"id":w[j].id, "idDolgnost":w[j].idDolgnost, "FIO":w[j].FIO};
				j++;
			};
		}
	)
	.done(function(){
		$.post(
			"propertes/prop.php",
			{"Method":"ManDolgnostSelect"},
			function(data){
				var o=jQuery.parseJSON(data);
				var i=0;

				while(o[i]!=null){
					$("#PropPrlTasksAddDialogTable").append(
						"<tr style='text-align:left; background-color:lightgray' idDolgnost='"+o[i].id+"'>"+
							"<td style='width:22px;' onclick='PropPrlTasksGrpClick(this)'><img src='images/arrow-turn-left.png'></td>"+
							"<td style='width:500px;' onclick='PropPrlTasksGrpClick(this)'>"+o[i].Dolgnost+"</td>"+
							"<td onclick=''><input></td>"+
						"<tr>"
					);
					i++;
				}
				o.length=0;
			}
		)
		.done(function(){
				var idTask=$(el).parent().parent().attr("idTask");
				$.post(
					"propertes/prop.php",
					{"Method":"PrlTasksSelect","id":idTask},
					function(data){
						var o=jQuery.parseJSON(data); var i=0;
						$("#PropPrlTasksAddDialogIdTask").val(o[i].idTask);
						$("#PropPrlTasksAddDialogCh1").prop("checked",(o[i].Ch1==1? true : false));
						$("#PropPrlTasksAddDialogCh1Inp").val((o[i].Ch1Inp!=null? o[i].Ch1Inp : ""));
						$("#PropPrlTasksAddDialogCh2").prop("checked",(o[i].Ch2==1? true : false));
						$("#PropPrlTasksAddDialogCh3").prop("checked",(o[i].Ch3==1? true : false));
						$("#PropPrlTasksAddDialogCh4").prop("checked",(o[i].Ch4==1? true : false));
						$("#PropPrlTasksAddDialogCh4Inp").val((o[i].Ch4Inp!=null? o[i].Ch4Inp : ""));
						$("#PropPrlTasksAddDialogNote").val(o[i].Note);
						//

						for(i=0; o[i]!=null;i++){
							if(o[i].idWorker==null){
								$("#PropPrlTasksAddDialogTable tr[idDolgnost='"+o[i].idDolgnost+"'] td:eq(2) input").val(o[i].Cost);
							}
							else
							{
								var idDolgnost=o[i].idDolgnost;
								var idWorker=o[i].idWorker;
								console.log(idWorker);
								if($("#PropPrlTasksAddDialogTable tr[idWorker='"+o[i].idWorker+"']").length==0)
								{
									var ii=0;
									while(WorkersList[ii]!=null)
									{
										if(WorkersList[ii].idDolgnost==o[i].idDolgnost)
											$("#PropPrlTasksAddDialogTable tr[idDolgnost='"+o[i].idDolgnost+"']").after(
												"<tr style='text-align:left;' DolgnostID='"+o[i].idDolgnost+"' idWorker='"+WorkersList[ii].id+"'>"+
													"<td style='width:22px;'></td>"+
													"<td style='width:500px;' onclick=''>"+WorkersList[ii].FIO+"</td>"+
													"<td onclick=''><input></td>"+
												"<tr>"
											);
										ii++;
									};
									$("#PropPrlTasksAddDialogTable tr[idDolgnost='"+idDolgnost+"']").find("td:eq(0) img").attr("src","images/arrow_skip.png");
								};
								$("#PropPrlTasksAddDialogTable tr[idWorker='"+o[i].idWorker+"'] td:eq(2) input").val(o[i].Cost);
							};
						};

						$("#PropPrlTasksAddDialog").dialog("open");
					}
				);
			});
	});
}
function PropPrlTasksDelete(el){
	if(confirm("Удалить задание?"))
		$.post(
			"propertes/prop.php",
			{"Method":"PrlTasksDelete","id":$(el).parent().parent().attr("idTask")},
			function(data){
				if(data=="ok") $(el).parent().parent().remove();
			}
		)
}

//-------------Справочники----------------------------
//----Должности----
function PropManDolgnostSelect(){
	$("#PropManDolgnostTable").find("tr").remove();
	$.post(
		"propertes/prop.php",
		{"Method":"ManDolgnostSelect"},
		function(data){
			var o=jQuery.parseJSON(data);
			var i=0;

			while(o[i]!=null){
				$("#PropManDolgnostTable").append(
					"<tr idDolgnost='"+o[i].id+"'>"+
						"<td onclick='PropManDolgnostEditStart(this)'>"+o[i].Dolgnost+"</td>"+
						"<td onclick='PropManDolgnostEditStart(this)'><img src='images/edit.png'></td>"+
						"<td onclick='PropManDolgnostDelete(this)'>"+(o[i].id>15? "<img src='images/delete.png' width=20>" : "")+"</td>"+
					"<tr>"
				);
				i++;
			}
		}
	);
}
function PropManDolgnostEditStart(el){
	$("#PropManDolgnostDialogID").val($(el).parent().attr("idDolgnost"));
	$("#PropManDolgnostDialogName").val($(el).parent().find("td:eq(0)").text());
	$("#PropManDolgnostDialog").dialog("open");
}
function PropManDolgnostEditSave(){
	if($("#PropManDolgnostDialogName").val()!="")
		$.post(
			"propertes/prop.php",
			{
				"Method":"ManDolgnostEditSave",
				"id":$("#PropManDolgnostDialogID").val(),
				"Name":$("#PropManDolgnostDialogName").val(),
			},
			function(data){
				if(data=="ok")
				{
					$("#PropManDolgnostTable tr[idDolgnost="+$("#PropManDolgnostDialogID").val()+"] td:eq(0)").text($("#PropManDolgnostDialogName").val());
					$("#PropManDolgnostDialog").dialog("close");
				}
				else
					console.log("Error:"+data);
			}
		);
}
function PropManDolgnostDelete(el){
	if(confirm("Удалить должность?"))
		$.post(
			"propertes/prop.php",
			{
				"Method":"ManDolgnostDelete",
				"id":$(el).parent().attr("idDolgnost")
			},
			function(data){
				if(data=="ok")
				{
					$(el).parent().remove();
				}
				else
					alert(data);
			}
		)
}
function PropManDolgnostAdd(){
	if($("PropManDolgnostAddInp").val()!="")
		$.post(
			"propertes/prop.php",
			{
				"Method":"ManDolgnostAdd",
				"Dolgnost":$("#PropManDolgnostAddInp").val()
			},
			function(data){
				$("#PropManDolgnostAddInp").val("");
				PropManDolgnostSelect();
			}
		)
}

//---------- Склад спецификация -----------------------
function PropSpeLoadTypeDoors(){
    $.post(
        "Propertes/PropSpe.php",
        {
            "Action":"LoadManuals"
        },
        function(o){
            o.TypeDoors.forEach(function(item,i,arr){
                $("#PropSpeTypeDoors").append("<option>"+item+"</option>");
            });
            o.Groups.forEach(function(item, i ,arr){
            	$("#PropSpeGroupTable").append(
            		"<tr idGroup='"+item.idGroup+"' onclick='PropSpeAddGroupSelect(this)' style='text-align: left;'>" +
						"<td>"+item.GroupName+"</td>"+
					"</tr>"
				);
			});
            //$("#PropSpeTypeDoors").append("<option>"+o[i]+"</option>");
        }
    )
}
function PropSpeAddGroup(){
	if($("#PropSpeTypeDoors").val()!="") {
        //Для начала скроем уже выбранные группы
        //--Массив выбранных групп
        var arrSelectedGroups = [];
        for (var i = 0; i < $("#PropSpeTable tr").length; i++)
            arrSelectedGroups[i] = $("#PropSpeTable tr:eq(" + i + ")").attr('idGroup');
        //console.log(arrSelectedGroups);
        //Проверка
        for (var i = 0; i < $("#PropSpeGroupTable tr").length; i++)
            if (arrSelectedGroups.indexOf($("#PropSpeGroupTable tr:eq(" + i + ")").attr("idGroup")) == -1) {
                $("#PropSpeGroupTable tr:eq(" + i + ")").show();
            } else
                $("#PropSpeGroupTable tr:eq(" + i + ")").hide();

        $("#PropSpeGroupDialog").dialog("open");
    };
}
function PropSpeAddGroupSelect(el){
    $("#PropSpeGroupTable tr").removeAttr("class");
    $(el).attr("class","Complite");
}
function PropSpeAddGroupSelected(){
	if($("#PropSpeGroupTable tr[class=Complite]").length==1){
		var TR=$("#PropSpeGroupTable tr[class=Complite]");
        PropSpeTable_RowAdd(TR.attr("idGroup"), TR.find("td:eq(0)").text());
        $("#PropSpeGroupDialog").dialog("close");
        $("#PropSpeBtnSave").show();
	}
}

function PropSpeTable_RowAdd(idGroup, GroupName){
    $("#PropSpeTable").append(
        "<tr idGroup='"+idGroup+"' Status='Add' idConstruct=''>" +
			"<td Type='BtnRemove'><img src='images/delete.png' width='15'></td>"+
			"<td Type='GroupName'>"+GroupName+"</td>"+
			"<td Type='TypeCalc'>" +
				"<select>" +
					"<option value='1'>м<sup>2</sup></option>"+
					"<option value='2'>На м. длинны</option>"+
					"<option value='3'>На изделие</option>"+
				"</select>" +
			"</td>"+
			"<td Type='Count'><input></td>"+
			"<td Type='Check'><span Type='Petlya'>" +
				"<input type='checkbox'> зависит от кол-ва петелей<br>"+
			"</span></td>"+
        "</tr>"
    );
    var TRNew=$("#PropSpeTable tr[idGroup="+idGroup+"]");
    TRNew.find("td[Type=Check] span").hide();
    TRNew.find("td[Type=TypeCalc] select").change(function(){
        if($(this).val()==3){
            TRNew.find("td[Type=Check] span").show();
        }else
            TRNew.find("td[Type=Check] span").hide();
    });
    TRNew.find("td[Type=TypeCalc] select").change(function() {
        PropSpeTable_PropChange(TRNew);
    });
    TRNew.find("td[Type=Count] input").change(function() {
        PropSpeTable_PropChange(TRNew);
    });
    TRNew.find("td[Type=Check] span input").change(function() {
        PropSpeTable_PropChange(TRNew);
    });
    TRNew.find("td[Type=BtnRemove] img").click(function(){
    	if(TRNew.attr("Status")=="Add"){
    		TRNew.remove();
		}
		else{
    		TRNew.attr("Status","Remove");
    		TRNew.hide();
            $("#PropSpeBtnSave").show();
		};

	});

    return TRNew;
}
function PropSpeTable_PropChange(TR){
	if(TR.attr("Status")=="Load")
		TR.attr("Status","Edit");
	$("#PropSpeBtnSave").show();
}

var PropSpeID="";
function PropSpeSave(){
	var arrSpe={}; var c=0;
	for(var i=0; i<$("#PropSpeTable tr").length; i++){
		var TR=$("#PropSpeTable tr:eq("+i+")");
		if(TR.attr("Status")!="Load")
		arrSpe[c++]={
			"Status":TR.attr("Status"),
			"idConstruct":TR.attr("idConstruct"),
			"idGroup":TR.attr("idGroup"),
			"TypeCalc":TR.find("td[Type=TypeCalc] select").val(),
			"Count":TR.find("td[Type=Count] input").val()=="" ? 0 : TR.find("td[Type=Count] input").val(),
			"Petlya":TR.find("td[Type=Check] span[Type=Petlya] input").prop("checked") ? 1 : 0
		};
	};
	$.post(
        "Propertes/PropSpe.php",
		{
			"Action":"Save",
			"idSpe":PropSpeID,
            "TypeDoor":$("#PropSpeTypeDoors").val(),
			"arrSpe":arrSpe
		},
		function(o){
			if(o.Result=="Ok") PropSpeLoad();
		}
	)
}

function PropSpeLoad(){
    $("#PropSpeTable tr").remove();
	$.post(
        "Propertes/PropSpe.php",
		{
			"Action":"LoadSpe",
			"TypeDoor":$("#PropSpeTypeDoors").val()
		},
		function(o){
			var i=-1;
            PropSpeID="";
			while(o[++i]!=null){
                PropSpeID=o[i].idTypeDoor;
				var TRNew = PropSpeTable_RowAdd(o[i].idGroup, o[i].GroupName);
				TRNew.find("td[Type=TypeCalc] select").val(o[i].TypeCalc);
                TRNew.find("td[Type=Count] input").val(o[i].Count);
                if(o[i].TypeCalc==3)
                    TRNew.find("td[Type=Check] span").show();
                if(o[i].Petlya==1)
                    TRNew.find("td[Type=Check] span[Type=Petlya] input").prop("checked",true);
                TRNew.attr("idConstruct",o[i].idConstruct);
                TRNew.attr("Status","Load");
			};
            $("#PropSpeBtnSave").hide();
		}
	)
}

//-------- Спецификация ---------------------
var PropSp18ManualGroups={
    GroupList:new Array(),
    Add:function(){
        var s=$("#PropSp18ManualGroupsInpAdd").val();
        if(s!="")
            $.post(
                "propertes/Sp18/GroupAdd.php",
                {
                    GroupName:s
                },
                function(o){
                    if(o!="Error") PropSp18ManualGroups.Select();
                }
            );
    },
    Select:function(){
        //$("#PropSp18ManualGroups div").remove();
        this.GroupList.length=0;
        $.post(
            "propertes/Sp18/GroupSelect.php",
            {

            },
            function(o){
                o.forEach(function(p){
                    PropSp18ManualGroups.GroupList.push({idGroup: p.idGroup, GroupName:p.GroupName});
                    var s="";
                    p.Group1c.forEach(function(g){
                        s+="<li>"+
                            "<span>"+g.GroupName+"</span>"+
                            "<i class='fas fa-btn fa-lg fa-trash'></i>"+
                            "</li>";
                    });
                    $("#PropSp18ManualGroups").append(
                        "<div idGroup="+p.idGroup+" class='PropSp18GroupOne'>"+
                        "<h4>"+
                        "<i Type='BtnUP' onclick='PropSp18ManualGroups.Groups1c.Show(this)' class='fas fa-btn fa-lg fa-angle-up'></i>"+
                        "<span Type='GroupName'>"+p.GroupName+"</span>"+
                        "<i Type='BtnAdd' onclick='PropSp18ManualGroups.Groups1c.Add(this)' class='fas fa-btn fa-lg fa-plus'></i>"+
                        "<i Type='BtnRemove' class='fas fa-btn fa-ls fa-trash'></i>"+
                        "</h4>"+
                        "<div style='display: none;'>"+
                        "<ul>"+s+"</ul>"+
                        "</div>"+
                        "</div>"
                    )
                })
            }
        )
    },
    Groups1c:{
        idGroup:undefined,
        Show:function(el){
            var g1c=$(el).parent().parent().find("div");
            switch (g1c.is(":visible")){
                case true:
                    $(el).attr("class","fas fa-btn fa-lg fa-angle-up");
                    g1c.hide();
                    break;
                case false:
                    $(el).attr("class","fas fa-btn fa-lg fa-angle-down");
                    g1c.show();
                    break;
            }
        },
        Add:function(el){
            this.idGroup=$(el).parent().parent().attr("idGroup");
            $("#PropSp18Dialog1c").dialog("open");
        },
        Select:function(){
            $.post(
                "propertes/Sp18/Group1cSelect.php",
                {},
                function (o) {
                    o.forEach(function(gr){
                        var s="";
                        if(gr.GroupChild.length>0) {
                            s="<ul>";
                            gr.GroupChild.forEach(function (g) {
                                s += "<li>"+
                                    "<span idGroup="+g.idGroup+" onclick='PropSp18ManualGroups.Groups1c.Selected(this)' ondblclick='PropSp18ManualGroups.Groups1c.DBClick(this)'>"+g.GroupName+"</span>"+
                                    "</li>";
                            });
                            s+="</ul>";
                        };
                        $("#PropSp18Group1cList").append(
                            "<li>"+
                            "<span idGroup="+gr.idGroup+" onclick='PropSp18ManualGroups.Groups1c.Selected(this)' ondblclick='PropSp18ManualGroups.Groups1c.DBClick(this)'>"+gr.GroupName+"</span>"+
                            "</li>"
                        );
                        $("#PropSp18Group1cList").append(s);
                    })
                }
            )
        },
        Selected:function(el){
            $("#PropSp18Group1cList span").removeAttr("Select");
            $(el).attr("Select","Select");
        },
        Save:function(el){
            //if(SelectSpan.length==1)
            $.post(
                "propertes/Sp18/Group1cAdd.php",
                {
                    idGroup:PropSp18ManualGroups.Groups1c.idGroup,
                    idGroup1c:$(el).attr("idGroup")
                },
                function(o){
                    PropSp18ManualGroups.Select();
                }
            );
            $("#PropSp18Dialog1c").dialog("close");
        },
        DBClick:function(el){
            this.Selected(el);
            this.Save(el);
        }
    }
}

var PropSp18Construct={
    CalclList:[{v:1, s:"Площадь"}, {v:2, s:"Периметр"}, {v:3, s:"Петля"}, {v:4, s:"Окно"}, {v:5, s:"Изделие"}],
    AddRow:function(){
        if($("#PropSp18ConstructDoorList").val()=="") return false;

        var c="<option value='-1'></option>";
        this.CalclList.forEach(function(o){
            c+="<option value='"+o.v+"'>"+o.s+"</option>";
        });
        var s="<option value='-1'></option>";
        PropSp18ManualGroups.GroupList.forEach(function(g){
            s+="<option value="+g.idGroup+">"+g.GroupName+"</option>";
        });
        $("#PropSp18ConstructTable").append(
            "<tr idCalc='-1' Status='Add'>"+
            "<td Type='Group'><select onchange='PropSp18Construct.EditRow(this)'>"+s+"</select></td>"+
            "<td Type=Calc><select onchange='PropSp18Construct.EditRow(this)'>"+c+"</select></td>"+
            "<td Type='Val'><input oninput='PropSp18Construct.EditRow(this)'></td>"+
            "<td Type='Save'><input type='checkbox' onchange='PropSp18Construct.EditRow(this)'></td>"+
            "</tr>"
        );
    },
    EditRow:function(el){
        var tr=$(el).parent().parent();
        tr.attr("Status", tr.attr("Status")=="Load" ? "Edit" : tr.attr("Status"));
    },
    Save:function(){
        var flagSuccess=true;
        var TypeDoor=$("#PropSp18ConstructDoorList").val();
        var tbody=$("#PropSp18ConstructTable tr");
        if(TypeDoor=="" || tbody.length==0) flagSuccess=false;
        //Проверка заполнености
        tbody.each(function(){
            flagSuccess=$(this).find("td[Type=Group] select").val()==-1 ? false : flagSuccess;
            flagSuccess=$(this).find("td[Type=Calc] select").val()==-1 ? false : flagSuccess;
            flagSuccess=$(this).find("td[Type=Val] input").val()=="" ? false : flagSuccess;
        });
        switch (flagSuccess){
            case false:
                alert("Ошибка: есть не заполненные поля!");
                break;
            case true:
                var arr=[];
                tbody.each(function(){
                    arr.push({
                        Status:$(this).attr("Status"),
                        idCalc:$(this).attr("idCalc"),
                        idGroup:$(this).find("td[Type=Group] select").val(),
                        TypeCalc:$(this).find("td[Type=Calc] select").val(),
                        Count:$(this).find("td[Type=Val] input").val(),
                        Save:$(this).find("td[Type=Save] input").prop("checked") ? 1 : 0
                    });
                });
                $.post(
                    "propertes/Sp18/ConstructSave.php",
                    {
                        TypeDoor:TypeDoor,
                        Rows:arr
                    },
                    function(o){
                        switch(o.Status){
                            case "Success":
                                alert("Расчеты успешно сохранены");
                                break;
                            case "Error":
                                alert("Произошла ошибка"+o.Error);
                                break;
                        }
                    }
                )
                break;
        };
    },
    Select:function(){
        $("#PropSp18ConstructTable tr").remove();
        var TypeDoor=$("#PropSp18ConstructDoorList").val();
        if(TypeDoor!="")
            $.post(
                "propertes/Sp18/ConstructSelect.php",
                {
                    TypeDoor:TypeDoor
                },
                function(Rows){
                    Rows.forEach(function(o){
                        var c="<option value='-1'></option>";
                        PropSp18Construct.CalclList.forEach(function(item){
                            c+="<option value='"+item.v+"' "+(item.v==o.TypeCalc ? "Selected" : "")+">"+item.s+"</option>";
                        });
                        var s="<option value='-1'></option>";
                        PropSp18ManualGroups.GroupList.forEach(function(item){
                            s+="<option value="+item.idGroup+" "+(item.idGroup==o.idGroup ? "Selected" : "")+">"+item.GroupName+"</option>";
                        });
                        $("#PropSp18ConstructTable").append(
                            "<tr idCalc='"+o.idCalc+"' Status='Load'>"+
                            "<td Type='Group'><select onchange='PropSp18Construct.EditRow(this)'>"+s+"</select></td>"+
                            "<td Type=Calc><select onchange='PropSp18Construct.EditRow(this)'>"+c+"</select></td>"+
                            "<td Type='Val'><input oninput='PropSp18Construct.EditRow(this)' value='"+o.Count+"'></td>"+
                            "<td Type='Save'><input type='checkbox' onchange='PropSp18Construct.EditRow(this)' "+(o.Save==1 ? "checked" : "")+"></td>"+
                            "</tr>"
                        );
                    })
                }
            )
    }
}