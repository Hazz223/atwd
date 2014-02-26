/**
 * Description of Delete
 * Script for sending a request to the Server to delete a region or area
 *
 * @author hlp2-winser
 */

deleteSelect = "";

// Which catagory to delete - Area or Region
$("#deleteCatagorySelectButton").click(function() {
    deleteSelect = $("#deleteCatagorySelect :selected").val();

    $("#regionDelete").hide();
    $("#areaDelete").hide();

    $("#deleteText").addClass("hidden");
    $("#deleteTableContainer").addClass("hidden");
    $("#deleteRawContainer").addClass("hidden");
    
    // Resetting the table
    $("#deleteTable tbody").html("<tr><th>Crime Name</th><th>Value</th></tr>");
    
    // Displays the appriprate drop down based on what was selected 
    switch (deleteSelect) {
        case "Region":
            $("#regionDelete").fadeIn("fast");
            break;
        case "Area":
            $("#areaDelete").fadeIn("fast");
            break;
        default:
            break;
    }
});

// The delete function - this sends of the request to the server 
$(".deleteButton").click(function() {
    var deleteValue = "";
    var title = "";
    switch (deleteSelect) {
        case "Region":
            title = $("#deleteRegionSelect :selected").val();
            deleteValue = $("#deleteRegionSelect :selected").val().replace(/\s/g, "_");
            break;
        case "Area":
            title = $("#deleteAreaSelect :selected").val();
            deleteValue = $("#deleteAreaSelect :selected").val().replace(/\s/g, "_");
            break;
        default:
            alert("failed!");
            break;
    }
    
    // The json request to the Delete part of the website
    $.getJSON("crimes/6-2013/delete/" + deleteValue + "/json", function(data) {
        // Used for expanding the delete area on the page
        // http://stackoverflow.com/questions/4965004/jquery-animate-height-toggle
        $("#deleteContainer").slideDown(function() {
            $(this).animate({height: 1000}, 200);
        });
        
        //Display raw information
        $("#deleteRaw").html(JSON.stringify(data, null, 4));

        // extra data from json response
        var area = data.response.crimes.area;
        var region = data.response.crimes.region;
        
        // Different responses if you're deleting a region or an area
        // This if statement deals with this.
        if (typeof area !== 'undefined') { 
            var areaTotal = data.response.crimes.area.total;
            var areadeleted = data.response.crimes.area.deleted;
            
            // Put data into a table
            $.each(areadeleted, function() {
                $("#deleteTable tr:last").after("<tr><td>" + this.id + "</td><td>" + this.total + "</td></tr>");
            });

            $("#deleteTable tr:last").after("<tr><th>Total</td><th>" + areaTotal + "</td></tr>");
        }

        if (typeof region !== 'undefined') {
            var regionTotal = data.response.crimes.region.total;
            var regionDeleted = data.response.crimes.region.deleted;
            
            // put data into a table
            $.each(regionDeleted, function() {
                $("#deleteTable tr:last").after("<tr><td>" + this.id + "</td><td>" + this.total + "</td></tr>");
            });

            $("#deleteTable tr:last").after("<tr><th>Total</td><th>" + regionTotal + "</td></tr>");
        }
        // display completion stuff
        $("#deleteHeader").html(title);
        $("#deleteText").removeClass("hidden");
        $("#deleteTableContainer").removeClass("hidden");
        $("#deleteRawContainer").removeClass("hidden");
        deleteSelect = "";

    });
});