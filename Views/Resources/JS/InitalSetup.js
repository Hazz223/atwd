            function PerformInitalPageSetup() {
                $.getJSON("http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/crimes/6-2013/json", function(data) { // needs to be updated
                    
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

                    $("#regionTotalRaw").html(JSON.stringify(data, null, 4));
                });
            }