// ==================================================
// * Project Name   :  Coinpay - Crypto Currency Site Template.
// * File           :  JS Base
// * Version        :  1.0.0
// * Author         :  XpressBuddy (https://themeforest.net/user/xpressbuddy/portfolio)
// * Developer      :  webrok (https://www.fiverr.com/webrok?up_rollout=true)
// ==================================================

(function($) {
  "use strict";

  // Back To Top - Start
  // --------------------------------------------------
  $(window).scroll(function() {
    if ($(this).scrollTop() > 200) {
      $('.backtotop:hidden').stop(true, true).fadeIn();
    } else {
      $('.backtotop').stop(true, true).fadeOut();
    }
  });
  $(".scroll").on('click', function() {
    $("html, body").animate({scrollTop: 0}, 0);
    return false; 
  });
  // Back To Top - End
  // --------------------------------------------------

  // Preloader and AOS Animation Load - Start
  // --------------------------------------------------
  $(window).on("load", function() {
    $("#preloader").hide();
    AOS.init({
      once: true,
    });
  });
  // Preloader and AOS Animation Load - End
  // --------------------------------------------------

  // Mobile Menu Button Class Chnage - Start
  // --------------------------------------------------
  $(".mobile_menu_btn").on('click', function(){
    $(".mobile_menu_btn > i").toggleClass("active");
  });
  // Mobile Menu Button Class Chnage - End
  // --------------------------------------------------

  // Sticky Header - Start
  // --------------------------------------------------
  $(window).on('scroll', function () {
    if ($(this).scrollTop() > 0) {
      $('.site_header').addClass("sticky")
    } else {
      $('.site_header').removeClass("sticky")
    }
  });
  // Sticky Header - End
  // --------------------------------------------------

  // Scrollspy Effect - Start
  // --------------------------------------------------
  $('.scrollspy_btn[href^="#"]').on('click', function (event) {
    event.preventDefault();
    var targetClass = $(this).attr('href');
    var $targetElement = $(targetClass);
    if ($targetElement.length) {
      var targetPosition = $targetElement.offset().top;
      $('html, body').animate(
      {
        scrollTop: targetPosition,
      },
      0
      );
    }
  });
  // Scrollspy Effect - End
  // --------------------------------------------------

  // Videos & Images popup - Start
  // --------------------------------------------------
  $('.video_popup_block').magnificPopup({
    type: 'iframe',
    preloader: false,
    removalDelay: 160,
    mainClass: 'mfp-fade',
    fixedContentPos: false
  });

  $('.zoom-gallery').magnificPopup({
    delegate: '.popup_image',
    type: 'image',
    closeOnContentClick: false,
    closeBtnInside: false,
    mainClass: 'mfp-with-zoom mfp-img-mobile',
    gallery: {
      enabled: true
    },
    zoom: {
      enabled: true,
      duration: 300,
      opener: function(element) {
        return element.find('img');
      }
    }
  });
  // Videos & Images popup - End
  // --------------------------------------------------

  // Odometer Counter - Start
  // --------------------------------------------------
  jQuery('.odometer').appear(function (e) {
    var odo = jQuery(".odometer");
    odo.each(function () {
      var countNumber = jQuery(this).attr("data-count");
      jQuery(this).html(countNumber);
    });
  });
  // Odometer Counter - End
  // --------------------------------------------------

  // multy count down - start
  // --------------------------------------------------
  $('.countdown_timer_block').each(function(){
    $('[data-countdown]').each(function() {
      var $this = $(this), finalDate = $(this).data('countdown');
      $this.countdown(finalDate, function(event) {
        var $this = $(this).html(event.strftime(''
          + '<li class="days_count"><span>%D</span><small>Days</small></li>'
          + '<li class="hours_count"><span>%H</span><small>Hours</small></li>'
          + '<li class="minutes_count"><span>%M</span><small>Minutes</small></li>'
          + '<li class="seconds_count"><span>%S</span><small>Seconds</small></li>'));
      });
    });
  });
  // multy count down - End
  // --------------------------------------------------

  // Copy Button - Start
  // --------------------------------------------------
  $(".copy_btn").on("click", function () {
    var $this = $(this);
    $this.text("Copied!");
    setTimeout(function() {
      $this.text("Copy");
    }, 2000);

    console.time("time1");
    var temp = $("<textarea>");
    $("body").append(temp);
    temp.val($(".code mark").text());
    temp.select();
    try {
      document.execCommand("copy");
    } catch (err) {
      console.error("Copy failed", err);
    }
    temp.remove();
    console.timeEnd("time1");
  });
  // Copy Button - End
  // --------------------------------------------------

  // Blog Carousel - Start
  // --------------------------------------------------
  const blogCarouselBlock = new Swiper('.blog_carousel_block', {
    loop: true,
    speed: 1000,
    grabCursor: false,
    slidesPerView: 1,
    autoplay: {
      delay: 4000,
      disableOnInteraction: true,
    },
    pagination: {
      el: ".bc-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".bc-button-next",
      prevEl: ".bc-button-prev",
    },
  });
  // Blog Carousel - End
  // --------------------------------------------------

  // Partner Carousel - Start
  // --------------------------------------------------
  const partnerLogoCarousel = new Swiper('.partner_logo_carousel', {
    loop: true,
    speed: 5000,
    spaceBetween: 30,
    grabCursor: false,
    centeredSlides: true,
    allowTouchMove: false,
    autoplay: {
      delay: 0,
      disableOnInteraction: true,
    },
    breakpoints: {
      0: {
        slidesPerView: 2,
      },
      991: {
        slidesPerView: 3,
      },
      1025: {
        slidesPerView: 4,
      },
      1200: {
        slidesPerView: 5,
      },
    },
  });
  // Partner Carousel - End
  // --------------------------------------------------

  // Roadmap Carousel and Flex Effect - Start
  // --------------------------------------------------
  $('.ico_roadmap_flexbox .roadmap_block').on('mouseover', function () {
    $('.ico_roadmap_flexbox .roadmap_block').removeClass('active');
    $(this).addClass('active');
  });

  const memeRoadmapCarousel = new Swiper('.meme_roadmap_carousel', {
    loop: true,
    speed: 1000,
    spaceBetween: 30,
    grabCursor: false,
    centeredSlides: true,
    autoplay: {
      delay: 3000,
      disableOnInteraction: true,
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
      },
      991: {
        slidesPerView: 2,
      },
      1025: {
        slidesPerView: 3,
      },
    },
  });
  // Roadmap Carousel and Flex Effect - End
  // --------------------------------------------------

  // Content Ticker Carousel - Start
  // --------------------------------------------------
  const contentTickerCarousel = new Swiper('.content_ticker_carousel', {
    loop: true,
    speed: 10000,
    spaceBetween: 60,
    grabCursor: false,
    centeredSlides: true,
    allowTouchMove: false,
    autoplay: {
      delay: 0,
      disableOnInteraction: true,
    },
  });
  // Content Ticker Carousel - End
  // --------------------------------------------------

  // Roadmap Block Active Effect - Start
  // --------------------------------------------------
  const $cards = $('.roadmap_carousel .swiper-slide .roadmap_block');
  let activeCard = null;
  $cards.on('mouseover', function () {
    if (activeCard) {
      $(activeCard).removeClass('active');
    }
    $(this).addClass('active');
  });
  $cards.on('mouseleave', function () {
    activeCard = this;
  });
  // Roadmap Block Active Effect - End
  // --------------------------------------------------


  // Language Select - Start
  // --------------------------------------------------
  const locales = ["en-GB","ar-SA","zh-CN","de-DE","es-ES","fr-FR","hi-IN","it-IT","in-ID","ja-JP","ko-KR","nl-NL","no-NO","pl-PL","pt-BR","sv-SE","fi-FI","th-TH","tr-TR","uk-UA","vi-VN","ru-RU","he-IL"];

  function getFlagSrc(countryCode) {
    return /^[A-Z]{2}$/.test(countryCode)
    ? `https://flagsapi.com/${countryCode.toUpperCase()}/shiny/64.png`
    : "";
  }

  $(document).ready(function () {
    function setSelectedLocale(locale) {
      const intlLocale = new Intl.Locale(locale);

      const $dropdownContent = $("#language_dropdown > ul");
      $dropdownContent.empty();

      const otherLocales = locales.filter(loc => loc !== locale);
      $.each(otherLocales, function (index, otherLocale) {
        const otherIntlLocale = new Intl.Locale(otherLocale);
        const otherLangName = new Intl.DisplayNames([otherLocale], { type: "language" }).of(otherIntlLocale.language);

        const $listEl = $("<li>").html(`${otherLangName} <img src="${getFlagSrc(otherIntlLocale.region)}" />`);
        $listEl.val(otherLocale);

        $listEl.on("mousedown", function () {
          setSelectedLocale(otherLocale);
        });

        $dropdownContent.append($listEl);
      });

      $("#language_active_btn").html(`<span><img src="${getFlagSrc(intlLocale.region)}" /></span> <i class="fa-solid fa-angle-down"></i>`);
    }

    setSelectedLocale(locales[0]);

    const browserLang = new Intl.Locale(navigator.language).language;
    $.each(locales, function (index, locale) {
      const localeLang = new Intl.Locale(locale).language;
      if (localeLang === browserLang) {
        setSelectedLocale(locale);
      return false; // Break loop
    }
  });
  });
  // Language Select - End
  // --------------------------------------------------


})(jQuery);