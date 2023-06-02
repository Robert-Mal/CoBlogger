import Swiper from './libs/swiper-bundle.min';

const swiper = new Swiper('.swiper', {
    slidesPerView: 'auto',
    spaceBetween: '10',
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});
