// Swiper.js Initialization — Globaliti Esport Badung

document.addEventListener('DOMContentLoaded', function () {

  // Roster Swiper — Player carousel per game
  const rosterSwiper = new Swiper('.roster-swiper', {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: false,
    pagination: {
      el: '.roster-swiper .swiper-pagination',
      clickable: true
    },
    navigation: {
      nextEl: '.roster-swiper .swiper-button-next',
      prevEl: '.roster-swiper .swiper-button-prev'
    },
    breakpoints: {
      480: { slidesPerView: 2, spaceBetween: 15 },
      768: { slidesPerView: 3, spaceBetween: 20 },
      1024: { slidesPerView: 4, spaceBetween: 25 }
    }
  });

  // Sponsor Swiper — Auto-scroll infinite
  const sponsorSwiper = new Swiper('.sponsor-swiper', {
    slidesPerView: 2,
    spaceBetween: 30,
    loop: true,
    autoplay: {
      delay: 2000,
      disableOnInteraction: false
    },
    speed: 800,
    breakpoints: {
      480: { slidesPerView: 3 },
      768: { slidesPerView: 4 },
      1024: { slidesPerView: 5 }
    }
  });

});
