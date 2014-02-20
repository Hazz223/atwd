function PerformInitalPageSetup() {

    // Need to use html 5 storage here

    if (supports_html5_storage()) {
        var storedTime = localStorage.getItem("timeStamp");
        $.getJSON("checkcache/" + storedTime, function(data) {
            alert(data.response.isCurrent);
            
            //DoCleanSetup();
            if (data.response.isCurrent && (localStorage.getItem("crimeData") !== null)) {
                VisulisationSetup(localStorage.getItem("crimeData"), localStorage.getItem("countryArray"), localStorage.getItem("rawData"), true);
            }
            else {
                DoCleanSetup();
            }

        });
    }
    else {
        console.log("local storage not available");
    }
}


function DoCleanSetup() {
    $.getJSON("crimes/6-2013/json", function(data) {

        var timeStamp = data.response.timestamp;
        var crimeRegions = data.response.crimes.region;
        var crimeData = data.response.crimes.region;
        var national = data.response.crimes.national;

        var englandObj = new Object();
        englandObj.id = "England";
        englandObj.total = data.response.crimes.england;

        var walesObj = new Object();
        walesObj.id = "Wales";
        walesObj.total = data.response.crimes.wales;

        var countryArray = new Array(englandObj, walesObj);

        $.each(national, function() {
            crimeData.push(this);
        });
        
        localStorage.setItem("crimeData", JSON.stringify(crimeData));
        localStorage.setItem("countryArray", countryArray);
        localStorage.setItem("timeStamp", timeStamp);
        
        var rawData = JSON.stringify(data, null, 4);
        localStorage.setItem("rawData", rawData);
        VisulisationSetup(crimeData, countryArray,rawData, false); // actually displays the information
    });
}

function VisulisationSetup(crimeData, countryArray, rawData, isStored) {
    if(isStored){
        crimeData = JSON.parse(crimeData); // this actually works!
    }
    
    
    console.log(countryArray);
    
    $.each(crimeData, function() {
        $("#totalRegionTable tr:last").after("<tr><td>" + this.id + "</td><td>" + this.total + "</td></tr>");
    });

    CreateRegionCharts(crimeData[0].id.replace(/\s/g, "_"), crimeData[0].id + " Information");

    CreateCharts(
            crimeData,
            document.getElementById("total-chart-pie"),
            document.getElementById("total-chart-bar"),
            "Region Totals",
            "Country Name",
            "Totals Including Fraud");
    CreateCharts(
            countryArray,
            document.getElementById("country-chart-pie"),
            document.getElementById("country-chart-bar"),
            "Countries Crime Totals",
            "Country Name",
            "Totals Including Fraud");

    $("#regionTotalRaw").html(rawData);
}

//http://diveintohtml5.info/storage.html
function supports_html5_storage() {
    try {
        return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
        return false;
    }
}