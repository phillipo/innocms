(function($) {
	
	$.fn.iAlert = function(optionsUser) {
		
		$this = $(this);
		
		var defaults = {
				title : 'Información',
				type : 'info',
				text: ''
				};
			
		var empty = {};
		var options = $.extend(empty, defaults, optionsUser);		

		var show = function(opt) {
			
			if (opt.type == '') {
				type = '';
			} else {
				type = ' alert-' + opt.type;
			}
			
			var message = '<div class="alert' + type + '"><button class="close" data-dismiss="alert">×</button><strong>' + opt.title + '</strong> ' + opt.text + '</div>';
			$this.prepend(message);			
			
		};
		
		show(options);
			
	};
		    
		    
})(jQuery);