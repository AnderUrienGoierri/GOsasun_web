# jQuery Erabilerak GOsasun Webgunean

GOsasun web proiektuan jQuery liburutegia modu intentsiboan erabili da JavaScript kodea erraztu eta arintzeko. Hemen webgunean zehar topa daitezkeen jQuery erabilera nagusien sailkapen bat, bakoitzaren kode adibide (pantailazo/bloke) argigarriekin:

## 1. DOM-a Kargatu Bitartean Itxarotea (`$(document).ready()`)
JavaScript kodea ez exekutatzeko HTML orria guztiz kargatu arte erabiltzen da. Fitxategi ia guztietan (adibidez: `jarraipenak.js`, `login.js`, `neurketak.js`) agertzen den egitura nagusia da.

```javascript
$(document).ready(function () {
  // Webguneko DOM-a guztiz kargatu denean exekutatuko den kodea
});
```

## 2. Elementuen Hautaketa (Selector-ak)
jQuery-ren `$()` funtzioa erabiliz, DOM elementuak era errazean hautatzen dira ID (`#`), klase (`.`) edo etiketen bidez. 

```javascript
// ID bidezko hautaketa
const pasahitza = $('#pasahitza').val().trim();

// Klase bidezko hautaketa
const errenkadak = $(".jarraipen-taula tbody tr");

// Etiketa bidezko hautaketa
var opts = $("#pazienteakList option");
```

## 3. Gertaeren Kudeaketa (Event Handling)
Erabiltzailearen elkarrekintzak (klikatzean, idaztean, inprimakiak bidaltzean...) kudeatzeko hainbat metodo erabiltzen dira: `.on()`, `.click()`, `.submit()`, `.keyup()`.

**Adibidea: Klik gertaera bat kudeatzea (`jarraipenak.js`)**
```javascript
// IGO Dokumentua modal-a ireki klik egitean
$(".ireki-igo-modal").on("click", function () {
    const jId = $(this).data("id");
    $("#modal_jarraipen_id").val(jId);
    $("#igoDokumentuaModala").fadeIn(300);
});
```

**Adibidea: Inprimaki bat bidaltzea (`login.js`)**
```javascript
$('#loginForm').submit(function(e) {
    const email = $('#email').val().trim();
    // Balidazioa egin eta erroreak erakutsi
});
```

## 4. DOM Manipulazioa eta Animazioak (Efektuak)
Orriko elementuen itxura aldatzeko, edukiak sartzeko edo trantsizio leunak sortzeko metodoak asko erabili dira: `.html()`, `.show()`, `.hide()`, `.fadeIn()`, `.fadeOut()`, `.addClass()`.

**Adibidea: Elementuen klaseak aldatzea eta DOM edukia ezartzea (`jarraipenak.js`)**
```javascript
// Taula baten barruko gelaxka (td) bati klase bat gehitzea
$(this).find("td").eq(2).addClass("testu-arriskua-ezk");

// HTML edukia dinamikoki ordezkatzea
const eremua = $("#dokumentuZerrendaEremua");
eremua.html('<p class="testua-erdian padding-20">Kargatzen...</p>');
```

**Adibidea: Elementuak ezkutatu/erakusteko animazioak**
```javascript
// Elementua astiro erakutsi (agertarazi)
$("#ikusiDokumentuakModala").fadeIn(300);

// Elementua ezkutatu
$(".modala").fadeOut(300);
```

## 5. Datuak Eskuratu eta Ezarri
Erabiltzaileak inprimakietan idatzitako datuak irakurtzeko edo HTML elementuek gordetzen dituzten `data-*` atributuak irakurtzeko: `.val()`, `.text()`, `.data()`.

```javascript
// Input baten balioa eskuratu
var value = $(this).val().toLowerCase();

// 'data-id' atributuaren balioa lortu
var pId = opts[i].getAttribute('data-id');
const jId = $(this).data("id");
```

## 6. Eskaera Asinkronoak (AJAX)
Orria birkargatu gabe datza-basetik datuak lortu eta HTMLan txertatzeko `$.ajax` erabiltzen da. Adibidez, `jarraipenak.js`-en paziente baten dokumentuak bistaratzeko eta `grafikak.js`-en.

**Adibidea: AJAX dei bat datuak lortzeko (`jarraipenak.js`)**
```javascript
$.ajax({
    url: 'lortu_dokumentuak_ajax.php',
    type: 'GET',
    data: { id: jId },
    success: function(response) {
        // Deia ondo badoa, jasotako edukia (HTML/Testua) pantailan txertatu
        eremua.html(response);
    },
    error: function() {
        // Errorea gertatzen bada mezua erakutsi
        eremua.html('<p class="alerta alerta-errorea">Errorea datuak kargatzean.</p>');
    }
});
```

## 7. DOM Elementuen Zeharkatzea (Traversing)
Erlazionaturiko elementuak bilatzeko, gurasoetatik umeengana edo alderantziz bidaiatzeko metodoak: `.closest()`, `.find()`, `.each()`, `.eq()`.

**Adibidea: Elementu bakoitzetik iteratzea taula baten errenkadak iragazteko (`jarraipenak.js`)**
```javascript
$(".jarraipen-taula tbody tr").each(function () {
    // Uneko errenkadako 3. zutabearen balioa (SIS) ateratzea
    var sist = parseInt($(this).find("td").eq(2).text());
    
    // Baldintza batzuen arabera erakutsi edo ezkutatu errenkada hau
    if (sist > 140) {
        $(this).show();
    } else {
        $(this).hide();
    }
});
```
