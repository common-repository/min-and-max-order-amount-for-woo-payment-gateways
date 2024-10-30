<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) )
     exit;

class MMAWPG_Lite_Functions {
    public function __construct(){
        // Filtering the place order button
        add_filter( 'woocommerce_order_button_html', array($this, 'mmawpg_woo_place_order_btn_html'), 10, 1 );
        //Ajax action
        add_action('wp_ajax_mmawpg_ajax_action', array($this, 'mmawpg_ajax_action_callback'));
        add_action('wp_ajax_nopriv_mmawpg_ajax_action', array($this, 'mmawpg_ajax_action_callback'));
        //Add the error notice
        add_action('woocommerce_review_order_before_submit', array($this, 'mmawpg_before_submit_btn'));
    }
    
    // Add the custom order button.
    public function mmawpg_woo_place_order_btn_html($place_order ) {
        $button_text = apply_filters( 'mmawpg_order_button_text', esc_html__( 'Place order', 'mmawpg' ) );
        
        return '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="'.$button_text.'" data-value="'.$button_text.'" disabled="disabled">'.$button_text.'</button>'; 
    }
    
    //Ajax action callback
    public function mmawpg_ajax_action_callback(){
        $mmawpg_payment_method = sanitize_text_field($_POST['payment_type']);
        
        //Get payment method title
        switch ($mmawpg_payment_method) {
            case 'cod':
                $mmawpg_gateway_obj = new WC_Gateway_COD();
                $GLOBALS['mmawpg_payment_method_title'] = $mmawpg_gateway_obj->get_title();
                break;
            case 'cheque':
                $mmawpg_gateway_obj = new WC_Gateway_Cheque();
                $GLOBALS['mmawpg_payment_method_title'] = $mmawpg_gateway_obj->get_title();
                break;
            case 'paypal':
                $mmawpg_gateway_obj = new WC_Gateway_Paypal();
                $GLOBALS['mmawpg_payment_method_title'] = $mmawpg_gateway_obj->get_title();
                break;
            case 'bacs':
                $mmawpg_gateway_obj = new WC_Gateway_BACS();
                $GLOBALS['mmawpg_payment_method_title'] = $mmawpg_gateway_obj->get_title();
                break;
        }
        
        $mmawpg_exc_shipping = get_option('mmawpg_exc_shipping_crg');
        if($mmawpg_exc_shipping == 'yes'){
            $GLOBALS['mmawpg_order_amount'] = WC()->cart->subtotal;
        }else{
            $GLOBALS['mmawpg_order_amount'] = WC()->cart->total;
        }
        
        //Veriables for the minimum amount
        $mmawpg_cod_min_amount = get_option('mmawpg_cod_min');
        $mmawpg_cheque_min_amount = get_option('mmawpg_cheque_min');
        $mmawpg_paypal_min_amount = get_option('mmawpg_paypal_min');
        $mmawpg_bacs_min_amount = get_option('mmawpg_bank_min');
        //Veriables for the maximum amount
        $mmawpg_cod_max_amount = get_option('mmawpg_cod_max');
        $mmawpg_cheque_max_amount = get_option('mmawpg_cheque_max');
        $mmawpg_paypal_max_amount = get_option('mmawpg_paypal_max');
        $mmawpg_bacs_max_amount = get_option('mmawpg_bank_max');
        
        switch ($mmawpg_payment_method) {
            case 'cod':
                if($mmawpg_cod_min_amount <= $mmawpg_cod_max_amount || $mmawpg_cod_max_amount == 0):
                if($mmawpg_cod_min_amount != 0):
                if($GLOBALS['mmawpg_order_amount'] < $mmawpg_cod_min_amount){

                    $this->mmawpg_print_notice($mmawpg_cod_min_amount, 'min');
                }
                endif;
                if($mmawpg_cod_max_amount != 0):
                if($mmawpg_cod_max_amount < $GLOBALS['mmawpg_order_amount']){

                    $this->mmawpg_print_notice($mmawpg_cod_max_amount, 'max');
                }
                endif;
                endif;
                break;
            case 'cheque':
                if($mmawpg_cheque_min_amount <= $mmawpg_cheque_max_amount || $mmawpg_cheque_max_amount == 0):
                if($mmawpg_cheque_min_amount != 0):
                if($GLOBALS['mmawpg_order_amount'] < $mmawpg_cheque_min_amount){

                    $this->mmawpg_print_notice($mmawpg_cheque_min_amount, 'min');
                }
                endif;
                if($mmawpg_cheque_max_amount != 0):
                if($mmawpg_cheque_max_amount < $GLOBALS['mmawpg_order_amount']){

                    $this->mmawpg_print_notice($mmawpg_cheque_max_amount, 'max');
                }
                endif;
                endif;
                break;
            case 'paypal':
                if($mmawpg_paypal_min_amount <= $mmawpg_paypal_max_amount || $mmawpg_paypal_max_amount == 0):
                if($mmawpg_paypal_min_amount != 0):
                if($GLOBALS['mmawpg_order_amount'] < $mmawpg_paypal_min_amount){

                    $this->mmawpg_print_notice($mmawpg_paypal_min_amount, 'min');
                }
                endif;
                if($mmawpg_paypal_max_amount != 0):
                if($mmawpg_paypal_max_amount < $GLOBALS['mmawpg_order_amount']){

                    $this->mmawpg_print_notice($mmawpg_paypal_max_amount, 'max');
                }
                endif;
                endif;
                break;
            case 'bacs':
                if($mmawpg_bacs_min_amount <= $mmawpg_bacs_max_amount || $mmawpg_bacs_max_amount == 0):
                if($mmawpg_bacs_min_amount != 0):
                if($GLOBALS['mmawpg_order_amount'] < $mmawpg_bacs_min_amount){
                    
                    $this->mmawpg_print_notice($mmawpg_bacs_min_amount, 'min');
                }
                endif;
                if($mmawpg_bacs_max_amount != 0):
                if($mmawpg_bacs_max_amount < $GLOBALS['mmawpg_order_amount']){

                    $this->mmawpg_print_notice($mmawpg_bacs_max_amount, 'max');
                }
                endif;
                endif;
                break;
        }
        wp_die();
    }
    
