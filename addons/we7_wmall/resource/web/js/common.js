$(document).on('click', '#select-all', function(){
	var scope = $(this).data('scope');
	$(scope).find(':checkbox').prop('checked', $(this).prop('checked'));
});
