<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function magicPaiement( $order, $paiementMethod ) {
	$selectedGatewayArray = array();
	$selectedGateway = "";
	$gateways        = WC()->payment_gateways->get_available_payment_gateways( );
	foreach ( $gateways as $id => $gateway ) {
		if ( isset( $gateway->enabled ) && 'yes' === $gateway->enabled ) {
			if($gateway->method_title == $paiementMethod){
				array_push($selectedGatewayArray,$gateway);
				$selectedGateway  = $gateway;
            }
		}
	}

	?>


    <div id="magic-paiement" class="">

<?php
//wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => WC()->checkout() ) );
// wc_get_template( 'checkout/form-pay.php', array( 'order' => $order, 'available_gateways' => $selectedGatewayArray) );
wc_get_template( 'checkout/payment-method.php', array('gateway' => $selectedGateway) );
//echo do_shortcode('[woocommerce_checkout]');
?>
    </div>
<?php } 