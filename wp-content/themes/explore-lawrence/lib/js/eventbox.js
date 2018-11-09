(function($){
  $(document).ready(function() {
    $('.event-thumb').click(function(){ $(this).colorbox({href:$(this).attr('src'), rel:$(this).attr('rel')}) });
  });
})(jQuery)