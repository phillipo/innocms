(function($) {
	
	$.fn.iModal = function(optionsUser) {
		
		$this = $(this);
		
		var defaults = {
			title : 'Ventana flotante',
			id : 'ui-modal-window',
			width : 900,
			height : 200,
			resizable: false,			
			content : '',
			buttons : {
					"Ok" : function() { $('#modal-window').dialog('close'); },
					"Cerrar" : function() { $('#modal-window').dialog('close'); }
			}
		};
		
		var empty = {};
		var options = $.extend(empty, defaults, optionsUser);
		
		var init = function(opt) {
			
			// Creamos la caja
			if ($('#ui-modal-window').length == 0) $('body').append('<div id="ui-modal-window"><div id="modal-window-body" class="span10"></div></div>');					
		
			// Rellenamos información
			$('#modal-window-body').load(opt.content);
			
			// Lanzamos cuadro de dialogo
			$('#ui-modal-window').dialog('close');
			$('#ui-modal-window').dialog(opt);

		};
		
		
		init(options);
		
	};
	
})(jQuery);