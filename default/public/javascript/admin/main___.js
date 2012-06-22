var tmp = '';


/**
 *  COMMON FUNCTIONS
 */

// Check all elements
function jqCheckAll(status) { 

	$("form#ui-form input:checkbox").each(function() { 
        this.checked = status;
    }); 
	
}

//List of checked items 
function getChecked() {
	// Elementos seleccionados
	var datos = [];
	$("input[name='listID[]']:checked").each(function() {
		datos.push($(this).val());
	});
	
	// Devolvemos elementos seleccionados
	return datos;	
}

// Get Limit for Textareas 
function getLimit(string) {	
	// Comprobamos que se envian datos	
	if (string == '' || string == undefined) return false;
	// Parametros
	var pattern = /limit(\d+)/i;
	var result = string.match(pattern);
	// Comprobamos resultado Regexp
	if (result != undefined || result != null) return parseInt(result[1]);
	else return 0;
}

// Extract a number from string
function getId(string) {
	// Comprobamos que se envian datos
	if (string == '' || string == undefined) return '';
	// Parametros
	var pattern = /\d/g;
	var result = string.match(pattern);
	// Comprobamos resultado Regexp
	if (result != undefined && result != null) return result.join('');
	else return false;
}

//Extract a number from string
function getName(string) {
	// Comprobamos que se envian datos
	if (string == '' || string == undefined) return '';
	// Parametros
	var pattern = /\w/g;
	var result = string.match(pattern);
	// Comprobamos resultado Regexp
	if (result != undefined && result != null) return result.join('');
	else return false;
}

// Extracts and set limiters 
function setLimiter(obj) {
	
	// Parametros
	var limit = getLimit(obj.attr('class'));
	var text = obj.val();
	var message = $('#' + obj.attr('id') + 'Limit');
	
	// Comprobamos longitud
	if (text.length > limit) {
		text = text.substr(0,limit);
		obj.val(text);
		message.css('color','#f00');
	} else {
		message.css('color','');
	}
	
	// Actualizamos limites por pantalla
	message.text(' ( ' + text.length + ' / ' + limit + ' caracteres escritos )');

}

