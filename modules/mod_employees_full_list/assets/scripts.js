(function($){
	$(document).ready(function(){

		let employeesList = $('.employees-full-list-wrapper');
		
		$('.employees-full-list-show-more-button-wrapper .more-employees').on('click', function(){ //gets additional employee data objects to render based on <data-limit> value
			getActiveEmployees(employeesList);		
		});
	})

	/**
	 * [getActiveEmployees - method to fetch 'active' employees from database via AJAX request and append retrieved data to the list]
	 * @param  {[jQuery Object]} employeesList [jQuery object holding object of employees list wrapper]
	 * @return {[void]}
	 */
	function getActiveEmployees(employeesList)
	{
		$.ajax({
			url: 'index.php?option=com_ajax&module=employees_full_list&format=json&method=getActiveEmployees',
			method: 'GET',
			data: {
				limit: employeesList.data('limit'),
				offset: employeesList.data('offset'),
				id: employeesList.data('id'),
			},
			success: function(response){
				employeesList.append(response.data['html']);
				let offset = employeesList.data('offset');
				let limit = employeesList.data('limit');
				employeesList.data('offset', (offset + limit));
				if(response.data['last-batch'] == true){
					$('.employees-full-list-show-more-button-wrapper .more-employees').remove();
				}
			}
		})
	}
})(jQuery)