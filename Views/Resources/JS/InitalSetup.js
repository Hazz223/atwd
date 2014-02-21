function PerformInitalPageSetup() {
    console.log("Checking local storage...");

    if (supports_html5_storage()) {
        var storedTime = localStorage.getItem("timeStamp");

        if (storedTime !== null) {
            console.log("Local Storage Found");
            $.getJSON("checkcache/" + storedTime, function(data) {
                console.log("Getting data from local storage...");
                if (data.response.isCurrent) {
                    console.log("Data stored is current. Using data...");
                    var rawResponse = localStorage.getItem("rawData");
                    FromLocalStore(rawResponse);
                }
                else {
                    console.log("Data stored is not up to date...");
                    DoCleanSetup();
                }
            });
        }
        else{
            DoCleanSetup();
        }
    }
    else {
        console.log("local storage not available");
        DoCleanSetup();
    }
}


function DoCleanSetup() {
    console.log("Clean Setup Started");
    $.getJSON("crimes/6-2013/json", function(data) {
        var timeStamp = data.response.timestamp;
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

        var rawData = JSON.stringify(data, null, 4);
        localStorage.setItem("rawData", rawData);
        localStorage.setItem("timeStamp", timeStamp);
        console.log("local storage updated");
        
        VisulisationSetup(crimeData, countryArray, rawData);

    });
}


function FromLocalStore(data) {
    console.log("Data used from local store");
    data = JSON.parse(data);

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

    var rawData = JSON.stringify(data, null, 4);
    VisulisationSetup(crimeData, countryArray, rawData);
}

function VisulisationSetup(crimeData, countryArray, rawData) {

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