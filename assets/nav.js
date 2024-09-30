$(document).ready(function() {
    let nav = $('#sideNav'),
        maxHeight = Math.max($(document).height(), $(window).height());

    nav.css('height', maxHeight);
})
