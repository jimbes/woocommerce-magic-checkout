<?php
/**
 * Plugin Name: WooCommerce Magic Checkout
 * Plugin URI: https://www.jimmy-besse.fr/
 * Description: Popup Checkout For Woocommerce
 * Author: Jimmy Besse
 * Author URI: https://www.jimmy-besse.fr/
 * Version: 1.0.0
 * Requires at least: 4.4
 * Tested up to: 5.5
 * WC requires at least: 3.0
 * WC tested up to: 4.3
 * Text Domain: woocommerce-magic-checkout
 * Domain Path: /languages
 *
 */

add_filter( 'woocommerce_locate_template', 'woo_adon_plugin_template', 1, 3 );
function woo_adon_plugin_template( $template, $template_name, $template_path ) {
	global $woocommerce;
	$_template = $template;
	if ( ! $template_path )
		$template_path = $woocommerce->template_url;

	$plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/templates/woocommerce/';

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			$template_path . $template_name,
			$template_name
		)
	);

	if( ! $template && file_exists( $plugin_path . $template_name ) )
		$template = $plugin_path . $template_name;

	if ( ! $template )
		$template = $_template;

	return $template;
}


define( 'MAGIC_CHECKOUT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
/*if( ! class_exists( 'Gamajo_Template_Loader' ) ) {
	require MAGIC_CHECKOUT_PLUGIN_DIR . '/includes/class-gamajo-template-loader.php';
}
require MAGIC_CHECKOUT_PLUGIN_DIR . '/includes/class-magiccheckout-template-loader.php';
*/

/**
 * The [magic_checkout] shortcode.  Accepts a ID and will display a popup.
 *
 * @param array  $atts     Shortcode attributes. Default empty.
 * @param string $content  Shortcode content. Default null.
 * @param string $tag      Shortcode tag (name). Default empty.
 *
 * @return string
 */

function magic_checkout_shortcode( $atts = [], $content = null, $tag = '' ) {
	// normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$magic_checkout_atts = shortcode_atts(
		array(
			'id' => '1',
		), $atts, $tag
	);
	ob_start();getButton($magic_checkout_atts['id']);
	return ob_get_clean();
}

/**
 * Central location to create all shortcodes.
 */
function magic_checkout_shortcode_init() {
	wp_register_style('cssMagicCheckout', plugin_dir_url( __FILE__ ) . 'assets/css/magic-checkout.css');
	wp_enqueue_style('cssMagicCheckout');
	wp_register_script( 'jsMagicCheckout', plugin_dir_url( __FILE__ ) . 'assets/js/magic-checkout.js',array('jquery'), '1.0.0', true);
	wp_enqueue_script('jsMagicCheckout');
	wp_register_script( 'jsLiveQuery', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.livequery.min.js',array('jquery'), '1.0.0', true);
	wp_enqueue_script('jsLiveQuery');
	wp_localize_script( 'jsMagicCheckout', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
	add_shortcode( 'magic_checkout', 'magic_checkout_shortcode' );

}

function magic_checkout_create_custom_field() {
	$args = array(
		'id' => 'magic_checkout_intro',
		'label' => __( 'Texte Intro', 'woocommerce-magic-checkout' ),
		'class' => 'woocommerce-magic-checkout-intro',
		'desc_tip' => true,
		'description' => __( 'Ce texte remplacera le text de popup de paiement (Ajouter), ce produit doit être le produit lié', 'woocommerce-magic-checkout' ),
	);
	woocommerce_wp_text_input( $args );
	$args = array(
		'id' => 'magic_checkout_outro',
		'label' => __( 'Texte Outro', 'woocommerce-magic-checkout' ),
		'class' => 'woocommerce-magic-checkout-outro',
		'desc_tip' => true,
		'description' => __( 'Ce texte remplacera le text de popup de paiement (pour être stylée), ce produit doit être le produit lié', 'woocommerce-magic-checkout' ),
	);
	woocommerce_wp_text_input( $args );
}
add_action( 'woocommerce_product_options_advanced', 'magic_checkout_create_custom_field' );

function magic_checkout_save_custom_field( $post_id ) {
	$product = wc_get_product( $post_id );
	$titleIntro = isset( $_POST['magic_checkout_intro'] ) ? $_POST['magic_checkout_intro'] : '';
	$product->update_meta_data( 'magic_checkout_intro', sanitize_text_field( $titleIntro ) );
	$titleOutro = isset( $_POST['magic_checkout_outro'] ) ? $_POST['magic_checkout_outro'] : '';
	$product->update_meta_data( 'magic_checkout_outro', sanitize_text_field( $titleOutro ) );
	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'magic_checkout_save_custom_field' );

add_action( 'init', 'magic_checkout_shortcode_init' );
require MAGIC_CHECKOUT_PLUGIN_DIR . '/includes/class-Magic-Order.php';
require MAGIC_CHECKOUT_PLUGIN_DIR . '/templates/popup-checkout.php';
require MAGIC_CHECKOUT_PLUGIN_DIR . '/templates/popup-paiement.php';
require MAGIC_CHECKOUT_PLUGIN_DIR . '/templates/buttonDisplay.php';
require MAGIC_CHECKOUT_PLUGIN_DIR . '/webservice.php';