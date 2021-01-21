<?php
/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

$totals         = $order->get_order_item_totals(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
$paiementMethod = "";
if ( isset( $_GET["paiementMethod"] ) && ! empty( $_GET["paiementMethod"] ) ) {
	$paiementMethod = $_GET["paiementMethod"];
}
?>

<?php if ( isset( $_GET["ismagic"] ) ) { ?>
    <link href="<?php echo plugins_url().'/woocommerce-magic-checkout/assets/css/iframe.css' ?>" rel="stylesheet">
<?php } ?>

<form id="order_review" method="post">
	<?php if ( ! isset( $_GET["ismagic"] ) ) { ?>
        <table class="shop_table">
            <thead>
            <tr>
                <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                <th class="product-quantity"><?php esc_html_e( 'Qty', 'woocommerce' ); ?></th>
                <th class="product-total"><?php esc_html_e( 'Totals', 'woocommerce' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php if ( count( $order->get_items() ) > 0 ) : ?>
				<?php foreach ( $order->get_items() as $item_id => $item ) : ?>
					<?php
					if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
						continue;
					}
					?>
                    <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
                        <td class="product-name">
							<?php
							echo apply_filters( 'woocommerce_order_item_name', esc_html( $item->get_name() ), $item, false ); // @codingStandardsIgnoreLine

							do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

							wc_display_item_meta( $item );

							do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
							?>
                        </td>
                        <td class="product-quantity"><?php echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', esc_html( $item->get_quantity() ) ) . '</strong>', $item ); ?></td><?php // @codingStandardsIgnoreLine ?>
                        <td class="product-subtotal"><?php echo $order->get_formatted_line_subtotal( $item ); ?></td><?php // @codingStandardsIgnoreLine ?>
                    </tr>
				<?php endforeach; ?>
			<?php endif; ?>
            </tbody>
            <tfoot>
			<?php if ( $totals ) : ?>
				<?php foreach ( $totals as $total ) : ?>
                    <tr>
                        <th scope="row"
                            colspan="2"><?php echo $total['label']; ?></th><?php // @codingStandardsIgnoreLine ?>
                        <td class="product-total"><?php echo $total['value']; ?></td><?php // @codingStandardsIgnoreLine ?>
                    </tr>
				<?php endforeach; ?>
			<?php endif; ?>
            </tfoot>
        </table>
	<?php } ?>
    <div id="payment">
		<?php if ( $order->needs_payment() ) : ?>
            <ul class="wc_payment_methods payment_methods methods">
				<?php
				if ( ! empty( $available_gateways ) ) {
					if ( ! isset( $_GET["ismagic"] ) ) {
						foreach ( $available_gateways as $gateway ) {
							wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
						}
					}else{
						foreach ( $available_gateways as $gateway ) {
						    if($gateway->method_title == $paiementMethod) {
							    wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
						    }
						}
					}
				} else {
					echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) ) . '</li>'; // @codingStandardsIgnoreLine
				}
				?>
            </ul>
		<?php endif; ?>
        <div class="form-row">
            <input type="hidden" name="woocommerce_pay" value="1"/>
	        <?php if ( ! isset( $_GET["ismagic"] ) ) { ?>
			<?php wc_get_template( 'checkout/terms.php' ); ?>
	        <?php } ?>
			<?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

			<?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

			<?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

			<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
        </div>
    </div>
</form>

