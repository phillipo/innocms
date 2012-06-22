(function($) {
	
	$.fn.iPopFile = function(optionsUser) {
		
		$this = $(this);
		
		var defaults = {
				title : 'Soy un elemento informativo',		
				content : '',
				url: '/admin/filebrowser/info/??',
			};
			
		var empty = {};
		var options = $.extend(empty, defaults, optionsUser);
					
		
	
		var showPopFile = function(element, data) {
			
			$('#' + element).popover({
				'title' : 'Información',
				'content' : data,
				'trigger' : 'manual'
			});	
			
			$('#' + name).popover('hide');
			
		};
		
		var init = function(opt) {
		
			$this.live('hover', function(e) {
				
				e.preventDefault();
				
				// Datos atributo
				var name = $(this).attr('id');
				var id = getId(name);
				
				// AJAX
				var dataSend = {'file': id};			
				$.ajax({
					url: opt.url ,
					type: 'POST',
					dataType: 'json',
					data: dataSend,
					success: function(data) {			
						if (data.jResult == 'ok') {									
							showPopFile(name, data.jMessage);											
						} else if (data.jResult == 'failed') {
							alert(data.jMessage);
						} else {
							alert('Error desconocido');
						}					
						
					},
					complete: function(data) {
		
					},
					error: function() {
						alert('se ha producido un error');
					}
				});			
			
			});
			
			$this.live('click', function(e) {
				
				e.preventDefault();
				
				// Datos atributo
				var name = $(this).attr('id');			
				$('#' + name).popover('show');
				
			});
			
			$this.live('focusout', function(e) {
				
				e.preventDefault();
				
				// Datos atributo
				var name = $(this).attr('id');			
				$('#' + name).popover('hide');
				
			});		
			
		}
		
		init(options);
	
	}
	
	
})(jQuery);