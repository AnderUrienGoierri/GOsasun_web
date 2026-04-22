# GOsasun Webguneko CSS Klaseen Zerrenda eta Azalpena

Dokumentu honetan GOsasun proiektuan zehar erabilitako CSS klase guztiak aurkituko dituzu, beraien funtzio eta helburuaren arabera multzokatuta. Egitura hau erabiliz klaseen berrerabilpena eta mantentze-lanak errazten dira.

## 1. Egitura eta Banaketa (Layout & Flexbox/Grid)
Webgunearen oinarrizko hezurdura, edukiontziak, eta elementuen kokapena zehazteko klaseak.
*   **`edukiontzi-ertaina`, `kutxa-osoa`, `kutxa-txikia`**: Orriaren atal desberdinen edukiera eta zabalera mugatzen dute (adib. erdiko zutabea edo pantaila osoko atala).
*   **`zabalera-osoa`, `zabal-100`, `zabalera-erdia`, `w-osoa`**: Elementuek edukiontziaren %100 edo %50 okupatzeko klase lagungarriak.
*   **Flexbox klaseak**: `flex-erdian`, `flex-goi-15`, `flex-bukaera`, `flex-zentratua-20`, `flex-osoa`, `talde-flex`, `flex-bat`. Elementuak lerrokatzeko eta espazioa banatzeko.
*   **Grid klaseak**: `inprimaki-grid`, `grid-egutegia`, `biometria-grid`, `menu-sareta`, `panel-sareta`. Elementuak sareta formatuan aurkezteko.
*   **Edukiontzi nagusiak**: `hasiera-edukiontzia`, `aginte-panel-kutxa`, `agenda-edukiontzia`, `egutegia-edukiontzia`, `fitxa-edukiontzia`.

## 2. Txartelak eta Blokeak (Cards & Boxes)
Informazioa bloke bisualetan (txarteletan) erakusteko klaseak.
*   **`kutxa-zuria`, `txartel-zuria`, `txartel-zuri-argia`**: Hondo zuria, ertz biribilduak eta itzal leunak dituzten blokeak. Informazio garrantzitsua nabarmentzeko.
*   **`kutxa-zuria-itzala`, `neurketa-kutxa`, `errezeta-kutxa-argia`**: Aurrekoaren aldaerak, testuinguru berezietarako (adib. pazienteen neurketak edo errezetak).
*   **`estat-txartela`, `ezaugarri-txartela`, `menu-txartela`, `portal-txartela`**: Aginte-paneleko eta atari nagusietako aukerak erakusten dituzten txartel bereziak.

## 3. Botoiak eta Ekintzak (Buttons & Actions)
Erabiltzailearen elkarreragin elementuetarako estiloak.
*   **Botoi nagusiak**: `botoia`, `botoi-nagusia`, `botoi-sortu`, `bidali-botoia`. Ekintza positiboak eta ohikoak egiteko (kolore urdin edo berdea izan ohi dute).
*   **Botoi txikiak eta bereziak**: `botoi-nagusia-txikia`, `botoi-ertza-txikia`, `editatu-botoia`, `ezabatu-botoia`, `irakurri-botoia`.
*   **Ekintza negatiboak**: `botoi-itsua-gorria`, `arrisku-kolorea` (kaleratzeak, ezabatzeak).
*   **Botoi taldeak**: `botoi-taldea`, `botoi-taldea-flex`, `ekintza-botoiak`. Zenbait botoi elkarren ondoan jartzeko.

## 4. Taulak (Tables)
Datu-baseko informazioa (pazienteak, hitzorduak, dokumentuak) zutabetan erakusteko.
*   **Oinarrizko taulak**: `datu-taula`, `taula-sinplea`, `taula-moldakorra`, `datu-sareta-modernoa`. Egitura eta ertzak definitzen dituzte.
*   **Taula espezifikoak**: `paziente-taula`, `dokumentu-taula`, `jarraipen-taula`, `abisu-taula`.
*   **Eragiketa gelaxkak**: `taula-ekintzak`, `grid-th`.

