
function toggleMenu() {
    $('.nav-bar-elements').toggle();
}

$(window).resize(function() {
    if ($(window).width() > 1285) {
        $('.nav-bar-elements').show();
    } else {
        $('.nav-bar-elements').hide();
    }
});

$(document).ready(function() {
    $('.nav-bar-elements a').on('click', function() {
        if ($(window).width() <= 1285) {
            toggleMenu();
        }
    });
});

$(document).ready(function() {
    $.onchange($(window).width(), function() {
        if ($(window).width() <= 1285) {
            $('.nav-bar-elements').hide();
        }
    });
});