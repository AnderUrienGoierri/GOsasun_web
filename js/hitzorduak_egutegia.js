/* hitzorduak_egutegia.js */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Nabigazio botoiak (astea, hilabetea, eguna)
    const bistaBotoiak = document.querySelectorAll('.bista-hautatzailea .bista-botoia');
    bistaBotoiak.forEach(botoia => {
        if (botoia.getAttribute('href') === '#') {
            botoia.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Bista aldatuta: ' + this.textContent);
            });
        }
    });

    // 2. Eguneko gelaxken klikak desgaituta hitzorduak sortzea mugatuta dagoelako
    const egunak = document.querySelectorAll('.egun-gelaxka');
    egunak.forEach(eguna => {
        eguna.style.cursor = 'default';
    });
});

/**
 * Hitzordu baten xehetasunak ikusteko orrira birbideratu
 * @param {number} id Hitzorduaren identifikadorea
 */
function viewAppointment(id) {
    if (id) {
        window.location.href = 'hitzordu_fitxa.php?id=' + id;
    }
}

// Modal funtzioak ezabatu dira fitxa orria erabiltzean
function closeModal() {
    const modal = document.getElementById('hitzorduModala');
    if (modal) modal.style.display = 'none';
}
