// Carouselaren logika: Zinta jarraia (conveyor belt)
$(document).ready(function() {
    const container = $('.ezaugarriak-container');
    if (container.length) {
        // Klonatu txartelak amaierarik gabeko begizta (infinite loop) sortzeko
        const cards = container.children();
        container.append(cards.clone());
    }
});
