(function ($) {
    'use strict';

    //Trigger update_checkout
    jQuery(document.body).on('change', 'input[name="payment_method"]', function () {
        jQuery(document.body).trigger('update_checkout');
    });
    
    //For error message
    function mmawpg_lite_ajax_func() {
        var $payment_type = jQuery('input[name="payment_method"]:checked').val();

        jQuery.ajax({
            url: mmawpg_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'mmawpg_ajax_action',
                payment_type: $payment_type
            },
            success: function (data) {
                if (data != '') {
                    jQuery('#mmawpgnotice').html(data);
                    jQuery('button[name="woocommerce_checkout_place_order"]').attr('disabled', '');
                } else {
                    jQuery('button[name="woocommerce_checkout_place_order"]').attr('disabled', false);
                }
            },
            error: function (jqXhr, textStatus, errorMessage) {
                console.log('Error' + errorMessage);
            }
        });
    }

    //Load when ajax is complete
    jQuery(document).ajaxComplete(function () {
        mmawpg_lite_ajax_func();
    });

})(jQuery);