    //Display error notice
    public function mmawpg_print_notice($mmawpg_gat_amount, $mmawpg_min_max){
        
        switch($mmawpg_min_max){
            case 'min':
                $mmawpg_calc_cart = get_option('mmawpg_calc_cart');
                if($mmawpg_calc_cart == 'yes'){
                    $GLOBALS['mmawpg_required_amount'] = $mmawpg_gat_amount - $GLOBALS['mmawpg_order_amount'];
                }else{
                    $GLOBALS['mmawpg_required_amount'] = $mmawpg_gat_amount;
                }
                
                break;
            case 'max':
                $GLOBALS['mmawpg_required_amount'] = $mmawpg_gat_amount;
                break;
        }
        //Add a shortcode to display the min/max amount.
        add_shortcode('mmawpg_amount', 'mmawpg_amount_scode');
        function mmawpg_amount_scode($atts, $content = null){
            
            ob_start();
            $mmawpg_currency_symble = get_woocommerce_currency_symbol();
            $mmawpg_currency_pos = get_option('woocommerce_currency_pos');
            
            switch($mmawpg_currency_pos){
                case 'left':
                    return $mmawpg_currency_symble.$GLOBALS['mmawpg_required_amount'];
                    break;
                case 'right':
                    return $GLOBALS['mmawpg_required_amount'].$mmawpg_currency_symble;
                    break;
                case 'left_space':
                    return $mmawpg_currency_symble.' '.$GLOBALS['mmawpg_required_amount'];
                    break;
                case 'right_space':
                    return $GLOBALS['mmawpg_required_amount'].' '.$mmawpg_currency_symble;
                    break;
            }
            
            return ob_get_clean();
        }
        
        //Add a shortcode to display payment method
        add_shortcode('mmawpg_method', 'mmawpg_method_scode');
        function mmawpg_method_scode($atts, $content = null){
            
            ob_start();
            return $GLOBALS['mmawpg_payment_method_title'];
            return ob_get_clean();
        }

        switch($mmawpg_min_max){
            case 'min':
            $mmawpg_notice = get_option('mmawpg_error_notice_min');
                break;
            case 'max':
                $mmawpg_notice = get_option('mmawpg_error_notice_max');
                break;
        }
        
        $mmawpg_print_notice = do_shortcode("$mmawpg_notice");
        wc_print_notice( esc_html__( $mmawpg_print_notice, 'mmawpg' ), 'error' );
    }
    
    //Display error notice
    public function mmawpg_before_submit_btn(){
        echo '<div id="mmawpgnotice"></div>';
    }
}
new MMAWPG_Lite_Functions();