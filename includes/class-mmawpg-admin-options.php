<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) )
     exit;

class MMAWPG_Lite_Settings_Tab {

    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_mmawpg_settings_tab', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_mmawpg_settings_tab', __CLASS__ . '::update_settings' );
        add_filter( 'mmawpg_wc_settings_tab', __CLASS__ . '::installed_payment_methods', 10 );
    }
    
    /**
     * @return array $settings_tabs Array of WooCommerce setting tabs
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['mmawpg_settings_tab'] = esc_html__( 'Min/Max Order Amount', 'mmawpg' );
        return $settings_tabs;
    }

    /**
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }

    /**
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }

    /**
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {
        $mmawpg_settings_array = array(
            'mmawpg_section_title' => array(
                'name'     => esc_html__( 'Settings', 'mmawpg' ),
                'type'     => 'title',
                'id'       => 'mmawpg_section_title_settings'
            ),
            'mmawpg_enable' => array(
                'name' => esc_html__( 'Enable/Disable', 'mmawpg' ),
                'type' => 'checkbox',
                'desc' => esc_html__( 'Enable min & max order amount for woocommerce payment gateways.', 'mmawpg' ),
                'id'   => 'mmawpg_enable_setting',
                'default' => 'yes',
            ),
            'mmawpg_error_notice_min' => array(
                'name' => esc_html__( 'Min amount notice', 'mmawpg' ),
                'type' => 'text',
                'desc' => esc_html__( 'Available shortcode: [mmawpg_amount], [mmawpg_method]', 'mmawpg' ),
                'id'   => 'mmawpg_error_notice_min',
                'default' => esc_html__('You must spend [mmawpg_amount] to order via [mmawpg_method].', 'mmawpg' ),
                'placeholder' => esc_html__('You must spend [mmawpg_amount] to order via [mmawpg_method]', 'mmawpg' ),
                'desc_tip' =>  true
            ),
            'mmawpg_error_notice_max' => array(
                'name' => esc_html__( 'Max amount notice', 'mmawpg' ),
                'type' => 'text',
                'desc' => esc_html__( 'Available shortcode: [mmawpg_amount], [mmawpg_method]', 'mmawpg' ),
                'id'   => 'mmawpg_error_notice_max',
                'default' => esc_html__('Maximum limit [mmawpg_amount] to order via [mmawpg_method].', 'mmawpg' ),
                'placeholder' => esc_html__('Maximum limit [mmawpg_amount] to order via [mmawpg_method]', 'mmawpg' ),
                'desc_tip' =>  true
            ),
            'mmawpg_exc_shipping' => array(
                'name' => esc_html__( 'Exclude shipping charge', 'mmawpg' ),
                'type' => 'checkbox',
                'desc' => esc_html__( 'Exclude shipping charges from the total cart sum.', 'mmawpg' ),
                'id'   => 'mmawpg_exc_shipping_crg'
            ),
            'mmawpg_calc_cart' => array(
                'name' => esc_html__( 'Calculate cart amount', 'mmawpg' ),
                'type' => 'checkbox',
                'desc' => esc_html__( 'Enable to display the last required order amount. Example: (Min amount - Cart total).', 'mmawpg' ),
                'id'   => 'mmawpg_calc_cart'
            ),
            'mmawpg_section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'mmawpg_section_end_settings'
            ),
            'mmawpg_method_section_title' => array(
                'name'     => esc_html__( 'Set min & max order amounts based on payment gateways', 'mmawpg' ),
                'type'     => 'title',
                'id'       => 'mmawpg_method_section_title_settings'
            ),
            'mmawpg_cod_min' => array(
                'name' => esc_html__( 'Cash on delivery', 'mmawpg' ),
                'type' => 'number',
                'desc' => esc_html__( 'Min amount', 'mmawpg' ),
                'id'   => 'mmawpg_cod_min',
                'custom_attributes'	=> array(
					'min'	=> '0'
				),
                'default' => '0',
                'placeholder' => '0'
            ),
            'mmawpg_cod_max' => array(
                'type' => 'number',
                'desc' => esc_html__( 'Max amount', 'mmawpg' ),
                'id'   => 'mmawpg_cod_max',
                'custom_attributes'	=> array(
					'min'	=> '0'
				),
                'default' => '0',
                'placeholder' => '0',
            ),
            'mmawpg_cheque_min' => array(
                'name' => esc_html__( 'Check payment', 'mmawpg' ),
                'type' => 'number',
                'desc' => esc_html__( 'Min amount', 'mmawpg' ),
                'id'   => 'mmawpg_cheque_min',
                'custom_attributes'	=> array(
					'min'	=> '0'
				),
                'default' => '0',
                'placeholder' => '0'
            ),
            'mmawpg_cheque_max' => array(
                'type' => 'number',
                'desc' => esc_html__( 'Max amount', 'mmawpg' ),
                'id'   => 'mmawpg_cheque_max',
                'custom_attributes'	=> array(
					'min'	=> '0'
				),
                'default' => '0',
                'placeholder' => '0',
            ),
            'mmawpg_bank_min' => array(
                'name' => esc_html__( 'Direct bank transfer', 'mmawpg' ),
                'type' => 'number',
                'desc' => esc_html__( 'Min amount', 'mmawpg' ),
                'id'   => 'mmawpg_bank_min',
                'custom_attributes'	=> array(
					'min'	=> '0'
				),
                'default' => '0',
                'placeholder' => '0',
            ),
            'mmawpg_bank_max' => array(
                'type' => 'number',
                'desc' => esc_html__( 'Max amount', 'mmawpg' ),
                'id'   => 'mmawpg_bank_max',
                'custom_attributes'	=> array(
					'min'	=> '0'
				),
                'default' => '0',
                'placeholder' => '0',
            ),
            'mmawpg_paypal_min' => array(
                'name' => esc_html__( 'Paypal', 'mmawpg' ),
                'type' => 'number',
                'desc' => esc_html__( 'Min amount', 'mmawpg' ),
                'id'   => 'mmawpg_paypal_min',
                'custom_attributes'	=> array(
					'min'	=> '0'
				),
                'default' => '0',
                'placeholder' => '0',
            ),
            'mmawpg_paypal_max' => array(
                'type' => 'number',
                'desc' => esc_html__( 'Max amount', 'mmawpg' ),
                'id'   => 'mmawpg_paypal_max',
                'custom_attributes'	=> array(
					'min'	=> '0'
				),
                'default' => '0',
                'placeholder' => '0',
            ),
            'mmawpg_stripe_min' => array(
                'name' => esc_html__( 'Stripe (Pro)', 'mmawpg' ),
                'type' => 'number',
                'desc' => esc_html__( 'Min amount', 'mmawpg' ),
                'custom_attributes'	=> array(
					'min'	=> '0',
                    'disabled'=> 'disabled'
				),
                'default' => '0',
                'placeholder' => '0',
            ),
            'mmawpg_stripe_max' => array(
                'type' => 'number',
                'desc' => 'Max amount <br> <a style="color:red" href="https://codecanyon.net/item/minimum-and-maximum-amounts-for-woocommerce-payment-gateways/25130217" target="_blank">Upgrade to pro</a>',
                'custom_attributes'	=> array(
					'min'	=> '0',
                    'disabled'=> 'disabled'
				),
                'default' => '0',
                'placeholder' => '0',
            ),
        );

        return apply_filters( 'mmawpg_wc_settings_tab', $mmawpg_settings_array );
    }

    public static function installed_payment_methods($mmawpg_settings_array){
        $available_payment_methods = WC()->payment_gateways()->get_available_payment_gateways();

        unset($available_payment_methods['cod']);
        unset($available_payment_methods['bacs']);
        unset($available_payment_methods['cheque']);
        unset($available_payment_methods['paypal']);
        unset($available_payment_methods['stripe']);

        foreach($available_payment_methods as $method_id=>$available_method){

            $method_title = $available_method->title;

            $mmawpg_settings_array['mmawpg_'.$method_id.'_min'] = array(
                'name' => esc_html__( $method_title.' (Pro)', 'mmawpg' ),
                'type' => 'number',
                'desc' => esc_html__( 'Min amount', 'mmawpg' ),
                'custom_attributes' => array(
                    'min'   => '0',
                    'disabled'=> 'disabled'
                ),
                'default' => '0',
                'placeholder' => '0',
            );

            $mmawpg_settings_array['mmawpg_'.$method_id.'_max'] = array(
                'type' => 'number',
                'desc' => 'Max amount <br> <a style="color:red" href="https://codecanyon.net/item/minimum-and-maximum-amounts-for-woocommerce-payment-gateways/25130217" target="_blank">Upgrade to pro</a>',
                'custom_attributes' => array(
                    'min'   => '0',
                    'disabled'=> 'disabled'
                ),
                'default' => '0',
                'placeholder' => '0',
            );
            
        }

        $mmawpg_settings_array['mmawpg_method_section_end'] = array(
             'type' => 'sectionend',
             'id' => 'mmawpg_method_section_end_settings'
        );

        return $mmawpg_settings_array;
    }
}

MMAWPG_Lite_Settings_Tab::init();