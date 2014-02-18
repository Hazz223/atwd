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
                        <li class="nav-bar" id="country"><a href="#Country">Country</a></li>
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
        <div class="postive standard-height" id="homeContainer">
            <div class="container">
                <div class="row">
                    <div class="col-mid-12">
                        <h1>Totals</h1>
                        <div id="total-chart-pie" class="chart"></div>
                        <div id="total-chart-bar" class="chart"></div>
                        <div>
                            <div class="container">
                                <div class="push"></div>
                            </div>
                        </div>
                        <div class="chart-text data-box">
                            <table id="totalRegionTable" class="table">
                                <tr>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        Total
                                    </th>
                                </tr>
                            </table>
                        </div>
                        <div class="chart-text data-box">
                            <h3>Raw Data</h3>
                            <pre id="regionTotalRaw">
                                
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="negative country-container" id="countryContainer">
            <div class="container">
                <div class="row">
                    <div class="col-mid-12">
                        <h1>Country Totals</h1>
                        <div id="country-chart-pie" class="chart"></div>
                        <div id="country-chart-bar" class="chart"></div>
                        <div class="chart-text">                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="positive standard-height "id="searchContainer">
            <div class="container">
                <div class="row">
                    <div class="col-mid-12">
                        <h1>Search</h1>
                        <div id="region-chart-pie" class="chart"></div>
                        <div id="region-chart-bar" class="chart"></div>
                        <div class="chart-text data-box">
                            <select id="region-selector" class="region-selector-names">
                                <option>Please select one...</option>
                                <?php
                                foreach ($_SESSION["regionNames"] as $name) {
                                    echo "<option>" . $name . "</option>";
                                }
                                ?>
                            </select>
                            <button id="choose-region"class="btn btn-primary" type="button">View Totals</button>
                            
                            <table id="searchRegionTable" class="table">
                                <tr>
                                    <th>
                                        Area Names
                                    </th>
                                    <th>
                                        Total
                                    </th>
                                </tr>
                            </table>
                        </div>
                        <div class="chart-text data-box">
                            <h3>Raw Data</h3>
                            <pre id="searchRegionData"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="negative add-container" id="addContainer">
        <div class="container">
            <div class="row">
                <div class="col-mid-12">
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
                                foreach ($_SESSION["crimeNamesAbv"] as $key => $value) {
                                    echo "<option value='" . $key . "'>" . $value . "</option>";
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
                    <div id="comeplted-add-area" class="chart hidden">
                        <h2>Completed</h2>
                        <div id="completedMessage"></div>
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
                    <div>
                        <div class="container">
                            <div class="push"></div>
                        </div>
                    </div>
                    <div id="addAreaTableContainer" class="chart-text data-box hidden">
                        <table id="addAreaTable" class="table">
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
                    <div id="addAreaRawContainer" class="chart-text data-box hidden">
                        <h3>Raw Data</h3>
                        <pre id="addAreaRaw"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="positive delete-container" id="deleteContainer">
        <div class="container">
            <div class="row">
                <div class="col-mid-12">

                    <div class="chart">
                        <h1>Delete Area</h1>
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
                        <div id="areaDelete" class="delete-option">
                            <p>

                                <select id="deleteAreaSelect">
                                    <option>Pick an area</option>
                                    <?php
                                    foreach ($_SESSION["areaNames"] as $name) {
                                        echo "<option>" . $name . "</option>";
                                    }
                                    ?>
                                </select>
                                <button id="deleteAreaSelectButton" class="btn btn-danger deleteButton" type="button">Delete</button>
                            </p>
                        </div>
                    </div>
                    <div class="chart hidden" id="deleteText">
                        <h1 id="deleteHeader" class="inlineHeaders"></h1><h3 class="inlineHeaders"> has now been deleted</h3>
                        <div>
                            </br>
                            <p>Would you like to refresh the page? </br><button id="refreshButton" class="btn btn-success">Refresh</button></p>
                        </div>
                    </div>
                </div>
                <div id="deleteTableContainer" class="chart-text data-box hidden">
                    <table id="deleteTable" class="table">
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
                <div id="deleteRawContainer" class="chart-text data-box hidden">
                    <h3>Raw Data</h3>
                    <pre id="deleteRaw"></pre>
                </div>
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
<script type="text/javascript" src="Views/Resources/JS/Search.js"></script>
<script type="text/javascript" src="Views/Resources/JS/InitalSetup.js"></script>
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
</script>
</body>
</html>