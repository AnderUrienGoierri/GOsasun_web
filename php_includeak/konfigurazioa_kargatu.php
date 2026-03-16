<?php
// Badaezpada, sesioa hasita ez badago
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Konfigurazioa kargatzen du XML-tik.
 * 
 * @param bool $publikoa True bada, config.xml arrunta bakarrik kargatuko da.
 * @return array Konfigurazioaren balioak.
 */
function kargatuKonfigurazioa($publikoa = false) {
    // Lehenetsitako balioak (fitxategia hutsik badoa)
    $konf = [
        'hizkuntza' => 'eu',
        'kolore_nagusia' => '#4361ee',
        'bigarren_kolorea' => '#3f37c9',
        'gaia' => 'argia',
        'sistema_izena' => 'GOsasun',
        'hitzordu_muga' => '20',
        'ordutegia_ireki' => '08:00',
        'ordutegia_itxi' => '20:00',
        'mantenimendua' => 'ez'
    ];

    $xml_path = __DIR__ . '/../xml_konfigurazioa/config.xml';

    // Orrialde publikoa ez bada, erabiltzailearen konfigurazioa bilatu
    if (!$publikoa && isset($_SESSION['erabiltzaile_id'])) {
        $user_xml_path = __DIR__ . '/../xml_konfigurazioa/config_user_' . $_SESSION['erabiltzaile_id'] . '.xml';
        if (file_exists($user_xml_path)) {
            $xml_path = $user_xml_path;
        }
    }

    if (file_exists($xml_path)) {
        $xml_conf = simplexml_load_file($xml_path);
        if ($xml_conf) {
            $konf['hizkuntza'] = (string)$xml_conf->hizkuntza ?: $konf['hizkuntza'];
            $konf['kolore_nagusia'] = (string)$xml_conf->kolore_nagusia ?: $konf['kolore_nagusia'];
            $konf['bigarren_kolorea'] = (string)$xml_conf->bigarren_kolorea ?: $konf['bigarren_kolorea'];
            $konf['gaia'] = (string)$xml_conf->gaia ?: $konf['gaia'];
            
            if (isset($xml_conf->osasun_zentroa)) {
                $konf['sistema_izena'] = (string)$xml_conf->osasun_zentroa->sistema_izena ?: $konf['sistema_izena'];
                $konf['hitzordu_muga'] = (string)$xml_conf->osasun_zentroa->mediku_bakoitzeko_gehienezko_hitzorduak ?: $konf['hitzordu_muga'];
                $konf['ordutegia_ireki'] = (string)$xml_conf->osasun_zentroa->ordutegia_ireki ?: $konf['ordutegia_ireki'];
                $konf['ordutegia_itxi'] = (string)$xml_conf->osasun_zentroa->ordutegia_itxi ?: $konf['ordutegia_itxi'];
                $konf['mantenimendua'] = (string)$xml_conf->osasun_zentroa->mantenimendu_modua ?: $konf['mantenimendua'];
            }
        }
    }

    return $konf;
}
?>
