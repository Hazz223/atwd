<?php
/**
 * Description of HomeView
 * This is the main client view
 * Uses Region objects and crime objects from the controller 
 * to populate drop downs - hence why a refresh is needed after an add and delete
 * 
 * @author hlp2-winser
 */
?>
<!DOCTYPE html>
<html>
    <!-- /**
 * Description of index
 * This is the main client view
 * Uses Region objects and crime objects from the controller 
 * to populate drop downs - hence why a refresh is needed after an add and delete
 * 
 * @author hlp2-winser
 */ -->
    <meta charset="UTF-8">
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
                        <li class="nav-bar" id="documentation"><a href="#Documentation">Documentation</a></li>
                        <li class="nav-bar" id="source"><a href="#Source">Source Code</a></li>
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
                    </div>
                    <div class='row'>
                        <div class='col-mid-12'>
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
                        <div>
                            <p>
                                For the changes to take affect, you need to refresh the page. Would you like to refresh the page?
                                <button class="btn btn-success refresh-button">Refresh</button>
                            </p>
                        </div>
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
                            <p>For the changes to take affect, you need to refresh the page. Would you like to refresh the page?
                                </br>
                                <button class="btn btn-success refresh-button">Refresh</button>
                            </p>
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
    <div class="negative documentation-container standard-height" id="documentationContainer">
        <div class="container">
            <div class="row">
                <div class="col-mid-12">
                    <div class="chart">
                        <h1>Documentation</h1>
                        <?php
                        // Gets the documentation from here. this can also be found at the url specified in marking scheme
                        include "../Views/Static/index.html"
                        ?>
                    </div>
                    <div class="chart ">
                        <h2>References</h2>
                        <div class="refernce-box">
                            <table class="table ">
                                <tr>
                                    <th>Resource</th>
                                    <th>Link</th>
                                </tr>
                                <tr>
                                    <td>Bootstrap - Template</td>
                                    <td><a href="http://getbootstrap.com/examples/starter-template/">http://getbootstrap.com/examples/starter-template/</a></td>
                                </tr>
                                <tr>
                                    <td>Bootstrap - Footer</td>
                                    <td><a href="http://getbootstrap.com/examples/sticky-footer/">http://getbootstrap.com/examples/sticky-footer/</a></td>
                                </tr>
                                <tr>
                                    <td>JQuery Library</td>
                                    <td><a href="http://jquery.com/">http://jquery.com/</a></td>
                                </tr>
                                <tr>
                                    <td>Length of Javascript Object (ie. Associative Array)</td>
                                    <td><a href="http://stackoverflow.com/questions/5223/length-of-javascript-object-ie-associative-array">http://stackoverflow.com/questions/5223/length-of-javascript-object-ie-associative-array</a></td>
                                </tr>
                                <tr>
                                    <td>No7. The Past, Present & Future of Local Storage for Web Applications</td>
                                    <td><a href="http://diveintohtml5.info/storage.html">http://diveintohtml5.info/storage.html</a></td>
                                </tr>
                                <tr>
                                    <td>Git Hub Viewer - James Dibble</td>
                                    <td><a href="https://github.com/james-dibble/AdvancedWebAssignment/blob/master/Public/script/views/documentation/index/doc.js">James dibble's project</a></td>
                                </tr>
                                <tr>
                                    <td>Repo.js - Darcy Clarke</td>
                                    <td><a href="http://darcyclarke.me/dev/repojs/">http://darcyclarke.me/dev/repojs/</a></td>
                                </tr>
                                <tr>
                                    <td>Stack Overflow: Deleting all files from a folder using php</td>
                                    <td><a href="http://stackoverflow.com/questions/4594180/deleting-all-files-from-a-folder-using-php">http://stackoverflow.com</a></td>
                                </tr>
                                <tr>
                                    <td>Stack Overflow: Remove Excess White Space from WIthin a String</td>
                                    <td><a href="http://stackoverflow.com/questions/1703320/remove-excess-whitespace-from-within-a-string">http://stackoverflow.com</a></td>
                                </tr>
                                <tr>
                                    <td>Stack Overflow: JQuery Animate Height Toggle</td>
                                    <td><a href="http://stackoverflow.com/questions/4965004/jquery-animate-height-toggle">http://stackoverflow.com</a></td>
                                </tr>
                                <tr>
                                    <td>Stack Overflow: In JQuery is there a way for slidedown method to scroll the page down too</td>
                                    <td><a href="http://stackoverflow.com/questions/472930/in-jquery-is-there-way-for-slidedown-method-to-scroll-the-page-down-too">http://stackoverflow.com</a></td>
                                </tr>
                                <tr>
                                    <td>Stack Overflow: Jquery refresh reload the page on clicking a button</td>
                                    <td><a href="http://stackoverflow.com/questions/19207781/jquery-refresh-reload-the-page-on-clicking-a-button">http://stackoverflow.com</a></td>
                                </tr>
                                <tr>
                                    <td>.htaccess information</td>
                                    <td><a href="http://httpd.apache.org/docs/current/howto/htaccess.html">http://httpd.apache.org/docs/current/howto/htaccess.html</a></td>
                                </tr>
                                <tr>
                                    <td>James Dibble - Help with XML validation</td>
                                    <td>James dibble helped with creating the correct XML schema and validation of XML against the schema</td>
                                </tr>
                                <tr>
                                    <td>Schema Generator</td>                               
                                    <td><a href="http://www.freeformatter.com/xsd-generator.html">http://www.freeformatter.com/xsd-generator.html</a></td>
                                </tr>
                                <tr>
                                    <td>PHP Manuel</td>
                                    <td><a href="http://www.php.net/manual/en/">http://www.php.net/manual/en/</a></td>
                                </tr>
                                <tr>
                                    <td>Generate XML Namespace with PHP Dom</td>
                                    <td><a href="http://stackoverflow.com/questions/9082032/generate-xml-namespace-with-php-dom">http://stackoverflow.com/</a></td>
                                </tr>
                                <tr>
                                    <td>Stack Overflow: Add table to row in jquery</td>
                                    <td><a href="http://stackoverflow.com/questions/171027/add-table-row-in-jquery">http://stackoverflow.com</a></td>
                                </tr>
                                <tr>
                                    <td>Stack Overflow: JQuery Animate Height Toggle</td>
                                    <td><a href="http://stackoverflow.com/questions/4965004/jquery-animate-height-toggle">http://stackoverflow.com</a></td>
                                </tr>
                                <tr>
                                    <td>Google Charts</td>
                                    <td><a href="https://developers.google.com/chart/">https://developers.google.com/chart/</a></td>
                                </tr>
                                <tr>
                                    <td>Stack Overflow: How can i create download link in html</td>
                                    <td><a href="http://stackoverflow.com/questions/2793751/how-can-i-create-download-link-in-html">http://stackoverflow.com/questions/2793751/how-can-i-create-download-link-in-html</a></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="positive standard-height" id="sourceContainer">
        <div class="container">
            <div class="row">
                <div class="col-mid-12">
                    <div id="github stuff">
                        <h1>Source Code</h1>
                        <div id="repoBrowser" class="source-container"></div>
                        <div>
                            <h2>Download</h2>
                            <!--http://stackoverflow.com/questions/2793751/how-can-i-create-download-link-in-html -->
                            <p>You can download all of the source code here: <a href="crimes/doc/SourceCodeDownload.zip"  download="atwd-project-hlp2-Winser">Download zip</a></p>
                            <p>Or download the git repository here: <a href="https://github.com/Hazz223/atwd">Git Repository</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="footer" class="footer"> <!-- http://getbootstrap.com/examples/sticky-footer/ -->
    <div class="container">
        <div class="footer">
            <p class="text-muted">Created by Harry Winser (hlp2-winser, 10016143)</p>
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
<script type="text/javascript" src="Views/Resources/JS/GitRepoDisplay.js"></script>
<script type="text/javascript" src="Views/Resources/JS/RefreshButton.js"></script>
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
