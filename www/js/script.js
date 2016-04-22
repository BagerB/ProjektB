$(document).ready(function() {
    $('img').click(function() {
        $('img').fadeTo(0, 0.5);
        $(this).fadeTo('fast', 1);
    });
});
