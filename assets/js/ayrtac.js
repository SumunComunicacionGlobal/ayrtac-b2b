// Añade clase a body cuando se hace scroll
window.addEventListener("scroll", function() {
    if (window.scrollY > 80) {
        document.body.classList.add("scrolled");
    } else {
        document.body.classList.remove("scrolled");
    }
});

// Añade drag para los elementos con scroll horizontal
document.addEventListener('DOMContentLoaded', (event) => {
    const sliders = document.querySelectorAll('.is-style-group-horizontal-scroll');
    let isDown = false;
    let startX;
    let scrollLeft;
  
    // Añade el evento a cada slider
    sliders.forEach(slider => {
        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.classList.add('active');
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.classList.remove('active');
        });
        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.classList.remove('active');
        });
        slider.addEventListener('mousemove', (e) => {
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 3; //scroll-fast
            slider.scrollLeft = scrollLeft - walk;
            console.log(walk);
        });
    });
  
});


document.addEventListener('DOMContentLoaded', function () {
    // Registrar el plugin ScrollTrigger
    gsap.registerPlugin(ScrollTrigger);

    // Animar elementos con la clase .animate al entrar en el viewport
    gsap.utils.toArray('.wp-block-cover__inner-container, .wp-block-media-text__content, .is-layout-grid > * > *, #colophon .safe-svg-cover').forEach(function (element) {
        gsap.from(element, {
            scrollTrigger: {
                trigger: element, // Elemento que activa la animación
                start: 'top 80%', // Inicia cuando el top del elemento está al 80% del viewport
                end: 'bottom 20%', // Termina cuando el bottom del elemento está al 20% del viewport
                toggleActions: 'play none none none', // Reproducir solo una vez
            },
            opacity: 0,
            y: 50,
            duration: 0.3,
        });
    });

    // Animar cada elemento .wc-block-product individualmente con un desfase
    gsap.utils.toArray('.wc-block-product, .wp-block-button, .wp-block-columns .wp-block-columns').forEach(function (product, index) {
        gsap.from(product, {
            scrollTrigger: {
                trigger: product, // Cada elemento tiene su propio trigger
                start: 'top 80%', // Inicia cuando el top del elemento está al 80% del viewport
                toggleActions: 'play none none none', // Reproducir solo una vez
            },
            opacity: 0,
            y: 50,
            duration: 0.3,
            delay: index % 3 * 0.1, // Desfase de 100ms para cada fila de 3 elementos
        });
    });
});

// Añade clases al body cuando interactuamos con el menú
jQuery(function($) {
    $('li.mega-menu-item').on('open_panel', function() {
        $('body').addClass('menu-open');
    });
});

jQuery(function($) {
    $('li.mega-menu-item').on('close_panel', function() {
        $('body').removeClass('menu-open');
    });
});

// Añade scroll suave al aplicar filtro
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilterParam = Array.from(urlParams.keys()).some(key => key.startsWith('filter_'));

    if (hasFilterParam) {
        const targetElement = document.querySelector('.wp-block-woocommerce-product-collection');
        if (targetElement) {
            setTimeout(() => {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }, 300); // delay
        }
    }
});

// Cerrar filtro al pulsar en el botón de cerrar #cerrar-filtro
jQuery(function($) {
    $('a[href="#cerrar-filtro"]').on('click', function(e) {
        e.preventDefault();
        $('body').removeClass('filter-open');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#filtro, a[href="#abrir-filtro"]').length) {
            $('body').removeClass('filter-open');
        }
    });
});

// Añade clase al body cuando se abre el filtro
jQuery(function($) {
    $('a[href="#abrir-filtro"]').on('click', function(e) {
        e.preventDefault();
        $('body').addClass('filter-open');
    });
});

// Añadir comportamiento hover a los links del mega menú tabbed
jQuery(function($) {
    $('.mega-menu-tabbed > ul > li:not(.mega-toggle-on) > a').hover(
        function() {
            $(this).trigger('click');
        }
    );
});