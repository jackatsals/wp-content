// JavaScript Document

function open_admin(popstate) {
	if (typeof popstate == 'undefined') {
		var popstate = false;
	}

	var place_id = null,
		google_api_key = null,
		section = null,
		hour_types = ['regular', 'special'],
		time_format = null,
		week_start = null,
		browser_hour_format_test = (/chrom(e|ium)/i.test(navigator.userAgent) && !/msie|edge/i.test(navigator.userAgent)) ? false : (new Date(2020, 4, 8, 18, 0, 0, 0).toLocaleTimeString()),
		browser_hour_format = ((typeof browser_hour_format_test == 'string' && parseInt(browser_hour_format_test) == 18) ? 24 : 12),
		hour_format_12 = '12_dot_trim',
		hour_format_24 = '24_colon',
		data = [],
		weekdays = [],
		weekend = [],
		e = null,
		i = 0,
		k = null,
		t = null,
		regex = null,
		hour_type = null,
		count = null;
	
	if (jQuery('#opening-hours-settings').length) {
		place_id = jQuery('#place-id').val();
		google_api_key = jQuery('#api-key').val();
	}
	
	if (jQuery('.section', '#wpbody-content').length) {
		if (!jQuery('.nav-tab-active', jQuery('nav:eq(0)', '#wpbody-content')).length || typeof window.location.hash == 'string' && window.location.hash.length) {
			jQuery('.section', '#wpbody-content').each(function(section_index) {
				section = (typeof window.location.hash == 'string' && window.location.hash.length) ? window.location.hash.replace(/^#([\w-]+)/, '$1') : null;
				if (section == null && section_index == 0 || section != null && section == jQuery(this).attr('id')) {
					if (jQuery(this).hasClass('hide')) {
						jQuery(this).removeClass('hide');
					}
				}
				else if (!jQuery(this).hasClass('hide')) {
					jQuery(this).addClass('hide');
				}
			});
			
			if (jQuery('.nav-tab-active', jQuery('nav:eq(0)', '#wpbody-content')).length >= 1) {
				jQuery('.nav-tab-active', jQuery('nav:eq(0)', '#wpbody-content')).each(function(section_index) {
					if (section != null && jQuery(this).attr('href') != '#' + section || section == null && section_index == 0) {
						jQuery(this).removeClass('nav-tab-active');
					}
                });
			}
			
			jQuery('.nav-tab', jQuery('nav:eq(0)', '#wpbody-content')).each(function(tab_index) {
				section = (typeof jQuery(this).attr('href') == 'string') ? jQuery(this).attr('href').replace(/^.*#([\w-]+)/, '$1') : null;
				
				if ((tab_index == 0 && (section == null || typeof window.location.hash == 'undefined' || !window.location.hash.length)) || typeof window.location.hash == 'string' && window.location.hash.length && window.location.hash.replace(/^#([\w-]+)/, '$1') == section) {
					jQuery(this).addClass('nav-tab-active').prop('aria-current', 'page');
				}
			});
		}
	}
	
	if (popstate) {
		if (jQuery('.section', '#wpbody-content').length) {
			jQuery('.nav-tab', jQuery('nav:eq(0)', '#wpbody-content')).removeClass('nav-tab-active').removeProp('aria-current');
		}
		return;
	}
	
	if (jQuery('.is-dismissible').length) {
		jQuery('.is-dismissible').each(function(index, element) {
			if (!jQuery(this).hasClass('notice-success') && !jQuery(this).hasClass('notice-error')) {
				jQuery(this).remove();
			}
		});
	}
	
	if (jQuery('div', '#widgets-right').length) {
		jQuery('div', '#widgets-right').each(function() {
            if (typeof jQuery(this).attr('id') == 'string' && jQuery(this).attr('id').match(/(?:(?:we[_-]?are[_-]?)?open|opening[_-]?hours)/i) != null) {
				// console.log('Open - Widget');
			}
        });
	}
	
	if (!jQuery('#opening-hours, #opening-hours-settings').length) {
		return;
	}
	
	if (jQuery('#opening-hours, #opening-hours-settings').hasClass('closed')) {
		jQuery('#opening-hours, #opening-hours-settings').removeClass('closed');
	}
	
	if (!browser_hour_format_test) {
		jQuery('.opening-hours:eq(0)', '#wpbody-content').append('<input type="time" id="open-time-test" name="open-time-test">');
		browser_hour_format = (jQuery('#open-time-test').width() < 81) ? 24 : 12;
		jQuery('#open-time-test').remove();
	}

	if (jQuery('#opening-hours').length && browser_hour_format == 12) {
		jQuery('#opening-hours').addClass('hours-12');
	}
	
	if (jQuery('#opening-hours-settings').length && jQuery('#time-format').length && !jQuery('#time-format').val().length) {
		if (browser_hour_format == 12) {
			jQuery('#time-format').val(hour_format_12);
			jQuery('#time-type-12').prop('checked', true);
			jQuery('#time-format').closest('td').removeClass('hours-24').addClass('hours-12');
		}
		else {
			jQuery('#time-format').val(hour_format_24);
			jQuery('#time-type-24').prop('checked', true);
			jQuery('#time-format').closest('td').removeClass('hours-12').addClass('hours-24');
		}
	}
	
	jQuery('.is-dismissible').each(function() {
		if (jQuery(this).hasClass('notice-success') || jQuery(this).hasClass('notice-error')) {
			jQuery(this).addClass('visible');
		}
		else {
			jQuery(this).remove();
		}
	});
	
	setTimeout(function() {
		if (jQuery('.is-dismissible').length) {
			jQuery('.is-dismissible').slideUp(300, function() { jQuery(this).remove(); });
		}
	}, 15000);

	if (jQuery('#open-regular').length && jQuery('#open-special').length) {
		if (typeof sessionStorage.getItem('we_are_open_hours') == 'string' && JSON.parse(sessionStorage.getItem('we_are_open_hours')) != null) {
			jQuery('.paste.disabled', '#opening-hours').each(function() {
				jQuery(this).removeClass('disabled');
			});
		}

		jQuery('#open-save, #open-delete').on('click', function(event) {
			event.preventDefault();
			i = 0;
			
			if (jQuery(this).is('#open-delete')) {				
				jQuery('.check-column > :checkbox', '#open-special').each(function() {
					if (jQuery(this).is(':checked') && typeof jQuery(this).closest('tr').attr('id') == 'string' && jQuery(this).closest('tr').attr('id').match(/^special-hours-(new|\d+)$/i) != null && jQuery(':input[type=date]:eq(0)', jQuery(this).closest('tr')).length) {
						jQuery(':input[type=date]:eq(0)', jQuery(this).closest('tr')).val('');
						
						if (jQuery(this).closest('tr').attr('id').match(/^special-hours-\d+$/i) != null) {
							jQuery(this).closest('tr').addClass('delete');
							jQuery(this).closest('tr').fadeOut(300, function() { jQuery(this).remove(); });
						}
					}
				});
			}
			
			data = {
				action: 'we_are_open_admin_ajax',
				type: (jQuery(this).is('#open-delete')) ? 'delete' : 'update',
				regular: new Array(6),
				special: new Array(),
				closure: null
			};

			for (var t in hour_types) {
				jQuery('tr:gt(0)', '#open-' + hour_types[t]).each(function() {
					hour_type = hour_types[t];
					
					if (hour_type == 'regular' || hour_type == 'special' && !jQuery(this).hasClass('delete')) { 
						count = (hour_type == 'regular') ? jQuery(this).data('id') : ((jQuery(':input:eq(0)', jQuery('.date-column', this)).val().length) ? i : null);
						k = (hour_type == 'regular') ? count : jQuery(this).attr('id').replace(/^[\w-]+-([^-]+)$/, '$1');
						
						if (count != null) {
							if (jQuery('.hours-column', this).hasClass('closed') || jQuery('.closed-text', this).is(':visible')) {
								data[hour_type][count] = {
									closed: true,
									date: (hour_type == 'special') ? jQuery(':input:eq(0)', jQuery('.date-column', this)).val() : null,
									hours: [],
									hours_24: false
								};
							}
							else if (jQuery('.hours-column', this).hasClass('hours-24') || jQuery('#' + hour_type + '-time-' + k + '-start').length && jQuery('#' + hour_type + '-time-' + k + '-start').is(':visible') && jQuery('#' + hour_type + '-time-' + k + '-start').val().match(/^00:00$/) != null && jQuery('#' + hour_type + '-time-' + k + '-end').length && jQuery('#' + hour_type + '-time-' + k + '-end').is(':visible') && jQuery('#' + hour_type + '-time-' + k + '-end').val().match(/^(?:00:00|23:5[5-9])$/) != null) {
								data[hour_type][count] = {
									closed: false,
									date: (hour_type == 'special') ? jQuery(':input:eq(0)', jQuery('.date-column', this)).val() : null,
									hours: [],
									hours_24: true
								};
							}
							else {
								data[hour_type][count] = {
									closed: false,
									date: (hour_type == 'special') ? jQuery(':input:eq(0)', jQuery('.date-column', this)).val() : null,
									hours: [
										[
											(jQuery('#' + hour_type + '-time-' + k + '-start').length && jQuery('#' + hour_type + '-time-' + k + '-start').is(':visible')) ? jQuery('#' + hour_type + '-time-' + k + '-start').val() : null,
											(jQuery('#' + hour_type + '-time-' + k + '-end').length && jQuery('#' + hour_type + '-time-' + k + '-end').is(':visible')) ? jQuery('#' + hour_type + '-time-' + k + '-end').val() : null
										],
										[
											(jQuery('#' + hour_type + '-time-' + k + '-start-extended').length && jQuery('#' + hour_type + '-time-' + k + '-start-extended').is(':visible')) ? jQuery('#' + hour_type + '-time-' + k + '-start-extended').val() : null,
											(jQuery('#' + hour_type + '-time-' + k + '-end-extended').length && jQuery('#' + hour_type + '-time-' + k + '-end-extended').is(':visible')) ? jQuery('#' + hour_type + '-time-' + k + '-end-extended').val() : null
										],
										[
											(jQuery('#' + hour_type + '-time-' + k + '-start-extended-2').length && jQuery('#' + hour_type + '-time-' + k + '-start-extended-2').is(':visible')) ? jQuery('#' + hour_type + '-time-' + k + '-start-extended-2').val() : null,
											(jQuery('#' + hour_type + '-time-' + k + '-end-extended-2').length && jQuery('#' + hour_type + '-time-' + k + '-end-extended-2').is(':visible')) ? jQuery('#' + hour_type + '-time-' + k + '-end-extended-2').val() : null
										]
									],
									hours_24: false
								};
							}
							
							if (hour_type == 'special') {
								i++;
							}
						}
					}
				});
				
				if (jQuery('#closure-start').length && jQuery('#closure-start').is(':visible') && jQuery('#closure-start').val().length) {
					data.closure = [ jQuery('#closure-start').val(), jQuery('#closure-end').val() ];
				}
			}
			
			jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
				if (response.success) {
					if (jQuery('#delete-possible').is(':visible')) {
						jQuery('.check-column > :checkbox:checked', '#open-special').each(function() {
							jQuery(this).prop('checked', false);
						});
						
						jQuery('#delete-possible').fadeOut(300);
					}
					
					if (typeof response.closure == 'object' && typeof response.closure.count == 'number' && jQuery('#closure-dates').length && jQuery('.closed-text', '#closure-dates').length && jQuery('.closed-text', '#closure-dates').is(':visible')) {
						if (response.closure.count == 1) {
							jQuery('.closed-text:eq(0)', '#closure-dates').text(jQuery('.closed-text:eq(0)', '#closure-dates').data('singular').replace(/%[us]/, response.closure.count));
						}
						else if (response.closure.count > 1) {
							jQuery('.closed-text:eq(0)', '#closure-dates').text(jQuery('.closed-text:eq(0)', '#closure-dates').data('plural').replace(/%[us]/, response.closure.count));
						}
					}
					
					open_message(response.message, 'success');
				}
				else {
					open_message(response.message, 'error');
				}

			}, 'json');
		});

		jQuery('#closure-toggle').on('click', function(event) {
			event.preventDefault();
			if (jQuery('#closure-dates').is(':hidden')) {
				jQuery('#closure-information').slideDown(200, function() {
					jQuery('#closure-toggle').html(jQuery('#closure-toggle').data('hide') + ' ' + jQuery('.dashicons', '#closure-toggle')[0].outerHTML.replace(/([\b_-])down([\b_-]|$)/i, '$1up$2'));
					jQuery('#closure-start').val('');
					jQuery('#closure-end').val('');
					jQuery('#closure-dates').fadeIn(500, function() {
						jQuery(':input:visible:eq(0)', this).trigger('focus');
					});
				});
			}
			else {
				jQuery('#closure-dates').fadeOut(500, function() {
					jQuery('#closure-toggle').html(jQuery('#closure-toggle').data('show') + ' ' + jQuery('.dashicons', '#closure-toggle')[0].outerHTML.replace(/([\b_-])up([\b_-]|$)/i, '$1down$2'));
					jQuery('#closure-information').slideUp(200);
				});
			}
		});

		jQuery('#open-google-business-populate').on('click', function(event) {
			event.preventDefault();
			data = {
				action: 'we_are_open_admin_ajax',
				type: 'google_business'
			};
			
			jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
				if (!response.success) {
					open_message(response.message, 'error');
					return;
				}

				if (typeof response.regular == 'object') {
					for (var i in response.regular) {
						var regular = response.regular[i];
						if (regular.closed) {
							if (!jQuery('#regular-hours-' + i + '-closed').is(':hidden')) {
								continue;
							}

							jQuery('.dashicons-minus', jQuery('#regular-hours-' + i)).removeClass('dashicons-minus').addClass('dashicons-plus');
							jQuery('#regular-hours-' + i + '-base').hide();
							jQuery('#regular-hours-' + i + '-extended').hide();
							jQuery('#regular-hours-' + i + '-extended-2').hide();
							jQuery('#regular-hours-' + i + '-closed').show();
							jQuery('.hours-column', jQuery('#regular-hours-' + i)).removeClass('hours-24').addClass('closed');
							jQuery('#regular-time-' + i + '-start').val('');
							jQuery('#regular-time-' + i + '-end').val('');

							continue;
						}

						if (regular.hours_24) {
							if (jQuery('.hours', jQuery('#regular-hours-' + i)).hasClass('hours-24')) {
								continue;
							}

							jQuery('.dashicons-minus', jQuery('#regular-hours-' + i)).removeClass('dashicons-minus').addClass('dashicons-plus');
							jQuery('#regular-hours-' + i + '-closed').hide();
							jQuery('#regular-hours-' + i + '-extended').hide();
							jQuery('#regular-hours-' + i + '-extended-2').hide();

							if (jQuery('#regular-hours-' + i + '-base').is(':hidden')) {
								jQuery('#regular-hours-' + i + '-base').show();
							}

							jQuery('.hours-column', jQuery('#regular-hours-' + i)).removeClass('closed').addClass('hours-24');

							jQuery('#regular-time-' + i + '-start').val('00:00');
							jQuery('#regular-time-' + i + '-end').val('00:00');
							
							continue;
						}
					
						jQuery('#regular-hours-' + i + '-closed').hide();
						jQuery('.dashicons-minus', jQuery('#regular-hours-' + i + '-closed')).removeClass('dashicons-minus').addClass('dashicons-plus');
						jQuery('.hours-column', jQuery('#regular-hours-' + i)).removeClass('closed').removeClass('hours-24');
						
						if (regular.hours.length < 3 && jQuery('#regular-hours-' + i + '-extended-2').is(':visible')) {
							jQuery('#regular-time-' + i + '-start-extended-2').val('');
							jQuery('#regular-time-' + i + '-end-extended-2').val('');
							jQuery('#regular-hours-' + i + '-extended-2').hide();
							jQuery('.dashicons-minus', jQuery('#regular-hours-' + i + '-extended')).removeClass('dashicons-minus').addClass('dashicons-plus');
						}
						
						if (regular.hours.length < 2 && jQuery('#regular-hours-' + i + '-extended').is(':visible')) {
							jQuery('#regular-time-' + i + '-start-extended').val('');
							jQuery('#regular-time-' + i + '-end-extended').val('');
							jQuery('#regular-hours-' + i + '-extended').hide();
							jQuery('.dashicons-minus', jQuery('#regular-hours-' + i)).removeClass('dashicons-minus').addClass('dashicons-plus');
						}

						if (jQuery('#regular-hours-' + i + '-base').is(':hidden')) {
							jQuery('#regular-hours-' + i + '-base').show();
						}
						
						if (regular.hours.length >= 2 && jQuery('#regular-hours-' + i + '-extended').is(':hidden')) {
							jQuery('#regular-hours-' + i + '-extended').show();
							jQuery('.dashicons-minus', jQuery('#regular-hours-' + i + '-base')).removeClass('dashicons-plus').addClass('dashicons-minus');
						}

						if (regular.hours.length >= 3 && jQuery('#regular-hours-' + i + '-extended-2').is(':hidden')) {
							jQuery('#regular-hours-' + i + '-extended-2').show();
							jQuery('.dashicons-minus', jQuery('#regular-hours-' + i + '-extended')).removeClass('dashicons-plus').addClass('dashicons-minus');
						}
						
						if (regular.hours.length >= 1 && typeof regular.hours[0] == 'object' && regular.hours[0].length == 2) {
							jQuery('#regular-time-' + i + '-start').val(regular.hours[0][0]);
							jQuery('#regular-time-' + i + '-end').val(regular.hours[0][1]);
						}
						
						if (regular.hours.length >= 2 && typeof regular.hours[1] == 'object' && regular.hours[1].length == 2) {
							jQuery('#regular-time-' + i + '-start-extended').val(regular.hours[1][0]);
							jQuery('#regular-time-' + i + '-end-extended').val(regular.hours[1][1]);
						}
						
						if (regular.hours.length >= 3 && typeof regular.hours[2] == 'object' && regular.hours[2].length == 2) {
							jQuery('#regular-time-' + i + '-start-extended-2').val(regular.hours[2][0]);
							jQuery('#regular-time-' + i + '-end-extended-2').val(regular.hours[2][1]);
						}
					}
				}

				open_message(response.message, 'success');
			}, 'json');
		});
				
		jQuery('a.closed-text, a.closed, a.hours-24, a.copy, a.paste, a.add-subtract-toggle, :input[type=time]', '#open-regular, #open-special').each(function() {
			open_regular(this);
		});
		
		jQuery(':input', '#open-special').each(function() {
			open_special(this);
		});

		open_special(jQuery(':input[type=date]:eq(0)', jQuery('tbody > tr:last', '#open-special')), true);
	}

	if (jQuery('#setup.section', '#wpbody-content').length && typeof jQuery('#setup.section', '#wpbody-content').data('hunter') == 'object' && jQuery('#setup.section', '#wpbody-content').data('hunter') != null) {
		data = jQuery('#setup.section', '#wpbody-content').data('hunter');
		google_api_key = (typeof data.api_key == 'string' && data.api_key.length > 10) ? data.api_key : null;
		place_id = (typeof data.place_id == 'string' && data.place_id.length > 10) ? data.place_id : null;
		time_format = (typeof data.time_format == 'string' && data.time_format.length > 2) ? data.time_format : null;
		week_start = (typeof data.week_start == 'number' && data.week_start >= 0 || typeof data.week_start == 'string' && parseInt(data.week_start) >= 0) ? parseInt(data.week_start) : null;
		update = (typeof data.update == 'number' && data.update > 0 || typeof data.week_start == 'string' && parseInt(data.week_start) > 0) ? parseInt(data.update) : null;

		if (!jQuery('#place-id').val().length) {
			if (!jQuery('#api-key').val().length) {
				jQuery('#api-key').val(google_api_key);
			}
	
			jQuery('#place-id').val(place_id);
		}

		if (!jQuery('#time-format').val().length) {
			if (time_format != null) {
				jQuery('option', '#time-format').each(function() {
					if (!jQuery('#time-format').val().length && typeof jQuery(this).data('php') == 'string' && jQuery(this).data('php') == time_format) {
						jQuery('#time-format').val(jQuery(this).attr('value'));
					}
				});
				
				if (time_format.match(/^g.*$/) != null) {
					if (!jQuery('#time-format').val().length) {
						jQuery('#time-format').val(hour_format_12);
					}
					
					jQuery('#time-type-12').prop('checked', true);
					jQuery('#time-format').closest('td').removeClass('hours-24').addClass('hours-12');
				}
				else if (time_format.match(/^H.*$/) != null) {
					if (!jQuery('#time-format').val().length) {
						jQuery('#time-format').val(hour_format_24);
					}
					
					jQuery('#time-type-24').prop('checked', true);
					jQuery('#time-format').closest('td').removeClass('hours-12').addClass('hours-24');
				}
			}
			
			if (!jQuery('#time-format').val().length) {
				jQuery('#time-format').val('24_colon');
				jQuery('#time-type-24').prop('checked', true);
				jQuery('#time-format').closest('td').removeClass('hours-12').addClass('hours-24');
			}
		}
		
		if (!jQuery(':input:checked', jQuery('#week-start-0').closest('td')).length) {
			jQuery('#week-start-0').prop('checked', true);
		}
	}
	
	if (jQuery('.section', '#wpbody-content').length) {
		jQuery('#time-separator, #time-group-separator, #day-separator, #day-range-separator, #day-range-suffix, #day-range-suffix-special, #hours-24-text, #weekdays-text, #weekend-text, #everyday-text', '#open-settings-separators').each(function() {
			if (jQuery(this).val().match(/^"([^"]*)"$/) == null && jQuery(this).val().match(/^\s+|\s+$/) != null) {
				jQuery(this).val('"' + jQuery(this).val() + '"');
			}
			
			jQuery(this).on('focus keyup change blur', function(event) {
				if (event.type == 'focus') {
					if (jQuery(this).val().match(/^".*"$/) != null) {
						jQuery(this).val(jQuery(this).val().replace(/^"(.*)"$/, '$1'));
					}
					return;
				}
				
				if (jQuery(this).parent().hasClass('value-text-empty') || jQuery(this).parent().hasClass('value-text-suffix-empty')) {
					if (!jQuery(this).val().length && jQuery('.dashicons', jQuery(this).siblings('.value-text')).hasClass('dashicons-yes-alt')) {
						jQuery('.dashicons', jQuery(this).siblings('.value-text')).removeClass('dashicons-yes-alt').addClass('dashicons-marker');
						jQuery('.dashicons', jQuery(this).siblings('.value-empty')).removeClass('dashicons-marker').addClass('dashicons-yes-alt');
						return;
					}
					
					if (jQuery(this).val().length && jQuery('.dashicons', jQuery(this).siblings('.value-empty')).hasClass('dashicons-yes-alt')) {
						jQuery('.dashicons', jQuery(this).siblings('.value-empty')).removeClass('dashicons-yes-alt').addClass('dashicons-marker');
						jQuery('.dashicons', jQuery(this).siblings('.value-text')).removeClass('dashicons-marker').addClass('dashicons-yes-alt');
					}
					
					if (!jQuery(this).val().length || jQuery(this).parent().hasClass('value-text-empty')) {
						return;
					}
					
					regex = new RegExp('^.+' + ((jQuery('#day-range-suffix').val().length) ? jQuery('#day-range-suffix').val() : '[\w]') + '$');
					
					if (jQuery('.dashicons', jQuery(this).siblings('.value-suffix')).hasClass('dashicons-marker') && jQuery(this).val().match(regex) != null) {
						jQuery('.dashicons', jQuery(this).siblings('.value-suffix')).removeClass('dashicons-marker').addClass('dashicons-yes-alt');
					}
					else if (jQuery('.dashicons', jQuery(this).siblings('.value-suffix')).hasClass('dashicons-yes-alt') && jQuery(this).val().match(regex) == null) {
						jQuery('.dashicons', jQuery(this).siblings('.value-suffix')).removeClass('dashicons-yes-alt').addClass('dashicons-marker');
					}
					
					return;
				}

				if (!jQuery(this).siblings('.leading-space').hasClass('disabled')) {
					if (jQuery(this).val().match(/^"?\s+.*"?$/) != null) {
						jQuery('.dashicons', jQuery(this).siblings('.leading-space')).removeClass('dashicons-marker').addClass('dashicons-yes-alt');
					}
					else {
						jQuery('.dashicons', jQuery(this).siblings('.leading-space')).removeClass('dashicons-yes-alt').addClass('dashicons-marker');
					}
				}
				else if (jQuery(this).val().match(/^\s+[^\s]+.*$/) != null) {
					jQuery(this).val(jQuery(this).val().replace(/^\s+([^\s]+.*)$/, '$1'));
				}

				if (!jQuery(this).siblings('.trailing-space').hasClass('disabled')) {
					if (jQuery(this).val().match(/^"?.*\s+"?$/) != null) {
						jQuery('.dashicons', jQuery(this).siblings('.trailing-space')).removeClass('dashicons-marker').addClass('dashicons-yes-alt');
					}
					else {
						jQuery('.dashicons', jQuery(this).siblings('.trailing-space')).removeClass('dashicons-yes-alt').addClass('dashicons-marker');
					}
				}
				else if (jQuery(this).val().match(/^.*[^\s]+\s+$/) != null) {
					jQuery(this).val(jQuery(this).val().replace(/^(.*[^\s]+)\s+$/, '$1'));
				}
				
				if (jQuery(this).is('#day-range-suffix')) {
					regex = (jQuery(this).val().length) ? new RegExp('^(.+)(' + jQuery(this).val() + ')$') : null;
					
					jQuery('.value-text-suffix-empty', '#open-settings-separators').each(function() {
						if (regex != null && jQuery('#day-range-suffix').val().length) {
                            if (jQuery(':input:eq(0)', this).val().length && jQuery(':input:eq(0)', this).val().match(regex) != null && jQuery('.dashicons', jQuery('.value-suffix:eq(0)', this)).hasClass('dashicons-marker')) {
								jQuery('.dashicons', jQuery('.value-suffix:eq(0)', this)).removeClass('dashicons-marker').addClass('dashicons-yes-alt');
							}
                            else if ((!jQuery(':input:eq(0)', this).val().length || jQuery(':input:eq(0)', this).val().match(regex) == null) && jQuery('.dashicons', jQuery('.value-suffix:eq(0)', this)).hasClass('dashicons-yes-alt')) {
								jQuery('.dashicons', jQuery('.value-suffix:eq(0)', this)).removeClass('dashicons-yes-alt').addClass('dashicons-marker');
							}
						}
						else if ((regex == null || !jQuery(':input:eq(0)', this).val().length || regex != null && jQuery(':input:eq(0)', this).val().match(regex) == null) && jQuery('.dashicons', jQuery('.value-suffix:eq(0)', this)).hasClass('dashicons-yes-alt')) {
							jQuery('.dashicons', jQuery('.value-suffix:eq(0)', this)).removeClass('dashicons-yes-alt').addClass('dashicons-marker');
						}
					});
				}
				
				if (event.type == 'keyup') {
					return;
				}

				if ((event.type == 'change' || event.type == 'blur') && jQuery(this).val().match(/^"([^"]*)"$/) == null && jQuery(this).val().match(/^\s+|\s+$/) != null) {
					jQuery(this).val('"' + jQuery(this).val() + '"');
				}
			});
			
			if (jQuery(this).siblings('.action').length) {
				jQuery(this).siblings('.action').each(function() {
                    jQuery(this).on('click', function(event) {
						event.preventDefault();
						
						if (jQuery(this).hasClass('value-empty')) {
							if (jQuery('.dashicons', this).hasClass('dashicons-marker')) {
								jQuery('.dashicons', jQuery(this).siblings('.value-text')).removeClass('dashicons-yes-alt').addClass('dashicons-marker');
								jQuery('.dashicons', this).removeClass('dashicons-marker').removeClass('dashicons-marker').addClass('dashicons-yes-alt');
								jQuery(this).siblings(':input:eq(0)').val('').trigger('focus');
							}
							
							return;
						}
						
						if (jQuery(this).hasClass('value-suffix')) {
							if (!jQuery('#day-range-suffix').val().length || !jQuery(this).siblings(':input:eq(0)').val().length) {
								return;
							}
							
							regex = new RegExp('^(.+)(' + jQuery('#day-range-suffix').val() + ')$');
							
							if (jQuery(this).siblings(':input:eq(0)').val().match(regex) == null && jQuery('.dashicons', this).hasClass('dashicons-marker')) {
								jQuery('.dashicons', this).removeClass('dashicons-marker').addClass('dashicons-yes-alt');
								jQuery(this).siblings(':input:eq(0)').val(jQuery(this).siblings(':input:eq(0)').val() + jQuery('#day-range-suffix').val()).trigger('focus');
							}
							else if (jQuery(this).siblings(':input:eq(0)').val().match(regex) != null && jQuery('.dashicons', this).hasClass('dashicons-yes-alt')) {
								jQuery('.dashicons', this).removeClass('dashicons-yes-alt').addClass('dashicons-marker');
								jQuery(this).siblings(':input:eq(0)').val(jQuery(this).siblings(':input:eq(0)').val().replace(regex, '$1')).trigger('focus');
							}
							
							return;
						}
					})
                });
			}
			
			jQuery('.highlight').each(function() {
				jQuery(this).on('click', function() {
					if (jQuery(this).text().match(/^[0-9a-f][0-9a-f:.-]{7,80}$/) == null) {
						return;
					}
					
					if (window.getSelection && document.createRange) {
						selection = window.getSelection();
						range = document.createRange();
						range.selectNodeContents(this);
						selection.removeAllRanges();
						selection.addRange(range);
						return;
					}
					
					if (document.selection && document.body.createTextRange) {
						range = document.body.createTextRange();
						range.moveToElementText(this);
						range.trigger('select');
						return;
					}
				});
			});
		});
		
		jQuery('#time-format', '#open-setup').on('change', function() {
			if (jQuery('option[value="' + jQuery(this).val() + '"]', jQuery(this)).hasClass('hours-12')) {
				if (jQuery(this).closest('td').hasClass('hours-24')) {
					jQuery(this).closest('td').removeClass('hours-24').addClass('hours-12');
				}
				jQuery('#time-type-12').prop('checked', true);
			}
			else {
				if (jQuery(this).closest('td').hasClass('hours-12')) {
					jQuery(this).closest('td').removeClass('hours-12').addClass('hours-24');
				}
				jQuery('#time-type-24').prop('checked', true);
			}
		});
	
		jQuery('#time-type-12', '#open-setup').on('change', function() {
			if (jQuery(this).closest('td').hasClass('hours-24')) {
				jQuery(this).closest('td').removeClass('hours-24').addClass('hours-12');
			}

			if (typeof jQuery('#time-format').val() == 'string' && jQuery('#time-format').val().match(/^12.*$/) == null) {
				jQuery('#time-format').val(hour_format_12);
			}
		});
	
		jQuery('#time-type-24', '#open-setup').on('change', function() {
			if (jQuery(this).closest('td').hasClass('hours-12')) {
				jQuery(this).closest('td').removeClass('hours-12').addClass('hours-24');
			}

			if (typeof jQuery('#time-format').val() == 'string' && jQuery('#time-format').val().match(/^24.*$/) == null) {
				jQuery('#time-format').val(hour_format_24);
			}
		});

		jQuery('#time-separator', '#open-settings-separators').on('change', function() {
			jQuery('option', '#time-format').each(function() {
				if (typeof jQuery(this).data('initial') == 'string') {
					jQuery(this).html(jQuery(this).data('initial').replace(/^(.+)\s+–\s+(.+)$/i, '$1' + jQuery('#time-separator').val().replace(/^"|"$/g, '') + '$2'));
				}
			});
		});
			
		weekdays = (jQuery('#weekdays', '#open-setup').val().length) ? jQuery('#weekdays', '#open-setup').val().split(',') : [];

		jQuery(':checkbox', jQuery('#weekdays', '#open-setup').closest('td')).each(function() {
			jQuery(this).prop('checked', (weekdays.indexOf(String(jQuery(this).attr('value'))) >= 0));
			
            jQuery(this).on('change', function() {
				weekdays = [];
				if (jQuery(this).is(':checked') && jQuery('#weekend-' + jQuery(this).val(), '#open-setup').is(':checked')) {
					jQuery('#weekend-' + jQuery(this).val(), '#open-setup').prop('checked', false);
					weekend = [];
					jQuery(':checkbox:checked', jQuery('#weekend', '#open-setup').closest('td')).each(function() {
						weekend.push(jQuery(this).val());
					});
					jQuery('#weekend', '#open-setup').val(weekend.join(','));
				}
				
				jQuery(':checkbox:checked', jQuery('#weekdays', '#open-setup').closest('td')).each(function() {
                    weekdays.push(jQuery(this).val());
                });
				jQuery('#weekdays', '#open-setup').val(weekdays.join(','));
			});
        });
		
		weekend = (jQuery('#weekend', '#open-setup').val().length) ? jQuery('#weekend', '#open-setup').val().split(',') : [];

		jQuery(':checkbox', jQuery('#weekend', '#open-setup').closest('td')).each(function() {
			jQuery(this).prop('checked', (weekend.indexOf(String(jQuery(this).attr('value'))) >= 0));
			
            jQuery(this).on('change', function() {
				weekend = [];
				if (jQuery(this).is(':checked') && jQuery('#weekdays-' + jQuery(this).val(), '#open-setup').is(':checked')) {
					jQuery('#weekdays-' + jQuery(this).val(), '#open-setup').prop('checked', false);
					weekdays = [];
					jQuery(':checkbox:checked', jQuery('#weekdays', '#open-setup').closest('td')).each(function() {
						weekdays.push(jQuery(this).val());
					});
					jQuery('#weekdays', '#open-setup').val(weekdays.join(','));
				}
				
				jQuery(':checkbox:checked', jQuery('#weekend', '#open-setup').closest('td')).each(function() {
                    weekend.push(jQuery(this).val());
                });
				jQuery('#weekend', '#open-setup').val(weekend.join(','));
			});
        });
		
		if (jQuery('#structured-data', '#wpbody-content').length && jQuery('#name:visible', '#wpbody-content').length && !jQuery('#name:visible', '#wpbody-content').val().length && jQuery('#place-name:visible', '#wpbody-content').length && jQuery('#place-name:visible', '#wpbody-content').val().length) {
			jQuery('#name', '#wpbody-content').val(jQuery('#place-name', '#wpbody-content').val());
		}

		jQuery('#structured-data', '#wpbody-content').on('change', function() {
			jQuery('.structured-data', '#wpbody-content').each(function() {
                if (jQuery('#structured-data', '#wpbody-content').is(':checked')) {
					jQuery(this).show();
				}
				else {
					jQuery(this).hide();
				}
            });
			
			if (!jQuery('#name', '#wpbody-content').val().length && jQuery('#place-name', '#wpbody-content').length && jQuery('#place-name', '#wpbody-content').val().length) {
				jQuery('#name', '#wpbody-content').val(jQuery('#place-name', '#wpbody-content').val());
			}

			if (jQuery('#structured-data', '#wpbody-content').is(':checked')) {
				jQuery('#name', '#wpbody-content').trigger('focus');
			}
		});
		
		jQuery('#timezone').on('click', function (event) {
			event.preventDefault();
			document.location.href = jQuery('#timezone').siblings('a').attr('href');
			return false;
		});
		
		jQuery('#structured-data-preview').on('click', function (event) {
			event.preventDefault();
			if (jQuery('#open-overlay').length) {
				jQuery('#open-overlay').remove();
			}
			
			jQuery('#structured-data-preview').after('<div id="open-overlay"></div>');
			jQuery('#open-overlay').on('click', function(event) {
				if (jQuery(event.target).attr('id') == 'open-overlay') {
					jQuery(this).fadeOut(300, function() { jQuery(this).remove(); });
				}
			});
			
			jQuery('#open-overlay').append('<div id="open-close" class="close"><span class="dashicons dashicons-no" title="Close"></span></div><pre id="open-structured-data"></pre>');

			jQuery('#open-close').on('click', function() {
				jQuery('#open-overlay').fadeOut(300, function() { jQuery('#open-overlay').remove(); });
			});
			
			data = {
				action: 'we_are_open_admin_ajax',
				type: 'structured_data'
			};
			
			if (jQuery('#logo').length) {
				data['logo'] = jQuery('#logo').val();
			}

			if (jQuery('#name').length) {
				data['name'] = jQuery('#name').val();
			}

			if (jQuery('#address').length) {
				data['address'] = jQuery('#address').val();
			}

			if (jQuery('#telephone').length) {
				data['telephone'] = jQuery('#telephone').val();
			}

			if (jQuery('#business-type').length) {
				data['business_type'] = jQuery('#business-type').val();
			}

			if (jQuery('#price-range').length) {
				data['price-range'] = jQuery('#price-range').val();
			}

			jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
				if (response.success) {
					jQuery('#open-structured-data').html(response.data);
					open_syntax_highlight(jQuery('#open-structured-data'));
				}
				else {
					jQuery(this).fadeOut(300, function() { jQuery(this).remove(); });
				}
			}, 'json');
		});
		
		jQuery('#google-data-preview').on('click', function (event) {
			event.preventDefault();
			if (jQuery('#open-overlay').length) {
				jQuery('#open-overlay').remove();
			}
			
			jQuery('#google-data-preview').after('<div id="open-overlay"></div>');
			jQuery('#open-overlay').on('click', function(event) {
				if (jQuery(event.target).attr('id') == 'open-overlay') {
					jQuery(this).fadeOut(300, function() { jQuery(this).remove(); });
				}
			});
			
			jQuery('#open-overlay').append('<div id="open-close" class="close"><span class="dashicons dashicons-no" title="Close"></span></div><pre id="open-google-data"></pre>');

			jQuery('#open-close').on('click', function() {
				jQuery('#open-overlay').fadeOut(300, function() { jQuery('#open-overlay').remove(); });
			});
			
			data = {
				action: 'we_are_open_admin_ajax',
				type: 'google_data_preview'
			};

			jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
				if (response.success && response.data.length) {
					jQuery('#open-google-data').html(response.data);
					open_syntax_highlight(jQuery('#open-google-data'));
				}
				else {
					jQuery(this).fadeOut(300, function() { jQuery(this).remove(); });
				}
			}, 'json');
		});
		
		jQuery('#separators-button').on('click', function (event) {
			event.preventDefault();

			data = {
				action: 'we_are_open_admin_ajax',
				type: 'separators',
				time_separator: jQuery('#time-separator').val(),
				time_group_separator: jQuery('#time-group-separator').val(),
				day_separator: jQuery('#day-separator').val(),
				day_range_separator: jQuery('#day-range-separator').val(),
				day_range_suffix: jQuery('#day-range-suffix').val(),
				day_range_suffix_special: jQuery('#day-range-suffix-special').val(),
				closed_text: jQuery('#closed-text').val(),
				hours_24_text: jQuery('#hours-24-text').val(),
				weekdays_text: jQuery('#weekdays-text').val(),
				weekend_text: jQuery('#weekend-text').val(),
				everyday_text: jQuery('#everyday-text').val()
			};
			
			jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
				if (response.success) {
					open_message(response.message, 'success');
				}
				else {
					open_message(response.message, 'error');
				}
			}, 'json');
		});
		
		jQuery('#google-credentials-help').on('click', function (event) {
			event.preventDefault();
			if (jQuery('#google-credentials-steps').is(':visible')) {
				jQuery('#google-credentials-steps').slideUp(300);
			}
			else {
				jQuery('#google-credentials-steps').slideDown(300);
			}
		});
		
		jQuery('#google-credentials-button').on('click', function () {
			existing_button = jQuery('#google-credentials-button').html();
			jQuery('#google-credentials-button').html('Saving&hellip;');
			data = {
				action: 'we_are_open_admin_ajax',
				type: 'google_business_credentials',
				api_key: jQuery('#api-key').val(),
				place_id: jQuery('#place-id').val()
			};

			jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
				if (response.success) {
					jQuery('#google-credentials-button').html('Saved');
					
					if (typeof response.google_data_exists == 'boolean' && response.google_data_exists && jQuery('#google-data-preview').is(':hidden')) {
						jQuery('#google-data-preview').fadeIn(300);
					}
					else if ((typeof response.google_data_exists != 'boolean' || typeof response.google_data_exists == 'boolean' && !response.google_data_exists) && jQuery('#google-data-preview').is(':visible')) {
						jQuery('#google-data-preview').fadeOut(300);
					}
					
					if (response.business_name == null || response.business_name != null && !response.business_name.length) {
						jQuery('#place-name').val('');
						
						if (jQuery('#place-name').is(':visible')) {
							jQuery('#place-name').closest('.google-data').hide();
						}
					}
					else if (response.business_name != null && response.business_name.length) {
						if (jQuery('#place-name').is(':hidden')) {
							jQuery('#place-name').closest('.google-data').show();
						}
						
						jQuery('#place-name').val(response.business_name);
					}
					
					if (typeof response.message == 'string') {
						open_message(response.message);
					}
					
					setTimeout(function() { jQuery('#google-credentials-button').html(existing_button); }, 1200);
				}
				else {
					if (typeof response.message == 'string') {
						open_message(response.message, 'error');
					}
					
					jQuery('#google-credentials-button').html('Retry');
				}
			}, 'json');
		});

		jQuery('#custom-styles-button').on('click', function () {
			existing_button = jQuery('#custom-styles-button').html();
			jQuery('#custom-styles-button').html('Saving&hellip;');
			data = {
				action: 'we_are_open_admin_ajax',
				type: 'custom_styles',
				custom_styles: jQuery('#custom-styles').val()
			};

			jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
				if (response.success) {
					jQuery('#custom-styles-button').html('Saved');
					setTimeout(function() { jQuery('#custom-styles-button').html(existing_button); }, 1200);
				}
				else {
					if (typeof response.message == 'string') {
						open_message(response.message, 'error');
					}
					jQuery('#custom-styles-button').html('Retry');

				}
			}, 'json');
		});

		jQuery('a[href*="#"]', '#shortcodes').on('click', function(event) {
			event.preventDefault();
			if (jQuery(jQuery(this).attr('href'), '#shortcodes').length) {
				jQuery([document.documentElement, document.body]).animate({
					scrollTop: jQuery(jQuery(this).attr('href'), '#shortcodes').offset().top - 35
				}, 150);
			}
		});
		
		jQuery('#clear-cache-button').on('click', function () {
			sessionStorage.removeItem('we_are_open_hours');
			jQuery('#clear-cache-button').html('Clearing&hellip;');
			data = {
				action: 'we_are_open_admin_ajax',
				type: 'clear_cache'
			};

			jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
				if (response.success) {
					jQuery('#clear-cache-button').html('Cleared');
					setTimeout(function() { document.location.href = document.location.href.replace(location.hash, ''); }, 300);
				}
				else {
					jQuery('#clear-cache-button').html('Retry Clear Cache');
				}
			}, 'json');
		});

		jQuery('#reset-button').on('click', function () {
			if (jQuery('#reset-confirm-text').is(':hidden')) {
				jQuery('#reset-confirm-text').slideDown(300);
			}
			else if (jQuery('#reset-confirm-text').is(':visible') && jQuery('#reset-all').is(':checked')) {
				data = {
					action: 'we_are_open_admin_ajax',
					type: 'reset'
				};

				jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
					if (response.success) {
						document.location.href = document.location.href.replace(location.hash, '');
					}
					else {
						jQuery('#reset-all').prop('checked', false);
					}
				}, 'json');
			}
		});

		jQuery('.nav-tab', jQuery('nav:eq(0)', '#wpbody-content')).each(function(tab_index) {
			jQuery(this).on('click', function (event) {
				event.preventDefault();
				section = (typeof jQuery(this).attr('href') == 'string') ? jQuery(this).attr('href').replace(/#([\w-]+)/, '$1') : null;
				
				if (jQuery('.is-dismissible', '#wpbody-content').length) {
					jQuery('.is-dismissible', '#wpbody-content').remove();
				}
				
				if (tab_index != jQuery('.nav-tab-active', jQuery('nav:eq(0)', '#wpbody-content')).index('.nav-tab')) {
					jQuery('.nav-tab:not(:eq('+tab_index+'))', jQuery('nav:eq(0)', '#wpbody-content')).removeClass('nav-tab-active').removeProp('aria-current');
					jQuery('.nav-tab:eq('+tab_index+')', jQuery('nav:eq(0)', '#wpbody-content')).addClass('nav-tab-active').prop('aria-current', 'page');
				}
				
				jQuery('.section', '#wpbody-content').each(function(section_index) {
					if (section == null && section_index == 0 || section != null && section == jQuery(this).attr('id')) {
						if (jQuery(this).hasClass('hide')) {
							jQuery(this).removeClass('hide');
						}
					}
					else if (!jQuery(this).hasClass('hide')) {
						jQuery(this).addClass('hide');
					}
				});
				
				data = {
					action: 'we_are_open_admin_ajax',
					type: 'section',
					section: (typeof section == 'string' && section.match(/^setup$/i) == null) ? section : null
				};

				jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
					if (response.success) {
						if (window.history && window.history.pushState) {
							history.pushState(null, null, '#' + section);
						}
						else {
							location.hash = '#' + section;
						}
					}
				}, 'json');
						
				setTimeout(function() {
					window.scrollTo(0, 0);
					setTimeout(function() {
						window.scrollTo(0, 0);
						if (tab_index != jQuery('.nav-tab-active', jQuery('nav:eq(0)', '#wpbody-content')).index('.nav-tab')) {
							jQuery('.nav-tab:not(:eq('+tab_index+'))', jQuery('nav:eq(0)', '#wpbody-content')).removeClass('nav-tab-active').removeProp('aria-current');
							jQuery('.nav-tab:eq('+tab_index+')', jQuery('nav:eq(0)', '#wpbody-content')).addClass('nav-tab-active').prop('aria-current', 'page');
						}
						}, 100);
					}, 10);
			});
		});

		setTimeout(function() {
			window.scrollTo(0, 0);
			setTimeout(function() {
				window.scrollTo(0, 0);
				}, 100);
			}, 10);
	}
	
	open_media_image();
	open_syntax_highlight();
		
	return;
}

