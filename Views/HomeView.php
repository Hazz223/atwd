<?php ?>

<html>
    <head>
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="Views/Resources/CSS/layout.css" rel="stylesheet">
    </head>
    <body>
        <div role="navigation" class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header"> <!-- http://getbootstrap.com/examples/starter-template/ -->
                    <a href="#" class="navbar-brand">Crime Stats</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-bar active" id="home"><a href="#">Home</a></li>
                        <li class="nav-bar" id="search"><a href="#Search">Search</a></li>
                        <li class="nav-bar" id="add"><a href="#Add">Add</a></li>
                        <li class="nav-bar" id="delete"><a href="#Delete">Delete</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div>
            <div class="container">
                <div class="push"></div>
            </div>
        </div>
        <div class="postive" id="homeContainer">
            <div class="container">
                <div class="row">
                    <div class="col-mid-12">
                        <h1>Totals</h1>
                        <div id="total-chart-pie" class="chart"></div>
                        <div id="total-chart-bar" class="chart"></div>
                        <div class="chart-text">Test of the area where i could put text</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="negative" id="countryContainer">
            <div class="container">
                <div class="row">
                    <div class="col-mid-12">
                        <h1>Counties Totals</h1>
                        <div id="country-chart-pie" class="chart"></div>
                        <div id="country-chart-bar" class="chart"></div>
                        <div class="chart-text">Test of the area where i could put text</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="positive" id="searchContainer">
            <div class="container">
                <div class="row">
                    <div class="col-mid-12">
                        <h1>Search</h1>
                        <div id="region-chart-pie" class="chart"></div>
                        <div id="region-chart-bar" class="chart"></div>
                        <div class="chart-text">
                            <select id="region-selector" class="region-selector-names">
                                <option>Please select one...</option>
                            </select>
                            <button id="choose-region"class="btn btn-primary" type="button">View Totals</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="negative" id="addContainer">
            <div class="container">
                <div class="row">
                    <div class="col-mid-6">
                        <div id="add-area-input" class="chart">
                            <h1>Add Area</h1>
                            <div id="newAreaRegion">
                                <p>
                                    Select Region: 
                                    <select id="choose-region-add" class="">
                                        <option>Please select one...</option>
                                        <?php
                                        foreach ($_SESSION["regionNames"] as $name) {
                                            echo "<option>" . $name . "</option>";
                                        }
                                        ?>
                                    </select>
                                </p>
                            </div>

                            <p> 
                                New Area Name:  
                                <input placeholder="New Area Name" id="newAreaName"/>
                            </p>
                            <p> 
                                Select Crime:  
                                <select id="choose-crime-add" class="crime-names">
                                    <option>Please select one...</option>
                                    <?php
                                    foreach ($_SESSION["crimeNames"] as $name) {
                                        echo "<option>" . $name . "</option>";
                                    }
                                    ?>
                                </select>
                            </p>
                            <p> 
                                Value:  
                                <input placeholder="Crime Value" id="newCrimeValue"/>
                            </p>
                            <p> 
                                <button id="addCrimeValue"class="btn btn-primary" type="button">Add Crime</button>
                            <hr/>
                            <button id="submitCrime"class="btn btn-success hidden" type="button">Submit Crime</button>
                            </p>
                        </div>
                    </div>
                    <div class="col-mid-6">
                        <div id="comeplted-add-area" class="chart hidden">
                            <h2>Completed</h2>
                            <div id="completedChart"></div>
                        </div>
                        <div id="add-area-table" class="chart addResults hidden">
                            <h2 id="newAreaHeader" class="inlineHeaders"></h2><h4 id="regionHeader" class="inlineHeaders"></h4>
                            <table id="newAreaTable" class="table">
                                <tr>
                                    <th>
                                        Crime Name
                                    </th>
                                    <th>
                                        Value
                                    </th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="positive" id="deleteContainer">
            <div class="container">
                <div class="row">
                    <div class="col-mid-12">
                        <h1>Delete Area</h1>
                        <div class="chart">
                            <p>
                                <select id="deleteCatagorySelect">
                                    <option>Pick an option...</option>
                                    <option>Region</option>
                                    <option>Area</option>
                                </select>
                                <button id="deleteCatagorySelectButton"class="btn btn-primary" type="button">Select</button>
                            </p>
                            <p>
                            <div id="regionDelete" class="delete-option">
                                <select id="deleteRegionSelect">
                                    <option>Pick a region</option>
                                    <?php
                                    foreach ($_SESSION["regionNames"] as $name) {
                                        echo "<option>" . $name . "</option>";
                                    }
                                    ?>
                                </select>
                                <button id="deleteRegionSelectButton" class="btn btn-danger deleteButton" type="button">Delete</button>
                            </div>
                            </p>
                            <p>
                            <div id="areaDelete" class="delete-option">
                                <select id="deleteAreaSelect">
                                    <option>Pick an area</option>
                                    <?php
                                    foreach ($_SESSION["areaNames"] as $name) {
                                        echo "<option>" . $name . "</option>";
                                    }
                                    ?>
                                </select>
                                <button id="deleteAreaSelectButton" class="btn btn-danger deleteButton" type="button">Delete</button>
                            </div>
                            </p>
                        </div>
                        <div class="chart" id="deleteText"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="footer" class="footer"> <!-- http://getbootstrap.com/examples/sticky-footer/ -->
            <div class="container">
                <div class="footer">
                    <p class="text-muted">Place sticky footer content here.</p>
                </div>
            </div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="Views/Resources/JS/Charts.js"></script>
        <script type="text/javascript" src="Views/Resources/JS/Delete.js"></script>
        <script type="text/javascript" src="Views/Resources/JS/Add.js"></script>
        <script type="text/javascript" src="Views/Resources/JS/ErrorDecode.js"></script>
        <script type="text/javascript" src="Views/Resources/JS/NavBar.js"></script>
        <script type="text/javascript">

            // http://stackoverflow.com/questions/5223/length-of-javascript-object-ie-associative-array
            Object.size = function(obj) {
                var size = 0, key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key))
                        size++;
                }
                return size;
            };

            var newAreaDataArray = new Object();
            var newAreaRegion = null;
            var newAreaName = null;

            $(document).ready(function() {
                PerformInitalPageSetup();
                $("#choose-region-add").prop("disabled", false);
                $("#newAreaName").prop("disabled", false);
            });

            function PerformInitalPageSetup() {
                $.getJSON("http://localhost/awtd/crimes/6-2013/json", function(data) { // needs to be updated
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

                    $.each(crimeRegions, function() {
                        $(".region-selector-names").append($("<option/>", {value: this.id, text: this.id})); // mighte generate this using php instead
                    });

                    CreateRegionCharts(crimeData[0].id.replace(/\s/g, "_"), crimeData[0].id + " Information");
                    CreateCharts(crimeData, document.getElementById("total-chart-pie"), document.getElementById("total-chart-bar"), "Region Totals");
                    CreateCharts(countryArray, document.getElementById("country-chart-pie"), document.getElementById("country-chart-bar"), "Countries Crime Totals");
                });
            }

            $("#choose-region").click(function() {
                var regionName = $("#region-selector :selected").val();
                var cleanedName = regionName.replace(/\s/g, "_");
                CreateRegionCharts(cleanedName, regionName + " Information");
            });

            function CreateRegionCharts(region, title) {
                $.getJSON("http://localhost/awtd/crimes/6-2013/" + region + "/json", function(data) {

                    var crimeData = null;

                    if (region === "Action_Fraud" || region === "British_Transport_Police") {
                        var testArray = new Array(data.response.crimes.national);
                        crimeData = testArray;
                    }
                    else {
                        crimeData = data.response.crimes.region.areas;
                    }
                    CreateCharts(crimeData, document.getElementById("region-chart-pie"), document.getElementById("region-chart-bar"), title);
                });
            }


        </script>
    </body>
</html>
