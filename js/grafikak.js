// js/grafikak.js
$(document).ready(function() {
    let nireGrafika = null;
    const kanbasa = document.getElementById('osabide-grafika');
    if (!kanbasa) return; 
    const ctx = kanbasa.getContext('2d');
    
    // Hasierako marrazketa
    marraztuGrafika('pisua');
    
    // Aldaketak kudeatu hautatzailean
    $('#datu-mota').on('change', function() {
        marraztuGrafika($(this).val());
    });
    
    // Grafika motaren arabera marrazteko funtzioa
    function marraztuGrafika(mota) {
        if (typeof neurketakData === 'undefined' || neurketakData.length === 0) return;
        
        let datuMultzoak = [];
        let unitatea = '';
        let etiketak = [];
        
        if (mota === 'pisua') {
            // Iragazi datuak balioa dutenak bakarrik hartzeko
            const neurketaIragaziak = neurketakData.filter(d => d.pisua_kg !== null && d.pisua_kg !== undefined && d.pisua_kg !== '');
            const datuak = neurketaIragaziak.map(d => parseFloat(d.pisua_kg));
            etiketak = neurketaIragaziak.map(d => d.data);
            unitatea = 'kg';
            
            datuMultzoak.push({
                label: 'Pisua (kg)',
                data: datuak,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            });
            datuMultzoak.push(lortuRegresioLerroa(datuak, 'Joera (Pisua)', '#0056b3'));
            eguneratuEstatistikaPanela(datuak, unitatea);

        } else if (mota === 'tentsioa') {
            const neurketaIragaziak = neurketakData.filter(d => d.tentsio_sistolikoa !== null && d.tentsio_diastolikoa !== null);
            const sistolikoa = neurketaIragaziak.map(d => parseInt(d.tentsio_sistolikoa));
            const diastolikoa = neurketaIragaziak.map(d => parseInt(d.tentsio_diastolikoa));
            etiketak = neurketaIragaziak.map(d => d.data);
            unitatea = 'mmHg';
            
            datuMultzoak = [
                {
                    label: 'Tentsio Sistolikoa ',
                    data: sistolikoa,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.1
                },
                {
                    label: 'Tentsio Diastolikoa',
                    data: diastolikoa,
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.1
                }
            ];
            datuMultzoak.push(lortuRegresioLerroa(sistolikoa, 'Joera (Sist.)', '#a71d2a'));
            datuMultzoak.push(lortuRegresioLerroa(diastolikoa, 'Joera (Diast.)', '#117a8b'));
            eguneratuEstatistikaPanela(sistolikoa, unitatea, 'Sistolikoa', diastolikoa, 'Diastolikoa');

        } else if (mota === 'pultsua') {
            const neurketaIragaziak = neurketakData.filter(d => d.pultsua_ppm !== null && d.pultsua_ppm !== undefined && d.pultsua_ppm !== '');
            const datuak = neurketaIragaziak.map(d => parseInt(d.pultsua_ppm));
            etiketak = neurketaIragaziak.map(d => d.data);
            unitatea = 'ppm';
            
            datuMultzoak.push({
                label: 'Pultsua (ppm)',
                data: datuak,
                borderColor: '#fd7e14',
                backgroundColor: 'rgba(253, 126, 20, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            });
            datuMultzoak.push(lortuRegresioLerroa(datuak, 'Joera (Pultsua)', '#d4660a'));
            eguneratuEstatistikaPanela(datuak, unitatea);
        }
        
        if (nireGrafika) {
            nireGrafika.destroy();
        }
        
        nireGrafika = new Chart(ctx, {
            type: 'line',
            data: {
                labels: etiketak,
                datasets: datuMultzoak
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }

    // Regresio linealeko lerroa kalkulatzeko funtzioa (Karratu txikien metodoa)
    function lortuRegresioLerroa(datuak, etiketa, kolorea) {
        const n = datuak.length;
        if (n < 2) return { label: etiketa, data: datuak, showLine: false };

        let batX = 0, batY = 0, batXY = 0, batX2 = 0;
        for (let i = 0; i < n; i++) {
            batX += i;
            batY += datuak[i];
            batXY += i * datuak[i];
            batX2 += i * i;
        }

        const malda = (n * batXY - batX * batY) / (n * batX2 - batX * batX);
        const jatorria = (batY - malda * batX) / n;

        const regresioPuntuak = datuak.map((_, i) => malda * i + jatorria);

        return {
            label: etiketa,
            data: regresioPuntuak,
            borderColor: kolorea,
            borderWidth: 2,
            borderDash: [5, 5], 
            fill: false,
            pointRadius: 0, 
            tension: 0
        };
    }

    // Estatistika panela eguneratzeko funtzioa
    function eguneratuEstatistikaPanela(datuak1, unitatea1, etiketa1 = '', datuak2 = null, etiketa2 = '') {
        const panela = $('#estatistika-panela');
        if (!panela.length) return;

        let html = '';

        const kalkulatu = (d) => {
            const iragazita = d.filter(v => !isNaN(v) && v !== null);
            if (iragazita.length === 0) return null;
            const batura = iragazita.reduce((a, b) => a + b, 0);
            return {
                batazbestekoa: (batura / iragazita.length).toFixed(1),
                maximoa: Math.max(...iragazita),
                minimoa: Math.min(...iragazita),
                kopurua: iragazita.length
            };
        };

        const e1 = kalkulatu(datuak1);
        if (e1) {
            html += `
                <div class="estat-txartela">
                    <div class="estat-izenburua">${etiketa1 ? etiketa1 + ' ' : ''}Batez bestekoa</div>
                    <div class="estat-balioa">${e1.batazbestekoa}<span class="estat-unitatea">${unitatea1}</span></div>
                </div>
                <div class="estat-txartela">
                    <div class="estat-izenburua">${etiketa1 ? etiketa1 + ' ' : ''}Maximoa</div>
                    <div class="estat-balioa">${e1.maximoa}<span class="estat-unitatea">${unitatea1}</span></div>
                </div>
                <div class="estat-txartela">
                    <div class="estat-izenburua">${etiketa1 ? etiketa1 + ' ' : ''}Minimoa</div>
                    <div class="estat-balioa">${e1.minimoa}<span class="estat-unitatea">${unitatea1}</span></div>
                </div>
            `;
        }

        if (datuak2) {
            const e2 = kalkulatu(datuak2);
            if (e2) {
                html += `
                    <div class="estat-txartela">
                        <div class="estat-izenburua">${etiketa2} Batez bestekoa</div>
                        <div class="estat-balioa">${e2.batazbestekoa}<span class="estat-unitatea">${unitatea1}</span></div>
                    </div>
                `;
            }
        }

        panela.html(html);
    }

    // PDF Deskargatu logika html2pdf erabiliz
    $('#btn-deskargatu-pdf').on('click', function() {
        const element = document.getElementById('pdf-eremua');
        const alerta = $('#alerta-eremua');
        
        // Hide UI controls using CSS display tracking or html2pdf ignore property 
        // We set data-html2canvas-ignore="true" in PHP on non-printable elements
        
        alerta.html('<div class="kargatzen-mezua">PDF-a sortzen ari da, itxaron momentu bat mesedez...</div>');
        
        const dateObj = new Date();
        const dateStr = dateObj.getFullYear() + "-" + (dateObj.getMonth()+1) + "-" + dateObj.getDate();
        
        const opt = {
            margin:       15,
            filename:     'grafika_' + dateStr + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        // Output generating a blob and sending it via AJAX to pdf_sortu.php
        html2pdf().set(opt).from(element).output('blob').then(function(pdfBlob) {
            let formData = new FormData();
            formData.append('pdf', pdfBlob, opt.filename);
            
            // Send patient ID if available (from global context)
            if (typeof paziente_id !== 'undefined' && paziente_id !== 'null') {
                formData.append('paziente_id', paziente_id);
            }
            
            $.ajax({
                url: pdfEndpoint,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        let res = JSON.parse(response);
                        if(res.success) {
                            alerta.html('<div class="alerta alerta-arrakasta">' + res.msg + ' <b><a href="../' + res.url + '" target="_blank">Egin klik hemen ikusteko</a></b></div>');
                            
                            // Auto-download to the local OS file system
                            const link = document.createElement('a');
                            link.href = '../' + res.url;
                            link.download = res.filename;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        } else {
                            alerta.html('<div class="alerta alerta-errorea">' + res.error + '</div>');
                        }
                    } catch(e) {
                        alerta.html('<div class="alerta alerta-errorea">Errorea zerbitzariaren erantzunean (' + e.message + ').</div>');
                    }
                },
                error: function(xhr, status, error) {
                    alerta.html('<div class="alerta alerta-errorea">Errorea PDF-a gordetzean (' + error + '). Egiaztatu pdf_bezero_txostenak/ karpeta.</div>');
                }
            });
        });
    });
});

