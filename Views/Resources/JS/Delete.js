deleteSelect = "";


$("#deleteCatagorySelectButton").click(function() {
    deleteSelect = $("#deleteCatagorySelect :selected").val();

    $("#regionDelete").hide();
    $("#areaDelete").hide();

    $("#deleteText").addClass("hidden");
    $("#deleteTableContainer").addClass("hidden");
    $("#deleteRawContainer").addClass("hidden");

    $("#deleteTable tbody").html("<tr><th>Crime Name</th><th>Value</th></tr>"); // really don't like this...
    
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

    $.getJSON("crimes/6-2013/delete/" + deleteValue + "/json", function(data) {
        $("#deleteContainer").slideDown(function() {
            $(this).animate({height: 1000}, 200);
        });

        $("#deleteRaw").html(JSON.stringify(data, null, 4));

        var area = data.response.crimes.area;
        var region = data.response.crimes.region;

        if (typeof area !== 'undefined') { // due to differnt responses based on if you're deleteing an area or a region
            var areaTotal = data.response.crimes.area.total;
            var areadeleted = data.response.crimes.area.deleted;

            $.each(areadeleted, function() {
                $("#deleteTable tr:last").after("<tr><td>" + this.id + "</td><td>" + this.total + "</td></tr>");
            });

            $("#deleteTable tr:last").after("<tr><th>Total</td><th>" + areaTotal + "</td></tr>");
        }

        if (typeof region !== 'undefined') {
            var regionTotal = data.response.crimes.region.total;
            var regionDeleted = data.response.crimes.region.deleted;

            $.each(regionDeleted, function() {
                $("#deleteTable tr:last").after("<tr><td>" + this.id + "</td><td>" + this.total + "</td></tr>");
            });

            $("#deleteTable tr:last").after("<tr><th>Total</td><th>" + regionTotal + "</td></tr>");
        }

        $("#deleteHeader").html(title);
        $("#deleteText").removeClass("hidden");
        $("#deleteTableContainer").removeClass("hidden");
        $("#deleteRawContainer").removeClass("hidden");
        deleteSelect = "";

    });
});