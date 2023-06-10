<?php
/*
 * Plugin Name: HB Optimization billing for WooCommerce
 * Plugin URI: https://piglet.me/hb-line-tools-for-woocommerce
 * Description: HB Optimization for WooCommerce
 * Version: 0.1.0
 * Author: heiblack
 * Author URI: https://piglet.me
 * License:  GPL 3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
*/


add_filter('woocommerce_checkout_fields', 'remove_billing_fields_when_shipping_to_different_address');

function remove_billing_fields_when_shipping_to_different_address($fields) {

    $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
    $chosen_shipping_methods = str_replace(':', '_', $chosen_shipping_methods[0]);
    $option = get_option("woocommerce_".$chosen_shipping_methods."_settings");

    //自行取貨時(勾選)，不需要填寫帳單地址
    if($option && $option['shipping_billing_address'] == 'yes') {
        $fields['billing']['billing_first_name']['required'] = false;
        $fields['billing']['billing_last_name']['required']  = false;
        $fields['billing']['billing_company']['required']     = false;
        $fields['billing']['billing_address_1']['required']   = false;
        $fields['billing']['billing_address_2']['required']   = false;
        $fields['billing']['billing_city']['required']        = false;
        $fields['billing']['billing_state']['required']       = false;
        $fields['billing']['billing_postcode']['required']    = false;
        $fields['billing']['billing_country']['required']     = false;
        $fields['billing']['billing_phone']['required']       = false;
        $fields['billing']['billing_email']['required']       = false;
        return $fields;
    }
    //運送到不同地址時，不需要填寫帳單地址
    if (WC()->checkout->get_value('ship_to_different_address') == 1) {
        $fields['billing']['billing_first_name']['required'] = false;
        $fields['billing']['billing_last_name']['required']  = false;
        $fields['billing']['billing_company']['required']     = false;
        $fields['billing']['billing_address_1']['required']   = false;
        $fields['billing']['billing_address_2']['required']   = false;
        $fields['billing']['billing_city']['required']        = false;
        $fields['billing']['billing_state']['required']       = false;
        $fields['billing']['billing_postcode']['required']    = false;
        $fields['billing']['billing_country']['required']     = false;
        $fields['billing']['billing_phone']['required']       = false;
        $fields['billing']['billing_email']['required']       = false;
    }
    return $fields;
}


add_action('woocommerce_init', 'shipping_instance_form_fields_filters');

function shipping_instance_form_fields_filters()
{
    $shipping_methods = WC()->shipping->get_shipping_methods();
    if(isset($shipping_methods)){
        foreach($shipping_methods as $shipping_method) {
            if($shipping_method->id === 'local_pickup') {
                add_filter('woocommerce_shipping_instance_form_fields_' . $shipping_method->id, 'shipping_instance_form_add_extra_fields');
            }
        }
    }
}

function shipping_instance_form_add_extra_fields($settings)
{
    $settings['shipping_billing_address'] = [
        'title' => '免輸入帳單地址',
        'type' => 'checkbox', 
        'description' => '',
        'default' => 'no' // 确保为每个字段提供一个默认值
    ];
    return $settings;
} 
