(function($){
	$(document).ready(function(){
		$('#search-by-code').submit(function(e){
			e.preventDefault();

			$.ajax({
				url: 'index.php?option=com_ajax&module=search_by_code&format=json&method=getRealtyItem',
				type: 'GET',
				data: $(this).serialize(),
				success: (response) => {
					window.location = response.data;
				}
			})
		})
	});
})(jQuery);