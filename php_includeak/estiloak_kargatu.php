<?php
/**
 * CSS estiloak sortzen ditu konfigurazioaren arabera.
 * 
 * @param array $konf Konfigurazioaren balioak.
 * @return string CSS estiloak.
 */
function lortuEstiloak($konf) {

    // kolore ofizialak lortu 
    $css = "<style>\n";
    $css .= ":root {\n";
    $css .= "  --primary-color: " . htmlspecialchars($konf['kolore_nagusia']) . " !important;\n";
    $css .= "  --secondary-color: " . htmlspecialchars($konf['bigarren_kolorea']) . " !important;\n";
    $css .= "}\n";
    
    // gaiaren arabera estiloak
    if ($konf['gaia'] == 'iluna') {
        $css .= ":root {\n";
        $css .= "  --bg-body: #0f172a;\n";
        $css .= "  --bg-surface: #1e293b;\n";
        $css .= "  --text-main: #f8fafc;\n";
        $css .= "  --text-muted: #94a3b8;\n";
        $css .= "  --border-color: #334155;\n";
        $css .= "  --input-bg: #1e293b;\n";
        $css .= "  --input-border: #475569;\n";
        $css .= "  --shadow-color: rgba(0, 0, 0, 0.4);\n";
        $css .= "  --logo-filter: brightness(0) invert(1);\n";
        $css .= "}\n";
        
        // Beharrezkoak diren gainidazte orokorrak (lehenengo mailako elementuentzat)
        $css .= "body, main { background-color: var(--bg-body) !important; color: var(--text-main) !important; }\n";
        $css .= "input, select, textarea { background-color: var(--input-bg) !important; border-color: var(--input-border) !important; color: var(--text-main) !important; }\n";
        
        // Logo-ei eta atzeko planoari iragazkiak aplikatu
        $css .= ".logo-irudia, .footer-logo { filter: var(--logo-filter) !important; mix-blend-mode: normal !important; }\n";
        $css .= "body::before { opacity: 0.02 !important; filter: invert(1) !important; }\n";
    }
    $css .= "</style>\n";
    return $css;
}

/**
 * Zuzenean inprimatzen ditu estiloak lortuEstiloak erabiliz.
 */
function inprimatuEstiloak($konf) {
    echo lortuEstiloak($konf);
}
?>
