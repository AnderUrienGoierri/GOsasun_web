// Carouselaren logika
$(document).ready(function() {
    let currentSlide = 0;
    const slides = $('.ezaugarri-txartela');
    const totalSlides = slides.length;
    const container = $('.ezaugarriak-container');

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        const offset = - (currentSlide * (100 / totalSlides));
        container.css('transform', `translateX(${offset}%)`);
    }

    // 3 segunduoro aldatu ezaugarri txartelak
    setInterval(nextSlide, 3000);
});
