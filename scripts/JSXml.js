var xhttp = new XMLHttpRequest();
var GlobalXMLParams;
xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
    //myFunction(xhttp);
	GlobalXMLParams = xhttp.responseXML;
    }
};
xhttp.open("GET", "../params.xml", true);
xhttp.send();


function ParamGetValue(s) {
    return GlobalXMLParams.getElementsByTagName(s)[0].childNodes[0].nodeValue;
}