/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function CheckForError(data) {
    alert("here");
    var json = $.parseJSON(data);
    if (typeof json === "object") {
        alert("Json!");
    }
    else {
        alert("Not json");
    }
}

function DecodeErrorMessage(data) {

//    alert($.parseXML(data)); // returning null
//    $xml = $( xml );
//    var errorCode= xml.find("error[code]");
//    var errorDesc= xml.find("error[desc]");
//    
//    alert(errorCode);
//    alert(errorDesc);

    var parser = new DOMParser();
    var xmlDoc = parser.parseFromString(data, "text/xml");
    console.log(xmlDoc);
    var code = xmlDoc.getElementsByTagName("reponse");
    console.log(code);
    if (xmlDoc.documentElement.nodeName === "parsererror")
    {
        var errStr = xmlDoc.documentElement.childNodes[0].nodeValue;
        var errStr = errStr.replace(/</g, "&lt;");
        console.log(errStr);
    }
    else
    {
        console.log("We cool");
    }
}
