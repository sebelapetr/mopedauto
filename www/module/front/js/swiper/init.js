var swiper = new Swiper(".swiper-photos", {
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    slidesPerView: 4,
    spaceBetween: 5,
    breakpoints: {
        550: {
            slidesPerView: 3,
            spaceBetween: 10
        },
        991: {
            slidesPerView: 4,
            spaceBetween: 5
        },
        1140: {
            slidesPerView: 3,
            spaceBetween: 15
        }
    }
});