deleteSelect = "";


$("#deleteCatagorySelectButton").click(function() {


    deleteSelect = $("#deleteCatagorySelect :selected").val();

    if (deleteSelect === "Country") {

    }

    switch (deleteSelect) {
        case "Region":
            $("#regionDelete").fadeIn("fast");
            break;
        case "Area":
            $("#areaDelete").fadeIn("fast");
            break;
        default:
            alert("failed!");
            break;
    }
});

$(".deleteButton").click(function() {
    var deleteValue = "";
    
    switch (deleteSelect) {
        case "Region":
            deleteValue = $("#deleteRegionSelect :selected").val().replace(/\s/g, "_");
            break;
        case "Area":
            deleteValue = $("#deleteAreaSelect :selected").val().replace(/\s/g, "_");
            break;
        default:
            alert("failed!");
            break;
    }
    
    $.getJSON("http://localhost/awtd/crimes/6-2013/delete/"+deleteValue+"/json", function(data){
        $("#deleteText").html("Deleted");
    });
});