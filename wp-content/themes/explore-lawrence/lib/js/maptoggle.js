(function($) {
	function toggleMap(mapcat){
		var maps = $('.maps');
			maps.css( "zIndex", -10 );
		if($('#map'+mapcat).length > 0 ) {
			$('#map'+mapcat).css( "zIndex", 0 );
		} else {
			$('#map0').css( "zIndex", 0 );
		}
	}
	$(window).load( function() {
		toggleMap(0);
		$('.nearlink').click( function() {
			toggleMap($(this).attr('data-cat'));
		});
	});
})(jQuery)