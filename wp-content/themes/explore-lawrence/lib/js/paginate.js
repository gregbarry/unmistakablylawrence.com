(function($) {

  /* var perPage = 10; */
  var perPage = $('.page-var').data('per-page');
  var scrollspeed = 700;
   //alert(perPage);
  function getPageNum(){
    var hashtag = parent.location.hash;
    var hashPageNum = 1; //default
    if ( hashtag != '') {
      hashPageNum = hashtag.split('-')[1];
    }
	showMap(hashPageNum);
    return hashPageNum;
  }
  
  function showMap(page){
    var maps = $('.maps');
    maps.css( "zIndex", -10 );
    $('#map'+page).css( "zIndex", 0 );
    if (typeof mapInit == 'function') { 
      mapInit(page); 
    }
  }
  
  function updatePage() {
    var pagenum = getPageNum();
    if ( $('.listing-item').length ){
      var items = $('.listing-item').not('.featured');
    }
    if ( $('.event-listing-item').length ){
      var items = $('.event-listing-item');
    }
    if (pagenum != 1){
      $('.featured').css('display', 'none');
    }else{
      $('.featured').css('display', 'initial');
    }
    var showFrom = perPage * (pagenum - 1);
    var showTo = showFrom + perPage;
    items.hide().slice(showFrom, showTo).show();
    $('.pagination').pagination('drawPage', pagenum);
    // var target = $('#scroll-to');
    // $('html,body').animate({
      // scrollTop: target.offset().top - 50
    // }, scrollspeed);
  }
  
  window.onpopstate = function(e){
    updatePage();
  };

  
  $(window).load( function() {

    if ( $('.listing-item').length ){
      var items = $('.listing-item').not('.featured');
    }
    if ( $('.event-listing-item').length ){
      var items = $('.event-listing-item');
    }
    var itemCount = items.length;
    if (itemCount > perPage ) {
      var hashPageNum = getPageNum();
      var currentPage = 1;
      items.slice(perPage).hide();
      $(function() {
        $('.pagination').pagination({
            items: itemCount,
            itemsOnPage: perPage,
            cssStyle: 'light-theme',
            prevText: '<',
            nextText: '>',
            currentPage: getPageNum(),
            onPageClick: function(pageNumber) { // this is where the magic happens
            // someone changed page, lets hide/show trs appropriately
            var showFrom = perPage * (pageNumber - 1);
            var showTo = showFrom + perPage;

            items.hide() // first hide everything, then show for the new page
            .slice(showFrom, showTo).show();
                var target = $('#scroll-to');
                $('html,body').animate({
                  scrollTop: target.offset().top - 50
                }, scrollspeed);
        }
        });
        updatePage();
        $('.pagination').pagination('drawPage', hashPageNum);
      });
    }
  });
})(jQuery)