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

    // Ezarpenen modala ireki/itxi
    var modal = document.getElementById("ezarpenakModala");
    var btns = document.querySelectorAll("#irekiEzarpenakModala, #irekiEzarpenakModalaMugikorra");
    var span = document.querySelector(".itxi-modala");

    if (modal) {
        btns.forEach(function(btn) {
            btn.onclick = function(e) {
                e.preventDefault();
                modal.style.display = "block";
            }
        });

        if (span) {
            span.onclick = function() {
                modal.style.display = "none";
            }
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Gordeta badago, ireki modala mezua ikusteko
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('ezarpenak_gordeta') || urlParams.has('ezarpenak_reset')) {
            modal.style.display = "block";
        }
    }
});
