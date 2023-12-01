$(document).ready(function() {
    let nav = $('#sideNav'),
        maxHeight = Math.max($(document).height(), $(window).height());

    console.log(maxHeight);

    nav.css('height', maxHeight);
})
