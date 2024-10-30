var isrc_tagBox, array_unique_noempty;

(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals
        factory(jQuery);
    }

}(function ($) {
    'use strict';
	var tagDelimiter = ( window.isrc_tagsSuggestL10n && window.isrc_tagsSuggestL10n.tagDelimiter ) || ',';

	// Return an array with any duplicate, whitespace or empty values removed
	array_unique_noempty = function( array ) {
		var out = [];

		$.each( array, function( key, val ) {
			val = $.trim( val );

			if ( val && $.inArray( val, out ) === -1 ) {
				out.push( val );
			}
		} );

		return out;
	};

	
	
    function isrc_tagBox(el, options) {
        var noop = function () { },
            that = this,
            defaults = {
                ajax_save: false,
                save_action: 'isrc_save_tags',
                serviceUrl: ajaxurl,
				ajaxtag : $('div.ajaxtag'),
				tag_key : '',
				type : 'POST',
                dataType: 'json',
				security : '',
                params: {
					action : '',
					tags : ''
					},
            };

        // Shared variables:
        that.element = el;
        that.el = $(el);
        that.options = $.extend({}, defaults, options);

        // Initialize and set options:
        that.init();
        that.setOptions(options);

    }
	
    $.isrc_tagBox = isrc_tagBox;

    isrc_tagBox.prototype = {

		clean : function( tags ) {
            var that = this,
                options = that.options;

			if ( ',' !== tagDelimiter ) {
				tags = tags.replace( new RegExp( tagDelimiter, 'g' ), ',' );
			}

			tags = tags.replace(/\s*,\s*/g, ',').replace(/,+/g, ',').replace(/[,\s]+$/, '').replace(/^[,\s]+/, '');

			if ( ',' !== tagDelimiter ) {
				tags = tags.replace( /,/g, tagDelimiter );
			}

			return tags;
		},

		parseTags : function(el) {
            var that = this,
                options = that.options,
				id = el.id,
				num = id.split('-check-num-')[1],
				taxbox = $(el).closest('.tagsdiv'),
				thetags = taxbox.find('.the-tags'),
				current_tags = thetags.val().split( tagDelimiter ),
				new_tags = [];

			delete current_tags[num];

			$.each( current_tags, function( key, val ) {
				val = $.trim( val );
				if ( val ) {
					new_tags.push( val );
				}
			});

			thetags.val( this.clean( new_tags.join( tagDelimiter ) ) );

			this.quickClicks( taxbox );
			this.maybeSave();
			return false;
		},

		quickClicks : function( el ) {
            var that = this,
                options = that.options;

			var thetags = $('.the-tags', el),
				tagchecklist = $('.tagchecklist', el),
				id = $(el).attr('id'),
				current_tags, disabled;

			if ( ! thetags.length )
				return;

			disabled = thetags.prop('disabled');

			current_tags = thetags.val().split( tagDelimiter );
			tagchecklist.empty();

			$.each( current_tags, function( key, val ) {
				var span, xbutton;

				val = $.trim( val );

				if ( ! val )
					return;

				// Create a new span, and ensure the text is properly escaped.
				span = $('<li />').text( val );

				// If tags editing isn't disabled, create the X button.
				if ( ! disabled ) {
					/*
					 * Build the X buttons, hide the X icon with aria-hidden and
					 * use visually hidden text for screen readers.
					 */
					xbutton = $( '<button type="button" id="' + id + '-check-num-' + key + '" class="ntdelbutton">' +
						'<span class="remove-tag-icon" aria-hidden="true"></span>' +
						'<span class="screen-reader-text">' + window.isrc_tagsSuggestL10n.removeTerm + ' ' + span.html() + '</span>' +
						'</button>' );

					xbutton.on( 'click keypress', function( e ) {
						// On click or when using the Enter/Spacebar keys.
						if ( 'click' === e.type || 13 === e.keyCode || 32 === e.keyCode ) {
							/*
							 * When using the keyboard, move focus back to the
							 * add new tag field. Note: when releasing the pressed
							 * key this will fire the `keyup` event on the input.
							 */
							if ( 13 === e.keyCode || 32 === e.keyCode ) {
 								$( this ).closest( '.tagsdiv' ).find( 'input.newtag' ).focus();
 							}

							that.userAction = 'remove';
							that.parseTags( this );
							
						}
					});

					span.prepend( '&nbsp;' ).prepend( xbutton );
				}

				// Append the span to the tag list.
				tagchecklist.append( span );
			});
			// The buttons list is built now, give feedback to screen reader users.
			that.screenReadersMessage();
		},
		flushTags : function( el, a, f ) {
            var that = this,
                options = that.options;

			var tagsval, newtags, text,
				tags = $( '.the-tags', el ),
				newtag = $( 'input.newtag', el );

			a = a || false;

			text = a ? $(a).text() : newtag.val();

			/*
			 * Return if there's no new tag or if the input field is empty.
			 * Note: when using the keyboard to add tags, focus is moved back to
			 * the input field and the `keyup` event attached on this field will
			 * fire when releasing the pressed key. Checking also for the field
			 * emptiness avoids to set the tags and call quickClicks() again.
			 */
			if ( 'undefined' == typeof( text ) || '' === text ) {
				return false;
			}

			tagsval = tags.val();
			newtags = tagsval ? tagsval + tagDelimiter + text : text;

			newtags = this.clean( newtags );
			newtags = array_unique_noempty( newtags.split( tagDelimiter ) ).join( tagDelimiter );
			tags.val( newtags );
			this.quickClicks( el );

			if ( ! a )
				newtag.val('');
			if ( 'undefined' == typeof( f ) )
				newtag.focus();

			this.maybeSave();
			return false;
		},

		get : function( id ) {
            var that = this,
                options = that.options;

			var tax = id.substr( id.indexOf('-') + 1 );

			$.post( ajaxurl, { 'action': 'get-tagcloud', 'tax': tax }, function( r, stat ) {
				if ( 0 === r || 'success' != stat ) {
					return;
				}

				r = $( '<p id="tagcloud-' + tax + '" class="the-tagcloud">' + r + '</p>' );

				$( 'a', r ).click( function() {
					that.userAction = 'add';
					that.flushTags( $( '#' + tax ), this );
					return false;
				});

				$( '#' + id ).after( r );
			});
		},
		maybeSave : function(){
            var that = this,
                options = that.options;
				
				if( options.ajax_save ){
					that.save();
					}

			},
		save : function() {
            var that = this,
                options = that.options,
				taxbox = $(that.el).closest('.tagsdiv'),
				thetags = taxbox.find('.the-tags'),
				current_tags = thetags.val().split( tagDelimiter ),
                serviceUrl = options.serviceUrl,
                params;
				
           		params = options.params;
				params.action = options.save_action;
				params.tag_key = options.tag_key;
				params.tags = current_tags;
				params.security = options.security;

               $.ajax({
                    url: serviceUrl,
                    data: params,
                    type: options.type,
                    dataType: options.dataType,
                }).done(function (data) {
					if( data.status == 'error' ){
						alert( data.msg );
						}
                }).fail(function (jqXHR, textStatus, errorThrown) {

                });

		},

		/**
		 * Track the user's last action.
		 *
		 * @since 4.7.0
		 */
		userAction: '',

		/**
		 * Dispatch an audible message to screen readers.
		 *
		 * @since 4.7.0
		 */
		screenReadersMessage: function() {
            var that = this,
                options = that.options;

			var message;

			switch ( this.userAction ) {
				case 'remove':
					message = window.isrc_tagsSuggestL10n.termRemoved;
					break;

				case 'add':
					message = window.isrc_tagsSuggestL10n.termAdded;
					break;

				default:
					return;
			}

			//window.wp.a11y.speak( message, 'assertive' );
		},
		prepareforsave:function(){
            var that = this,
                options = that.options,
				ajaxtag = that.element;

		$( ajaxtag ).each( function() {
			that.flushTags(this, false, 1);
		});
		},
		
		init : function() {
            var that = this,
                options = that.options,
				ajaxtag = that.element;

			$(that.element).each( function() {
				that.quickClicks( this );
			});

			$( '.tagadd', ajaxtag ).click( function() {
				that.userAction = 'add';
				that.flushTags( $( this ).closest( '.tagsdiv' ) );
			});

			$( 'input.newtag', ajaxtag ).keyup( function( event ) {
				if ( 13 == event.which ) {
					that.userAction = 'add';
					that.flushTags( $( this ).closest( '.tagsdiv' ) );
					event.preventDefault();
					event.stopPropagation();
				}
			}).keypress( function( event ) {
				if ( 13 == event.which ) {
					event.preventDefault();
					event.stopPropagation();
				}
			}).each( function( i, element ) {
				//$( element ).wpTagsSuggest();
			});

			// save tags on post save/publish
			$('#post').submit(function(){
				$( ajaxtag ).each( function() {
					that.flushTags(this, false, 1);
				});
			});

			// Fetch and toggle the Tag cloud.
			$('.tagcloud-link').click(function(){
				// On the first click, fetch the tag cloud and insert it in the DOM.
				that.get( $( this ).attr( 'id' ) );
				// Update button state, remove previous click event and attach a new one to toggle the cloud.
				$( this )
					.attr( 'aria-expanded', 'true' )
					.unbind()
					.click( function() {
						$( this )
							.attr( 'aria-expanded', 'false' === $( this ).attr( 'aria-expanded' ) ? 'true' : 'false' )
							.siblings( '.the-tagcloud' ).toggle();
					});
			});
	},
		
		setOptions: function (suppliedOptions) {
            var that = this,
                options = that.options;
            $.extend(options, suppliedOptions);
        },


    };
	
    // Create chainable jQuery plugin:
    $.fn.isrc_tagBox = function (options, args) {
        var dataKey = 'isrc_tagbox';
        // If function invoked without argument return
        // instance of the first matched element:

        if (arguments.length === 0) {
            return this.first().data(dataKey);
        }

        return this.each(function () {
            var inputElement = $(this),
                instance = inputElement.data(dataKey);

            if (typeof options === 'string') {
                if (instance && typeof instance[options] === 'function') {
                    instance[options](args);
                }
            } else {
                // If instance already exists, destroy it:
                if (instance && instance.dispose) {
                    instance.dispose();
                }
                instance = new isrc_tagBox(this, options);
                inputElement.data(dataKey, instance);
            }
        });
    };

}));