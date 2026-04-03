/* hitzorduak_egutegia.js */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Redirect to creation page when clicking on a calendar day
    const egunak = document.querySelectorAll('.egun-gelaxka');
    egunak.forEach(eguna => {
        eguna.addEventListener('click', function(e) {
            // Check if we clicked on the background of the cell (not an existing appointment)
            if (e.target.classList.contains('egun-gelaxka') || e.target.classList.contains('egun-zenbakia')) {
                // We need to find the date. We can extract it from the PHP context if we had a data-date attribute.
                // For now, let's look for existing appointments in the same cell to find the date, 
                // or we can rely on hitzorduak.php to have added a data-date attribute (which I should have added).
                const dataDate = this.getAttribute('data-date');
                if (dataDate) {
                    window.location.href = 'hitzordua_sortu.php?data=' + dataDate;
                }
            }
        });
    });

    // 2. Nabigazio botoiak (astea, hilabetea, eguna) - Existing logic
    const bistaBotoiak = document.querySelectorAll('.bista-hautatzailea .bista-botoia');
    bistaBotoiak.forEach(botoia => {
        if (botoia.getAttribute('href') === '#') {
            botoia.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Bista aldatuta: ' + this.textContent);
            });
        }
    });
});

// Redirect to edit page
function viewAppointment(id) {
    window.location.href = 'hitzordua_editatu.php?id=' + id;
}

// Modal functions are no longer used but kept empty for compatibility if any other script calls them
function openModal() { window.location.href = 'hitzordua_sortu.php'; }
function closeModal() {}
function confirmDelete() {}
