jQuery(function($){
	$('div.field-vimeo_video').each(function(){
		var span = $(this).find('span');
		var input = $(this).find('input.hidden');
		$(this).find('a.change').click(function(){
			input.css('display', 'block');
			input.attr('value', '');
			span.css('display', 'none');
		})
	})
});
