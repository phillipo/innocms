var tmp = '';


/**
 *  COMMON FUNCTIONS
 */

function padZeros(n) {
	return (n < 10)? '00' + n : (n < 100)? '0' + n : '' + n;
}


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
		});		
		
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
	if ($('input[name="page[page_redirect]"]').length > 0) {
				
		// Mostramos el seleccionado y ocultamos el resto
		$('.redirectChecker').each(function() {
			if ($(this).is(':checked'))  $('#ui-redirect-'+$(this).val()).show();
			else $('#ui-redirect-'+$(this).val()).hide();
		});
		
		// Segun opcion mostramos
		$('input[name="page[page_redirect]"]').live('click', function() {
		
			var value = $(this).val();
			
			// Ocultamos todos
			$('#ui-redirect-external').hide();
			$('#ui-redirect-internal').hide();
			$('#ui-redirect-none').hide();
			
			// Mostramos segun valor
			$('#ui-redirect-' + value).show();
			
		});
		
	}
	
	// Slider de plantillas
	if ($('#templateCarousel').length > 0) {
		
		//$('#templateCarousel').tinyscrollbar();
		$('#templateCarousel').tinyscrollbar({ axis: 'x', sizethumb: 100 });
		
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

	/************** MODULOS ************************/
	
	if ($('.editable').length > 0) {
		
		$('.editable').each(function() {	
			
			var zoneId = getId($(this).attr('id'));
			
			if (zoneId) {

				$(this).append('<div class="ui-component-toolbar-editable"><i class="icon-plus icon-white"></i>Añadir items</div>' +
						'<ul class="ui-component-list">' +
						'	<li id="ui-module-1" class="ui-component-toolbar-movable no-nest">Item 1</li>' +
						'	<li id="ui-module-2" class="ui-component-toolbar-movable no-nest">Item 2</li>' +
						'</ul>');
				
				$('.ui-component-list').iTree({					
					connectWith: '.ui-component-list',
					listType: 'ul',
					disableNesting: 'no-nest',
					forcePlaceholderSize: true,
					helper:	'clone',
					items: 'li',
					maxLevels: 0,
					opacity: .6,
					placeholder: 'placeholder',
					revert: 250,
					tabSize: 0,
					tolerance: 'pointer',					
					update: function() {
						// Peticions AJAX
						var dataSend = $('ol.iTree').iTree('serialize',{key:'pages'});
						$.ajax({
							url: '/admin/page/design/', 
							data: dataSend,
							type: 'POST',
							success: function(data) {

							}
						});	
					}
				});
			}
			
		});
		
		$('.ui-component-toolbar-editable').live('click', function(e) {
            e.preventDefault();
            
            var components = '<ul id="component-list" class="radio">' +
            	'<li>' + 
            	'	<a href="#" class="ui-component component-text"></a>' +
            	'	<input type="checkbox" name="aaaa">' +
            	'</li>' +
            	'<li>' + 
            	'	<a href="#" class="ui-component component-image"></a>' +
            	'	<input type="checkbox" name="aaaa">' +
            	'</li>' +
            	'<li>' + 
            	'	<a href="#" class="ui-component component-gallery"></a>' +
            	'	<input type="checkbox" name="aaaa">' +
            	'</li>' +
            	'<li>' + 
            	'	<a href="#" class="ui-component component-flash"></a>' +
            	'	<input type="checkbox" name="aaaa">' +
            	'</li>' +
            	'</ul>'            	
            	;
            
            $('.ui-component').live('click',function() {
            	
            	if ($(this).parent().find('input').is(':checked')) {
            		$(this).find('span').remove();
            		$(this).parent().find('input').removeAttr('checked');
            	} else {
            		$(this).prepend('<span class="selected"></div>');
            		$(this).parent().find('input').attr('checked','checked');
            	}
            	
            	
            });
            
            bootbox.dialog(components,
            [{'label':'Aceptar'}],
            {'header':'Listado de modulos'}
            );			
		});
		
		
		
		
	}
	
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
	
	
	/************** ESTRUCTURA *******/
	
	if ($('#structurePages').length > 0) {
		
		$('.ui-removeIt').hide();
		$('#ui-removePageItems').live('click',function(e) {
			e.preventDefault();
			$('.ui-removeIt').toggle();	
			
			if ($('.ui-removeIt').css('display') == 'none')
				$('ol.iTree').iTree({disabled:false});
			else 
				$('ol.iTree').iTree({disabled:true});
						
		});
		
			
		$('ol.iTree').iTree({
			// Options
			disableNesting: 'no-nest',
			forcePlaceholderSize: true,
			handle: 'div',
			helper:	'clone',
			items: 'li',
			maxLevels: 3,
			opacity: .6,
			placeholder: 'placeholder',
			revert: 250,
			tabSize: 25,
			tolerance: 'pointer',
			toleranceElement: '> div',
			// Events
			start: function(event,ui) {
				$('#ui-removePageItems').hide();
			},
			stop: function(event,ui) {
				$('#ui-removePageItems').show();
			},			
			
			update: function(event, ui) {

				$('ol.iTree').find('li').each(function(){	
					
					if ($(this).children('ol') && $(this).children('ol').children().length) {
						$(this).removeClass('isPage').addClass('expanded');
					} else {
						$(this).addClass('isPage').removeClass('expanded');
					}
					
				});	
				
				// Peticions AJAX
				var dataSend = $('ol.iTree').iTree('serialize',{key:'pages'});
				
				$.ajax({
					url: '/admin/page/sort/', 
					data: dataSend,
					type: 'POST',
					success: function(data) {

					}
				});	
				
			}

		});			

		// Metodo para eliminar elementos
		if ($('.ui-removeIt').length > 0) {
				
			$('.ui-removeIt').live('click', function(e) {
						
				e.preventDefault();
				
				// Listado items seleccionados
				var url = $(this).attr('href');
				
				// Confirmamos antes de borrar
				var message = '<p>Ha seleccionado un registro.  ¿Desea eliminar dichos registros?</p>'; 
				var dataSend = {};
				
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
		
		
	}
	
	
}


/* Scripts for CONTENIDOS / CATEGORIAS / SETS */
function contents() {

	/************** FICHAS *******************/
    
	// Metodo para convertir un texto en formato SLUG
	if ($('.ui-slug').length > 0) {
		$('.ui-slug').slugIt( { events: 'keypress keyup change',output: '.ui-slug-result' } );
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

/************** USERS / USERGROUPS *****/

function users() {
	
	var setUsergroupItems = function() {
		
		var listItems = new Array();
		$('ul#newUsers li').each(function() {					
			listItems.push(getId($(this).attr('id')));					
		});
		
		return listItems.join(',');		
		
	};	
	
	// Añade elementos a la lista	
	if ($('#ui-browserUserList').length > 0) {
		
		// Método para mostrar/ocultar slider con ficheros
		$('#ui-browserUserList').live('click', function(e) {
			
			e.preventDefault();
			$('#browserUserList').toggle();
			
		});
		
		// Metodo para añadir un fichero a un set		
		$('.ui-addUser').live('click', function() {
		
			var id = getId(this.id);

			// Añadimos contenido al HTML				
			$('#userList').prepend($('<li id="addedUserItem-' + id + '">').load('/admin/usergroup/addUser/' + id));
			$('#userItem-' + id).fadeOut('slow', function() {
				$('input[name="usergroup_users"]').val(setUsergroupItems());
			});
			
		});
			
		
		// Metodo para eliminar ficheros de los sets		
		$('.ui-removeUser').live('click', function(e) {
			
			e.preventDefault();
			
			var id = getId(this.id);
			
			// Objeto a borrar
			var self = $('#addedUserItem-' + id);			
			self.fadeOut('slow', function() {
				self.remove();
				$('#userItem-' + id).show();
				$('#userItem-' + id).fadeIn('slow');
				$('input[name="usergroup_users"]').val(setUsergroupItems());
			});				
			

		});
		
	}
	
}


/**************************** SETS *****/

function sets() {
	
	
	var setFileItems = function() {
		
		var listItems = new Array();
		$('ul#mySortable li').each(function() {					
			listItems.push(getId($(this).attr('id')));					
		});
		
		return listItems.join(',');		
		
	};
	
	// Añade elementos a la lista	
	if ($('#ui-browserFileSet').length > 0) {
		
		// Método para mostrar/ocultar slider con ficheros
		$('#ui-browserFileSet').live('click', function(e) {
			
			e.preventDefault();
			$('#browserFileSet').toggle();
			
		});
		
		// Metodo para añadir un fichero a un set
		if ($('.ui-addFileSet').length > 0) {
			
			$('.ui-addFileSet').live('click', function() {
			
				var id = getId(this.id);
				
				// Añadimos contenido al HTML				
				$('#mySortable').append($('<li class="span2" id="itemSet-' + id + '">').load('/admin/set/addItem/' + id));
				
				// Actualizamos listado de ficheros
				$('input[name="attributes[files]"]').val(setFileItems());
				
				//$('ul#mySortable').sortable('refresh');				
				
			});
			
		}
		
		// Metodo para eliminar ficheros de los sets
		if ($('.ui-removeFileSet').length > 0) {
			
			$('.ui-removeFileSet').live('click', function(e) {
							
				e.preventDefault();
				
				// Objeto a borrar
				var self = $('#itemSet-' + getId(this.id));
				
				// Confirmamos antes de borrar
				var message = '<p>Ha seleccionado eliminar fichero.  ¿Desea eliminar dicho fichero?</p>'; 
				
				bootbox.dialog(
					message,				
					[{
					'label':'Si, eliminar registros',
					'class' : 'btn-danger',
					'callback' : function() {					

							// Eliminamos de la vista
							self.fadeOut('slow', function() {
								// Eliminamos del html
								self.remove();
								// Actualizamos listado
								$('input[name="attributes[files]"]').val(setFileItems());					
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
				

				
				
//				// AJAX
//				$.getJSON(path + '/?jsoncallback=?', dataSend, function(data) {
//					if (data.jResult == 'ok') {
//						self.fadeOut('slow', function() {
//							self.remove();
//						});
//					}
//				});
				
			});
			
		}
		
		// Ordenacion de items dentro de un set		
		if ($('.ui-sortfiles').length > 0) {
				
			/*
			var id = $('.ui-sortfiles').attr('id');			
			$( "#" + id ).sortable({
				//placeholder: "ui-state-highlight",
				change: function() {
					// Enviamos peticion para actualizar listado
					var listItems = new Array();
					$('ul#mySortable li').each(function() {					
						listItems.push(getId($(this).attr('id')));					
					});
					
					$('input[name="attributes[files]"]').val(listItems.join(','));					
					
				}
			
			});
			
			$( "#" + id ).disableSelection();
			*/
			
		}		
		
		
		
	}
	
	

	
}

function upload() {
		
	if ($('#pickfiles').length > 0) {
	
		var uploader = new plupload.Uploader({
			runtimes : 'html5',
			browse_button : 'pickfiles',
			container : 'container',
			max_file_size : '10mb',
			url : '/admin/filebrowser/upload/',
			flash_swf_url : '/javascript/admin/plupload/plupload.flash.swf',
			filters : [
			    {title : "Todos", extensions : "avi,m4v,m4a,m4v,mov,mpg,mpeg,doc,docx,odt,ods,xls,xla,xlsx,csv,xml,jpg,jpeg,gif,png,pdf,zip,rar,ppt,pptx,qt,rtf,swf,flv,txt,wma,wmv,wav"},
			],
			resize : {width : 1600, height : 1200, quality : 90}
		});
	
		// Metodo para inicializar el upload
		uploader.bind('Init', function(up, params) {
			//$('#filelist').append('<div>Current runtime: ' + params.runtime + '</div>');
		});
	
		uploader.init();
		
		// Metodo para iniciar la subida de archivos
		$('#uploadfiles').live('click', function(e) {
			uploader.start();
			e.preventDefault();
			$('#filelist').html();
		});	
		
		// Metodo para eliminar archivos del listado
	    $('.ui-removeFileUploadList').live('click', function(e) {
	    	e.preventDefault();
	       	$('#itemFileList' + this.id).remove();  
	       	if (uploader.getFile(this.id)) {
	       		uploader.removeFile(uploader.getFile(this.id));
	       	}     	
	    });
	    
	    // Metodo para actualizar la pantalla
	    $('#cleanfiles').live('click', function(e) {
	    	e.preventDefault();
	    	window.location.reload(false);    	
	    });
	      
	    // Metodo para añadir archivos al listado
		uploader.bind('FilesAdded', function(up, files) {
			
			$.each(files, function(i, file) {
				$('#filelist').append(
					'<div id="itemFileList' + file.id + '" class="addedFile">' +
					'	<div class="btn-group">' +
					'		<button class="btn btn-mini"><i class="icon-file"></i>' + file.name + ' (' + plupload.formatSize(file.size) + ')</button>' +
					'		<button class="ui-removeFileUploadList btn btn-mini btn-danger" id="' + file.id + '" title="Quitar de la lista"><i class="icon-trash icon-white"></i></button>' +
					'	</div>' +
					'</div>');
			});
	
			up.refresh(); // Reposition Flash/Silverlight
			
		});
		
		// Metodo lanzado cuando se cambia la cola de archivos
		uploader.bind('QueueChanged', function(up) {
			if (up.files.length == 0) {
				$('#filelist').hide();
				$('#uploadfiles').hide();
			} else {
				$('#filelist').show();
				$('#uploadfiles').show();
			}
	    });
	
		// Metodo para mostrar el progreso de subida de un archivo
		uploader.bind('UploadProgress', function(up, file) {
			
			//$('#progressbar > .bar').css('width', file.percent + "%");
			$('#' + file.id).remove();
			$('#itemFileList' + file.id + ' .btn-group').append('<button id="' + file.id + '" class="btn btn-mini btn-warning" title="Subiendo archivo al servidor"><i class="icon-time icon-white"></i> ' + padZeros(file.percent) + "%" + '</button>');
			//$('#' + file.id + " b").html(file.percent + "%");
		});
	
		// Metodo para cuando un archivo se ha subido
		uploader.bind('FileUploaded', function(up, file) {
			// Eliminamos icono de eliminar
			$('#' + file.id).remove();
			$('#itemFileList' + file.id + ' .btn-group').append('<button id="' + file.id + '" class="btn btn-mini btn-success" title="Subido correctamente"><i class="icon-ok icon-white"></i></button>');		
		});	
		
		// Metodo para realizar una acción una vez finalizada la subida de todos los archivos
		uploader.bind('UploadComplete', function() {		
			$('#cleanfiles').show();		
		});	
		
		// Metodo para mostrar errores
		uploader.bind('Error', function(up, err) {	
			
			if ($('#itemFileList' + err.file.id).length == 0) {
				
				$('#filelist').append(
						'<div id="itemFileList' + err.file.id + '" class="addedFile">' +
						'	<div class="btn-group">' +
						'		<button class="btn btn-mini"><i class="icon-file"></i>' + err.file.name + ' (' + plupload.formatSize(err.file.size) + ')</button>' +
						'		<button class="ui-removeFileUploadList btn btn-mini btn-danger" id="' + err.file.id + '" title="Quitar de la lista"><i class="icon-trash icon-white"></i></button>' +
						'	</div>' +
						'</div>');
				
				$('#filelist').show();
				
			}
			
			// Eliminamos icono de eliminar
			$('#' + err.file.id).remove();
			$('#itemFileList' + err.file.id + ' .btn-group').append('<button id="' + err.file.id + '" class="btn btn-mini btn-danger" title="Se ha producido un error. '+ err.message +'"><i class="icon-ban-circle icon-white"></i></button>');		
			up.refresh(); // Reposition Flash/Silverlight
			
		});	
	
	}
	
}


$(document).ready(function(){

	// Metodos comunes

	common();
	
	pages();
	contents();	
	sets();
	upload();
	users();
	
});