function open_regular(e) {
	if (jQuery(e).is('a')) {
		jQuery(e).on('click', function(event) {
			e = this;
			event.preventDefault();

			if (jQuery(e).hasClass('disabled')) {
				return false;
			}

			var current_opening_hours = (typeof sessionStorage.getItem('we_are_open_hours') == 'string') ? JSON.parse(sessionStorage.getItem('we_are_open_hours')) : null;
			
			if (jQuery(e).hasClass('copy')) {
				if (jQuery(e).closest('.hours-column').hasClass('closed')) {
					current_opening_hours = null;
					sessionStorage.removeItem('we_are_open_hours');

					jQuery('.paste', '#opening-hours').each(function() {
						jQuery(this).addClass('disabled');
					});

					return false;
				}

				current_opening_hours = {
					closed: false,
					hours_24: (jQuery(e).closest('.hours-column').hasClass('hours-24')),
					hours: []
				};

				if (!jQuery(e).closest('.hours-column').hasClass('hours-24')) {
					if (!jQuery('.base:eq(0)', jQuery(e).closest('.hours-column')).is(':visible') || !jQuery('.base:eq(0) :input:eq(0)', jQuery(e).closest('.hours-column')).val().length || !jQuery('.base:eq(0) :input:eq(1)', jQuery(e).closest('.hours-column')).val().length) {
						current_opening_hours.closed = true;
					}
					else {
						current_opening_hours.hours[0] = [ jQuery('.base:eq(0) :input:eq(0)', jQuery(e).closest('.hours-column')).val(), jQuery('.base:eq(0) :input:eq(1)', jQuery(e).closest('.hours-column')).val() ];
						
						if (jQuery('.extended:eq(0)', jQuery(e).closest('.hours-column')).is(':visible') && jQuery('.extended:eq(0) :input:eq(0)', jQuery(e).closest('.hours-column')).val().length && jQuery('.extended:eq(0) :input:eq(1)', jQuery(e).closest('.hours-column')).val().length) {
							current_opening_hours.hours[1] = [ jQuery('.extended:eq(0) :input:eq(0)', jQuery(e).closest('.hours-column')).val(), jQuery('.extended:eq(0) :input:eq(1)', jQuery(e).closest('.hours-column')).val() ];

							if (jQuery('.extended-2:eq(0)', jQuery(e).closest('.hours-column')).is(':visible') && jQuery('.extended-2:eq(0) :input:eq(0)', jQuery(e).closest('.hours-column')).val().length && jQuery('.extended-2:eq(0) :input:eq(1)', jQuery(e).closest('.hours-column')).val().length) {
								current_opening_hours.hours[2] = [ jQuery('.extended-2:eq(0) :input:eq(0)', jQuery(e).closest('.hours-column')).val(), jQuery('.extended-2:eq(0) :input:eq(1)', jQuery(e).closest('.hours-column')).val() ];
							}
						}
					}	
				}

				jQuery('.paste.disabled', '#opening-hours').each(function() {
					jQuery(this).removeClass('disabled');
				});
				
				sessionStorage.setItem('we_are_open_hours', JSON.stringify(current_opening_hours));
				return false;
			}
			
			if (jQuery(e).hasClass('paste')) {
				if (current_opening_hours == null) {
					return false;
				}

				e = jQuery(e).closest('.hours-column');
				if (current_opening_hours.closed) {
					if (jQuery(e).hasClass('closed')) {
						return false;
					}

					jQuery('.dashicons-minus', e).removeClass('dashicons-minus').addClass('dashicons-plus');
					jQuery('.base:eq(0)', e).hide();
					jQuery('.extended:eq(0)', e).hide();
					jQuery('.extended-2:eq(0)', e).hide();
					jQuery('.closed:eq(0)', e).show();
					jQuery(e).removeClass('hours-24').addClass('closed');
					jQuery('.base:eq(0) :input:eq(0)', e).val('');
					jQuery('.base:eq(0) :input:eq(1)', e).val('');

					return false;
				}
				
				if (jQuery(e).hasClass('closed')) {
					jQuery(e).removeClass('closed');
					jQuery('.closed:eq(0)', e).hide();
				}

				if (current_opening_hours.hours_24) {
					if (jQuery('.hours', e).hasClass('hours-24')) {
						return false;
					}

					jQuery('.dashicons-minus', e).removeClass('dashicons-minus').addClass('dashicons-plus');
					jQuery('.closed:eq(0)', e).hide();
					jQuery('.extended:eq(0)', e).hide();
					jQuery('.extended-2:eq(0)', e).hide();

					if (jQuery('.base:eq(0)', e).is(':hidden')) {
						jQuery('.base:eq(0)', e).show();
					}

					jQuery(e).addClass('hours-24');

					jQuery('.base:eq(0) :input:eq(0)', e).val('00:00');
					jQuery('.base:eq(0) :input:eq(1)', e).val('00:00');
					
					return false;
				}
			
				jQuery('.dashicons-minus', jQuery('.closed:eq(0)', e)).removeClass('dashicons-minus').addClass('dashicons-plus');
				jQuery(e).removeClass('hours-24');
				
				if (current_opening_hours.hours.length < 3 && jQuery('.extended-2:eq(0)', e).is(':visible')) {
					jQuery('.extended-2:eq(0) :input:eq(0)', e).val('');
					jQuery('.extended-2:eq(0) :input:eq(1)', e).val('');
					jQuery('.extended-2:eq(0)', e).hide();
					jQuery('.dashicons-minus', jQuery('.extended:eq(0)', e)).removeClass('dashicons-minus').addClass('dashicons-plus');
				}
				
				if (current_opening_hours.hours.length < 2 && jQuery('.extended:eq(0)', e).is(':visible')) {
					jQuery('.extended:eq(0) :input:eq(0)', e).val('');
					jQuery('.extended:eq(0) :input:eq(1)', e).val('');
					jQuery('.extended:eq(0)', e).hide();
					jQuery('.dashicons-minus', e).removeClass('dashicons-minus').addClass('dashicons-plus');
				}

				if (jQuery('.base:eq(0)', e).is(':hidden')) {
					jQuery('.base:eq(0)', e).show();
				}
				
				if (current_opening_hours.hours.length >= 2 && jQuery('.extended:eq(0)', e).is(':hidden')) {
					jQuery('.extended:eq(0)', e).show();
					jQuery('.dashicons-minus', jQuery('.base:eq(0)', e)).removeClass('dashicons-plus').addClass('dashicons-minus');
				}

				if (current_opening_hours.hours.length >= 3 && jQuery('.extended-2:eq(0)', e).is(':hidden')) {
					jQuery('.extended-2:eq(0)', e).show();
					jQuery('.dashicons-minus', jQuery('.extended:eq(0)', e)).removeClass('dashicons-plus').addClass('dashicons-minus');
				}
				
				if (current_opening_hours.hours.length >= 1 && typeof current_opening_hours.hours[0] == 'object' && current_opening_hours.hours[0].length == 2) {
					jQuery('.base:eq(0) :input:eq(0)', e).val(current_opening_hours.hours[0][0]);
					jQuery('.base:eq(0) :input:eq(1)', e).val(current_opening_hours.hours[0][1]);
				}
				
				if (current_opening_hours.hours.length >= 2 && typeof current_opening_hours.hours[1] == 'object' && current_opening_hours.hours[1].length == 2) {
					jQuery('.extended:eq(0) :input:eq(0)', e).val(current_opening_hours.hours[1][0]);
					jQuery('.extended:eq(0) :input:eq(1)', e).val(current_opening_hours.hours[1][1]);
				}
				
				if (current_opening_hours.hours.length >= 3 && typeof current_opening_hours.hours[2] == 'object' && current_opening_hours.hours[2].length == 2) {
					jQuery('.extended-2:eq(0) :input:eq(0)', e).val(current_opening_hours.hours[2][0]);
					jQuery('.extended-2:eq(0) :input:eq(1)', e).val(current_opening_hours.hours[2][1]);
				}

				return false;
			}

			if (jQuery(e).hasClass('closed')) {
				jQuery(':input:eq(0)', jQuery(e).closest('li').siblings('.base')).val('');
				jQuery(':input:eq(1)', jQuery(e).closest('li').siblings('.base')).val('');
				jQuery('.base:visible, .extended:visible, .extended-2:visible', jQuery(e).closest('td')).slideUp(300, function() {
					jQuery('.add-subtract-toggle .dashicons-minus', jQuery('.base, .extended', jQuery(e).closest('td'))).removeClass('dashicons-minus').addClass('dashicons-plus');
					jQuery(':input:eq(0)', jQuery('.base, .extended, .extended-2', jQuery(e).closest('td'))).val('');
					jQuery(':input:eq(1)', jQuery('.base, .extended, .extended-2', jQuery(e).closest('td'))).val('');
				});
				jQuery('.closed:eq(0)', jQuery(this).closest('td')).slideDown(300);
				jQuery(this).closest('td').addClass('closed').removeClass('hours-24');

				return false;
			}

			jQuery(e).closest('td').removeClass('closed');

			if (jQuery('.closed:eq(0)', jQuery(e).closest('td')).is(':visible')) {
				jQuery('.closed:eq(0)', jQuery(e).closest('td')).slideUp(300);
			}
			
			if (!jQuery(e).closest('li').hasClass('extended-2')) {
				if ((jQuery(e).closest('li').hasClass('closed') || jQuery(e).hasClass('add-subtract-toggle')) && jQuery(e).closest('li').next().is(':hidden')) {
					jQuery(e).closest('li').next().slideDown(300, function() {
						if (jQuery(e).hasClass('closed-text') || jQuery(e).hasClass('add-subtract-toggle')) {
							jQuery(':input:eq(0)', this).trigger('focus');
						}
						if (!jQuery(e).closest('li').hasClass('closed')) {
							jQuery('.add-subtract-toggle .dashicons', jQuery(this).closest('li').prev()).removeClass('dashicons-plus').addClass('dashicons-minus');
						}
					});
				}
				else {
					if (jQuery(e).closest('li').hasClass('base') && jQuery(e).closest('li').siblings('.extended-2').is(':visible')) {
						jQuery(':input', jQuery(e).closest('li').siblings('.extended-2')).val('');
						jQuery(e).closest('li').siblings('.extended-2').slideUp(300, function() {
							jQuery('.add-subtract-toggle .dashicons', jQuery(this).prev()).removeClass('dashicons-minus').addClass('dashicons-plus');
							jQuery('.add-subtract-toggle .dashicons', this).removeClass('dashicons-minus').addClass('dashicons-plus');
						});
					}
					
					jQuery(':input', jQuery(e).closest('li').next()).val('');
					jQuery(e).closest('li').next().slideUp(300, function() {
							jQuery('.add-subtract-toggle .dashicons', jQuery(this).prev()).removeClass('dashicons-minus').addClass('dashicons-plus');
							jQuery('.add-subtract-toggle .dashicons', this).removeClass('dashicons-minus').addClass('dashicons-plus');
						});
						
					if (jQuery(e).hasClass('add-subtract-toggle')) {
						jQuery(':input:eq(0)', jQuery(e).closest('li')).trigger('focus');
					}
				}
			}
			
			if (jQuery(e).hasClass('hours-24') && !jQuery(e).closest('td').hasClass('hours-24') ) {
				jQuery('.base :input:eq(0)', jQuery(e).closest('td')).val('00:00');
				jQuery('.base :input:eq(1)', jQuery(e).closest('td')).val('00:00');
				jQuery(e).closest('td').addClass('hours-24');

				return false;
			}
			
			if (jQuery('.base :input:eq(0)', jQuery(e).closest('td')).val() == '00:00' && (jQuery('.base :input:eq(1)', jQuery(e).closest('td')).val() == '00:00' || jQuery('.base :input:eq(1)', jQuery(e).closest('td')).val() == '23:59')) {
				jQuery(':input:eq(0)', jQuery('.base, .extended, .extended-2', jQuery(e).closest('td'))).val('');
				jQuery(':input:eq(1)', jQuery('.base, .extended, .extended-2', jQuery(e).closest('td'))).val('');
				jQuery(e).closest('td').removeClass('hours-24');

				return false;
			}

			return false;
		});
	}
	
	if (jQuery(e).is(':input') && jQuery(e).attr('type') == 'time') {
		jQuery(e).on('keyup change blur', function(event) {
			if (jQuery(':input[type=time]:eq(0)', jQuery(this).closest('li')).val() == '00:00' && (jQuery(':input[type=time]:eq(1)', jQuery(this).closest('li')).val() == '00:00' || jQuery(':input[type=time]:eq(1)', jQuery(this).closest('li')).val() == '23:59')) {
				jQuery(this).closest('td').addClass('hours-24');
			}
			else if (jQuery(this).closest('td').hasClass('hours-24')) {
				jQuery(this).closest('td').removeClass('hours-24');
			}
		});
	}
	return;
}

