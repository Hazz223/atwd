/**
 * Description of ErrorDecode
 * This was going to be implemented and display the errors recieved. However I
 * ran out of time to work on this, and as such it never came to be. The start 
 * is here though. 
 * 
 * @author hlp2-winser
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

// Coould never get it parse the xml

//    alert($.parseXML(data)); // returning null
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
        console.log("No error");
    }
}
