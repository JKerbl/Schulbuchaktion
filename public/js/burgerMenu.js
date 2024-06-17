/*
document.addEventListener('DOMContentLoaded', function() {
    const burgerMenu = document.querySelector('.burger-menu');
    const navLinks = document.querySelector('.navbar-links'); // Änderung hier

    burgerMenu.addEventListener('click', function() {
        burgerMenu.classList.toggle('burger-menu-active');
        navLinks.classList.toggle('burger-menu-active');
    });
});*/

// Write a method toggleMenu which toggles the visibility of the navbar-links
// you can use jQuery to do this!
// It is not working! Fix it.


// $(document).ready(function() {
//
//
//     $('.burger-menu').click(toggleMenu);
// });


function toggleMenu() {
    // Dazu einfach die Klasse nav-bar-elements toggle!
    $('.nav-bar-elements').toggle();
    // wenn nav-bar-elements nicht display none ist, dann dieser klasse nav-bar-company-name-and-icon margin-top: 1em hinzufügen!:
    // if ($('.nav-bar-elements').css('display') !== 'none') {
    //     $('.nav-bar-company-name-and-icon').css('margin-top', '0.9em');
    // } else {
    //     $('.nav-bar-company-name-and-icon').css('margin-top', '0');
    // }
}

$(window).resize(function() {
    if ($(window).width() > 1150) {
        $('.nav-bar-elements').show();
    } else {
        $('.nav-bar-elements').hide();
    }
});

$(document).ready(function() {
    $('.nav-bar-elements a').on('click', function() {
        if ($(window).width() <= 1150) {
            toggleMenu();
        }
    });
});

$(document).ready(function() {
    $.onchange($(window).width(), function() {
        if ($(window).width() <= 1150) {
            $('.nav-bar-elements').hide();
        }
    });
});