function open_special(e, event) {
	if (jQuery(e).attr('type') != 'date' && !jQuery(e).is(':checkbox')) {
		return;
	}
	
	if (!event) {
		jQuery(e).on('change blur', function() {
			return open_special(this, true);
		});
		return;
	}

	var special_delete = 0,
		html = '';
	
	if (jQuery(e).is(':checkbox')) {
		jQuery('.check-column > :checkbox', '#open-special').each(function() {
            if (jQuery(this).is(':checked') && typeof jQuery(this).closest('tr').attr('id') == 'string' && jQuery(this).closest('tr').attr('id').match(/^special-hours-(new|\d+)$/i) != null && jQuery(':input[type=date]', jQuery(this).closest('tr')).length) {
				special_delete++;
			}
        });
		
		if (special_delete > 0 && jQuery('#delete-possible').is(':hidden') && jQuery('tbody > tr', '#open-special').length > 1) {
			jQuery('#delete-possible').fadeIn(300);
		}
		else if (special_delete == 0 && jQuery('#delete-possible').is(':visible')) {
			jQuery('#delete-possible').fadeOut(300);
		}
	
		return;
	}

	if (jQuery(e).val().length && jQuery(e).val().match(/^\d{4}-\d{1,2}-\d{1,2}$/) != null && jQuery(e).closest('tr').index() == jQuery('tbody > tr', '#open-special').length - 1) {
		html = jQuery('#special-hours-new')[0].outerHTML;
		html = html.replace(/(id="special-hours-new")/, '$1 style="display: none;"').replace(/ hours-24/, '').replace(/value="[^"]*"/g, 'value=""').replace(/([\b_"\[-])new([\b_"\]-])/g, '$1' + (jQuery('tbody > tr', '#open-special').length - 1) + '$2');
		jQuery(e).closest('tr').after(html);
		e = jQuery('#special-hours-' + (jQuery('tbody > tr', '#open-special').length - 2));
		setTimeout(function() { jQuery(e).fadeIn(300, function() { jQuery(e).removeAttr('style'); }); }, 400);
		open_special(jQuery(':input[type=date]:eq(0)', e));
		jQuery('.extended:eq(0)', e).hide();
		jQuery('.extended-2:eq(0)', e).hide();
		jQuery('a.closed-text, a.closed, a.hours-24, a.copy, a.paste, a.add-subtract-toggle', e).each(function() {
			open_regular(this);
		});
	}
	else if ((!jQuery(e).val().length || jQuery(e).val().match(/^\d{4}-\d{1,2}-\d{1,2}$/) == null) && jQuery('tbody > tr', '#open-special').length > 1 && jQuery(e).closest('tr').index() == jQuery('tbody > tr', '#open-special').length - 2 && (!jQuery(':input[type=date]:eq(0)', jQuery('tbody > tr:last', '#open-special')).val().length || jQuery(':input[type=date]:eq(0)', jQuery('tbody > tr:last', '#open-special')).val().match(/^\d{4}-\d{1,2}-\d{1,2}$/) == null)) {
		jQuery('tbody > tr:eq(' + (jQuery('tbody > tr', '#open-special').length - ((jQuery('tbody > tr:last', '#open-special').is('#special-hours-new')) ? 2 : 1)) +')', '#open-special').fadeOut(300, function() { jQuery(this).remove(); });
	}
	
	return;
}

function open_message(message, type) {
	if (typeof message != 'string') {
		return;
	}
	
	if (typeof type != 'string') {
		var type = 'success';
	}
	
	var e = (jQuery('#open-settings').length) ? jQuery('#open-settings') : jQuery('#open'),
		html = '<div id="open-message" class="notice ' + type + ' notice-' + type + ' visible is-dismissible">\n'
			+ '<p><strong>' + message + '</strong></p>\n'
			+ '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>\n'
			+ '</div>';
	
	if (jQuery('#open-message').length) {
		jQuery('#open-message').remove();
	}
	
	jQuery('h1:eq(0)').after(html);
	jQuery('button.notice-dismiss:eq(0)', '#open-message').on('click', function () {
		jQuery('#open-message').remove();
	});
	
	setTimeout(function() {
		if (jQuery('#open-message').length) {
			jQuery('#open-message').slideUp(300, function() { jQuery(this).remove(); });
		}
	}, 15000);

	return;
}

function open_media_image() {
	var data = {},
		image_id = null
		image_frame = null,
		selection = null,
		gallery_ids = new Array(),
		my_index = 0;
	
	jQuery('#logo-image-delete').on('click', function(event) {
		data = {
			action: 'we_are_open_admin_ajax',
			type: 'logo_delete'
		};
	
		jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
			if (response.success) {
				jQuery('#logo-image-id').val('');
				jQuery('img', '#logo-image-preview').remove();
				jQuery('#logo-image-preview').html('');
				jQuery('#logo-image').html(jQuery('.dashicons', '#logo-image')[0].outerHTML + ' ' + jQuery('#logo-image').data('set-text'));
				jQuery('.logo-image:eq(0)').addClass('empty');
				jQuery('.delete', '.logo-image:eq(0)').hide();
				jQuery('#logo-image-row').addClass('empty');
			}
		}, 'json');
		
		return;		
	});

	jQuery('#logo-image, #logo-image-preview').on('click', function(event) {
		event.preventDefault();
		
		if (typeof wp == 'undefined') {
			return;
		}
				
		if (image_frame) {
			image_frame.open();
		}
		
		image_frame = wp.media({
			title: 'Select Media',
			multiple: false,
			library: {
				type: 'image',
			}
		});
		
		image_frame.on('select', function() {
			selection = image_frame.state().get('selection');
			gallery_ids = new Array();
			my_index = 0;
			
			selection.each(function(attachment) {
				gallery_ids[my_index] = attachment['id'];
				my_index++;
			});
			
			image_id = gallery_ids.join(",");
			jQuery('#logo-image-id').val(image_id);
			
			data = {
				action: 'we_are_open_admin_ajax',
				type: 'logo',
				id: image_id
			};
			
			jQuery.post(we_are_open_admin_ajax.url, data, function(response) {
				if (response.success) {
					jQuery('#logo-image-row').removeClass('empty');
					jQuery('.logo-image.empty').removeClass('empty');
					jQuery('#logo-image-preview')
						.html(response.image)
						.addClass('image');
					jQuery('#logo-image').html(jQuery('.dashicons', '#logo-image')[0].outerHTML + ' ' + jQuery('#logo-image').data('replace-text'));
					jQuery('.delete', '.logo-image:eq(0)').css('display', 'inline-block');
				}
			}, 'json');
		});
		
		image_frame.on('open', function() {
			var selection = image_frame.state()
				.get('selection'),
				ids = jQuery('#logo-image-id').val().split(',');
				
			ids.forEach(function(id) {
				var attachment = wp.media.attachment(id);
				attachment.fetch();
				selection.add(attachment ? [attachment] : []);
			});
		});
		
		image_frame.open();
	});
	
	return;
}

