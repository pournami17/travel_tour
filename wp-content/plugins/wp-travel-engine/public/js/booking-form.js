'use strict';
jQuery(document).ready(function($){
    var currentTab = $('.wpte-bf-step.active');
    var currentTabContent = $('.wpte-bf-step-content.active');
    var isDateSelected = false;

    window.wteCartFields = {
        'action' : 'wte_add_trip_to_cart',
        'nonce'   : $('#nonce').val(),
        'trip-id' : wte.trip.id,
        'travelers' : 1,
        'trip-cost' : wte.trip.price,
    };

    populateHTML();

    // Toggle detail.
    $('.wpte-bf-toggle-wrap .wpte-bf-toggle-title').click(function(event) {
        event.preventDefault();
        $(this).parents('.wpte-bf-toggle-wrap').toggleClass('active');
        $(this).siblings('.wpte-bf-toggle-content').slideToggle();
	});

    /**
	 * Handle increment and decrement of travellers in the travellers section.
	 */
    jQuery('.wpte-bf-content-travellers .wpte-bf-number-field').each(function() {
        var spinner = jQuery(this),
            input = spinner.find('input[type="text"]'),
            btnUp = spinner.find(".wpte-bf-plus"),
            btnDown = spinner.find(".wpte-bf-minus"),
            min = input.attr("min"),
            max = input.attr("max");

        btnUp.click(function(event) {
            event.preventDefault();
            var input = $(this).parent().find('input')
            var max = $(input).attr('max');


            var value = parseFloat(input.val());
			++value;

            if (value >= max) {
                value = max;
            }

            spinner.find("input").val(value);
            spinner.find("input").trigger("change");

            // Get traveller type.
            var type = $(this).parents('.wpte-bf-number-field').find('input[type="text"]').data('cartField');

            // Add data to the cart fields.
            window.wteCartFields[type] = value;

            populateHTML();
        });

        btnDown.click(function(event) {
            event.preventDefault();

            var input = $(this).parent().find('input')
            var min = $(input).attr('min');

			var value = parseFloat(input.val());
            --value;

            if (value <= min) {
                value = min;
			}

            spinner.find("input").val(value);
            spinner.find("input").trigger("change");

            // Get traveller type.
            var type = $(this).parents('.wpte-bf-number-field').find('input[type="text"]').data('cartField');

            // Add data to the cart fields.
            window.wteCartFields[type] = value;

            populateHTML();
        });
    });

    /**
 * Populate the html fields.
 */

    function populateHTML() {
        if ( ! $('body').hasClass('single-trip') ) {
            return;
        }

        // Calculate total.
        var travellersCost                    = calculateTravellersTotalCost();
            window.wte.trip.travellersCost    = travellersCost;
        var grandTotal                        = calculateGrandTotal();
        var formattedTravellersCost           = wteGetFormatedPriceWithCurrencyCodeSymbol(travellersCost, wte.currency.code, wte.currency.symbol);
        var formattedGrandTotal               = wteGetFormatedPrice(grandTotal);
        var formattedGrandTotalWithCodeSymbol = wteGetFormatedPriceWithCurrencyCodeSymbol(grandTotal, wte.currency.code, wte.currency.symbol);

        var tripPrice = wteGetFormatedPriceWithCurrencyCodeSymbol( wte.trip.price, wte.currency.code);


        // Add data to the cart fields.
        window.wteCartFields['trip-cost'] = grandTotal;

        // Update the total cost.
        $('.wpte-bf-price-amt').html(formattedGrandTotal);

        var numberFields = $('.wpte-bf-content-travellers .wpte-bf-number-field > input[type="text"]');
        var html = '';
        $.each(numberFields, function(index, numberField){
            var count = $(numberField).val();
            var cartField = $(numberField).data('cartField');
            var type = $(numberField).data('type');
            var costField = $(numberField).data('costField');
            var cost = calculateSingleTravellerTypeCost(numberField);
            var formattedCost = wteGetFormatedPriceWithCurrencyCodeSymbol(cost, wte.currency.code, wte.currency.symbol );
            var pricing_type = $(numberField).data('pricing-type') || 'per-person';

            window.wteCartFields[cartField] = count;
            window.wteCartFields[costField] = cost;

            var capitalizeType = type.charAt(0).toUpperCase() + type.substring(1);

            if ( count > 1 && 'traveler' == type ) {
                capitalizeType = 'Travelers';
            } else if ( count > 1 && 'child' == type ) {
                capitalizeType = 'Children';
            } else if ( count > 1 && 'Adult' == type || 'adult' == type) {
                capitalizeType = 'Adults';
            } else if ( count > 1 && ( 'Group' == type || 'per-group' === pricing_type ) ) {
                count = 1;
            }

            // Calculate new price from the cost.
            var price = cost / count;
            if ( ! isFinite( price ) ) {
                price = $(numberField).data('cost');
                if ( '' == price ) price = 0;
                try {
                    price = applyFixStratingDatePrice( window.wteCartFields['trip-date'], price );

                } catch(err) {

                }
            }
            price = parseFloat( price );
            price = price.toFixed(2);
            var formattedPriceWithSymbol = wteGetFormatedPriceWithCurrencySymbol( price, wte.currency.symbol );
            var formattedPrice = wteGetFormatedPrice( price );
            var priceHtml = wte.currency.code  + '<b>' + formattedPrice + '</b>';
            jQuery(this).parents('.wpte-bf-traveler-block').find('.wpte-bf-price ins').html(priceHtml);


            if( 0 == cost ) {
                return;
            }

            html = html + `
                <tr>
                    <td>${count} x ${capitalizeType} <span class="wpte-bf-info">(${formattedPriceWithSymbol}/${type})<span></span></td>
                    <td>${formattedCost}</td>
                </tr>
            `;
        });

        $('.wpte-bf-travellers-price-table tbody').html(html);

            // Update the grand total.
        $('.wte-bf-price-detail .wpte-bf-total').html(`
            Total: <b>${formattedGrandTotalWithCodeSymbol}</b>
        `);
    }

    /**
     * Calculate travellers total cost.
     */
    function calculateTravellersTotalCost() {

        // Get all the number fields.
        var numberFields = $('.wpte-bf-content-travellers .wpte-bf-number-field > input[type="text"]');
        var total = 0.0;

        // Calculate total.
        $.each(numberFields, function(index, numberField) {

            var cost = calculateSingleTravellerTypeCost(numberField);
            total = total + cost;
        });

        return total;
    }

    function calculateSingleTravellerTypeCost(numberField) {
        var count = $(numberField).val();
        var price = $(numberField).data('cost');

        if ( isNaN( price ) || '' == price ) {
            price = 0;
        }


        try {
            price = parseFloat( applyFixStratingDatePrice( window.wteCartFields['trip-date'], price ) );
        } catch(err){

        }

        var type         = $(numberField).data('type');
        var pricing_type = $(numberField).data('pricing-type') || 'per-person';

        if( ( 'Group' == type || 'per-group' == pricing_type ) && count > 0 ) {
            cost = parseFloat(price);
        }
        else {
            var cost = parseInt(count) * parseFloat(price);
        }


        try {
            cost = parseFloat( applyGroupDiscount(count, type, cost) );
        } catch(err){
        }

        return cost;
    }

    var availableDates = [];
    try {
        var availableDatesCount = wte_fix_date.cost.length;
        for(var i = 0; i < availableDatesCount; i++) {
            availableDates.push(Object.keys(wte_fix_date.cost[i])[0]);
        }
    } catch(err){

    }

    function checkAvailableDates(date) {
        var dmy = $.datepicker.formatDate( $.datepicker.ISO_8601, date );

        var fixDatesCount = wte_fix_date.seats_available.length;
        for(var index = 0; index < fixDatesCount; index++) {
            if ( wte_fix_date.seats_available[index][dmy] == '0' ) {
                return [false, "", "Unavailable"];
            }
        }

        if ($.inArray(dmy, availableDates) !== -1) {
            return [true, "", "Available"];
        } else {
            return [false, "", "Unavailable"];
        }
    }

    /**
     * Change to the next tab afeter selecting the date.
     */
    $( ".wpte-bf-datepicker" ).datepicker({
        minDate: 0,
        beforeShowDay: (0 == availableDates.length || '' == window.wte_fix_date.enabled) ? null : checkAvailableDates,
        dateFormat: 'yy-mm-dd',
        onSelect: function(dateText, inst) {
            isDateSelected = true;

            // Get the next tab.
            var nextTab = getNextTab();
            if ( nextTab ) {
				// Deactive the current tab.
				$('.wpte-bf-step').removeClass('active');
				$(currentTab).removeClass('active');

                changeTab(nextTab);
            }

            if ( window.wteCartFields['trip-date'] == dateText ) {
                return;
            }
            window.wteCartFields['trip-date'] = dateText;

            try {
                if ( '' == window.wte_fix_date.enabled ) {
                    return;
                }
            } catch(err) {}


            try {
                var seatsAvailableLength = wte_fix_date.seats_available.length;
                for(var i = 0; i < seatsAvailableLength; i++) {
                    var seatsAvailable = wte_fix_date.seats_available[i][dateText];
                    var price = wte_fix_date.cost[i][dateText];

                    if ( undefined !== seatsAvailable ) {
                        var numberFields = $('.wpte-bf-content-travellers').find('input[type="text"]');
                        $.each(numberFields, function(index, numberField){

                            var cartField = $(numberField).data('cartField');
                            var defaultCount = ( 'travelers' == cartField || 'pricing_options[adult][pax]' == cartField) ? 1 : 0;
                            $(numberField).val(defaultCount);
                            $(numberField).attr('max', seatsAvailable);
                            // $(numberField).data('cost', price);
                            // var priceHtml = window.wte.currency.code + '<b> ' + wteGetFormatedPrice(price) + '</b>';
                            // $(numberField).parents('.wpte-bf-traveler-block').find('.wpte-bf-price ins').html(priceHtml);
                        });
                    }
                }
            } catch(err) {}
            populateHTML();
        }
    });

    // Change the tab.
    $('.wpte-bf-btn-wrap > input[type="button"]').click(function(event) {
        event.preventDefault();

        // Get the next tab.
        var nextTab = getNextTab(currentTab);
        if ( nextTab ) {
			// Deactive the current tab.
			$('.wpte-bf-step').removeClass('active');
			$(currentTab).removeClass('active');

            changeTab(nextTab);
        } else {

            // Add data to the cart.
            $.ajax({
                type: "POST",
                url: WTEAjaxData.ajaxurl,
                data: window.wteCartFields,
                success: function (data) {
                    if ( data.success ) {

                        // toastr.success( data.data.message, 'WP Travel Engine' );

                        $("#price-loading").fadeOut(500);
                        location.href = wp_travel_engine.CheckoutURL;

                    } else {
                        var i;
                        for( i = 0; i < data.data.length; i++ ) {

                            // Show Errors.
                            toastr.error( data.data[i], 'WP Travel Engine Error',  {
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": false,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": false,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "5000",
                                "timeOut": "5000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            } );
                        }

                    }
                }
            });
        }
    });

    /**
     * Change the tab and the tab content on click.
     */
    $("#wpte-booking-form").on("click", ".wpte-bf-step", function(event) {
        event.preventDefault();


        // Don't change the tab if date is not selected.
        if (!isDateSelected) {
            return false;
		}

		// Deactive the current tab.
		$('.wpte-bf-step').removeClass('active');
		$(this).removeClass('active');

        changeTab(this);

    });

    /**
     * Change the tabs to the supplied tab.
     */
    function changeTab(tab) {


        if ( ! isDateSelected) {
            return false;
        }

        // Set the current tab to next tab.
        currentTab = tab;

        // Get the index of the old tab.
        var tabs = $(".wpte-bf-step");
        var index = $(tabs).index(tab);

        // Change the tab content according to the tab.
        $(currentTabContent).fadeOut('slow', function(){

			// Active next tab.
			$(currentTab).addClass('active');

            $(currentTabContent).removeClass('active');
            currentTabContent = $('.wpte-bf-step-content')[index];
            $(currentTabContent).css('display', '' );
            $(currentTabContent).css('opacity', '');
            $(currentTabContent).addClass('active');

            // Show price details except in calender.
            if (index === 0 ) {
                $('.wte-bf-price-detail').css('display', 'none');
            } else {
                $('.wte-bf-price-detail').css('display', '');
            }

            // If it is the last tab, change the continue to checkout.
            if (index + 1 >= tabs.length) {
                $('.wte-bf-price-detail .wpte-bf-btn-wrap input[type="button"]').val(wte.bookNow);
            } else {
				$('.wte-bf-price-detail .wpte-bf-btn-wrap input[type="button"]').val('Continue');
			}
        });
    }

    /**
     * Get next tab in the selection.
     */
    function getNextTab(tab) {
        // Get the index of the old tab.
        var tabs = $(".wpte-bf-step");
        var index = $(tabs).index(tab);

        // Return false if there is no next tab.
        if (index + 1 >= tabs.length) {
            return false;
        }

        return tabs[index + 1];
    }
});


/**
 *  Format the price. (e.g. 1200 -> 1,200)
 *
 * @param {float} price          Price to be formatted.
 * @param {string} code          Currency code.
* @param {boolean} format        Whether to format the price or not. (default = true)
 *
 * @return {string} Formatted price.
 */
function wteGetFormatedPrice( price, format, numberOfDecimals ) {
    // Set default values.
    price = price || 0.0;
    format = format || true;
    numberOfDecimals = numberOfDecimals || 2;

    // Bail early if the format is false.
    if ( false == format ) {
        return price;
    }

    price = parseFloat( price );
    price = price.toFixed(numberOfDecimals);
    price = price.replace('.00','');

    price = addCommas(price);

    return price;
}

/**
 *  Format price with currency code. (e.g. USD 1,200)
 *
 * @param {float} price          Price to be formatted.
 * @param {string} code          Currency code.
 * @param {boolean} format        Whether to format the price or not. (default = true)
 * @param {int} numberOfDecimals Number of numbers after decimal point.
 *
 * @return {string} Formatted price with currency code.
 */
function wteGetFormatedPriceWithCurrencyCode( price, code, format, numberOfDecimals ) {
    // Set default values
    code = code || 'USD'

    var formattedPrice = code + ' ' + wteGetFormatedPrice( price, format, numberOfDecimals );

    return formattedPrice;
}

/**
 *  Format price with currency code and symbol. (e.g. USD $1,200)
 *
 * @param {float} price        Price to be formatted.
 * @param {string} code        Currency code.
 * @param {string} symbol      Currency symbol.
 * @param {boolean} format     Whether to format the price or not. (default = true)
 * @param {int} numberOfDecimals Number of number after decimal point.
 *
 * @return {string} Formatted price with currency code and symbol.
 */
function wteGetFormatedPriceWithCurrencyCodeSymbol( price, code, symbol, format, numberOfDecimals ) {
    // Set default values.
    code = code || 'USD';
    symbol = symbol || '$';

    var formattedPrice = code + ' ' + symbol + wteGetFormatedPrice( price, format, numberOfDecimals);

    return formattedPrice;
}

function wteGetFormatedPriceWithCurrencySymbol( price, symbol, format, numberOfDecimals ) {
    // Set default values.
    symbol = symbol || '$';

    var formattedPrice = symbol + wteGetFormatedPrice( price, format, numberOfDecimals);

    return formattedPrice;
}

 /**
 * Calculate grand total.
 */
function calculateGrandTotal() {
    var travellersCost = parseFloat( window.wte.trip.travellersCost );
    var extraServicesCost = parseFloat( window.wte.trip.extraServicesCost );

    return travellersCost + extraServicesCost;
}
