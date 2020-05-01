jQuery(document).ready(function( $ ) {
   // Back to top button
   
  $('.back-to-top').click(function(){
    $('html, body').animate({scrollTop : 0},1500, 'easeInOutExpo');
    return false;
  });

  // Header fixed on scroll
  $(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
      $('#header').addClass('header-scrolled');
    } else {
      $('#header').removeClass('header-scrolled');
    }
  });

  if ($(window).scrollTop() > 100) {
    $('#header').addClass('header-scrolled');
  }

  // Real view height for mobile devices
  if (window.matchMedia("(max-width: 767px)").matches) {
    $('#intro').css({ height: $(window).height() });
  }

    // Mobile Navigation
    // if ($('#nav-menu-container').length) {
    //   var $mobile_nav = $('#nav-menu-container').clone().prop({
    //     id: 'mobile-nav'
    //   });
    //   $mobile_nav.find('> ul').attr({
    //     'class': '',
    //     'id': ''
    //   });
    //   $('body').append($mobile_nav);
    //   $('body').prepend('<button type="button" class="open_btn_alt"><i class="ti-menu"></i></button>');
    //   $('body').append('<div id="mobile-body-overly"></div>');
    //   $('#mobile-nav').find('.menu-has-children').prepend('<i class="fa fa-chevron-down"></i>');
  
    //   $(document).on('click', '.menu-has-children i', function(e) {
    //     $(this).next().toggleClass('menu-item-active');
    //     $(this).nextAll('ul').eq(0).slideToggle();
    //     $(this).toggleClass("fa-chevron-up fa-chevron-down");
    //   });
  
    //   $(document).on('click', '.open_btn_alt', function(e) {
    //     $('body').toggleClass('mobile-nav-active');
    //     $('.open_btn_alt i').toggleClass('fa-times fa-bars');
    //     $('#mobile-body-overly').toggle();
    //   });
  
    //   $(document).click(function(e) {
    //     var container = $("#mobile-nav, #mobile-nav-toggle");
    //     if (!container.is(e.target) && container.has(e.target).length === 0) {
    //       if ($('body').hasClass('mobile-nav-active')) {
    //         $('body').removeClass('mobile-nav-active');
    //         $('#mobile-nav-toggle i').toggleClass('fa-times fa-bars');
    //         $('#mobile-body-overly').fadeOut();
    //       }
    //     }
    //   });
    // } else if ($("#mobile-nav, #mobile-nav-toggle").length) {
    //   $("#mobile-nav, #mobile-nav-toggle").hide();
    // }
  
    // Smooth scroll for the menu and links with .scrollto classes
    $('.nav-menu a, #mobile-nav a, .scrollto').on('click', function() {
      if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
        var target = $(this.hash);
        if (target.length) {
          var top_space = 0;
  
          if ($('#header').length) {
            top_space = $('#header').outerHeight();
  
            if( ! $('#header').hasClass('header-fixed') ) {
              top_space = top_space - 20;
            }
          }
  
          $('html, body').animate({
            scrollTop: target.offset().top - top_space
          }, 1500, 'easeInOutExpo');
  
          if ($(this).parents('.nav-menu').length) {
            $('.nav-menu .menu-active').removeClass('menu-active');
            $(this).closest('li').addClass('menu-active');
          }
  
          if ($('body').hasClass('mobile-nav-active')) {
            $('body').removeClass('mobile-nav-active');
            $('#mobile-nav-toggle i').toggleClass('fa-times fa-bars');
            $('#mobile-body-overly').fadeOut();
          }
          return false;
        }
      }
    })



    // Carousel

    jQuery(".owl-carousel").owlCarousel({
      dots:true,
      center: true,
      loop:true,
      margin:10,
      responsiveClass:true,
      autoplay: true,
      autoplayTimeout: 5000,
      items: 3,
      responsiveClass:true,
    responsive:{
        0:{
            items:1,
           
        },
        600:{
            items:3,
            dots:true,
          
        },
        1000:{
            items:3,
            dots:true,
           
        }
      }
      
  });
});




//faq accordion
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function () {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  });
}


$('.open_btn').on('click', function(){
  $('aside').toggleClass('slim');
  $('.main_content').toggleClass('zero_padding');
});
