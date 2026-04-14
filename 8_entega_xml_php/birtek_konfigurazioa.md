# GOsasun - XML Konfigurazio Sistema

Dokumentu honek GOsasun web aplikazioan XML bidezko konfigurazioak nola funtzionatzen duen azaltzen du.

## 1. Konfigurazio Mailak

Aplikazioak bi konfigurazio maila bereizten ditu:

### A. Konfigurazio Orokorra (`konfigurazio_orokorra.xml`)

- **Fitxategia:** `xml_konfigurazioa/konfigurazio_orokorra.xml`
- **Noiz erabiltzen da:** Aplikazio osorako zentroaren balio lehenetsi korporatibo gisa, beste norbaitek bere ezarpen propioak ez dituenean.
- **Ezaugarria:** Harrerako langileek bakarrik eguneratu dezakete fitxategi hau modu globalean, edozeinek sistemaren datu base orokorra ukitu ez dezan.
- **Gordetzea:** Harrerako langileak bere ezarpenen orriko "Zentroaren Konfigurazio Orokorra" ataletik soilik aldatu ahal du (form_type: 'osasun_zentroa').

### B. Bisitarien Konfigurazio Tenporala (Saioa)

- **Non gordetzen da:** PHP arakatzailearen saioan (`$_SESSION['guest_konfig']`), eta ez XML artxibo batean.
- **Noiz erabiltzen da:** Logeatu gabeko erabiltzaile anonimo bat orri publikoan (`index.php`...) Ezarpenak aldatzen saiatzean.
- **Ezaugarria:** Aplikazioaren datu global garrantzitsuak kutsatzea (overwrite) ekiditen du erabiltzaile anonimoen babes gisa.
- **Gordetzea:** Bisitariak gai iluna edo hizkuntza aukeratu dezake interfaze eguneatu bat edukitzeko, baina bere aukera tenporala izango da nabigatzailea ixten duen arte. XML-a ez da inoiz ukitzen.

### C. Erabiltzailearen Konfigurazioa (`konfig_erabiltzailea_{id}.xml`)

- **Fitxategia:** `xml_konfigurazioa/konfig_erabiltzailea_X.xml` (non X erabiltzailearen IDa den).
- **Noiz erabiltzen da:** Saioa hasi ondoren (Pazientea, Medikua edo Harrera panelak).
- **Ezaugarria:** Erabiltzaile bakoitzak bere interfazearen itxura (koloreak, hizkuntza, gaia) pertsonalizatu dezake.
- **Gordetzea:** Saioa hasita dagoenean, aldaketak erabiltzaileari dagokion XML fitxategi espezifikoan gordetzen dira. Hurrengoan saioa hastean, bere pertsonalizazioa kargatuko da.

## 2. Funtzionamendu Teknikoa

### Kargatzea (`php_orri_includeak/konfigurazioa_kargatu.php`)

`kargatuKonfigurazioa()` funtzioak algoritmo hau jarraitzen du:

1. Lehenetsitako balioak kargatzen ditu (PHP array batean).
2. `konfigurazio_orokorra.xml` kargatzen du balio orokorrekin.
3. Saioa hasita badago, `konfig_erabiltzailea_{id}.xml` badagoen begiratzen du.
4. Badago, erabiltzailearen balioek aurrekoak gainidazten dituzte.

### Gordetzea (`php_orri_laguntzaileak/ezarpenak_gorde.php`)

`ezarpenak_gorde.php` fitxategiak prozesatzen ditu inprimakiak:

- `form_type === 'osasun_zentroa'` bada -> `konfigurazio_orokorra.xml` orokorrean gorde daiteke (Harrerako langileen baimenez).
- `form_type === 'orokorra'` bada eta sesioa badago (erabiltzaile logeatua) -> Erabiltzailearen XML pertsonal espezifikoa kudeatzen da. 
- `form_type === 'orokorra'` bada eta sesioa EZ badago (bisitari ezezaguna) -> Aldaketak `$_SESSION['guest_konfig']` aldagaian gordetzen dira modu tenporalean bakarrik XML zaratatu gabe.
- `reset` ekintzak erabiltzaile erregistratuaren XML fitxategia ezabatzen du (`unlink`), berriz ere konfigurazio orokorra heredatuz. Global-ean egitean, balio korporatiboak txertatuko dira orokorrean.

## 3. Demo Bideoa

Prozesu guztia ikusteko, kontsultatu `mp4/konfigurazio_demoa.mp4` bideoa. Bertan ikusten da:

1. Index orrian kolorea aldatzea (globala).
2. Saioa hasi eta panel barruan kolorea aldatzea (erabiltzailearena).
3. Berreskuratu botoiaren erabilera.

## 4. PHP Kodearen Inplementazioa (Pantailazoak)

Aplikazioaren ezarpenen kudeaketa hainbat fitxategitan banatzen da. Jarraian, logikaren gakoak eta horien inplementazioa erakusten dira:

### 4.1. Berrezartze Logika (`php_orri_laguntzaileak/ezarpenak_gorde.php`)
Erabiltzaile batek "Reset Pertsonala" eskatzen duenean bere pertsonalizazioak zuzenean ezabatzen dira (`unlink`), zentroaren orokorra lehenetsia denez bertatik jasotzeko oinordetza bidez. Aldiz, zentroak "Reset Globala" eskatzean balio korporatiboak idatziko dira (`konfigurazio_orokorra.xml` barruan).

```php
    // Ekintza 'reset' bada
    if ($ekintza === 'reset') {
        if ($form_type === 'orokorra' && isset($_SESSION['erabiltzaile_id'])) {
            // Pertsonala: ezabatu XML fitxategi pertsonala, konfigurazio orokorra jasotzeko (hereditatez)
            if (file_exists($xml_path)) {
                unlink($xml_path);
            }
        } else {
            // Globala ('osasun_zentroa'): hasierako balioak ezarri (Basque, Light, Blue)
            $hizk = 'eu'; 
            $kol_nag = '#4361ee'; 
            $big_kol = '#3f37c9'; 
            $foot_kol = '#2b2d42'; 
            $gaia = 'argia';

            $xml = new DOMDocument("1.0", "UTF-8");
            // ... (XML egitura sortu eta gorde)
            $xml->save($xml_path);
        }
        // ... (bideratze lerroak)
```

### 4.2. Harrerako Kudeaketa Orokorra (`php_harrera_langileak/ezarpenak.php`)
Harrerako profilean, ohiko erabiltzaile ezarpenen azpian, esklusiboa den **Osasun Zentroaren Konfigurazio Orokorra** atala dago. Inprimaki honek `form_type="osasun_zentroa"` bidaltzen du, `konfigurazio_orokorra.xml` fitxategi globala eguneratzeko.

```html
<!-- Zentroaren Konfigurazio Orokorra (Administratzaileentzat soilik) -->
<div class="kutxa-zuria ertz-lodi-urdina marjina-goi-30">
    <!-- ... izenburua ... -->
    <form action="../php_orri_laguntzaileak/ezarpenak_gorde.php" method="POST">
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
