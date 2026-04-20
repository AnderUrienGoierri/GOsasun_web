<?php

function normalizatu_fitxategi_baimenak(string $fitxategiBidea): bool
{
    if ($fitxategiBidea === '' || !file_exists($fitxategiBidea)) {
        return false;
    }

    clearstatcache(true, $fitxategiBidea);
    @chmod($fitxategiBidea, 0644);

    if (DIRECTORY_SEPARATOR === '\\') {
        $komandoa = 'icacls ' . ihes_egin_windows_biderako($fitxategiBidea) . ' /reset';
        @exec($komandoa, $irteera, $irteeraKodea);

        if (($irteeraKodea ?? 1) !== 0) {
            return false;
        }
    }

    clearstatcache(true, $fitxategiBidea);
    return is_readable($fitxategiBidea);
}

function ihes_egin_windows_biderako(string $bidea): string
{
    return '"' . str_replace('"', '""', $bidea) . '"';
}