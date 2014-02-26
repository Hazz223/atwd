/**
 * Description of Search
 * Allows you to search for a region or Further Statistic from a drop down
 * 
 * @author hlp2-winser
 */

// Function to select which region/Further Stat to search for.
$("#choose-region").click(function() {
    var regionName = $("#region-selector :selected").val();
    var cleanedName = regionName.replace(/\s/g, "_");
    $("#searchRegionTable tbody").html("<tr><th>Area Names</th><th>Total</th></tr>"); // Cleans the table - bit of a hack

    CreateRegionCharts(
            cleanedName,
            regionName + " Information",
            "Region Name",
            "Total Including Fraud");
});

// Creates the Region charts based on the json received
function CreateRegionCharts(region, title) {
    $.getJSON("crimes/6-2013/" + region + "/json", function(data) {
        
        // Raw data
        $("#searchRegionData").html(JSON.stringify(data, null,4));
        
        var crimeData = null;
        
        // Need to check for Further Stats, else we treat it as normal
        if (region === "Action_Fraud" || region === "British_Transport_Police") {
            var fStatsArray = new Array(data.response.crimes.national);
            crimeData = fStatsArray;
        }
        else {
            crimeData = data.response.crimes.region.areas;
        }
        
        // Loop through adding it to the table
        $.each(crimeData, function() {
            $("#searchRegionTable tr:last").after("<tr><td>" + this.id + "</td><td>" + this.total + "</td></tr>");
        });
        
        // Creates the region serach charts
        CreateCharts(
                crimeData,
                document.getElementById("region-chart-pie"),
                document.getElementById("region-chart-bar"),
                title,
                "Region Name",
                "Total Including Fraud");
    }).fail(function(data){
        alert("Error Occured. Failed to search for data");
    });
}
