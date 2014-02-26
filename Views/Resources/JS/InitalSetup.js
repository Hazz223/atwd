/**
 * Description of IntitalSetup
 * This script runs at page startup.
 *
 * @author hlp2-winser
 */

// Checks local storage, and creates the approprate charts and places the data 
// on start up
function PerformInitalPageSetup() {
    console.log("Checking local storage...");
    
    //http://diveintohtml5.info/storage.html
    if (supports_html5_storage()) { // checks to see if the browser supports storage
        var storedTime = localStorage.getItem("timeStamp");

        if (storedTime !== null) { // if no time stamp = first time visit/ clean storage
            console.log("Local Storage Found");

            $.getJSON("checkcache/" + storedTime, function(data) { // request to check if data is current
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
        else {
            DoCleanSetup();
        }
    }
    else {
        console.log("local storage not available");
        DoCleanSetup();
    }
}

// Gets the data from the server, saves it to local storage, then displays the information
function DoCleanSetup() {
    console.log("Clean Setup Started");
    $.getJSON("crimes/6-2013/json", function(data) {
        // extract information from the json response
        var timeStamp = data.response.timestamp;
        var crimeData = data.response.crimes.region;
        var national = data.response.crimes.national;

        // Creating objects and adding them to an array, so they can be used in the same way
        // the json objects are in the charts.js
        var englandObj = new Object();
        englandObj.id = "England";
        englandObj.total = data.response.crimes.england;

        var walesObj = new Object();
        walesObj.id = "Wales";
        walesObj.total = data.response.crimes.wales;

        var countryArray = new Array(englandObj, walesObj);

        // Adds the national stuff to the same data as the regions, so that they
        // can all be displayed together
        $.each(national, function() {
            crimeData.push(this);
        });

        var rawData = JSON.stringify(data, null, 4);

        // Store stuff to local storage
        // raw data for later use
        localStorage.setItem("rawData", rawData);
        localStorage.setItem("timeStamp", timeStamp);
        console.log("local storage updated");

        VisulisationSetup(crimeData, countryArray, rawData);

    });
}

// Gets the raw JSON from the local store
function FromLocalStore(data) {
    console.log("Data used from local store");
    // Have to parse it, as it's a string
    data = JSON.parse(data);

    var crimeData = data.response.crimes.region;
    var national = data.response.crimes.national;

    // Creating objects and adding them to an array, so they can be used in the same way
    // the json objects are in the charts.js
    var englandObj = new Object();
    englandObj.id = "England";
    englandObj.total = data.response.crimes.england;

    var walesObj = new Object();
    walesObj.id = "Wales";
    walesObj.total = data.response.crimes.wales;

    var countryArray = new Array(englandObj, walesObj);

    // Adds the national stuff to the same data as the regions, so that they
    // can all be displayed together
    $.each(national, function() {
        crimeData.push(this);
    });

    var rawData = JSON.stringify(data, null, 4);
    VisulisationSetup(crimeData, countryArray, rawData);
}

// Displays the data on the page
function VisulisationSetup(crimeData, countryArray, rawData) {
    
    // Puts the data into a table
    $.each(crimeData, function() {
        $("#totalRegionTable tr:last").after("<tr><td>" + this.id + "</td><td>" + this.total + "</td></tr>");
    });
    
    // Creation of region charts
    CreateRegionCharts(crimeData[0].id.replace(/\s/g, "_"), crimeData[0].id + " Information");
    // Creation of the Totals pie/bar charts
    CreateCharts(
            crimeData,
            document.getElementById("total-chart-pie"),
            document.getElementById("total-chart-bar"),
            "Region Totals",
            "Country Name",
            "Totals Including Fraud");
            
    // Creation of the Country pie/ bar charts
    CreateCharts(
            countryArray,
            document.getElementById("country-chart-pie"),
            document.getElementById("country-chart-bar"),
            "Countries Crime Totals",
            "Country Name",
            "Totals Including Fraud");
            
    // Puts the raw data on the page
    $("#regionTotalRaw").html(rawData);
}

// Taken from: 
//http://diveintohtml5.info/storage.html
function supports_html5_storage() {
    try {
        return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
        return false;
    }
}