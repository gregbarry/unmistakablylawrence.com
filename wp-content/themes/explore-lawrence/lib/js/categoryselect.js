(function($) {
	$(window).load( function() {
		$('.category_select').change( function() {
			var cat_id = $(this).val();
			$.ajax({
				url: 'https://unmistakablylawrence.com/simpleview/getSubcategories.php',
				type: 'post',
				data: 'cat_id=' + cat_id,
				dataType: 'json',
				success: function(json) {
					$(".sub-categories option").remove();
					$(".sub-categories").append('<option value="">All</option>');
					$.each(json.subcatsJson, function(idx, obj) {
						if(cat_id == obj.cat_id || cat_id == '')
							$(".sub-categories").append('<option value="'+ obj.sub_cat_id +'">'+ obj.name +'</option>');
					});
				}
			});
		});
	});
})(jQuery)