(function($) {
  $(document).ready( function() {
    $('.site-inner .menu-item-1364').toggle();
    $('.site-inner .menu-item-2225').toggle();
    $('.menu.accord li').children('ul').toggle();//.mobile-menu-wrap.menu.accord
    $('.menu.accord li:has(ul)').prepend('<div class="expand-button plus"></div>');
    $('.expand-button').click(function(){
      $(this).toggleClass('plus');
      $(this).toggleClass('minus');
      $(this).parent().children('ul').slideToggle();
    });
  });
})(jQuery)