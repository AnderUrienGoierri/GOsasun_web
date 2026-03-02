$(document).ready(function() {
    // Mugikorreko menuaren togglea
    $('.menu-botoia').click(function() {
        $('.nabigazio-estekak').toggleClass('aktiboa');
    });

    // Itxi menua kanpoan klik egitean
    $(document).click(function(event) {
        if (!$(event.target).closest('header').length) {
            $('.nabigazio-estekak').removeClass('aktiboa');
        }
    });
});
