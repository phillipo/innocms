// (C) FruitBatInShades.com Jan 2009
//
// Simple extensions to turn a ul into a treeview
//
// History:
// 0.1 - Simply folds nested treeviews by hiding li.directory ul's and fires passed in callback when the node has href=#= or class of file
//
// TERMS OF USE
// 
// jQuery.fbis.Treeview is licensed under a Creative Commons License and is copyrighted (C)2008 by FruitBatInShades, a.k.a. Lee Davies
// For details, visit http://creativecommons.org/licenses/by/3.0/uk/
// Based up jQuery File Tree by Cory S.N. LaViska.
/*
    Usage: $('#ParentDiv').treeview(options,nodeClick)
    
    Simple plugin that takes a unordered list, collapses all li.directory on init and handles expanding and collapsing those nodes.
    It also accepts a callback that is fired when a link with a href of '#' is found.
*/
if (jQuery) (function($) {
    $.extend($.fn, {
        treeview: function(options, callback) {
            // Defaults
            if (!options) var options = {};

            // initialise tree
            var tree = $(this);
            // Disable selection for sortable
            tree.disableSelection();
            //collapse folder initially
            tree.find('ul.isNode').hide();
            tree.find('li.isFolder').addClass('expanded');
            //add click handler
			tree.live('click', function(e) { handleClick(e); });
			// sortable
			tree.sortable({
				connectWith: 'ul',
				placeholder: 'ui-state-highlight',
				items: 'li',
				tolerance: 'intersec',
				axis: 'y',
				containment: tree,
				// Events
				start: function(event, ui) {						
					tree.find('.isNode').show();
					tree.sortable('refresh');
				},
				stop: function(event,ui) {
					moveTo(ui);
					tree.find('.isNode').hide();				
				},		
				over: function(event,ui) {
					var element = $(ui.item);
					$('#structurePages').append('-| ' + element.attr('id'));
				},
				update: function(event, ui) {
					//$.post('/admin/page/sort/', { pages: update_page_order() } );
					//refresh();
				}
			});			

            function handleClick(e) {
                //is the click on me or a child
                var node = $(e.target);
                //check the link is a directory
                if (node.is('li.isFolder')) { //Is it a directory listitem that fired the click?
                    //do collapse/expand
                    if (node.hasClass('collapsed')) {
                        node.find('>ul').toggle(); //need the > selector else all child nodes open
                        node.removeClass('collapsed').addClass('expanded');
                    }
                    else if (node.hasClass('expanded')) {
                        node.find('>ul').toggle();
                        node.removeClass('expanded').addClass('collapsed');
                    }
                    //its one of our directory nodes so stop propigation
                    e.stopPropagation();
                } else if (node.attr('href') == '#' | node.hasClass('file')) {
                    //its a file node with a href of # so execute the call back
                    callback(e);
                }
                
            }  
            
    		function update_page_order() {
    			
    			var arr = '';			
    			tree.find('ul:first > li').each(function(){				
    				arr += ','+ $(this).attr('id');
    				if ($(this).has('ul')) {
    					subitems($(this).attr('id'));
    				}				
    			});
    			
    			function subitems(pid) {				
    				$('#' + pid + ' > ul:first > li').each(function(){					
    					if ($(this).attr('id') != undefined) {
    						arr += ',' + $(this).attr('id') + '/' + pid;
    					}					
    					if ($(this).has('ul')) {
    						subitems($(this).attr('id'));
    					}										
    				});				
    			}
    			
    			return arr;
    			
    		}
    		
    		function refresh() {
    			
    			tree.find('li').each(function() {
    				
    				if ($(this).children('ul').length > 1) {
    					//alert(this.id + ' tiene hijos ' + $(this).children().length);
    					$(this).removeClass('isPage');
    					$(this).addClass('isFolder expanded');
    				} else {
    					//alert(this.id + ' no tiene hijos');
    					$(this).removeClass('isFolder collapsed expanded');
    					$(this).addClass('isPage');    					
    				}
    				
    			});
    			
    		}
    		
    		function moveTo(e) {
    			var node = $(e.target);
    			var element = $(e.item);
    			if (element.has('ul > li').length) {
    				
    			} else {
    				
    			}
    		};
            
            
        }
    });
})(jQuery);