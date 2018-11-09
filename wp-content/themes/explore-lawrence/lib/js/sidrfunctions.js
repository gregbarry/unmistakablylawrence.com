(function($) {
  $(window).load(function() {
    function setup() {
      if ($(window).width() > 769) {
      } else {
        if ($(window).width() <= 640) {
          mobile_menu();
        }
      }
    }

    setup();
  $(document).ready( function() {
    $('.arrow').remove();
    $('.sub-arrow').remove();
    $('.sub-sub-arrow').remove();
    $('.mobile-menu-wrap li').children('ul').toggle();//.mobile-menu-wrap.menu.accord
    $('.mobile-menu-wrap li:has(ul)').prepend('<div class="mobile-expand-button orange"><i class="fa fa-plus"></i></div>')
    $('.mobile-expand-button').click(function(){
      console.log('click');
      $(this).toggleClass('orange').children().toggleClass('fa-plus');
      $(this).toggleClass('blue').children().toggleClass('fa-minus');
      $(this).parent().children('ul').slideToggle();
    });
  });
    $(window).resize(function() {
      setup();
    });
    
      function accordion_open_close(obj, event, depth, arrow) {
/*
      var h = $(obj).height();
      var hh = $(obj).parent().children('ul').first().height();
      var hhh = $(obj).parents('.sub-menu').height();
      var hhhh = $(obj).parents('.sub-sub-menu').height();

      //console.log(hhh);

      if ($(obj).parent().hasClass('open')) {

        $(obj).parent().removeClass('open');

        $(obj).children('i', arrow).addClass('fa-angle-double-right').removeClass('fa-angle-double-down');

        $(obj).parent().height(h);

        if ($(obj).parent().hasClass('sub-item')) {

          var xh = $(obj).parents('.sub-menu').height();

          $(obj).parents('.menu-item').height( xh + h );

        }

      } else {

        $(depth, '.sidr').removeClass('open').height(h);

        //console.log('this');

        $(obj).parent().addClass('open');

        $('i', arrow).removeClass('fa-angle-double-down').addClass('fa-angle-double-right');

        $(obj).children('i', arrow).removeClass('fa-angle-double-right').addClass('fa-angle-double-down');

        $(obj).parent().height(h + hh);

        if ($(obj).parent().hasClass('sub-item')) {

          //console.log('subsub');

          var xh = $(obj).parents('.sub-menu').height();

          $(obj).parents('.menu-item').height( xh + hh + h );

        } else if( $(obj).parent().hasClass('sub-sub-item') ) {
          var xh = $(obj).parents('.sub-sub-menu').height();

          $(obj).parents('.menu-item').height( xh + hhh + hh + h );        
        
        }

      }
*/
    }

    

    function mobile_menu() {

    //console.log('mobile-menu init');

    $('.mobile-menu-button').sidr();

    $('.no-link').click( function(e) {

      e.preventDefault();

      $(this).parent().children('.arrow').click();

    });
    $('.close').click( function(e) {

      e.preventDefault();

      $.sidr('close', 'sidr');

    });

   /* $('.sub-sub-arrow').click( function(e) {

      e.preventDefault();

      accordion_open_close(this, e, '.sub-sub-item', '.sub-sub-arrow');

    });    
    
    $('.sub-arrow').click( function(e) {

      e.preventDefault();

      accordion_open_close(this, e, '.sub-item', '.sub-arrow');

    });

    $('.arrow').click( function(e) {

      //console.log('click');

      e.preventDefault();

      accordion_open_close(this, e, '.menu-item', '.arrow');

    });    */
    $('.site-container').on('touchstart click', function(e) {

      if ($('body').hasClass('sidr-open')) {

        e.preventDefault();

        $.sidr('close', 'sidr');

      }

    });

  }
  mobile_menu()
});
})(jQuery)
