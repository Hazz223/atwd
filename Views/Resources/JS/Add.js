/**
 * Description of Add
 * Script for sending Post Requests - Adding an area to a region
 *
 * @author hlp2-winser
 */

var firstAdd = true;

// Gathering the information fto be sent to the server
$("#addCrimeValue").click(function() {
    $("#comeplted-add-area").addClass("hidden");
    $("#addAreaTableContainer").addClass("hidden");
    $("#addAreaRawContainer").addClass("hidden");
    
    // Reset of the Table - bit of a hack.
    $("#addAreaTable tbody").html("<tr><th>Crime Name</th><th>Value</th></tr>");
    
    // Gets the region that the new area will be added too.
    newAreaRegion = $("#choose-region-add :selected").val();
    
    // Gets the crime. This is the abrivated version
    var crime = $("#choose-crime-add :selected").val();
    
    // Makes sure that we've selected stuff from the drop downs
    if (newAreaRegion !== "Please select one..." && crime !== "Please select one...") {
        newAreaName = $("#newAreaName").val();

        var crime = $("#choose-crime-add :selected").val();
        // gets the text. Different from the val
        var crimeText = $("#choose-crime-add :selected").text();
        
        // Gets how much the we want to set it to. 
        var crimeVal = $("#newCrimeValue").val();
        newAreaDataArray[crime] = crimeVal;

        // Disables the drop down so users can't change things
        $("#choose-region-add").prop("disabled", "disabled");
        $("#newAreaName").prop("disabled", "disabled");

        // Check that region isn't empty
        if (newAreaRegion !== "") {
            $("#newAreaHeader").html(newAreaName);
            $("#regionHeader").html(" " + newAreaRegion);
            $("#add-area-table").removeClass("hidden");
        }
        
        // Adding this data to the table, so the user can see what they are adding
        //http://stackoverflow.com/questions/171027/add-table-row-in-jquery
        $("#newAreaTable tr:last").after("<tr><td>" + crimeText + "</td><td>" + crimeVal + "</td></tr>");
        if (firstAdd) {
            $("#submitCrime").removeClass("hidden");
        }
    }
    else {
        alert("Error - Incorrect input");
    }
});

// Actually sending the data to the server
$("#submitCrime").click(function() {
    // formating the data to be sent
    var cleanedAreaName = newAreaName.replace(/\s/g, "_");
    var cleanedRegionName = newAreaRegion.replace(/\s/g, "_");
    var completedString = "";
    
    var length = Object.size(newAreaDataArray);
    var i = 1;
    
    // Formats the data ready to be sent to the server
    $.each(newAreaDataArray, function(key, value) {
        if (i >= length) {
            completedString = completedString.concat(key + ":" + value); // the last one
        }
        else {
            completedString = completedString.concat(key + ":" + value + "-");
        }
        i++;
    });
    
    // the actualy request to the server
    $.getJSON("crimes/6-2013/post/" + cleanedRegionName + "/" + cleanedAreaName + "/" + completedString + "/json", function(addData) {
        // Expand the cntainer that this information sits in.
        // http://stackoverflow.com/questions/4965004/jquery-animate-height-toggle
        $("#addContainer").slideDown(function() { 
            $(this).animate({height: 1000}, 200);
        });
        
        // Used to show the raw data
        $("#addAreaRaw").html(JSON.stringify(addData, null, 4));

        // Hides he old information
        $("#add-area-table").addClass("hidden");
        
        // Displays the complete display
        $("#comeplted-add-area").removeClass("hidden");
        
        // Extracts the relavent information from the JSON
        var areaName = addData.response.crimes.region.area.id;
        var regionName = addData.response.crimes.region.id;
        var recorded = addData.response.crimes.region.area.recorded;
        var areaTotal = addData.response.crimes.region.area.total;
        var regionTotal = addData.response.crimes.region.total;
        
        // Added the results to the table to display to the user
        $.each(recorded, function() {
            $("#addAreaTable tr:last").after("<tr><td>" + this.id + "</td><td>" + this.total + "</td></tr>");
        });

        $("#addAreaTable tr:last").after("<tr><th>Area Total</td><td>" + areaTotal + "</td></tr>");
        $("#addAreaTable tr:last").after("<tr><th>Region Total Total</td><td>" + regionTotal + "</td></tr>");

        $("#addAreaTableContainer").removeClass("hidden");
        $("#addAreaRawContainer").removeClass("hidden");

        $("#completedMessage").html("<p>For the Region ["+regionName+"], a new Area [" + areaName + "] as been added. Please refresh the page to view the changes across the entire applcation.</p>");
        
        // Reset of the stuff hidden behind the scenes
        newAreaDataArray = new Object();
        newAreaName = "";
        newAreaRegion = "";
        $("#choose-region-add").prop("disabled", false);
        $("#newAreaName").prop("disabled", false);
        $("#newAreaName").val("");
        $("#newCrimeValue").val("");
        $("#add-area-table").addClass("hidden");

        $("#newAreaTable tbody").html("<tr><th>Crime Name</th><th>Value</th></tr>");
        $("#submitCrime").addClass("hidden");
    }).fail(function(data) { 
        // Error - rubbish, but ran out of time to implement this properly
        alert("An error occured. Failed to add Area to data");
        
        // reseting the stuff behind the scene
        newAreaDataArray = new Object();
        newAreaName = "";
        newAreaRegion = "";
        $("#choose-region-add").prop("disabled", false);
        $("#newAreaName").prop("disabled", false);
        $("#add-area-table").addClass("hidden");
        // need to clean table
        $("#newAreaTable tbody").html("<tr><th>Crime Name</th><th>Value</th></tr>");
        $("#submitCrime").addClass("hidden");
    });
});