## 5. Formularioak eta Sarrerak (Forms & Inputs)
Erabiltzaileak datuak sartzeko eremuak.
*   **Edukiontziak**: `inprimaki-edukiontzia`, `inprimaki-taldea`, `inprimaki-kutxa`, `inprimaki-lerroa`.
*   **Sarrerak (Inputs/Textareas)**: `inprimaki-sarrera`, `sarrera-handia`, `sarrera-testu-eremua`, `bilaketa-eremua`, `bilaketa-barra`.
*   **Etiketak (Labels)**: `inprimaki-etiketa`, `aukerak-etiketa`.
*   **Beste batzuk**: `checkbox-zerrenda`, `checkbox-20px`, `fitxategi-sarrera`.

## 6. Koloreak, Egoerak eta Alertak (Status & Alerts)
*   **Egoerak (Badges/Labels)**: `egoera-aktiboa`, `egoera-ez-aktiboa`, `egoera-hutsa`, `egoera-zain`, `status-bukatuta`, `status-ezeztatuta`, `irakurrita-badge`. 
*   **Alertak eta Mezuak**: `alerta`, `alerta-errorea`, `alerta-arrakasta`, `errore-mezua`, `kargatzen-mezua`.
*   **Kolore klaseak**: `testu-arriskua-ezk` (gorria neurketa txarretarako), `testu-grisa`, `testu-iluna-444`, `hondo-arrakasta`.

## 7. Tipografia eta Testu-lerrokatzea (Typography)
Testuen tamaina, kolorea eta kokapena aldatzeko utilitateak.
*   **Izenburuak**: `izenburu-nagusia`, `izenburu-nabarmena`, `izenburu-urdina`, `atal-izenburua`, `azpititulua`.
*   **Testu arruntak eta lerrokatzea**: `testu-normala`, `testua-erdian`, `testua-ezkerrean`.
*   **Testu txikiak/argiak**: `testu-gris-txikia`, `testu-txikia-200`, `deskribapen-grisa`, `argibide-testua`.
*   **Nabarmenduak**: `testu-handi-lodia`, `etiketa-lodia`, `zenbaki-handi-urdina`.

## 8. Modalak / Leiho Gainerakorrak (Modals)
Pantailaren gainean irekitzen diren elementuak (adib. dokumentu bat igotzeko edo baieztatzeko).
*   **Egitura**: `modal`, `modala`, `modala-inguratzailea`, `modal-wrapper`.
*   **Eduki blokeak**: `modal-edukia`, `modal-content`, `modala-handia`.
*   **Goiburua/Gorputza/Oina**: `modal-goiburua`, `modal-gorputza`, `itxi-modal`.

## 9. Tarteak, Marjinak eta Betegarriak (Spacing)
Elementuen arteko airea (margin/padding) definitzeko klase utility-ak.
*   **Marjinak (Margin)**: `marjina-behe-10`, `marjina-behe-20`, `marjina-goi-20`, `marjina-gabea`, `marjina-esk-10`.
*   **Betegarriak (Padding)**: `padding-20`, `padding-goi-20`, `padding-behe-15`, `betegarria-15`.
*   **Hutsarteak (Gaps)**: `hutsartea-10`, `hutsartea-15`, `tarte-goia`, `tarte-behea-fleks`.

## 10. Ikonoak eta Irudiak (Icons & Images)
*   **Ikono tamainak**: `ikono-txikia`, `ikono-ertaina`, `ikono-handia`, `ikono-24px-erdian`, `ikono-48px`.
*   **Profil eta Irudiak**: `profil-argazkia`, `paziente-profil-irudia`, `hero-irudia`, `portal-irudia`, `logo-irudia`.

---
*Oharra: Klase hauek `estilo_orokorrak.css` eta orri zehatzetarako (adib. `hitzorduak.css`, `pazienteak.css`, etab.) sorturiko estilo fitxategi desberdinetan banatuta daude.*
