/**
 * Description of NavBar
 * Animates the page to move up and down based on what nav bar item you click
 * Also sets the active class on the navbar, to give correct highlighting
 * @author hlp2-winser
 */


var id = "home";


$(".nav-bar").click(function() {
    $("#" + id).removeClass("active");
    id = $(this).attr("id");

    $("#" + id).addClass("active");
    // http://stackoverflow.com/questions/472930/in-jquery-is-there-way-for-slidedown-method-to-scroll-the-page-down-too
    $('html, body').animate({
        scrollTop: $("#" + id + "Container").offset().top - 50
    }, 500);

});