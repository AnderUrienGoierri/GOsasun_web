# GOsasun - XML Konfigurazio Sistema

Dokumentu honek GOsasun web aplikazioan XML bidezko konfigurazioak nola funtzionatzen duen azaltzen du.

## 1. Konfigurazio Mailak

Aplikazioak bi konfigurazio maila bereizten ditu:

### A. Konfigurazio Orokorra (`konfigurazio_orokorra.xml`)

- **Fitxategia:** `xml_konfigurazioa/konfigurazio_orokorra.xml`
- **Noiz erabiltzen da:** Saioa hasi baino lehen (index.php, kontaktua.php...) eta balio lehenetsi gisa.
- **Ezaugarria:** Orrialde publikoetan egiten diren aldaketak fitxategi honetan gordetzen dira.
- **Gordetzea:** Orrialde printzipaleko "Ezarpenak" modaletik aldatzen direnean, **ez dira erabiltzaile bakoitzarentzat gordetzen**, globalak dira.

### B. Erabiltzailearen Konfigurazioa (`konfig_erabiltzailea_{id}.xml`)

- **Fitxategia:** `xml_konfigurazioa/konfig_erabiltzailea_X.xml` (non X erabiltzailearen IDa den).
- **Noiz erabiltzen da:** Saioa hasi ondoren (Pazientea, Medikua edo Harrera panelak).
- **Ezaugarria:** Erabiltzaile bakoitzak bere interfazearen itxura (koloreak, hizkuntza, gaia) pertsonalizatu dezake.
- **Gordetzea:** Saioa hasita dagoenean, aldaketak erabiltzaileari dagokion XML fitxategi espezifikoan gordetzen dira. Hurrengoan saioa hastean, bere pertsonalizazioa kargatuko da.

## 2. Funtzionamendu Teknikoa

### Kargatzea (`php_includeak/konfigurazioa_kargatu.php`)

`kargatuKonfigurazioa()` funtzioak algoritmo hau jarraitzen du:

1. Lehenetsitako balioak kargatzen ditu (PHP array batean).
2. `konfigurazio_orokorra.xml` kargatzen du balio orokorrekin.
3. Saioa hasita badago, `konfig_erabiltzailea_{id}.xml` badagoen begiratzen du.
4. Badago, erabiltzailearen balioek aurrekoak gainidazten dituzte.

### Gordetzea (`php_laguntzaileak/ezarpenak_gorde.php`)

`ezarpenak_gorde.php` fitxategiak prozesatzen ditu inprimakiak:

- `form_type === 'orokorra'` bada eta sesioa badago -> Erabiltzailearen XML-an gorde.
- Sesioa ez badago -> `konfigurazio_orokorra.xml` orokorrean gorde.
- `reset` ekintzak erabiltzailearen XML fitxategia ezabatzen du, balio orokorretara itzuliz.

## 3. Demo Bideoa

Prozesu guztia ikusteko, kontsultatu `mp4/konfigurazio_demoa.mp4` bideoa. Bertan ikusten da:

1. Index orrian kolorea aldatzea (globala).
2. Saioa hasi eta panel barruan kolorea aldatzea (erabiltzailearena).
3. Berreskuratu botoiaren erabilera.

## 4. PHP Kodearen Inplementazioa (Pantailazoak)

Aplikazioaren ezarpenen kudeaketa hainbat fitxategitan banatzen da. Jarraian, logikaren gakoak eta horien inplementazioa erakusten dira:

### 4.1. Berrezartze Logika (`php_laguntzaileak/ezarpenak_gorde.php`)
Erabiltzaile batek (edo zentroak) ezarpenak berrezartzea eskatzen duenean (`ekintza === 'reset'`), sistemak balio korporatibo lehenetsiak finkatzen ditu zuzenean XML fitxategian, fitxategia ezabatu beharrean.

```php
    // Ekintza 'reset' bada, hasierako balioak ezarri (Basque, Light, Blue)
    if ($ekintza === 'reset') {
        $hizk = 'eu'; 
        $kol_nag = '#4361ee'; 
        $big_kol = '#3f37c9'; 
        $foot_kol = '#2b2d42'; 
        $gaia = 'argia';

        $xml = new DOMDocument("1.0", "UTF-8");
        $xml->formatOutput = true;
        $root = $xml->createElement("konfigurazioa");
        $xml->appendChild($root);
     
        $root->appendChild($xml->createElement("hizkuntza", $hizk));
        $root->appendChild($xml->createElement("kolore_nagusia", $kol_nag));
        $root->appendChild($xml->createElement("bigarren_kolorea", $big_kol));
        $root->appendChild($xml->createElement("footer_kolorea", $foot_kol));
        $root->appendChild($xml->createElement("gaia", $gaia));
        
        $xml->save($xml_path);
        // ... (bideratze lerroak)
```

### 4.2. Harrerako Kudeaketa Orokorra (`php_harrera/ezarpenak.php`)
Harrerako profilean, ohiko erabiltzaile ezarpenen azpian, esklusiboa den **Osasun Zentroaren Konfigurazio Orokorra** atala dago. Inprimaki honek `form_type="osasun_zentroa"` bidaltzen du, `konfigurazio_orokorra.xml` fitxategi globala eguneratzeko.

```html
<!-- Zentroaren Konfigurazio Orokorra (Administratzaileentzat soilik) -->
<div class="kutxa-zuria ertz-lodi-urdina marjina-goi-30">
    <!-- ... izenburua ... -->
    <form action="../php_laguntzaileak/ezarpenak_gorde.php" method="POST">
        <input type="hidden" name="form_type" value="osasun_zentroa">
        <input type="hidden" name="itzulera" value="harrera">
        
        <!-- ... (inprimakiaren eremuak: hizkuntza, koloreak, gaia) ... -->
        
        <div class="testua-erdian-marjina-behe-20">
            <button type="submit" class="botoia botoi-nagusia zabalera-osoa-300px">
                <?php echo $itzulpenak->ezarpenak->gorde_botoia; ?>
            </button>
        </div>
    </form>
    
    <!-- ... (Zentroaren Reset botoia) ... -->
</div>
```

### 4.3. XML Fitxategiaren Egituren Adibidea (`xml_konfigurazioa/konfigurazio_orokorra.xml`)
Ezarpenak gordetzean, XML datu-basea honela geratzen da egituratuta:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<konfigurazioa>
  <hizkuntza>eu</hizkuntza>
  <kolore_nagusia>#4361ee</kolore_nagusia>
  <bigarren_kolorea>#3f37c9</bigarren_kolorea>
  <footer_kolorea>#2b2d42</footer_kolorea>
  <gaia>argia</gaia>
</konfigurazioa>
```