/* Scripts common */
function common() {
	
	// Gestion de tablas
	if ($('table.table').length > 0) {
		
		/* Default class modification */
		$.extend( $.fn.dataTableExt.oStdClasses, {
			"sSortAsc": "header headerSortDown",
			"sSortDesc": "header headerSortUp",
			"sSortable": "header"
		} );		
		
		$('table.table').dataTable({
			'bPaginate': false,
			'bInfo': false,
			'bFilter': false,
			'bLengthChange': false,
			'bAutoWidth': true,
			'aoColumnDefs': [{ "bSortable": false, "aTargets": [ 0 ] }]
		});
		
	}
	
	
	// Las opciones por defecto aparecen ocultas
	if ($('#optionsList').length > 0) {
		$('#optionsList').hide();	
	}

	// Metodo para mostrar un calendario jQuery
	if ($('.ui-datePicker').length > 0) {
	    $('.ui-datePicker').datetimepicker({
			regional: 'es',	
			showButtonPanel: true,
	    	numberOfMonths: 3,			
	    	dateFormat: 'dd-mm-yy',
			timeFormat: 'hh:mm:ss',	
			showSecond: true,
			stepHour: 1,
			stepMinute: 5,
			addSliderAccess: true,
			sliderAccessArgs: { touchonly: true }			
		});
	}
	
	// Metodo para activar/desactivar todos checkboxes de una lista
	if ($('.ui-checkAll').length > 0) {
		$('.ui-checkAll').live('click', function(){
		    var checked_status = this.checked; 
			jqCheckAll(checked_status);
		});		
	}

	// Muestra / Oculta opciones en los listados
	if ($('form#ui-form').length > 0) { 
		$("form#ui-form input:checkbox").live('click', function() {
		
			var hasChecked = false;
			
			$("form#ui-form input:checkbox").each(function() { 
		        if (this.checked == true) hasChecked = true;
		    }); 
			
			if (hasChecked == true) {
				$('#optionsList').show();
			} else {
				$('#optionsList').hide();
			}
		
		});
	}
	
	// Metodo para mostrar un WSINGIN en un campo de texto
	if ($('textarea.ui-tinyContent').length > 0) {
		
		tinyMCE.init({
			language : 'es',
			mode: 'specific_textareas',
	    	entity_encoding : "numeric", 
	    	theme: 'advanced',
	    	plugins: 'inlinepopups,fullscreen',
	    	height: 300,
	        editor_selector : 'ui-tinyContent',
	        // Skin options
	        skin : "cirkuit",
	    	// Theme options
	    	theme_advanced_resizing: false,
	    	theme_advanced_statusbar_location: 'bottom',
	    	theme_advanced_toolbar_location: 'top',
	    	// Theme buttons
	        	theme_advanced_buttons1 : "formatselect,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,outdent,indent,|,link,unlink,anchor,image",
	        	theme_advanced_buttons2 : "cut,copy,paste,|,undo,redo,|,code,preview,removeformat,|,forecolor,backcolor,|,charmap,|,fullscreen",
	        	theme_advanced_buttons3 : "",  			
			
		});
	    
	}	
	
	// Metodo para limitar caracteres de un area de texto
	if ($('textarea').length > 0 && $('textarea').hasClass('ui-limited')) {
					
		// Object
		var self = $('#' + $('textarea.ui-limited').attr('id'));
		
		// Inicializamos
		setLimiter(self);
		
		// Al pulsar actualizamos
		$('textarea').bind('paste keyup', function() {
			setLimiter(self);
		});
		
	}
	
	// Ventanas flotantes
	if ($('.ui-modal-launch').length > 0) {
	    	
		$('.ui-modal-launch').bind('click', function() {
			
			var id = getName($(this).attr('href'));			
			
			if ($(this).hasClass('ui-upload-browser')) {
				
				title = 'Navegador de archivos';
				//$().load('/admin/filebrowser/remote/images/'),
				var str = $('<div id="vamosaverloquepasa">Cargando contenidos...</div>');
				
				bootbox.dialog(
					str,
					[
						{
		                    'label' : 'Seleccionar!',
		                    'class' : 'btn-primary',
		                    'callback': function() {
								var field = $('.bootbox input[type="radio"]:checked');
								$('#content_content_image_id').val(field.val());
								return true;
		                    }
		                },	
						{
		                    'label' : 'Cerrar',
		                    'class' : 'btn',
		                    'callback' : function() {
		                    	return true;
		                    }
		                }						
					],
					{
						'animate': false,
						'header':'Seleccione una imagen para asociar'
					}
				);
				
				$.get('/admin/filebrowser/remote/images/', function(data) {
					str.html(data);
				});				
				
					
			} else {
				title = 'Seleccione un archivo';
				content = '/';
			}
			
		});
				
	}
	
	// Altura de las pestañas
	if ($('.tabbable').length > 0) {
		if ($('.tabs-left').length > 0) {
			$('.nav-tabs').css('height','400px');
		}
	}
	
	
}

function pages() {
	
	
	// Metodo para gestionar redirecciones
	if ($('input[name="attributes[redirect_type]"]').length > 0) {
		
		// Ocultamos todos
		$('#ui-redirect-external').hide();
		$('#ui-redirect-internal').hide();		
		
		// Segun opcion mostramos
		$('input[name="attributes[redirect_type]"]').bind('click', function() {
		
			var value = $(this).val();
			
			// Ocultamos todos
			$('#ui-redirect-external').hide();
			$('#ui-redirect-internal').hide();
			
			// Mostramos segun valor
			$('#ui-redirect-' + value).show();
			
		});
		
	}
	
	// Slider en los templates
	if ($('#ui-slide-templates').length > 0) {
		
		// Parametros
		var nav = $('#ui-slide-templates').find('ul.ui-slider-nav');
		var nextBtn = nav.find('li.next');
		var prevBtn = nav.find('li.prev');
		
		// Ocultamos excedentes
		$('#ui-slide-templates').css('overflow', 'hidden');
		
		// Activamos slider
		$('#ui-slide-templates').find('ul.ui-slider-container').cycle({
			fx: 'scrollHorz',
			prev: prevBtn,
			next: nextBtn,
			easing:  'easeInOutBack',
			timeout: 0
		});		
				
	}
	
	// EDICION DE MODULOS EN PLANTILLAS
	
	// Metodo para ordenar los modulos en las plantillas
	if ($('.ui-sortable').length > 0) {
		
		$('.ui-sortable').each(function(){
			var sortList = $('#' + $(this).attr('id'));
			sortList.sortable();
		});
		
		/*
		var sortList = $('#' + $('.iu-sortable').attr('id'));
		alert($('.iu-sortable').attr('id'));
		sortList.sortable();
		*/
		
	}

	//************** MODULOS ************************
	
	// Metodo para añadir modulos en una zona
	$('.addModule').bind('click',function(e) {

		e.preventDefault();
		
		// Parametros
		var templateId = 1;
		var zoneId = getId($(this).attr('id'));
		var zoneList = $('#zone' + zoneId);			
		
		// AJAX
		var dataSend = {'templateId': templateId , 'zoneId': zoneId };	
	
		$.ajax({
			url: '/admin/module/create',
			type: 'POST',
			dataType: 'json',
			data: dataSend,
			success: function(data) {		
				// Parametros
				var moduleId = data.id;				
				// Añadimos en esa zona un nuevo item
				zoneList.append('<li id="module1" class="blockModule"><a id="editModule' + moduleId + '" class="button pill icon edit">Editar</a><a href="#"  id="removeModule' + moduleId + '" class="removeModule button pill icon trash">Borrar</a><a href="#" class="button pill icon edit">Ocultar</a></li>' + "\n");
				// Refrescamos el indice de ordenación
				$('.ui-sortable').sortable('refresh');								
			},
			error: function() {
				alert('se ha producido un error');
			}
		});
			
	});

	// Metodo para editar modulos de una zona
	$('li.blockModule .editModule').live('click', function(e) {
		
		e.preventDefault();
		
		var moduleId = getId($(this).attr('id'));
		// AJAX
		
	});	
	
	// Metodo para borrar modulos de una zona
	$('li.blockModule .removeModule').live('click', function(e) {
		
		e.preventDefault();
		// Parametros
		var self = $(this);
		var moduleId = getId($(this).attr('id'));
		
		// AJAX
		var dataSend = {'moduleId':moduleId};
		$.ajax({
			url: '/admin/module/remove',
			type: 'POST',
			dataType: 'json',
			data: dataSend,			
			success: function(data) {
				self.parent('li').remove();
			},
			error: function(data) {
				alert('Se ha producido un error');
			}
			
		});
		
	});
	
	
	
	
	
}


