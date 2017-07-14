/*------------------------------------------------------------------------
 Solidres - Hotel booking plugin for WordPress
 ------------------------------------------------------------------------
 @Author    Solidres Team
 @Website   http://www.solidres.com
 @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
 @License   GNU General Public License version 3, or later
 ------------------------------------------------------------------------*/

if (typeof(Solidres) === 'undefined') {
    var Solidres = {};
}

Solidres.context = 'backend';

jQuery(document).ready(function ($) {
	$(".srform_select_country").change(function (e) {
		e.preventDefault();
		var country_id = $(this).val();
		$.ajax({
			type: 'post',
			dataType: 'html',
			url: solidres.ajaxurl,
			data: { action: 'solidres_load_states', country_id: country_id, security: solidres.nonce_load_states },
			success: function ( response ) {
				$('.srform_select_state').html( response );
			}
		});

		$.ajax({
			type : 'post',
			dataType : 'html',
			url : solidres.ajaxurl,
			data : { action: 'solidres_load_taxes', country_id : country_id, security : solidres.nonce_load_taxes },
			success: function( response ) {
				$('#srform_tax').html( response );
			}
		});
	});

	$("#srform").validate();

	$("#srform_valid_from").datepicker({
		dateFormat: 'dd-mm-yy'
	});
	$("#srform_valid_to").datepicker({
		dateFormat: 'dd-mm-yy'
	});
	$("#srform_valid_from_checkin").datepicker({
		dateFormat: 'dd-mm-yy'
	});
	$("#srform_valid_to_checkin").datepicker({
		dateFormat: 'dd-mm-yy'
	});

	$("#filter_checkin_from").datepicker({
		dateFormat: 'dd-mm-yy'
	});
	$("#filter_checkin_to").datepicker({
		dateFormat: 'dd-mm-yy'
	});
	$("#filter_checkout_from").datepicker({
		dateFormat: 'dd-mm-yy'
	});
	$("#filter_checkout_to").datepicker({
		dateFormat: 'dd-mm-yy'
	});


	function create_date_compare_res(date_string) {
		var date_part = date_string.split('-');
		var new_date = new Date(date_part[2], ( date_part[1] - 1 ), date_part[0]);
		return new_date;
	}

	$('#filter_reservation').click(function () {
		var checkin_from = $('#filter_checkin_from').val();
		var checkin_to = $('#filter_checkin_to').val();
		var checkout_from = $('#filter_checkout_from').val();
		var checkout_to = $('#filter_checkout_to').val();

		var cito = create_date_compare_res(checkin_to);
		var cifrom = create_date_compare_res(checkin_from);
		var coto = create_date_compare_res(checkout_to);
		var cofrom = create_date_compare_res(checkout_from);

		if (checkin_from != '' && checkin_to == '') {
			alert('Please select check-in to value');
			$('#filter_checkin_to').focus();
			return false;
		}
		else if (checkin_from == '' && checkin_to != '') {
			alert('Please select check-in from value');
			$('#filter_checkin_from').focus();
			return false;
		}
		else if (cito < cifrom) {
			alert('Check-in to to value not less than check-in from value');
			$('#filter_checkin_to').focus();
			return false;
		}
		else if (checkout_from != '' && checkout_to == '') {
			alert('Please select check-out to value');
			$('#filter_checkout_to').focus();
			return false;
		}
		else if (checkout_from == '' && checkout_to != '') {
			alert('Please select check-out from value');
			$('#filter_checkout_from').focus();
			return false;
		}
		else if (coto < cofrom) {
			alert('Check-out to to value not less than check-out from value');
			$('#filter_checkout_to').focus();
			return false;
		}
		else {
			return true;
		}
	});

	$(".select_reservation_asset_id").change(function () {
		var reservation_asset_id = $(this).val();
		$.ajax({
			type: 'post',
			dataType: 'html',
			url: solidres.ajaxurl,
			data: { action: 'solidres_load_coupons', reservation_asset_id: reservation_asset_id, security: solidres.nonce_load_coupons },

			success: function (response) {
				if (response == 0) {
					$('div.srform_coupon_id').html('No coupon available.');
				}
				else {
					$('div.srform_coupon_id').html(response);
				}
			}
		});
		$.ajax({
			type: 'post',
			dataType: 'html',
			url: solidres.ajaxurl,
			data: { action: 'solidres_load_extras', reservation_asset_id: reservation_asset_id, security: solidres.nonce_load_extras },

			success: function (response) {
				if (response == 0) {
					$('div.srform_extra_id').html('No extra available.');
				}
				else {
					$('div.srform_extra_id').html(response);
				}
			}
		});
	});

	$('.cancel_reservation_btn').click(function () {
		var reservation_id = $('#reservation_id').val();
		var customer_id = $('#customer_id').val();
		var nonce = $(this).attr('data-nonce');
		$.ajax({
			type: 'post',
			dataType: 'html',
			url: solidres.ajaxurl,
			data: {
				action: 'solidres_cancel_reservation',
				reservation_id: reservation_id,
				customer_id: customer_id,
				nonce: nonce
			},
			success: function (response) {
				if (response == 1) {
					$('span.reservation_status_user').html('Canceled');
					$('#cancel_reservation_form').empty();
					$('.reservation_code_row span').removeClass().addClass('canceled_code');
				}
				else {
					$('#cancel_reservation_form').append("<p>Can't cancel this reservation!</p>");
				}
			}
		});
		return false;
	});

	$('.submit_note_btn').click(function () {
		var reservation_note_text = $('.reservation_note_text').val();
		var notify_customer_check = $('#notify_customer').is(":checked");
		var visible_in_frontend_check = $('#visible_in_frontend').is(":checked");
		var reservation_id = $('#reservation_id').val();
		if (reservation_note_text != '') {
			$('.processing').removeClass('nodisplay');
			$.ajax({
				type: 'post',
				dataType: 'html',
				url: solidres.ajaxurl,
				data: {
					action: 'solidres_save_reservation_note',
					note_text: reservation_note_text,
					notify_check: notify_customer_check,
					visible_in_frontend_check: visible_in_frontend_check,
					reservation_id: reservation_id,
					security: solidres.nonce_save_note
				},
				success: function (response) {
					$('.processing').addClass('nodisplay');
					$('div.reservation_note_group').html('');
					$('div.reservation_note_group').html(response);
				}
			});
		}
		else {
			alert("Please type your message!");
			return false;
		}
		return false;
	});


    $('.postbox > h3').click(function (event) {
        $(this).parent('.postbox:not(:first-child)').toggleClass('closed').toggleClass('open');
    });

	$('#custom_fields_tab').tabs().addClass('ui-tabs-vertical ui-helper-clearfix');

	function convertToSlug(Text) {
		return Text
			.toLowerCase()
			.replace(/ /g, '_')
			.replace(/[^\w-]+/g, '')
			;
	}

	function convert_uft8_to_ascii(string) {
		return string
			.replace(/'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ'/g, 'a')
			.replace(/'đ|Đ'/g, 'd')
			.replace(/'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ'/g, 'e')
			.replace(/'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị'/g, 'i')
			.replace(/'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ'/g, 'o')
			.replace(/'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự'/g, 'u')
			.replace(/'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ'/g, 'y')
			;
	}

    var regex = /^[a-z0-9\ \s]+$/i;
    $('#add_new_group').click(function () {
        var valGroupname = $('#group_name').val();
        var groupname_converted = convert_uft8_to_ascii(valGroupname);
        var group_name_exist = 0;
        $('#custom_fields_tab li').each(function () {
            if ($(this).attr('aria-controls') == convertToSlug(valGroupname)) {
                group_name_exist = 1;
            }
        });
        if (group_name_exist == 0) {
            if (valGroupname == '') {
                alert('Please insert group name');
                $('#group_name').focus();
            } else if (!regex.test(groupname_converted)) {
                alert("Please enter letters and numbers only");
            } else {
                if ( solidres.is_multilingual ) {
                    var qtx = qTranslateConfig.js.get_qtx();
                }
                $('div#custom_fields_tab').removeClass('nodisplay');
                $('div#custom_fields_tab ul').append('<li class="' + convertToSlug(valGroupname) + '"><a href="#' + convertToSlug(valGroupname) + '" >' + valGroupname + '</a><a href="#" id="del_custom_field_group"><img src="' + solidres.plugin_url + '/assets/images/close_btn.png" /></a></li>');
                var customfield_view = '';
                customfield_view += '<div id="' + convertToSlug(valGroupname) + '" class="group_parent">';
                customfield_view += '<table class="form-table">';
                customfield_view += '<tbody>';
                customfield_view += '<tr class="add_new_field">';
                customfield_view += '<td class="first"><input type="text" name="" size="20" value="" class="new_custom_field_key" placeholder="Enter field name"></td>';
                customfield_view += '<td><textarea class="srform_textarea new_custom_field_value i18n-multilingual" id="new_custom_field_value_' + convertToSlug(valGroupname) + '" rows="5" name="ignfield[]" placeholder="Enter field value"></textarea></td>';
                customfield_view += '<td><input type="button" id="add_new_field" value="Add New Field" /></td>';
                customfield_view += '</tr>';
                customfield_view += '</tbody>';
                customfield_view += '</table>';
                customfield_view += '</div>';
                $('#custom_fields_tab').append(customfield_view);
                $('#group_name').val('');
                if ( solidres.is_multilingual ) {
                    qtx.addContentHookById('new_custom_field_value_' + convertToSlug(valGroupname));
                }
                $('#custom_fields_tab').tabs("refresh");
            }
        } else {
            alert("Group exist! ");
            $('#group_name').focus().select();
        }
    });

    $('body').on('click', 'a#del_custom_field_group', function (e) {
        e.preventDefault();
        var get_parent = $(this).parent().attr('aria-controls');
        $('li.' + get_parent).remove();
        $('#custom_fields_tab #' + get_parent).remove();
        $('#custom_fields_tab').tabs("refresh");
    });

    $('body').on('click', 'tr td a#del_custom_field_element', function (e) {
        e.preventDefault();
        var get_class_field = $(this).parent().parent().attr('class');
        $('tr.' + get_class_field).remove();
        $('#custom_fields_tab').tabs("refresh");
    });

    $('body').on('click', '#add_new_field', function () {
        var get_id_parent = $(this).parents('.group_parent').attr('id');
        var field_key = $('#' + get_id_parent + ' .new_custom_field_key').val();
        var field_value = $('#' + get_id_parent + ' .new_custom_field_value').val();
        var fieldname_converted = convert_uft8_to_ascii(field_key);
        var field_name_exist = 0;
        var new_field_add = '';
        $('#' + get_id_parent + ' tr:not(:first)').each(function () {
            if ($(this).attr('class') == 'field_' + convertToSlug(field_key)) {
                field_name_exist = 1;
            }
        });
        if (field_name_exist == 0) {
            if (field_key == '') {
                alert('Please enter field name!');
            } else if (!regex.test(fieldname_converted)) {
                alert("Please enter letters and numbers only");
            } else {
                var new_i18n_value = field_value;
                if ( solidres.is_multilingual ) {
                    var qtx = qTranslateConfig.js.get_qtx();
                    var h = qtx.hasContentHook( $('#' + get_id_parent + ' .new_custom_field_value').attr( 'id' ) );
                    if (h) {
                        for (var lang in h.fields) {
                            new_i18n_value += '[:' + lang + ']' + (lang == h.lang ? h.contentField.value : h.fields[lang].value);
                        }
                        new_i18n_value += '[:]';
                    }
                }
                new_field_add += '<tr class="field_' + convertToSlug(field_key) + '">';
                new_field_add += '<td class="first">' + field_key + '</td>';
                new_field_add += '<td>';
                new_field_add += '<textarea class="srform_textarea i18n-multilingual qtranxs-translatable" rows="5" name="srform[customfields][' + get_id_parent + '][' + convertToSlug(field_key) + ']" id="srform_customfields_' + get_id_parent + '_' + convertToSlug(field_key) + '">' + new_i18n_value + '</textarea></td>';
                new_field_add += '<td><a href="#" id="del_custom_field_element" title="Delete field"><img src="' + solidres.plugin_url + '/assets/images/close_btn.png" alt="Delete field"/></a>';
                new_field_add += '</tr>';
            }
            $('#' + get_id_parent + ' tbody ').append(new_field_add);
            $('#' + get_id_parent + ' .new_custom_field_key').val('');
            $('#' + get_id_parent + ' .new_custom_field_value').val('');
            if ( solidres.is_multilingual ) {
                if (h) {
                    for (var lang in h.fields) {
                        $(h.fields[lang]).val('');
                    }
                }
                qtx.addContentHookById('srform_customfields_' + get_id_parent + '_' + convertToSlug(field_key));
            }

            $('#custom_fields_tab').tabs("refresh");
        }
        else {
            alert("Field name exist! ");
            $('#' + get_id_parent + ' .new_custom_field_key').focus().select();
        }
    });

	var id_array = [];
	$('.gallery_img img').each(function () {
		id_array.push({'url': $(this).attr('src'), 'id': $(this).attr('alt')});
	});

	function delete_img() {
		$('body').on('click', 'div.delete_img', function (event) {
			var get_id_action = event.target.id;
			id_array = $.grep(id_array, function (e) {
				return e.id != get_id_action;
			});
			view_gallery();
		});
	}

	function view_gallery() {
		var gallery_img = '';
		$.each(id_array, function (index, val) {
			gallery_img += '<div class="gallery_img_wrap">';
			gallery_img += '<img src="' + val.url + '" id="images_' + val.id + '" alt="' + val.id + '" />';
			gallery_img += '<input type="hidden" name="srform[mediaId][]" value="' + val.id + '" >';
			gallery_img += '<div href="#" class="delete_img" title="Delete image" id="' + val.id + '"></div>';
			gallery_img += '</div>';
		});
		$('.gallery_img').html(gallery_img);
	}

	delete_img();

	$('.choose_img_gallery').click(function (e) {
		var attachment = [];
		var custom_uploader;
		e.preventDefault();
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}

		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: true
		});

		custom_uploader.on('select', function () {
			attachment = custom_uploader.state().get('selection').toJSON();
			$.each(attachment, function (index, val) {
				var existed = false;
				$.map(id_array, function (element, indexarray) {
					if (element.id == val.id) {
						existed = true;
					}
				});
				if (!existed) {
					id_array.push({'url': val.url, 'id': val.id});
				}
			});

			delete_img();
			view_gallery();
		});
		custom_uploader.open();
	});

    var regex = /^[a-z0-9\ \s]+$/i;

    $('body').on('click', '#add_new_room', function () {
        var get_id_parent = $(this).parents('.room_girdview').attr('id');
        var room_label = $('#' + get_id_parent + ' #room_label').val();
        var room_label_converted = convert_uft8_to_ascii(room_label);
        var room_label_exist = 0;
        var room_label_add = '';

        $('#' + get_id_parent + ' tr').each(function () {
            if ($(this).attr('id') == 'room_' + convertToSlug(room_label)) {
                room_label_exist = 1;
            }
        });
        if (room_label_exist == 0) {
            if (room_label == '') {
                alert('Please enter room label!');
                $('#' + get_id_parent + ' #room_label').focus();
            }
            else if (!regex.test(room_label_converted)) {
                alert("Please enter letters and numbers only");
                $('#' + get_id_parent + ' #room_label').focus();
            }
            else {
                room_label_add += '<tr id="room_' + convertToSlug(room_label) + '" class="room_item">';
                room_label_add += '<td class="first"><input type="text" name="srform[roomsnew][]" size="20" value="' + room_label + '" class="room_label" placeholder="Enter room label"></td>';
                room_label_add += '<td><a href="" class="delete_room" title="Delete Room"><img src="' + solidres.plugin_url + '/assets/images/close_btn.png" alt="Delete Room"/></a></td>';
                room_label_add += '</tr>';
                $('#' + get_id_parent + ' #room_label').val('');
                $('#' + get_id_parent + ' #room_label').focus();
            }
            $('#' + get_id_parent + ' tbody ').append(room_label_add);
        }
        else {
            alert('Room label exist!');
            $('#' + get_id_parent + ' #room_label').focus().select();
        }
    });

    $('body').on('click', 'a.delete_room', function (e) {
        e.preventDefault();
        $('tr.confirm_room_delete').remove();
        var get_id_parent = $(this).parents('.room_item').attr('id');
        var get_room_id = $('tr#' + get_id_parent + ' input').attr('alt');
        var room_id = $(this).attr('data-room_id');
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: solidres.ajaxurl,
            data: { action: 'solidres_delete_room', room_id: room_id, security: solidres.nonce_delete_room },
            success: function (response) {
                if (response.type == 'success') {
                    $('tr#' + get_id_parent).remove();
                }
                else if (response.type == 'error') {
                    $('tr#' + get_id_parent).after('<tr class="confirm_room_delete"><td colspan="2">There are constraints between this room and your reservations/limit booking records in database. Do you want to remove this room and all related constraints too? <a href="javascript:void(0)" class="accept_delete_room" data-parent="' + get_id_parent + '" data-room_id="' + get_room_id + '" >Yes</a> | <a class="reject_delete_room">No</a></td></tr>');
                }
            }
        })
    });

    $('input.room_label').keyup(function () {
        var get_input_value = $(this).val();
        var get_id_parent = $(this).parents('.room_item').attr('id');
        if (get_input_value == '') {
            $('tr#' + get_id_parent).attr("id", 'temp');
            $(this).attr('required', '');
        }
        var get_room_view = $(this).parents('.room_girdview').attr('id');
        var roomlabel_exist = 0;
        $('#' + get_room_view + ' tr').each(function () {
            if ($(this).attr('id') == 'room_' + convertToSlug(get_input_value)) {
                roomlabel_exist = 1;
            }
        });
        if (roomlabel_exist == 0) {
            $('tr#' + get_id_parent).attr("id", 'room_' + convertToSlug(get_input_value));
        }
        else {
            alert('Room label exist!');
        }
    });

    $('body').on('click', 'a.accept_delete_room', function (e) {
        e.preventDefault();
        var get_parrent_id = $(this).attr('data-parent');
        var room_id = $(this).attr('data-room_id');

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: solidres.ajaxurl,
            data: { action: 'solidres_confirm_delete_room', room_id: room_id, security: solidres.nonce_confirm_delete_room },
            success: function (response) {
                if (response.type == 'success') {
                    $('tr#' + get_parrent_id).remove();
                    $('tr.confirm_room_delete').remove();
                }
            }
        })
    });

    $('body').on('click', 'a.reject_delete_room', function (e) {
        e.preventDefault();
        $('tr.confirm_room_delete').remove();
    });

	$('.upload_srform_image').click(open_media_window);

	function open_media_window() {
		if (this.window === undefined) {
			this.window = wp.media({
				title: 'Insert a media',
				library: {type: 'image'},
				multiple: false,
				button: {text: 'Insert'}
			});

			var self = this; // Needed to retrieve our variable in the anonymous function below
			this.window.on('select', function () {
				var url = self.window.state().get('selection').first().toJSON();
				$('#srform_image').val(url.url);
			});
		}

		this.window.open();
		return false;
	}

    $('.upload_srform_paypal_custom_logo_image').click(open_media_window_paypal);

    function open_media_window_paypal() {
        if (this.window === undefined) {
            this.window = wp.media({
                title: 'Insert a media',
                library: {type: 'image'},
                multiple: false,
                button: {text: 'Insert'}
            });

            var self = this; // Needed to retrieve our variable in the anonymous function below
            this.window.on('select', function () {
                var url = self.window.state().get('selection').first().toJSON();
                $('#srform_paypal_custom_logo_image').val(url.url);
            });
        }

        this.window.open();
        return false;
    }

	$('.sortable').sortable();
	$('.sortable').disableSelection();

    $( '#srform_name' ).keyup( function() {
        $( '#srform_alias' ).val( convertToSlug( $(this).val() ) );
    });

    $( 'input[name*="qtranslate-fields[ignfield]"]' ).attr( 'disabled', 'disabled' );
});