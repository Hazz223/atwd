var firstAdd = true;
// This needs a bit of a clean up
$("#addCrimeValue").click(function() {
    $("#comeplted-add-area").addClass("hidden");
    $("#addAreaTableContainer").addClass("hidden");
    $("#addAreaRawContainer").addClass("hidden");
    $("#addAreaTable tbody").html("<tr><th>Crime Name</th><th>Value</th></tr>"); // really don't like this...
    
    newAreaRegion = $("#choose-region-add :selected").val();
    
    var crime = $("#choose-crime-add :selected").val();
    if (newAreaRegion !== "Please select one..." && crime !== "Please select one...") {
        newAreaName = $("#newAreaName").val();

        var crime = $("#choose-crime-add :selected").val();
        var crimeText = $("#choose-crime-add :selected").text();
        var crimeVal = $("#newCrimeValue").val();
        newAreaDataArray[crime] = crimeVal;

        $("#choose-region-add").prop("disabled", "disabled");
        $("#newAreaName").prop("disabled", "disabled");

        if (newAreaRegion !== "") {
            $("#newAreaHeader").html(newAreaName);
            $("#regionHeader").html(" " + newAreaRegion);
            $("#add-area-table").removeClass("hidden");
        }

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

$("#submitCrime").click(function() {
    var cleanedAreaName = newAreaName.replace(/\s/g, "_");
    var cleanedRegionName = newAreaRegion.replace(/\s/g, "_");
    var completedString = "";

    var length = Object.size(newAreaDataArray);
    var i = 1;
    $.each(newAreaDataArray, function(key, value) {
        if (i >= length) {
            completedString = completedString.concat(key + ":" + value); // the last one
        }
        else {
            completedString = completedString.concat(key + ":" + value + "-");
        }
        i++;
    });

    $.getJSON("http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/crimes/6-2013/post/" + cleanedRegionName + "/" + cleanedAreaName + "/" + completedString + "/json", function(addData) {
        // http://stackoverflow.com/questions/4965004/jquery-animate-height-toggle
        $("#addContainer").slideDown(function() { 
            $(this).animate({height: 1000}, 200);
        });

        $("#addAreaRaw").html(JSON.stringify(addData, null, 4));

        $("#add-area-table").addClass("hidden");
        $("#comeplted-add-area").removeClass("hidden");
        var areaName = addData.response.crimes.region.area.id;
        var recorded = addData.response.crimes.region.area.recorded;
        var areaTotal = addData.response.crimes.region.area.total;
        var regionTotal = addData.response.crimes.region.total;

        $.each(recorded, function() {
            $("#addAreaTable tr:last").after("<tr><td>" + this.id + "</td><td>" + this.total + "</td></tr>");
        });

        $("#addAreaTable tr:last").after("<tr><th>Area Total</td><td>" + areaTotal + "</td></tr>");
        $("#addAreaTable tr:last").after("<tr><th>Region Total Total</td><td>" + regionTotal + "</td></tr>");

        $("#addAreaTableContainer").removeClass("hidden");
        $("#addAreaRawContainer").removeClass("hidden");

        $("#completedMessage").html("For the Region [], a new Area [" + areaName + "] as been added. Please refresh the page to view the changes across the entire applcation.");

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
        newAreaDataArray = new Object();
        newAreaName = "";
        newAreaRegion = "";
        $("#choose-region-add").prop("disabled", false);
        $("#newAreaName").prop("disabled", false);
        $("#add-area-table").addClass("hidden");
        // need to clean table
        $("#newAreaTable tbody").html("<tr><th>Crime Name</th><th>Value</th></tr>"); // relaly don't like this...
        $("#submitCrime").addClass("hidden");

    });
});
