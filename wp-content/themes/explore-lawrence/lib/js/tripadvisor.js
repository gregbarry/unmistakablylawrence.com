(function($) {
  $(window).load( function() {
    $('form[name=online-booking]').on( 'submit', function (event){
      event.preventDefault()
      var selection = $('#trip-select').find(":selected").val();
      /*var secondary_selection = "";
      selection.replace(" ", "_");
      if (selection == "Vacation_Rentals"){selection = "VacationRentals";}
      if (selection == "Bed_and_Breakfast"){selection = "Hotels"; secondary_selection = "-c2";}*/
      console.log(""+selection);
      var win = window.open('http://www.tripadvisor.com/'+selection, '_blank');
      if(win){
          //Browser has allowed it to be opened
          win.focus();
      }else{
          //Broswer has blocked it
          alert('Please allow popups for this site');
      }
      return false;
    });
  });
})(jQuery)