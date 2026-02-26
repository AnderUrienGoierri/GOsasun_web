# GOsasun - Osasun Ataria

GOsasun pazienteen, medikuen eta harrerako langileen kudeaketarako aplikazio integral bat da. Aplikazio honek osasun-datuak jarraitzeko, hitzorduak erreserbatzeko, eta medikuaren eta pazientearen arteko komunikazio segurua bermatzeko diseinatuta dago.

## 📂 Formatu eta Karpeta Estruktura

Proiektua logika eta rol ezberdinen arabera antolatu da:

- php_hasiera/ - Atariaren atal publikoak (Saioa hasi, kontaktua).
- php_harrera/ - Harrerako langileen ataria (Paziente/mediku kudeaketa, hitzorduak, webguneko mezu publikoak).
- php_medikua/ - Medikuaren atari pribatua (Nire pazienteak, agendaren kudeaketa, errezetak, pazienteen jarraipena).
- php_pazientea/ - Pazientearen ataria (Datu pertsonalak, neurketen sarrera, hitzorduak eskatzea, abisuak).
- php_includeak/ - Webgune osoko atal komunak (goiburuak, oinak, CSS/JS loturak).
- php_laguntzaileak/ - Rolik behar ez duten puskak kudeatzeko (Login egiaztapenak, DB Konexioa, Saioa itxi).
- css/ eta js/ - Estilo grafikoak eta portaera dinamikoa (grafikoak, abisuak).
- sql/ - Datu-baseko diseinuaren eta eskemaren fitxategiak.
- pdf_txostenak/ - Sortutako txosten eta errezeta fisikoak gordetzeko karpeta.
- xml_exportableak/ - Sistemak sortzen dituen XML datuen esportazioak gordetzeko direktorioa.

### Orri Nagusiak

- index.php (Erroan): Webguneko atari nagusi publikoa, sarbide ezberdinekin.
- */index.php: Rol bakoitzaren kontrol-panela (Dashboard).
- */mezuak.php: Barne mezularitza sistemak.
- */hitzorduak.php: Orduen erreserbak eta gainbegiratzea.
- */pazienteak.php edo */medikuak.php: Kontsultarako direktorioak.

---

## 💾 Datu-Basearen Arkitektura

Datu-basea erlazio-eredu sendo batean oinarritzen da datuen osotasuna bermatzeko.

### 1. Taulak

Aplikazioaren entitate nagusiak honako taulak osatzen dute:

- **Rolak** (
ol_id, izena): Baimen maila bakoitzaren definizioa.
- **Erabiltzaileak** (rabiltzaile_id, mail, pasahitza, 
ol_id, ktibo): Sistemara sartzen diren kide guztien kontu nagusia (heredentzia gurasoa).
- **Pazienteak** (NAN, izena, abizenak, jaiotze-data, egoera klinikoa...): Erabiltzaileen taulatik heredatuta.
- **Medikuak** (elkargokide-zenbakia, espezialitatea...): Erabiltzaileen taulatik heredatuta.
- **Harrerako_Langileak**: Erabiltzaileen taulatik heredatuta.
- **Mediku_Paziente** (mediku_id, paziente_id): Lotura-taula. Paziente bakoitzari mediku bat (n:m edo 1:n) esleitzeko sistema.
- **Neurketak** (	entsioa, glukosa, pisua, etc.): Pazienteek sartutako jarraipen klinikoak.
- **Hitzorduak** (data, ordua, goera): Mediku eta pazienteen arteko agenda.
- **Errezetak**: Hitzorduekin edo medikazioekin lotutako diagnostikoak eta dokumentazioa.
- **Mezuak**: Barne mezularitza.
- **Kontaktua_Mezuak**: Webgune publikotik hasierako orrian jasotako mezuak.
- **Abisuak** (mota, 	estua, irakurrita): Neurketa anormaletan sortutako alerten taula.

### 2. Bistak (SQL Views)

Kontsultak arintzeko eta segurtasuna indartzeko, hainbat bista programatu dira datuak mozorrotu edo bateratzeko:

- V_Login: Saioa hasteko beharrezko oinarrizko datuak soilik eskaintzen ditu (pasahitza eta rola).
- V_Pazientea, V_Medikua, V_Harrera: Erabiltzaileen taula orokorra euren berezko taularekin lotzen duen ikuspegi bateratua.
- V_Mediku_Pazienteak: Mediku zehatz baten pazienteen zerrenda azkar bat ateratzeko lotura grafikoa.
- V_Hitzorduak_Osoa: Hitzorudak mediku eta paziente izenekin osatuta dakarren bista, gurutzeak saihesteko.
- V_Kanpoko_Mezuak eta V_Abisuak_Osoa: Panelen taulentzako bereziki egokitutako bistak.

### 3. Triggerak

Datuen koherentzia mantentzeko prozesu automatizatu txikiak:

- Eguneratu_Paziente_Datuak: Paziente batek Neurketak taulan pisu erregistro berri bat sartzen duenean (INSERT), pazientearen berezko fitxan dagoen zken_pisua eta zken_altuera automatikoki eguneratzen du.
- *(Oharra: Abisu klinikoen triggerrak JS logikara migratu dira orain bisuak_logika.js bidez kudeatzeko)*

### 4. Indizeak

*Oinarrizko foreign-key eta primary-key indizeak sortu ohi dira modu automatikoan (adib: paziente_id, mediku_id). Honela, kontsultak optimizatzen dira batez ere JOIN operazioetan.*

---

## 📄 Lizentzia

Proiektu hau **Apache License 2.0** lizentziapean argitaratu da. Informazio gehiago nahi izanez gero, irakurri [LICENSE](LICENSE) fitxategia.

© 2026 GOsasun Proiektua. Irakaskuntza edo erabilpen profesionalean oinarritua.
