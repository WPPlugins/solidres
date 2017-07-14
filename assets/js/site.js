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

Solidres.context = 'frontend';

Solidres.setCurrency = function(id) {
    jQuery.ajax({
        type: 'POST',
        url: solidres.ajaxurl,
        data: {
            id: parseInt(id),
            action: 'solidres_set_currency',
            security: solidres.nonce_set_currency
        },
        success: function(msg) {
            location.reload(true);
        }
    });
};

var isAtLeastOnRoomTypeSelected = function() {
    var numberRoomTypeSelected = 0;
    jQuery(".roomtype-quantity-selection").each(function() {
        if (jQuery(this).val() > 0) {
            numberRoomTypeSelected ++;
            return;
        }
    });

    if (numberRoomTypeSelected > 0) {
        jQuery('#sr-reservation-form-room button[type="submit"]').removeAttr('disabled');
    } else {
        jQuery('#sr-reservation-form-room button[type="submit"]').attr('disabled', 'disabled');
    }
};

jQuery(function($) {
	if (document.getElementById('sr-reservation-form-room')) {
		$('#sr-reservation-form-room').validate();
	}

	if (document.getElementById("sr-checkavailability-form")) {
		$("#sr-checkavailability-form").validate();
	}

	$(".roomtype-quantity-selection").change(function() {
		isAtLeastOnRoomTypeSelected();
	});

	if (document.getElementById("sr-availability-form")) {
		$("#sr-availability-form").validate();
	}

	function toggleRoomTypeDetails(target) {
		var room_type_details = $('div.' + target );
		if (room_type_details.hasClass('sr_hidden')) {
			room_type_details.removeClass('sr_hidden');
		} else {
			room_type_details.addClass('sr_hidden');
		}
	}

	var currenthash = window.location.hash;
	if (currenthash.indexOf('room_type_details_handler') > -1) {
		toggleRoomTypeDetails(currenthash.substring(1));
	}

	$('a.room_type_details').click(function(e) {
		e.preventDefault();
		toggleRoomTypeDetails($(this).attr('id'));
	});

	$('.coupon').on('click', '#apply-coupon', function () {
		$.ajax({
			type: 'POST',
			url: solidres.ajaxurl,
			data: {
                coupon_code: $('#coupon_code').val(),
                raid: $('input[name="id"]').val(),
                action: 'solidres_apply_coupon',
                security: solidres.nonce_apply_coupon
            },
			success: function(response) {
				if (response.status) {
					location.reload(true);
				}
			},
			dataType: 'JSON'
		});
	});

	$('#sr-remove-coupon').click(function(e) {
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: solidres.ajaxurl,
			data: {
                id: $(this).data('couponid'),
                action: 'solidres_remove_coupon',
                security: solidres.nonce_remove_coupon
            },
			success: function(response) {
				if (response.status) {
					location.reload(true);
				} else {
					alert( solidres_text.can_not_remove_coupon );
				}
			},
			dataType: 'JSON'
		});
	});

	$('button.load-calendar').click(function() {
		var self = $(this);
		var id = self.data( 'roomtypeid' );
		var target = $( '#availability-calendar-' + id );
		self.empty().html('<i class="fa fa-calendar"></i> '  + solidres_text.processing);
		self.attr( 'disabled', 'disabled' );
		var data = {
			action: 'solidres_load_availability_calendar',
			security : solidres.nonce_load_calendar,
			id: id
		};
		if ( target.children().length == 0 ) {
			$.post( solidres.ajaxurl, data, function ( html ) {
				self.removeAttr('disabled');
				if ( html.length > 0) {
					target.show().html( html );
					self.empty().html('<i class="fa fa-calendar"></i> ' + solidres_text_site.close_calendar);
				}
			});
		}
		else {
			target.empty().hide();
			self.empty().html( '<i class="fa fa-calendar"></i> ' + solidres_text_site.view_calendar );
			self.removeAttr( 'disabled' );
		}
	});

    function loadRoomForm(self) {
        var rtid = self.data('rtid');
        var raid = self.data('raid');
        var tariffid = self.data('tariffid');
        var adjoininglayer = self.data('adjoininglayer');
        $.ajax({
            type: 'GET',
            url: solidres.ajaxurl,
            data: {
                action: 'solidres_load_roomtypeform',
                security : solidres.nonce_load_room_form,
                rtid: rtid,
                raid: raid,
                tariffid: tariffid,
                quantity: self.val() > 0 ? self.val() : 1,
                adjoininglayer: adjoininglayer
            },
            success: function(data) {
                self.parent().find('.processing').css({'display': 'none'});
                $('#room-form-' + rtid + '-' + tariffid).empty().show().html(data);
                $('.sr-reservation-form').validate();
                var updateChildAgeDropdown = false; // trigger change at this time will update the child age form too, we dont want that!
                $('#solidres .room #room-form-' + rtid + '-' + tariffid + ' .trigger_tariff_calculating').trigger('change', [updateChildAgeDropdown]);
            }
        });
    }

    // In case the page is reloaded, we have to reload the previous submitted room type selection form
    $('.roomtype-quantity-selection').each(function() {
        var self = $(this);
        if ( self.val() > 0) {
            self.parent().find('.processing').css({'display': 'block'});
            $('#selected_tariff_' + self.data('rtid') + '_' + self.data('tariffid')).removeAttr("disabled");
            loadRoomForm(self);
        }
    });

    $('.roomtype-quantity-selection').change(function() {
        var self = $(this);
        var tariffid = self.data('tariffid');
        var rtid = self.data('rtid');
        var totalRoomsLeft = self.data('totalroomsleft');
        var currentQuantity = parseInt(self.val());
        var currentSelectedRoomTypeRooms = 0;
        var totalSelectableRooms = 0;
        if ( currentQuantity > 0) {
            self.parent().find('.processing').css({'display': 'block'});
            $('#selected_tariff_' + rtid + '_' + tariffid).removeAttr("disabled");
            loadRoomForm(self);
        } else {
            $('#room-form-' + rtid + '-' + tariffid).empty().hide();
            $('input[name="srform[selected_tariffs][' + rtid + ']"]').attr("disabled", "disabled");
            $('#selected_tariff_' + rtid + '_' + tariffid).attr("disabled", "disabled");
        }

        $('.quantity_' + rtid).each(function() {
            var s = $(this);
            var val = parseInt(s.val());
            if (val > 0) {
                currentSelectedRoomTypeRooms += val;
            }
        });

        totalSelectableRooms = totalRoomsLeft - currentSelectedRoomTypeRooms;

        $('.quantity_' + rtid).each(function() {
            var s = $(this);
            var val = parseInt(s.val());
            var from = 0;
            if (val > 0) {
                from = val + totalSelectableRooms;
            } else {
                from = totalSelectableRooms;
            }
            disableOptions(s, from);
        });

        if (totalSelectableRooms > 0 && totalSelectableRooms < totalRoomsLeft) {
            $('#num_rooms_available_msg_' + rtid).empty().text( solidres_text['only_' + totalSelectableRooms + '_left'] );
        } else if (totalSelectableRooms == 0) {
            $('#num_rooms_available_msg_' + rtid).empty();
        } else {
            $('#num_rooms_available_msg_' + rtid).empty().text($('#num_rooms_available_msg_' + rtid).data('original-text'));
        }
    });

    $('.roomtype-reserve').click(function() {
        var self = $(this);
        var tariffid = self.data('tariffid');
        var rtid = self.data('rtid');
        if ( $("#room-form-" + rtid + "-" + tariffid).children().length == 0) {
            self.siblings('.processing').css({'display': 'block'});
            $('#selected_tariff_' + rtid + '_' + tariffid).removeAttr("disabled");
            loadRoomForm(self);
        } else {
            $('#room-form-' + rtid + '-' + tariffid).empty().hide();
            $('input[name="jform[selected_tariffs][' + rtid + ']"]').attr("disabled", "disabled");
            $('#selected_tariff_' + rtid + '_' + tariffid).attr("disabled", "disabled");
        }
    });

    function disableOptions(selectEl, from) {
        $('option', selectEl).each(function() {
            var val = parseInt($(this).attr('value'));
            if (val > from) {
                $(this).attr('disabled', 'disabled');
            } else {
                $(this).removeAttr('disabled');
            }
        });
    }

	$('.guestinfo').on('click', 'input:checkbox', function() {
		var self = $(this);
		if (self.is(':checked')) {
			$('.' + self.data('target') ).removeAttr('disabled');
		} else {
			$('.' + self.data('target') ).attr('disabled', 'disabled');
		}
	});

	$('.room-form').on('click', 'input:checkbox', function() {
		var self = $(this);
		if (self.is(':checked')) {
			$('.' + self.data('target') ).removeAttr('disabled');
		} else {
			$('.' + self.data('target') ).attr('disabled', 'disabled');
		}
	});

	$('.trigger_checkinoutform').click(function() {
		var self = $(this);
		var tariffId = self.data( 'tariffid' );
		var roomtypeId = self.data( 'roomtypeid' );
		var oldLabel = self.text();
		var data = {
			action: 'solidres_load_checkinoutform',
			security : solidres.nonce_load_date_form,
			Itemid: self.data('itemid'),
			id: self.data( 'assetid' ),
			roomtype_id: roomtypeId,
			tariff_id: tariffId
		};

		if ( tariffId != '' ) {
			$('.checkinoutform').empty();
			self.text( 'Processing...' );
			$.post( solidres.ajaxurl, data, function ( data ) {
				$('.checkinoutform').empty().hide();
				$('#checkinoutform-' + roomtypeId + '-' + tariffId).show().empty().html(data);
				$('#room-form-' + roomtypeId + '-' + tariffId).empty();
				self.text( oldLabel );
			});
		}
	});

	$('#solidres').on('click', '.searchbtn', function() {
		var tariffid = $(this).data('tariffid');
		var roomtypeid = $(this).data('roomtypeid');
        if (solidres.enable_auto_scroll == 1) {
            $('#sr-checkavailability-form-component').attr('action', $('#sr-checkavailability-form-component').attr('action') + '#tariff-box-' + roomtypeid + '-' + tariffid);
        }
		$('#sr-checkavailability-form-component input[name=checkin]').val($('#tariff-box-' + roomtypeid + '-' + tariffid + ' input[name="checkin"]').val());
		$('#sr-checkavailability-form-component input[name=checkout]').val($('#tariff-box-' + roomtypeid + '-' + tariffid + ' input[name="checkout"]').val());
		$('#sr-checkavailability-form-component input[name=ts]').val($('input[name=fts]').val());
		$('#sr-checkavailability-form-component').submit();
	});

	$('.toggle_more_desc').click(function() {
		var self = $(this);
		$('#more_desc_' + self.data('target')).toggle();
		if ($('#more_desc_' + self.data('target')).is( ':visible' )) {
			self.empty().html('<i class="fa fa-eye"></i> ' + solidres_text_site.hide_info);
		} else {
			self.empty().html('<i class="fa fa-eye-slash"></i> ' + solidres_text_site.more_info );
		}
	});

	$('#sr-reservation-form-room').on('click', '.checkin_roomtype', function() {
		if (!$(this).hasClass("disabledCalendar")) {
			$('.checkin_datepicker_inline').slideToggle('slow', function() {
				if ($(this).is(":hidden")) {
					$(".checkout_roomtype").removeClass("disabledCalendar");
				} else {
					$(".checkout_roomtype").addClass("disabledCalendar");
				}
			});
		}
	});

	$('#sr-reservation-form-room').on('click', '.checkout_roomtype', function() {
		if (!$(this).hasClass("disabledCalendar")) {
			$('.checkout_datepicker_inline').slideToggle('slow', function() {
				if ($(this).is(":hidden")) {
					$(".checkin_roomtype").removeClass("disabledCalendar");
				} else {
					$(".checkin_roomtype").addClass("disabledCalendar");
				}
			});
		}
	});

	$('.guestinfo').on('click', '#register_an_account_form', function() {
		var self = $(this);
		if (self.is(':checked')) {
			$('.' + self.attr('id') ).show();
		} else {
			$('.' + self.attr('id') ).hide();
		}
	});

	$('.toggle-tariffs').click(function() {
		var self = $(this);
		var target = $('#tariff-holder-' + self.data('roomtypeid'));
		target.toggle();
		if (target.is(":hidden")) {
			self.html('<i class="fa fa-expand"></i> ' + solidres_text.show_tariffs );
		} else {
			self.html('<i class="fa fa-compress"></i> ' + solidres_text.hide_tariffs );
		}
	});

	var hash = location.hash;

	if (hash.indexOf('tariff-box') > -1) {
		var $el = $(hash),
			x = 1500,
			originalColor = $el.css("backgroundColor"),
			targetColor = $el.data("targetcolor");

		$el.css("backgroundColor", "#" + targetColor);
		setTimeout(function(){
			$el.css("backgroundColor", originalColor);
		}, x);
	}

	$(".filter_checkin_checkout").datepicker({
		numberOfMonths : 1,
		showButtonPanel : true,
		dateFormat : "dd-mm-yy",
		firstDay: 1
	});

	$(".sr-photo").colorbox({rel:"sr-photo", transition:"fade", width: "98%", height: "98%", className: "colorbox-w"});

    $('#toggle_login_form').click(function () {
        $('#solidres-inline-login-form').toggle();
    });
});
