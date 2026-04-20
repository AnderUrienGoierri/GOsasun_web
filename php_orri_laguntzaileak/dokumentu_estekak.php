<?php

function lortu_dokumentu_esteka(array $dokumentua, string $aurrizkia = '../'): string
{
    $bidea = trim((string)($dokumentua['bidea_zerbitzarian'] ?? ''));
    $fitxategiIzena = trim((string)($dokumentua['fitxategi_izena'] ?? ''));
    $hautagaiak = [];

    if ($bidea !== '') {
        // Normalizatu bidea (aldatu \ -> / eta kendu bikoiztutako /)
        $bideaNormalizatua = preg_replace('#/+#', '/', str_replace('\\', '/', $bidea));
        
        // Kendu Windows-eko unitate letra (adib: C:/)
        $bideaNormalizatua = preg_replace('/^[a-zA-Z]:\//', '', $bideaNormalizatua);
        
        // Kendu proiektuaren erroko bide absolutua (segurtasun gehigarria)
        $webErroaPos = strpos($bideaNormalizatua, 'GOsasun_web/');
        if ($webErroaPos !== false) {
            $bideaNormalizatua = substr($bideaNormalizatua, $webErroaPos + strlen('GOsasun_web/'));
        }
        
        $bideaNormalizatua = ltrim($bideaNormalizatua, '/');
        $hautagaiak[] = $bideaNormalizatua;

        if ($fitxategiIzena !== '' && strcasecmp(basename(rtrim($bideaNormalizatua, '/')), $fitxategiIzena) !== 0) {
            $hautagaiak[] = rtrim($bideaNormalizatua, '/') . '/' . ltrim(str_replace('\\', '/', $fitxategiIzena), '/');
        }
    }

    if ($fitxategiIzena !== '') {
        $hautagaiak[] = 'paziente_dokumentuak/' . ltrim(str_replace('\\', '/', $fitxategiIzena), '/');
    }

    $hautagaiak = array_values(array_unique(array_filter($hautagaiak)));

    foreach ($hautagaiak as $hautagaia) {
        if (dokumentu_fitxategia_existitzen_da($hautagaia)) {
            return $aurrizkia . ltrim($hautagaia, '/');
        }
    }

    if (!empty($hautagaiak)) {
        return $aurrizkia . ltrim($hautagaiak[0], '/');
    }

    return '#';
}

function dokumentu_fitxategia_existitzen_da(string $bideErlatiboa): bool
{
    $webErroa = dirname(__DIR__);
    $fitxategiBidea = $webErroa . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($bideErlatiboa, '/'));
    return is_file($fitxategiBidea);
}