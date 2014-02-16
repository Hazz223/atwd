var firstAdd = true;

$("#addCrimeValue").click(function() {
    newAreaName = $("#newAreaName").val();
    newAreaRegion = $("#choose-region-add :selected").val();
    var crime = $("#choose-crime-add :selected").val();
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
    $("#newAreaTable tr:last").after("<tr><td>" + crime + "</td><td>" + crimeVal + "</td></tr>");

    if(firstAdd){
        $("#submitCrime").removeClass("hidden");
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

    $.getJSON("http://localhost/awtd/crimes/6-2013/post/" + cleanedRegionName + "/" + cleanedAreaName + "/" + completedString + "/json", function(data) {
        $("#comeplted-add-area").removeClass("hidden");
        
        
        newAreaDataArray = new Object();
        newAreaName = "";
        newAreaRegion = "";
        $("#choose-region-add").prop("disabled", false);
        $("#newAreaName").prop("disabled", false);
        $("#newAreaName").val("");
        $("#newCrimeValue").val("");
        $("#add-area-table").addClass("hidden");
        // Need to clean the table
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
        $("#newAreaTable tbody").html("<tr><th>Crime Name</th><th>Value</th></tr>");        
        $("#submitCrime").addClass("hidden");
    });
});