<?php if ( isset( $_GET["ismagic"] ) ) {
   if(!empty($paiementMethod) && $paiementMethod == "PayPal"){ ?>
<script>

    function showLoader(container) {
        var loader = "<div id='magicLoader' style='margin-top:50px;max-width:50%;'>" +
            "<img src='data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIGlkPSJDYWxxdWVfMSIgZGF0YS1uYW1lPSJDYWxxdWUgMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgMjM5IDkxLjEiPjxkZWZzPjxzdHlsZT4uY2xzLTF7ZmlsbDojMWIxYTM3O308L3N0eWxlPjwvZGVmcz48dGl0bGU+bG9nbzwvdGl0bGU+PGcgaWQ9IkwzNWg5diI+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJNMTkyLjksMjguNWgtMS4xYy0uMy0xLjMtLjUtMi43LS45LTMuOWExOS4xLDE5LjEsMCwwLDAtMS4zLTMuNiw3LjcsNy43LDAsMCwwLTYuNy00LjcsNDcsNDcsMCwwLDAtNi41LDBjLTEuNS4xLTIuMS44LTIuMywyLjNzLS4xLDEtLjEsMS42VjM2YS45LjksMCwwLDAsLjEuNSw2LjksNi45LDAsMCwwLDgtMy43LDE0LjIsMTQuMiwwLDAsMCwxLjQtMy42Yy4yLTEsLjctMS4yLDEuNy0xVjQ2LjNjLS44LjItMS40LjEtMS40LTFhMy44LDMuOCwwLDAsMC0uMy0xYy0xLjMtNC45LTQuMi02LjktOS40LTYuNCwwLC4yLS4xLjQtLjEuN1Y1NC43Yy4xLDMuNC44LDQsNC4yLDRhMzguNCwzOC40LDAsMCwwLDUuOS0uMiw5LDksMCwwLDAsNi4yLTQuNywyNi4xLDI2LjEsMCwwLDAsMi41LTcuNiw1LjIsNS4yLDAsMCwxLC4yLTEuMWgxLjR2MTVIMTU5LjFWNTlsMi40LS40YTIuNywyLjcsMCwwLDAsMi4xLTIuMiwxNi40LDE2LjQsMCwwLDAsLjItMi4zVjIwLjNjLS4xLTMuMS0uOC0zLjktMy45LTQuM2gtLjhWMTQuOGgzNC4xQzE5My4xLDE5LjMsMTkzLDIzLjgsMTkyLjksMjguNVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0wLjIgMC40KSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTIxNC44LDM2LjVjNC43LjUsNy4zLTEuMSw4LjktNS4zYTE1LjIsMTUuMiwwLDAsMCwuNi0yYy4xLTEuMS43LTEuMywxLjctLjlWNDYuMmMtMS4xLjMtMS4yLjMtMS40LS45cy0uMS0uNS0uMi0uOGMtMS4zLTUtNC4yLTcuMS05LjUtNi42LDAsLjItLjEuNC0uMS43LDAsNS4zLS4xLDEwLjcsMCwxNiwuMSwzLjUuOCw0LjEsNC4zLDQuMWE1NC4zLDU0LjMsMCwwLDAsNS43LS4yLDguNSw4LjUsMCwwLDAsNi00LjQsMjEuOCwyMS44LDAsMCwwLDIuNy03LjlsLjMtMS4xaDEuNHYxNUgxOTkuOVY1OWwyLjQtLjRhMi40LDIuNCwwLDAsMCwyLTIuMiw4LjUsOC41LDAsMCwwLC4zLTIuM1YyMC40Yy0uMS0zLjMtLjgtNC00LTQuNGgtLjhWMTQuOEgyMzRsLS4zLDEzLjZoLTEuMmMtLjItMS0uMy0yLS42LTNhMzcuNiwzNy42LDAsMCwwLTEuMy00Yy0xLjQtMy0zLjctNC45LTctNS4xYTM0LjIsMzQuMiwwLDAsMC02LjQsMGMtMS41LjEtMi4xLjktMi4zLDIuNGEyMi42LDIyLjYsMCwwLDAtLjEsMi42VjM2LjVaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMC4yIDAuNCkiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik0yOS4xLDI5SDI3LjdjLS4zLTEuMy0uNC0yLjUtLjctMy43QTE1LjMsMTUuMywwLDAsMCwyMy4xLDE4YTguOCw4LjgsMCwwLDAtOC40LTIuNGMtNC42LjktNi43LDYtNC4xLDEwLjJhMTQuMSwxNC4xLDAsMCwwLDMuNiwzLjdjMi43LDIuMSw1LjcsMy45LDguNCw1LjlBNDAuNiw0MC42LDAsMCwxLDI4LjEsNDBjNC40LDQuNiw0LjYsMTEuNy43LDE2LjFhMTQuNCwxNC40LDAsMCwxLTguNyw0LjVjLTUuNC45LTEwLjcuNS0xNS42LTIuNC0xLjYtMS0yLjQtLjUtMi44LDEuNC0uMS4zLS4xLjYtLjIuOUguMlY0My4zSDEuN2MuMywxLjguNSwzLjcuOSw1LjVhMTYuMSwxNi4xLDAsMCwwLDIuNyw2LjUsMTAuNCwxMC40LDAsMCwwLDEyLjIsMy4zYzMuNS0xLjUsNS01LjQsMy42LTkuNWExMC45LDEwLjksMCwwLDAtNC01LjJjLTIuMS0xLjYtNC40LTMuMS02LjYtNC43YTU3LDU3LDAsMCwxLTYuMi00LjhBMTEuNywxMS43LDAsMCwxLDEuMSwyMmMxLjItNC4yLDQuNC02LjQsOC40LTcuNHMxMC4yLTEuMSwxNC45LDEuOGMyLjQsMS40LDIuNywxLjIsMy40LTEuNWEuOC44LDAsMCwxLC4xLS40aDEuMloiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0wLjIgMC40KSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTY3LjcsNTl2MS4xSDQzLjhWNTlsMi44LS4zYzIuNS0uMiwzLjUtMS4xLDMuOC0zLjVhOSw5LDAsMCwwLC4yLTIuM1YxNy4xYTIuOCwyLjgsMCwwLDAtLjEtLjksOS4yLDkuMiwwLDAsMC05LjYsNC40LDI3LjIsMjcuMiwwLDAsMC0zLjEsOC44Yy0uMiwxLS42LDEuMi0xLjUsMUwzNiwxNC44SDc1LjVjLS4xLDUuMS0uMiwxMC4zLS40LDE1LjUtMS4xLjMtMSwuMy0xLjQtLjlhNjguNiw2OC42LDAsMCwwLTIuOC04Yy0xLjktNC4xLTUuMy01LjctMTAtNS4yYTMuNywzLjcsMCwwLDAtLjEsMS4xdjM1YTIwLjEsMjAuMSwwLDAsMCwuMSwyLjVjLjIsMi42LDEuMywzLjcsMy44LDMuOVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0wLjIgMC40KSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTc2LjksMTUuOHYtMUg5Ni41djEuMWwtMy4xLjRjLTEuNi4zLTIuMSwxLjMtMS40LDIuOCwzLjMsNi4zLDYuNSwxMi41LDEwLjEsMTguOWEuOC44LDAsMCwxLC4xLS40YzEuOS0zLjgsMy44LTcuNiw1LjYtMTEuNGEyMC4yLDIwLjIsMCwwLDAsMS42LTUuM2MuNS0yLjctLjktNC40LTMuNy00LjhsLTEuNS0uMlYxNC44aDEyLjdjLjMuNy0uMSwxLjEtLjcsMS42YTE0LjcsMTQuNywwLDAsMC0zLjIsMy40LDcwLDcwLDAsMCwwLTQuMiw3LjZjLTEuOCwzLjQtMy41LDYuOC01LjIsMTAuMmEzLjMsMy4zLDAsMCwwLS40LDEuM2MwLDUuMywwLDEwLjUuMSwxNS44LjEsMy4xLjcsMy43LDMuOCw0LjFsLjkuMnYxLjFIODguM1Y1OWwxLjctLjJjMS45LS4zLDIuNy0xLDMtMi44YTEzLjQsMTMuNCwwLDAsMCwuMS0yVjQzLjRhNS4zLDUuMywwLDAsMC0uNC0xLjhMODAuNywxOS44YTguMiw4LjIsMCwwLDAtMS4yLTJjLS43LS44LTEuNi0xLjQtMi4zLTIuMVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0wLjIgMC40KSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTEzOS45LDE0Ljh2MS4xbC0xLjguMmMtMi4yLjItMy4xLDEuMS0zLjMsMy40YTI0LjQsMjQuNCwwLDAsMC0uMSwyLjdWNTMuNmEyNC40LDI0LjQsMCwwLDAsLjEsMi43Yy4yLDEuNS44LDIuMiwyLjIsMi4zYTUxLjIsNTEuMiwwLDAsMCw2LjgtLjEsOSw5LDAsMCwwLDUuNi0zLjYsMTkuOSwxOS45LDAsMCwwLDQtOS4zYy4yLTEsLjUtMS41LDEuNy0xLjFWNjAuMUgxMTkuOFY1OWwyLS4zYTIuNywyLjcsMCwwLDAsMi41LTIuNiwxNC45LDE0LjksMCwwLDAsLjItMi4xVjIwLjhoMGMtLjEtNC4yLTEuMy00LjUtNC4yLTQuOGgtLjZWMTQuOFoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0wLjIgMC40KSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTE0MS45LDc0LjRjLTEuMS4yLTIsLjYtMi4xLDEuOGEyLjksMi45LDAsMCwxLS43LDEuNmMtLjQuNS0xLjIsMS0xLjUuOWEyLjYsMi42LDAsMCwxLTEuMy0xLjYsNy44LDcuOCwwLDAsMSwuMi0yLjEsNC40LDQuNCwwLDAsMSwuMi0xYy0uOC4yLTEuMi41LTEuMywxLjNhNC45LDQuOSwwLDAsMS0yLjIsMy40Yy0uNi4zLTEuMi41LTEuNywwYTEuNCwxLjQsMCwwLDEsMC0xLjlsMS41LTEuNGMxLjItLjksMS4zLTEuNC43LTIuOGwtLjYuM2ExMC42LDEwLjYsMCwwLDAtMy41LDUuN2MtLjcsMy4yLTEuNiw2LjMtMi40LDkuNGE2LjgsNi44LDAsMCwxLTEuMiwyLjIsMS41LDEuNSwwLDAsMS0xLjMuNSwxLjQsMS40LDAsMCwxLS43LTEsNS4yLDUuMiwwLDAsMSwuMi0yLjUsNDkuMSw0OS4xLDAsMCwxLDIuNi02LjMsMTkuNCwxOS40LDAsMCwwLDItNS43LDQxLjYsNDEuNiwwLDAsMSwxLjItNC40Yy4xLS4zLjUtLjUuNy0uN2EyLjQsMi40LDAsMCwxLC41LDEsMTguOCwxOC44LDAsMCwxLS42LDMuMWMuNy0uNiwxLjEtMS4yLDEuNi0xLjdzMi41LS45LDMuMi43YTQuMSw0LjEsMCwwLDAsLjIuN2MuNy0uNSwxLjUtLjksMS44LTEuNXMxLjQtLjMsMi0uMy42LjYuOCwxYTIuOCwyLjgsMCwwLDEsLjEsMSwyLjcsMi43LDAsMCwwLDIuMy0xLjVjLjItLjMuNi0uNC45LS42YTMuOCwzLjgsMCwwLDEsLjMsMWMtLjIsMS4yLS41LDIuNC0uNywzLjdhMTAuNSwxMC41LDAsMCwwLDMuMS0zLjVjLjEtLjIuMS0uNC4yLS41bC44LS41Yy4yLjMuNS42LjQuOXMtLjUsMi40LS43LDMuN2E0LjksNC45LDAsMCwwLS4xLDEuMmguM2wyLjctNC44LS43LS41YTIuNSwyLjUsMCwwLDEtLjQtNC4zLDIuOCwyLjgsMCwwLDEsMS44LS40LDEuOSwxLjksMCwwLDEsLjksMS40LDIxLjUsMjEuNSwwLDAsMS0uNSwzLjRsMS45LjdjLjguMywxLC42LjUsMS40YTE0LjcsMTQuNywwLDAsMC0xLjgsNi44YzEuMi0zLjEsMi4zLTYuMiw0LjQtOC44aC4zYTIuOCwyLjgsMCwwLDEtLjEuOSwyOC41LDI4LjUsMCwwLDAtMy45LDkuNWMtLjEuMy0uNi45LS43LjhzLTEuMi0uNS0xLjItLjlhMTAuOCwxMC44LDAsMCwxLDAtNCw0MC44LDQwLjgsMCwwLDEsMS42LTUuNGwtMS4zLS41LTEuNSwyLjdhOS42LDkuNiwwLDAsMS0xLjQsMi4xLDEuNSwxLjUsMCwwLDEtMS4yLjUsMi4xLDIuMSwwLDAsMS0xLTEuMSwxNC42LDE0LjYsMCwwLDEsMC0yLjEsMTMuNiwxMy42LDAsMCwxLTEuNywxLjcsMS43LDEuNywwLDAsMS0xLjUuM2MtLjMtLjEtLjYtLjktLjYtMS4zQTEzLjcsMTMuNywwLDAsMSwxNDEuOSw3NC40Wm0tMTQuNSw3LjVjLTEuNiwyLTIuOSw2LjItMi40LDhBMzAuOSwzMC45LDAsMCwwLDEyNy40LDgxLjlabTIyLjgtOS4zYy4xLTEuMy4zLTIuMy40LTMuNHMtLjMtLjctLjgtLjRTMTQ4LjUsNzEuNiwxNTAuMiw3Mi42Wm0tMTIuNyw1aC40YTMuNywzLjcsMCwwLDAsMS40LTIuOGMuMS0uMy0uNC0uNy0uNi0xaC0uNEMxMzguMSw3NS4yLDEzNy44LDc2LjQsMTM3LjUsNzcuNlptLTMuOS0xLjUtLjItLjJjLS43LjYtMS40LDEuMS0xLjUsMi4xbC4zLjJaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMC4yIDAuNCkiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik0xOTkuNiw3Ni43YTYuMyw2LjMsMCwwLDEtMSwxLjIsNy45LDcuOSwwLDAsMS0xLjUsMS4yYy0xLjEuNS0xLjcuMS0yLjYtMS40LS41LjgtLjksMS43LTEuNSwyLjVzLS44LjYtMSwuNWExLjgsMS44LDAsMCwxLS44LS45Yy0uMS0uOC0uMS0xLjUtLjItMi4yYTUuMSw1LjEsMCwwLDEtMS44LjljLS4zLjEtLjctLjQtMS4zLS44LS41LDEuNi0xLDMuMy0xLjYsNWExLjcsMS43LDAsMCwxLS4xLjdjLS4yLjMtLjYsMS0uNy45cy0xLjItLjUtMS4yLS45YTEzLjQsMTMuNCwwLDAsMS0uMS00LDU1LjIsNTUuMiwwLDAsMSwxLjYtNS40LDEuOCwxLjgsMCwwLDAtMi4zLDBjLS44LjYtMS41LjItMi4yLS41LS42LDEuNC0xLjEsMi43LTEuNyw0YTIuMywyLjMsMCwwLDEtLjYsMS4yYy0uNS44LTEuMy44LTEuNy0uMWE1LjEsNS4xLDAsMCwxLS4yLTEuM2MtLjEtMS41LS4xLTMuMS0uMS00LjdzLjEtLjYuMS0uOSwxLjQuMiwxLjQsMS4yLDAsMi45LDAsNC40aC4zYy42LTEuNywxLjMtMy4zLDItNC45YTEuOCwxLjgsMCwwLDEsLjYtLjgsMjAuOSwyMC45LDAsMCwxLDEuMS0zLDEuNSwxLjUsMCwwLDEsMS44LS43LDEuNywxLjcsMCwwLDEsMS4xLDEuN2MtLjEsMS0uNCwyLS42LDMuMmwxLjguNmMxLjEuNSwxLjIuNi42LDEuNmExNC44LDE0LjgsMCwwLDAtMS43LDdjLjItLjcuMy0xLjIuNS0xLjcsMS4zLTIuNiwyLjUtNS4yLDMuOS03LjdzMS4zLTEuMywyLjUtLjlsMS4yLS4yYTIuNSwyLjUsMCwwLDEsLjIsMS4yYy0uMywxLjgtLjgsMy41LTEuMiw1LjNhMi4yLDIuMiwwLDAsMCwuMSwxLjFsLjktMS42Yy44LTEuNywxLjYtMy41LDIuNS01LjFzLjctLjQsMS4xLS43YTYsNiwwLDAsMSwuMiwxLjJsLTEuMiw0YTcuNiw3LjYsMCwwLDAtLjEsMS41bC4yLjJhNC4zLDQuMywwLDAsMCwxLjMtLjlsMi45LTQuMmMuNi0uOSwxLjItMS45LDEuOS0yLjhhMi45LDIuOSwwLDAsMSwxLjMtLjhjLjktLjMsMS41LjMsMS4zLDEuM2E1LjksNS45LDAsMCwxLTIuNywzLjMsMy4xLDMuMSwwLDAsMC0uOCwzLjcsMS4yLDEuMiwwLDAsMCwxLC4yLDcuMyw3LjMsMCwwLDAsMi4zLTEuNiwzMi44LDMyLjgsMCwwLDAsMi0zLjEsMS44LDEuOCwwLDAsMSwuOC0uOWMwLC4zLjEuNSwwLC43YTExLDExLDAsMCwxLTMuMiw0LjhDMjAyLjIsODAuMSwyMDAuNCw3OS41LDE5OS42LDc2LjdabS0xNS41LTQuMWMuMi0xLjMuMy0yLjMuNC0zLjRzLS4zLS44LS44LS40UzE4Mi40LDcxLjYsMTg0LjEsNzIuNlptNS40LDQuNmMyLTEsMi45LTMuMywyLTQuOFptLTUuOC0zLjktMS42LTEuMmExLjYsMS42LDAsMCwwLC4zLDEuNEMxODIuNSw3My42LDE4My4xLDczLjQsMTgzLjcsNzMuM1pNMjA0LDcxLjZsLS4zLS4zLTEuNSwyLjEuMy4yWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTAuMiAwLjQpIi8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJNMTg4LjMsM2EzLjUsMy41LDAsMCwxLTIuMSwzLjJsLTQuNCwxLjYtOC4zLDIuNWgtLjljLjEtLjMuMS0uNy4zLS45bDYuOC02LjZBMjAuMiwyMC4yLDAsMCwxLDE4Mi45LjFDMTg1LjQtMS4zLDE4OC40LjQsMTg4LjMsM1oiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0wLjIgMC40KSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTE2Ny41LDc3LjNjLTEuNiwxLjctMi42LDEuNS0zLS4xbC0yLjEsMi43Yy0uNS42LTEuMiwxLjEtMS45LjdhMi4yLDIuMiwwLDAsMS0uNy0xLjcsMzQuNywzNC43LDAsMCwxLDEuMS02LjMsODAuNCw4MC40LDAsMCwxLDIuNy04LjMsNC4xLDQuMSwwLDAsMSwxLjMtMS45LDEuNywxLjcsMCwwLDEsMS40LS4zLDEuMywxLjMsMCwwLDEsLjUsMS4xYy0uMSw0LTEuMiw3LjctNC4zLDEwLjRsLS41LjRjLS4zLDEuOS0uNywzLjgtMSw1LjZsLjQuMmExNy4zLDE3LjMsMCwwLDAsMS43LTIuMWwzLjItNS4xYy43LTEuMSwxLjMtMS4yLDIuNS0uNmguM2wxLS4zYy4xLjMuMy43LjIsMWwtMS4yLDUuM2E1LjMsNS4zLDAsMCwwLS4xLDEuM2MxLjUtMS45LDEuOS00LjQsMy40LTYuNC4yLjMuMi40LjIuNC0xLDIuMi0xLjksNC41LTMsNi43LS4yLjQtLjguNi0xLjIuOWE1LjUsNS41LDAsMCwxLS44LTEuNUE2LjIsNi4yLDAsMCwxLDE2Ny41LDc3LjNabS01LjEtNC43YzIuMi0xLjgsNC4xLTcuNSwzLjItOS43QTQyLjQsNDIuNCwwLDAsMCwxNjIuNCw3Mi42Wm01LjcuMWgtLjNMMTY2LDc3LjJDMTY3LjksNzYsMTY4LjgsNzQsMTY4LjEsNzIuN1oiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0wLjIgMC40KSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTIxMi4yLDcxLjZjMS4xLDAsMS4zLjUsMS4zLDEuNHMwLDIuOSwwLDQuM2guMmwxLjgtNC40Yy4xLS4xLjEtLjQuMy0uNWwuNi0uOC42LjlhMTMsMTMsMCwwLDAsLjUsMS40bDEuNi0xLjcsMS0uNWMuMS40LjQuOC4zLDEuMWwtMS4yLDRjLS4xLjUtLjEsMS4xLS4yLDEuNmwuMy4yYTYuNyw2LjcsMCwwLDAsMS42LTEuMmMxLjItMS43LDIuMy0zLjUsMy41LTUuM2E4LjYsOC42LDAsMCwxLDEuOS0xLjksMS41LDEuNSwwLDAsMSwxLjYsMGMuMy4xLjMsMS4xLjEsMS40YTIzLjUsMjMuNSwwLDAsMS0yLjYsMi45Yy0uOS43LTEuNCwyLjktLjcsMy43YTEuMiwxLjIsMCwwLDAsMSwuMiw4LjYsOC42LDAsMCwwLDIuNC0xLjcsMTguMywxOC4zLDAsMCwwLDEuOC0zbC42LTFoLjNjMCwuMi4xLjUsMCwuN2ExMC44LDEwLjgsMCwwLDEtMyw0LjZjLTEsLjgtMiwxLjQtMy4zLjlzLTEuNi0xLjQtMS42LTIuOGwtLjgsMS0xLjIsMS4zYTIuMiwyLjIsMCwwLDEtMi40LjUsMi4yLDIuMiwwLDAsMS0uOC0yLjRjLjEtLjguNC0xLjYuNS0yLjNsLTItLjctMS4zLDMuMWExMS4xLDExLjEsMCwwLDEtLjksMS45LjkuOSwwLDAsMS0xLjctLjEsOS40LDkuNCwwLDAsMS0uMy0uOVY3Mi4yQTEuNCwxLjQsMCwwLDEsMjEyLjIsNzEuNlptMTMuMiwxLjguMy4yLDEuNS0yLS4zLS4yWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTAuMiAwLjQpIi8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJNMjM1LjQsNzYuNGE2My41LDYzLjUsMCwwLDEsMi44LTE0LjMuNC40LDAsMCwxLC4yLS40LDEuOCwxLjgsMCwwLDEsLjctLjVjMCwuMi4yLjUuMi44cy0uOCwzLjEtMSw0LjZjLS42LDMuMi0xLDYuNC0xLjUsOS42YTIsMiwwLDAsMS0uMiwxLjFjLS4xLjMtLjQuNS0uNi43YTIuNCwyLjQsMCwwLDEtLjYtLjhDMjM1LjMsNzcsMjM1LjQsNzYuNywyMzUuNCw3Ni40WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTAuMiAwLjQpIi8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJNMTk3LjcsNjguOWEzLjUsMy41LDAsMCwxLC42LjljMCwuMS0uNC41LS40LjRhMS4xLDEuMSwwLDAsMS0uNy0uNkMxOTcuMSw2OS42LDE5Ny40LDY5LjMsMTk3LjcsNjguOVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0wLjIgMC40KSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTIyMC45LDcwLjVsLS43LS44Yy0uMS0uMS4zLS42LjQtLjVhMS4zLDEuMywwLDAsMSwuNy42QTcuOCw3LjgsMCwwLDEsMjIwLjksNzAuNVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0wLjIgMC40KSIvPjwvZz48Y2lyY2xlIGNsYXNzPSJjbHMtMSIgY3g9IjIzNS4zIiBjeT0iNzkuOSIgcj0iMC41Ii8+PC9zdmc+' alt='Logo'/> " +
            "<div class='loaderMagic'></div>" +
            "<p>Redirection vers PayPal...</p>" +
            "</div>";
        jQuery("#" + container).append(loader);
    }
    jQuery("#payment").css({"display":"none"});
    showLoader("order_review");
    jQuery("#order_review").ready(function(){
        jQuery("#payment_method_paypal").prop("checked",true);
        jQuery("#order_review").submit();
    })

</script>
<?php
}else{
       ?>
       <script>
           jQuery("label[for='wc-stripe-new-payment-method']").text("Enregistrer cette carte bancaire");
       </script>
           <?php
   }
 ?>
    <script>
        jQuery(".rowPayment").parents(".et_pb_row").prev().remove();
        jQuery(".rowPayment").parents(".et_pb_post_content").css({"background-color":"transparent"});

    </script>
<?php
} ?>