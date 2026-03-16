<?php
/**
 * CSS estiloak sortzen ditu konfigurazioaren arabera.
 * 
 * @param array $konf Konfigurazioaren balioak.
 */
function inprimatuEstiloak($konf) {
    echo "<style>\n";
    echo ":root {\n";
    echo "  --primary-color: " . htmlspecialchars($konf['kolore_nagusia']) . " !important;\n";
    echo "  --secondary-color: " . htmlspecialchars($konf['bigarren_kolorea']) . " !important;\n";
    echo "}\n";
    
    if ($konf['gaia'] == 'iluna') {
        echo "body, main { background-color: #121212 !important; color: #f0f0f0 !important; }\n";
        echo ".portal-txartela, .ezaugarri-txartela, .kutxa-zuria, .kaixo-atalak, .menu-txartela, .kutxa-zuria-800, .kutxa-zuria-700, .kuadro-estatistikak {\n";
        echo "    background-color: #1e1e1e !important; color: #f0f0f0 !important; border-color: #333 !important;\n";
        echo "}\n";
        echo "h1, h2, h3, .portal-info h3, .ezaugarri-txartela h3, .izenburu-nagusia, .izenburu-iluna {\n";
        echo "    color: #ffffff !important;\n";
        echo "}\n";
        echo ".azpititulua, .portal-info p, .ezaugarri-txartela p, .testu-gris-txikia, .azpititulu-grisa, .gray-text, .deskribapen-grisa {\n";
        echo "    color: #cccccc !important;\n";
        echo "}\n";
        echo "td, th { color: #f0f0f0 !important; border-color: #333 !important; }\n";
        echo "th { background: #2d2d2d !important; }\n";
        echo ".egoera-hutsa, .kutxa-hutsa-40, .inprimaki-kontrola { background-color: #1e1e1e !important; color: #fff !important; border-color: #555 !important; }\n";
    }
    echo "</style>\n";
}
?>
