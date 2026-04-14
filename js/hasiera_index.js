// Carouselaren logika modua
$(document).ready(function() {
    let currentSlide = 0;
    const slides = $('.ezaugarri-txartela');
    const totalSlides = slides.length;
    const wrapper = $('.ezaugarriak-wrapper');

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        
        // Kalkulatu posizioa (txartela + gap)
        const slideWidth = 320; // Txartelaren zabalera (300) + gap (20)
        wrapper.scrollTo({
            left: currentSlide * slideWidth,
            behavior: 'smooth'
        });
    }

    // 4 segunduoro aldatu ezaugarri txartelak
    setInterval(nextSlide, 4000);
});