function open_syntax_highlight(e) {
	if (typeof e == 'undefined') {
		var e = jQuery('#open-data');
	}
	
	if (!jQuery(e).length || jQuery('span', jQuery(e)).length) {
		return;
	}
	
	var json = e
		.html()
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;');

	jQuery(e)
		.html(json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
			var class_name = 'number';
			if (/^"/.test(match)) {
				if (/:$/.test(match)) {
					class_name = 'key';
				}
				else {
					class_name = 'string';
				}
			}
			else if (/true|false/.test(match)) {
				class_name = 'boolean';
			}
			else if (/null/.test(match)) {
				class_name = 'null';
			}
			return '<span class="' + class_name + '">' + match + '</span>';
		}));
		
	if (jQuery(e).attr('id').match(/structured[_-]?data/i) != null) {
		jQuery(e).html(jQuery(e).html().replace(/(<span\s+class="key">"image":<\/span>\s+<span\s+class="boolean)(">)(false)(<\/span>)/i, '$1 error$2$3 <span class="dashicons dashicons-warning" title="Required"></span>$4'));
	}
	
	return;
}

jQuery(document).ready(function($) {
	open_admin();
	if (window.history && window.history.pushState) {
		jQuery(window).on('popstate', function() {
			open_admin(true);
		});
	}
});

jQuery(window).on('keydown', function(event) {
    if (jQuery('.button-primary').is(':visible') && (event.ctrlKey || event.metaKey)) {
        if (String.fromCharCode(event.which).toLowerCase() == 's') {
            event.preventDefault();
			jQuery('.button-primary:visible:eq(0)').trigger('click');
			return false;
        }
    }
});
