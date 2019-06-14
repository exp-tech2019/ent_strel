Date.prototype.daysInMonth = function() {
        return 32 - new Date(this.getFullYear(), this.getMonth(), 32).getDate();
};

function GlobalSetAutorize(el, TypeResult)
{
	var auth=new Object();
	auth.FIO="";
	auth.Login="";
	$.post(
		"MainAutorize.php",
		{"Method":"GetSession"},
		function (data)
		{
			var o=jQuery.parseJSON(data);
			switch(TypeResult)
			{
				case "FIO": $(el).val(o.FIO); $(el).text(o.FIO); break;
				case "Login": $(el).val(o.Login); $(el).text(o.Login); break;
				case "Type": $(el).val(o.Login); $(el).text(o.Type); break;
			};
		}
	);
}

//Последний день месяца
function getLastDayOfMonth(year, month) {
	
	var MonthArray={"январь":0, "февраль":1, "март":2,"апрель":3,"май":4, "июнь":5, "июль":6 ,"август":7 ,"сентябрь" :8, "октябрь":9, "ноябрь":10, "декабрь":11};
  var date =null;
	if(!$.isNumeric(month)) 
	{
		date =new Date(year, MonthArray[ month.toLowerCase()] + 1, 0);
	}
	else
		date =new Date(parseInt( year),  parseInt(month)-1 + 1, 0);
  return date.getDate();
}

function guid() {
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }
  return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
    s4() + '-' + s4() + s4() + s4();
}

function guidSmall() {
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }
  return s4() + s4();
}