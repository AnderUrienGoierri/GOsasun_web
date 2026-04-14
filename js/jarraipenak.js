$(document).ready(function () {
  // 1. Bilaketa Live (Datalist bidezkoa)
  $("#pazienteBilatzailea").on("input", function () {
    var val = $(this).val();
    var opts = $("#pazienteakList option");
    for (var i = 0; i < opts.length; i++) {
      if (opts[i].value === val) {
        var pId = opts[i].getAttribute('data-id');
        $("#paziente_id").val(pId);
        $("#pazienteAukeraForm").submit();
        break;
      }
    }
  });

  // 2. Taula barruko iragazki laguntzailea (opcional, por si quieren filtrar la tabla cargada)
  $("#pazienteBilatzailea").on("keyup", function () {
    if ($(".jarraipen-taula").length) {
        var value = $(this).val().toLowerCase();
        $(".jarraipen-taula tbody tr").filter(function () {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    }
  });

  // 2. Ezohiko neurketak nabarmendu
  function nabarmenduEzohikoak() {
    $(".jarraipen-taula tbody tr").each(function () {
      // Split zutabeetan: SIS (index 2), DIA (index 3), Pultsua (index 4)
      var sist = parseInt($(this).find("td").eq(2).text());
      var diast = parseInt($(this).find("td").eq(3).text());
      var pultsu = parseInt($(this).find("td").eq(4).text());

      if (sist > 140 || sist < 90 || diast > 90 || diast < 60 || pultsu > 100 || pultsu < 50) {
        $(this).find("td").eq(2).addClass("testu-arriskua-ezk");
        $(this).find("td").eq(3).addClass("testu-arriskua-ezk");
        $(this).find("td").eq(4).addClass("testu-arriskua-ezk");
      }
    });
  }

  nabarmenduEzohikoak();

  // 3. Iragazkia: Ezohikoak soilik
  $("#ikusiEzohikoak").on("change", function () {
    if ($(this).is(":checked")) {
        $(".jarraipen-taula tbody tr").each(function() {
            var sist = parseInt($(this).find("td").eq(2).text());
            var diast = parseInt($(this).find("td").eq(3).text());
            var pultsu = parseInt($(this).find("td").eq(4).text());
            
            if (sist > 140 || sist < 90 || diast > 90 || diast < 60 || pultsu > 100 || pultsu < 50) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    } else {
      $(".jarraipen-taula tbody tr").show();
    }
  });
  // 3. --- MODAL LOGIKA ---
  
  // IGO Dokumentua modal-a ireki
  $(".ireki-igo-modal").on("click", function () {
    const jId = $(this).data("id");
    $("#modal_jarraipen_id").val(jId);
    $("#igoDokumentuaModala").fadeIn(300);
  });

  // IKUSI Dokumentuak modal-a ireki (AJAX bidez)
  $(".ireki-ikusi-modal").on("click", function () {
    const jId = $(this).data("id");
    const eremua = $("#dokumentuZerrendaEremua");
    
    eremua.html('<p class="testua-erdian padding-20">Kargatzen...</p>');
    $("#ikusiDokumentuakModala").fadeIn(300);
    
    $.ajax({
        url: 'lortu_dokumentuak_ajax.php',
        type: 'GET',
        data: { id: jId },
        success: function(response) {
            eremua.html(response);
        },
        error: function() {
            eremua.html('<p class="alerta alerta-errorea">Errorea datuak kargatzean.</p>');
        }
    });
  });
});

// Modal-a ixteko funtzio globala (HTML-tik deitua)
function itxiModala(modalId) {
    $("#" + modalId).fadeOut(300);
}

// Klikatu modaletik kanpo ixteko
$(window).on("click", function(event) {
    if ($(event.target).hasClass("modala")) {
        $(".modala").fadeOut(300);
    }
});

// 4. Ordenazio funtzionalitatea
let goraka = true;
let azkenZutabea = -1;

function ordenatuTaula(zutabeIndex) {
    let taula_gorputza = $(".jarraipen-taula tbody");
    let errenkadak = taula_gorputza.find("tr").get();
    
    if (azkenZutabea === zutabeIndex) {
        goraka = !goraka;
    } else {
        goraka = true;
        azkenZutabea = zutabeIndex;
    }
    
    errenkadak.sort((a, b) => {
        let cellsA = $(a).children('td');
        let cellsB = $(b).children('td');
        
        let testu_a = cellsA.eq(zutabeIndex).text().trim();
        let testu_b = cellsB.eq(zutabeIndex).text().trim();
        
        // Data formatua (YYYY/MM/DD)
        if (zutabeIndex === 0) {
            let data_a = new Date(testu_a);
            let data_b = new Date(testu_b);
            return goraka ? data_a - data_b : data_b - data_a;
        }
        
        // Zenbakizkoak (Tentsioa, Pultsua, Pisua)
        if ([2, 3, 4, 5, 6].includes(zutabeIndex)) {
            let zenbaki_a = parseFloat(testu_a.replace(/[^\d.-]/g, '')) || 0;
            let zenbaki_b = parseFloat(testu_b.replace(/[^\d.-]/g, '')) || 0;
            return goraka ? zenbaki_a - zenbaki_b : zenbaki_b - zenbaki_a;
        }
        
        // Testua
        if (testu_a.toLowerCase() < testu_b.toLowerCase()) return goraka ? -1 : 1;
        if (testu_a.toLowerCase() > testu_b.toLowerCase()) return goraka ? 1 : -1;
        return 0;
    });
    
    $.each(errenkadak, function(index, errenkada) {
        taula_gorputza.append(errenkada);
    });
}
