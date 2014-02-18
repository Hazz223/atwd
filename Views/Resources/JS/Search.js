$("#choose-region").click(function() {
    var regionName = $("#region-selector :selected").val();
    var cleanedName = regionName.replace(/\s/g, "_");
    $("#searchRegionTable tbody").html("<tr><th>Area Names</th><th>Total</th></tr>"); // relaly don't like this...

    CreateRegionCharts(
            cleanedName,
            regionName + " Information",
            "Region Name",
            "Total Including Fraud");
});

function CreateRegionCharts(region, title) {
    $.getJSON("http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/crimes/6-2013/" + region + "/json", function(data) {
        
        $("#searchRegionData").html(JSON.stringify(data, null,4));
        
        var crimeData = null;

        if (region === "Action_Fraud" || region === "British_Transport_Police") {
            var testArray = new Array(data.response.crimes.national);
            crimeData = testArray;
        }
        else {
            crimeData = data.response.crimes.region.areas;
        }

        $.each(crimeData, function() {
            $("#searchRegionTable tr:last").after("<tr><td>" + this.id + "</td><td>" + this.total + "</td></tr>");
        });

        CreateCharts(
                crimeData,
                document.getElementById("region-chart-pie"),
                document.getElementById("region-chart-bar"),
                title,
                "Region Name",
                "Total Including Fraud");
    });
}
