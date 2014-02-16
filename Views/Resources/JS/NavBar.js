
var id = "home";

$(".nav-bar").click(function() {
    $("#" + id).removeClass("active");
    id = $(this).attr("id");

    $("#" + id).addClass("active");
    $('html, body').animate({
        scrollTop: $("#" + id + "Container").offset().top - 50
    }, 500);

});