/* Scripts for CONTENIDOS / CATEGORIAS / SETS */
function contents() {

	/************** FICHAS *******************/
    
	// Metodo para convertir un texto en formato SLUG
	if ($('.ui-slug').length > 0) {
		$('.ui-slug').slugIt( { events: 'keypress keyup change',output: '.ui-slug-result' } );
	}
		
	/************** SETS **************/
	
	
	// Ordenacion de items dentro de un set
	
	if ($('.ui-sortfiles').length > 0) {
			
		var id = $('.ui-sortfiles').attr('id');
		
		$( "#" + id ).sortable({
			placeholder: "ui-state-highlight",
			stop: function() {
				// Enviamos peticion para actualizar listado
				var listItems = new Array();
				$('ul#mySortable li').each(function() {					
					listItems.push(getId($(this).attr('id')));					
				});
				
				$('input[name="attributes[files]"]').val(listItems.join(','));
				
				
			}
		});
		
		$( "#" + id ).disableSelection();
		
	}
	
			
	/************** SUBIR FICHEROS *************/
	
	if ($('.ui-uploadIt').length > 0) {
				
		$('.ui-uploadIt').hide();
		
		var dataFile = new FormData();		
		
		$('#attributes_image').bind('change', function(){
					
			var file = $(this)[0].files[0];
			
			if (file) {
				var fileSize = 0;
				if (file.size > 1024 * 1024) {
					fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
				} else {
					fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
				}
			
				$('#uploadInfo').html('<hr><h4>Información</h4><dl class="dl-horizontal">' + 
					'<dt>Tamaño:</dt><dd> ' + fileSize + ' ( max: 2MB )</dd>' +
					'<dt>Tipo:</dt><dd>' + file.type + '</dd>' +
					'</dl>'
				
				);
				
				dataFile.append('file',file);
				
				// Mostramos botón upload
				if (file.name != '') {
					$('.ui-uploadIt').show();
				} else {
					$('.ui-uploadIt').hide();
				}				
				
			}			
			
		});
		
		// Al salid de la casilla
		$('#attributes_image').bind('focusout', function() {
			
			var file = $(this)[0].files[0];
			if (file.name != '') {
				$('.ui-uploadIt').show();
			} else {
				$('.ui-uploadIt').hide();
			}
			
		});
		
		$('.ui-uploadIt').bind('click', function(e) {
			
			e.preventDefault();
			
			// AJAX
			$.ajax({
				url: '/admin/filebrowser/upload/??',
				type: 'POST',
				dataType: 'json',
				data: dataFile,	
				cache: false,
				processData: false,
				contentType: false,				
				success: function(data) {
					
					if (data.jResult == 'ok') {
						window.location.reload(true);
						$('#main').iAlert({'type':'success', 'title':'Informacion!','text':'Fichero subido correctamente'});
					} else {
						alert(data.jMessage);
					}
					
				},
				error: function(data) {
					alert('Se ha producido un error AJAX');
				}
				
			});			
			
		});
		
		
	}
	
	
	/************** LISTADOS *******************/

	// Metodo para filtrar segun datos
	if ($('.ui-filterList').length > 0) {
				
		$('.ui-filterList').live('click',function(e) {
			
			e.preventDefault();
			var url = $(this).attr('href');			
			$('#pageList').load(url + ' #pageList');
			
		});
		
	};
	
	// Metodo para paginar resultados
	if ($('.ui-paginationList').length > 0) {
		
		$('.ui-paginationList').live('click',function(e) {
			
			e.preventDefault();
			
			var url = $(this).attr('href'); 
			$('#pageList').load(url + ' #pageList');
		
		});
		
	}
	
	// Metodo para eliminar elementos
	if ($('.ui-removeThem').length > 0) {
			
		$('.ui-removeThem').live('click', function(e) {
					
			e.preventDefault();
			
			// Listado items seleccionados
			var checked = getChecked();
			var url = $(this).attr('href');
			
			// Datos Ajax
			var dataSend = {'ids': checked.join(',') };
			
			// Confirmamos antes de borrar
			var message = '<p>Ha seleccionado ' + checked.length + ' registro/s.  ¿Desea eliminar dichos registros?</p>'; 
			
			bootbox.dialog(
				message,				
				[{
				'label':'Si, eliminar registros',
				'class' : 'btn-danger',
				'callback' : function() {					
						// Peticion AJAX
						$.getJSON(url + '/?jsoncallback=?', dataSend, function(data) {								
							if (data.jResult == 'ok') {														
								window.location.reload(false);
								$('#main').iAlert({'type':'success', 'title':'Atención!','text':'Registros borrados correctamente'});		
							}												
						});					
					}
				},
				{
					'label':'No',
					'class' : 'btn',
					'callback' : function() {
					}										
				}],{
					'animate':true,
					'header':'Atención!',
					'backdrop':'static'
				}
				
			);

		});
			
	} 

	// Metodo para copiar elementos
	if ($('.ui-duplicateThem').length > 0) {
		
		$('.ui-duplicateThem').live('click', function(e) {
		
			e.preventDefault();
			
			// Listado items seleccionados
			var checked = getChecked();
			var url = $(this).attr('href');
						
			// Petición AJAX
			var dataSend = {'ids': checked.join(',') };
			
			// Confirmamos antes de duplicar
			var message = '<p>Ha seleccionado ' + checked.length + ' registro/s.  ¿Desea duplicar dichos registros?</p>';
			bootbox.dialog(message, 
					[{'label':'Si, duplicar registros',
						'class':'btn-primary',
						'callback': function() {					
								$.getJSON(url + '/?jsoncallback=?', dataSend, function(data) {
									
									if (data.jResult == 'ok') {
										window.location.reload(false);
									}				
									
								});
						
							}
						},
						{'label':'No',
						'class':'btn',
						'callback' : function() {}
						}
					],{
						'animate':true,
						'header':'Atención!',
						'backdrop':'static'
					}				
			);

		});
			
	} 
	
	// Metodo para copiar elementos
	if ($('.ui-moveThem').length > 0) {
		
		$('.ui-moveThem').live('click', function(e) {
			
			e.preventDefault();
			
			// Listado items seleccionados
			var checked = getChecked();
						
			// Petición AJAX
			var dataSend = {'ids': checked.join(',') };
			$.getJSON('/admin/' + iSection + '/copy/?jsoncallback=?', dataSend, function(data) {
				
				if (data.jResult == 'ok') {
					window.location.reload(false);
				}				
				
			});

		});
			
	} 	
	
	// Información de archivos
	if ($('.ui-file-info').length > 0) {
		$('.ui-file-info').iPopFile();
	}
	
	
}

function sets() {
	
	// Metodo para eliminar ficheros de los sets
	if ($('.ui-removeFileSet').length > 0) {
		
		$('.ui-removeFileSet').live('click', function(e) {
			
			e.preventDefault();
			
			var self = $('#itemSet-' + getId($(this).attr('id')));
			var path = $(this).attr('href');
			var dataSend = {'id':0};
			
			// AJAX
			$.getJSON(path + '/?jsoncallback=?', dataSend, function(data) {
				if (data.jResult == 'ok') {
					self.fadeOut('slow', function() {
						self.remove();
					});
				}
			});
			
		});
		
	}
	
	// Añade elementos a la lista	
	
}


$(document).ready(function(){

	// Metodos comunes

	common();
	
	pages();
	contents();	
	sets();
});