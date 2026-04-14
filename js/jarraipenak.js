$(document).ready(function () {
  // Bilaketa iragazkia
  $("#pazienteBilatzailea").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $(".jarraipen-taula tbody tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  // Ezohiko neurketak nabarmendu
  function nabarmenduEzohikoak() {
    $(".jarraipen-taula tbody tr").each(function () {
      var sist = parseInt($(this).find("td:nth-child(2)").text());
      var diast = parseInt($(this).find("td:nth-child(3)").text());
      var pultsu = parseInt($(this).find("td:nth-child(4)").text());

      if (sist > 140 || sist < 90 || diast > 90 || diast < 60 || pultsu > 100 || pultsu < 50) {
        $(this).addClass("ezohiko-neurketa");
      }
    });
  }

  nabarmenduEzohikoak();

  // Iragazkia: Ezohikoak soilik
  $("#ikusiEzohikoak").on("change", function () {
    if ($(this).is(":checked")) {
      $(".jarraipen-taula tbody tr").not(".ezohiko-neurketa").hide();
    } else {
      $(".jarraipen-taula tbody tr").show();
    }
  });
});

/**
 * Dokumentua editatzeko modala ireki
 */
function irekiModalEditatuDok(id, izena, desk) {
  document.getElementById("edit_dok_id").value = id;
  document.getElementById("edit_izenburua").value = izena;
  document.getElementById("edit_deskribapena").value = desk;
  document.getElementById("modalEditatuDok").style.display = "block";
}

/**
 * Dokumentua editatzeko modala itxi
 */
function itxiModalDok() {
  document.getElementById("modalEditatuDok").style.display = "none";
}

// Itxi modala kanpoan sakatzean
window.onclick = function (event) {
  const modal = document.getElementById("modalEditatuDok");
  if (event.target == modal) {
    itxiModalDok();
  }
};
