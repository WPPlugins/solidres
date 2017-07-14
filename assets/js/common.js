/*------------------------------------------------------------------------
 Solidres - Hotel booking extension for Joomla
 ------------------------------------------------------------------------
 @Author    Solidres Team
 @Website   http://www.solidres.com
 @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
 @License   GNU General Public License version 3, or later
 ------------------------------------------------------------------------*/
if (typeof(Solidres) === 'undefined') {
    var Solidres = {};
}

Solidres.options = {
    data: {},
    'get': function(key, def) {
        return typeof this.data[key.toUpperCase()] !== 'undefined' ? this.data[key.toUpperCase()] : def;
    },
    load: function(object) {
        for (var key in object) {
            this.data[key.toUpperCase()] = object[key];
        }
        return this;
    }
};

function isAtLeastOneRoomSelected () {
    var numberRoomTypeSelected = 0;
    jQuery(".reservation_room_select").each(function() {
        if (jQuery(this).is(':checked')) {
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
    $('#solidres').on('click', '.reservation-navigate-back', function() {
        $('.reservation-tab').removeClass('active');
        $('.reservation-single-step-holder').removeClass('nodisplay').addClass('nodisplay');
        var self = $(this);
        var currentstep = self.data('step');
        var prevstep = self.data('prevstep');
        var active = $('.' + prevstep).removeClass('nodisplay');
        active.find('button[type=submit]').removeAttr('disabled');
        $('.reservation-tab-' + prevstep).addClass('active').removeClass('complete');
        $('.reservation-tab-' + prevstep + ' span.badge').removeClass('badge-success').addClass('badge-info');
        $('.reservation-tab-' + currentstep + ' span.badge').removeClass('badge-info');
    });

    $('.confirmation').on('click', '#termsandconditions', function() {
        var self = $(this),
            submitBtn = $('.confirmation').find('button[type=submit]');
        if (self.is(':checked')) {
            submitBtn.removeAttr('disabled');
        } else {
            submitBtn.attr('disabled', 'disabled');
        }
    });

    $('#solidres .guestinfo').on('change', '.country_select', function() {
        $.ajax({
            url: solidres_common.ajaxurl,
            data: { action: 'solidres_load_states', country_id: $(this).val(), security : solidres_common.nonce_load_states },
            success : function(html) {
                if (html.length > 0) {
                    $('.state_select').empty().html(html);
                }
            }
        });
    });

    $('#solidres .room').on('change', '.trigger_tariff_calculating', function(event, updateChildAgeDropdown) {
        var self = $(this);
        var raid = self.data('raid');
        var roomtypeid = self.data('roomtypeid');
	var roomindex = self.data('roomindex');
        var roomid = self.data('roomid');        
        var tariffid = self.attr('data-tariffid');
        var adjoininglayer = self.attr('data-adjoininglayer');

		if (Solidres.context == "frontend" && Solidres.options.get('Hub_Dashboard') != 1) {
            var  target = roomtypeid + '_' + tariffid + '_' + roomindex;
        } else {
            var  target = roomtypeid + '_' + tariffid + '_' + roomid;
        }

        var adult_number = 1;
        if ($("select.adults_number[data-identity='" + target + "']").length) {
            adult_number = $("select.adults_number[data-identity='" + target + "']").val();
        }
        var child_number = 0;
        if ($("select.children_number[data-identity='" + target + "']").length) {
            child_number = $("select.children_number[data-identity='" + target + "']").val();
        }

        if (typeof updateChildAgeDropdown === 'undefined' || updateChildAgeDropdown === null ) {
            updateChildAgeDropdown = true;
        }

        if ( !updateChildAgeDropdown && self.hasClass('reservation-form-child-quantity') ) {
            return;
        }

        if (self.hasClass('reservation-form-child-quantity') && child_number >= 1 ) {
            return;
        }

        var data = {};
        data.action = 'solidres_calculate_tariff',
        data.security = solidres_common.nonce_cal_tariff,
        data.raid = raid;
        data.room_type_id = roomtypeid;
        data.room_index = roomindex;
        data.room_id = roomid;
        data.adult_number = adult_number;
        data.child_number = child_number;
        data.tariff_id = tariffid;
        data.adjoining_layer = adjoininglayer;

        for (var i = 0; i < child_number; i++) {
            var prop_name = 'child_age_' + target + '_' + i;
            data[prop_name] = $('.' + prop_name).val();
        }

        $.ajax({
            type: 'GET',
            url: solidres_common.ajaxurl,
            data: data,
            success: function(data) {
                if (!data.room_index_tariff.code && !data.room_index_tariff.value) {
                    $( '.tariff_' +  target ).text('0');
                } else {
                    $( '.tariff_' +  target ).text(data.room_index_tariff.formatted);
                    $( '#breakdown_' + target ).empty().html(data.room_index_tariff_breakdown_html);
                }
            },
            dataType: "json"
        });
    });

    $('#solidres').on('click', '.toggle_breakdown', function() {
	var target = $(this).attr('data-target');
        $('#breakdown_' + target).toggle();
    });

    $('#solidres').on('click', '.toggle_extra_details', function() {
        var target = $(this).data('target');
        $('#' + target).toggle();
    });

    $('#solidres').on('click', '.toggle_extracost_confirmation', function() {
        var target = $('.extracost_confirmation');
        var self = $(this);
        target.toggle();
        if (target.is(":hidden")) {
            $('.extracost_row').removeClass().addClass('nobordered extracost_row');
        } else {
            $('.extracost_row').removeClass().addClass('nobordered extracost_row first');
        }
    });

    $('#solidres').on('change', '.reservation-form-child-quantity', function (event, updateChildAgeDropdown) {
        if (typeof updateChildAgeDropdown === 'undefined' || updateChildAgeDropdown === null ) {
            updateChildAgeDropdown = true;
        }
        if (!updateChildAgeDropdown) {
            return;
        }
        var self = $(this);
        var quantity = self.val();
        var html = '';
        var raid = self.data('raid');
        var roomtypeid = self.data('roomtypeid');
        var roomid = self.data('roomid');
        var roomindex = self.data('roomindex');
        var tariffid = self.data('tariffid');
        var child_age_holder = self.siblings('.child-age-details');

        if (quantity > 0) {
            child_age_holder.removeClass('nodisplay');
        } else {
            child_age_holder.addClass('nodisplay');
        }

        for (var i = 0; i < quantity; i ++) {
            html += '<li>' + solidres_text.child + ' ' + (i + 1) +
                ' <select name="srform[room_types][' + roomtypeid + '][' + tariffid + ']['+ (Solidres.context == "frontend" ? roomindex : roomid) +'][children_ages][]" ' +
                'data-raid="' + raid + '"' +
                'data-roomtypeid="' + roomtypeid + '"' +
                'data-roomid="' + roomid + '"' +
                'data-roomindex="' + roomindex + '"' +
                'data-tariffid="' + tariffid + '"' +
                'required ' +
                'class="twelve columns child_age_' + roomtypeid + '_' + tariffid + '_' + (Solidres.context == "frontend" ? roomindex : roomid) + '_' + i + ' trigger_tariff_calculating"> ';

            html += '<option value=""></option>';

            for (var age = 1; age <= solidres_common.child_max_age_limit; age ++) {
                html += '<option value="' + age + '">' +
                    (age > 1 ? age + ' ' + solidres_text.child_age_selection_js : age + ' ' + solidres_text.child_age_selection_1_js)  +
                    '</option>';
            }

            html += '</select></li>';
        }

        child_age_holder.find('ul').empty().append(html);
    });

    var submitReservationForm = function(form) {
        var self = $(form),
        //url = self.attr( 'action'),
            formHolder = self.parent('.reservation-single-step-holder'),
            submitBtn = self.find('button[type=submit]'),
            currentStep = submitBtn.data('step');

        submitBtn.attr('disabled', 'disabled');
        submitBtn.html('<i class="fa fa-arrow-right"></i> ' + solidres_text.processing);
        if ($("div.wizard").length > 0) {
            $('html, body').animate({
                scrollTop: $("div.wizard").offset().top
            }, 700);
        }
        $.post( solidres_common.ajaxurl, self.serialize(), function(data) {
            if (data.status == 1) {
                $.ajax({
                    type: 'GET',
                    cache: false,
                    url: solidres_common.ajaxurl,
                    data: { action: 'solidres_reservation_progress', next_step: data.next_step, security : solidres_common.nonce_process_reservation},
                    success: function(response) {
                        formHolder.addClass('nodisplay');
                        submitBtn.removeClass('nodisplay');
                        submitBtn.html('<i class="fa fa-arrow-right"></i> ' + solidres_text.next);
                        var next = $('.' + data.next_step);
                        next.removeClass('nodisplay');
                        next.empty().append(response);
                        if (data.next == 'payment') {
                            $.metadata.setType("attr", "validate");
                        }
                        location.hash = '#form';
                        $('.reservation-tab').removeClass('active');
                        $('.reservation-tab-' + currentStep).addClass('complete');
                        $('.reservation-tab-' + currentStep + ' span.badge').removeClass('badge-info').addClass('badge-success');
                        $('.reservation-tab-' + data.next_step).addClass('active');
                        $('.reservation-tab-' + data.next_step + ' span.badge').addClass('badge-info');
                        var next_form = next.find('form.sr-reservation-form');
                        if (next_form.attr('id') == 'sr-reservation-form-guest') {
                            next_form.validate({
                                rules: {
                                    'srform[customer_email]': { required: true, email: true },
                                    'srform[payment_method]': { required: true },
                                    'srform[customer_password]' : {require: false, minlength: 8},
                                    'srform[customer_username]': {
                                        required: false,
                                        remote: {
                                            url: solidres_common.ajaxurl,
                                            type: 'POST',
                                            data: {
                                                username: function() {
                                                    return $('#customer_username').val();
                                                },
                                                action: 'solidres_check_username_exists',
                                                security : solidres_common.nonce_check_user_exists
                                            }
                                        }
                                    }
                                },
                                messages: {
                                    'srform[customer_username]': {
                                        remote: solidres_text.username_exists
                                    }
                                }
                            });
                            if (typeof $(".popover_payment_methods").popover != 'undefined' ) {
                                $(".popover_payment_methods").popover({
                                    "trigger" : "click",
                                    "placement" : "bottom"
                                });
                                $('.extra_desc_tips').popover('destroy');
                                $('.extra_desc_tips').popover({
                                    html: true,
                                    placement: "bottom",
                                    trigger: "click"
                                });
                            }

                        } else {
                            next_form.validate();
                        }

                        if (next.hasClass('confirmation')) {
                            $('.toggle_room_confirmation').click(function () {
                                var self = $(this);
                                $('#rc_' + self.data('target')).toggle();
                            });
                        }
                    }
                });
            }
        }, "json");
    }

    $('#solidres').on('submit', 'form.sr-reservation-form', function (event) {
        event.preventDefault();
        submitReservationForm(this);
    });

    $('.roomtype-reserve-exclusive').click(function () {
        var self = $(this);
        var tariffid = self.data('tariffid');
        var rtid = self.data('rtid');
        self.siblings('input[name="srform[room_types][' + rtid + '][' + tariffid + '][1][adults_number]"]').removeAttr('disabled');
        submitReservationForm(document.getElementById('sr-reservation-form-room'));
    });

    $('#solidres').on('change', '.occupancy_max_constraint', function() {
        var self = $(this);
        var max = self.data('max');
        var min = self.data('min');
        var roomtypeid = self.data('roomtypeid');
        var leftover = 0;
        var totalSelectable = 0;
        var roomindex = self.data('roomindex');
		var roomid = self.data('roomid');
		var tariffid = self.attr('data-tariffid');

		if (Solidres.context == "frontend") {
        var target = roomindex + '_' + tariffid + '_' + roomtypeid;
		} else {
			var  target = roomid + '_' + tariffid + '_' + roomtypeid;
		}

        if (max > 0) {
            $('.occupancy_max_constraint_' + target).each(function () {
                var s = $(this);
                var val = parseInt(s.val());
                if (val > 0) {
                    leftover += val;
                }
            });

            totalSelectable = max - leftover;

            $('.occupancy_max_constraint_' + target).each(function() {
                var s = $(this);
                var val = parseInt(s.val());
                var from = 0;
                if (val > 0) {
                    from = val + totalSelectable;
                } else {
                    from = totalSelectable;
                }
                disableOptions(s, from);
            });
        }

        if (min > 0) {
            var totalAdultChildNumber = 0;
            $('.occupancy_max_constraint_' + target).each(function() {
                var s = $(this);
                var val = parseInt(s.val());
                if (val > 0) {
                    totalAdultChildNumber += val;
                }
            });
            if (totalAdultChildNumber < min) {
                $('#error_' + target).show();
                $('.occupancy_max_constraint_' + target).addClass('warning');
                $('#sr-reservation-form-room button[type="submit"]').attr('disabled', 'disabled');
            } else {
                $('#error_' + target).hide();
                $('.occupancy_max_constraint_' + target).removeClass('warning');
                $('#sr-reservation-form-room button[type="submit"]').removeAttr('disabled', 'disabled');
            }
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

    $('#solidres').on('click', '.reservation_room_select', function() {
        var self = $(this);
        var room_selection_details = $('#room_selection_details_' + self.val());
        var priceTable = $('#room' + self.val() + ' dl dt table');
        var span = $('#room' + self.val() + ' dl dt label span');
        if (self.is(':checked')) {
            room_selection_details.show();
            priceTable.show();
            span.addClass('label-success');
            room_selection_details.find('select.tariff_selection').removeAttr('disabled');
            room_selection_details.find('input.guest_fullname').removeAttr('disabled');
            room_selection_details.find('select.adults_number').removeAttr('disabled');
            room_selection_details.find('select.children_number').removeAttr('disabled');
            $('#room_selection_details_' + self.val() + ' .extras_row_roomtypeform').each(function() {
                var li = $(this);
                var chk = li.children('input:checkbox');
                if (chk.is(':checked')) {
                    var sel = li.children('select');
                    sel.removeAttr('disabled');
                }
            });
        } else {
            room_selection_details.hide();
            priceTable.hide();
            span.removeClass('label-success');
            room_selection_details.find('select.tariff_selection').attr('disabled', 'disabled');
            room_selection_details.find('input.guest_fullname').attr('disabled', 'disabled');
            room_selection_details.find('select.adults_number').attr('disabled', 'disabled');
            room_selection_details.find('select.children_number').attr('disabled', 'disabled');
            room_selection_details.find('input:hidden').attr('disabled', 'disabled');
            room_selection_details.find('.extras_row_roomtypeform select').attr('disabled', 'disabled');
        }

        isAtLeastOneRoomSelected();
    });

	$('#solidres').on('click', '.room input:checkbox, .guestinfo input:checkbox', function() {
		var self = $(this);
		if (self.is(':checked')) {
			$('.' + self.data('target') ).removeAttr('disabled');
		} else {
			$('.' + self.data('target') ).attr('disabled', 'disabled');
		}
	});
    $('#solidres').on('change', '.tariff_selection', function() {
        var self = $(this);
        if (self.val() == '') {
            $('a.tariff_breakdown_' + self.data('roomid')).hide();
            $('span.tariff_breakdown_' + self.data('roomid')).text('0');
            return false;
        }
        var parent = self.parents('.room_selection_wrapper');
        var input = parent.find('.room_selection_details input[type="text"]');
        var checkboxes = parent.find('.room_selection_details input[type="checkbox"]');
        var select = parent.find('.room_selection_details select').not(self);
        var spans = parent.find('dt span');
        var breakdown_trigger = parent.find('dt a.toggle_breakdown');
        var breakdown_holder = parent.find('dt span.breakdown');
        var extra_input_hidden = parent.find('.extras_row_roomtypeform input[type="hidden"]');
        var adjoining_layer = self.find(':selected').data('adjoininglayer');

        input.attr('name', input.attr('name').replace(/^(srform\[room_types\])(\[[0-9]+\])(\[[-?0-9a-z]*\])(.*)$/, '$1$2[' + self.val() + ']$4'));
        if (extra_input_hidden.length > 0) {
            extra_input_hidden.attr('name', extra_input_hidden.attr('name').replace(/^(srform\[room_types\])(\[[0-9]+\])(\[[0-9a-z]*\])(.*)$/, '$1$2[' + self.val() + ']$4'));
        }

        select.each(function () {
            var self_sel = $(this);
            self_sel.attr('name', self_sel.attr('name').replace(/^(srform\[room_types\])(\[[0-9]+\])(\[[-?0-9a-z]*\])(.*)$/, '$1$2[' + self.val() + ']$4'));
            self_sel.attr('data-tariffid', self.val());
            if (self_sel.attr('data-identity')) {
                self_sel.attr('data-identity', self_sel.attr('data-identity').replace(/^([0-9]+)(_)([-?0-9a-z]*)(_)(.*)$/, '$1$2' + self.val() + '$4$5'));
            }
            self_sel.attr('data-adjoininglayer', adjoining_layer);
        });
        checkboxes.each(function() {
            $(this).removeAttr('disabled');
        });
        breakdown_trigger.attr('data-target', breakdown_trigger.data('target').replace(/^([0-9]+)(_)([0-9a-z]*)(_)(.*)$/, '$1$2' + self.val() + '$4$5'));
        breakdown_holder.attr('id', breakdown_holder.attr('id').replace(/^([a-z]+)(_)([0-9]+)(_)([-?0-9a-z]*)(_)(.*)$/, '$1$2$3$4' + self.val() + '$6$7'));
        spans.each(function () {
            var self_spa = $(this);
            self_spa.attr('class', self_spa.attr('class').replace(/^([a-z]+)(_)([0-9]+)(_)([-?0-9a-z]*)(_)(.*)$/, '$1$2$3$4' + self.val() + '$6$7'));
        });

        if (self.val() != '') {
            $('.tariff_breakdown_' + self.data('roomid')).show();
        } else {
            $('.tariff_breakdown_' + self.data('roomid')).hide();
        }

        $('#room' + self.data('roomid') + ' .adults_number.trigger_tariff_calculating').trigger('change');
    });

    $('#solidres').on('change paste keyup', '#sr-reservation-form-confirmation .total_price_tax_excl_single_line', function() {
        var sum = 0;
        $.each($('.total_price_tax_excl_single_line'), function() {
            sum += parseFloat($(this).val() != '' ? $(this).val() : 0 );
        });
        $('.total_price_tax_excl').text(sum);
        updateGrandTotal();
    });

    $('#solidres').on('change paste keyup', '#sr-reservation-form-confirmation .room_price_tax_amount_single_line', function() {
        var sum = 0;
        $.each($('.room_price_tax_amount_single_line'), function() {
            sum += parseFloat($(this).val() != '' ? $(this).val() : 0 );
        });
        $('.tax_amount').text(sum);
        updateGrandTotal();
    });

    $('#solidres').on('change paste keyup', '#sr-reservation-form-confirmation .extra_price_single_line', function() {
        var sum = 0;
        $.each($('.extra_price_single_line'), function() {
            sum += parseFloat($(this).val() != '' ? $(this).val() : 0 );
        });
        $('.total_extra_price').text(sum);
        updateGrandTotal();
    });

    $('#solidres').on('change paste keyup', '#sr-reservation-form-confirmation .extra_tax_single_line', function() {
        var sum = 0;
        $.each($('.extra_tax_single_line'), function() {
            sum += parseFloat($(this).val() != '' ? $(this).val() : 0 );
        });
        $('.total_extra_tax').text(sum);
        updateGrandTotal();
    });

    function updateGrandTotal() {
        sum = 0;
        $.each($('.grand_total_sub'), function() {
            sum += parseFloat($(this).text() != '' ? $(this).text() : 0 );
        });
        $('.grand_total').text(sum);
    }
    
});