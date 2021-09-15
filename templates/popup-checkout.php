<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function popup( $idProduct, $textValidate, $textPayment ) {
	$product         = wc_get_product( $idProduct );
	$crosssell       = wc_get_product( get_post_meta( $idProduct, '_crosssell_ids' )[0][0] );
	$active_gateways = array();
	$gateways        = WC()->payment_gateways->payment_gateways();
	foreach ( $gateways as $id => $gateway ) {
		if ( isset( $gateway->enabled ) && 'yes' === $gateway->enabled ) {
			$active_gateways[ $id ] = $gateway;
		}
	}
	?>
    <div id="magic-checkout">
        <form id="magic-checkout-form">
            <input type="hidden" name="action" value="submitFormPopup">
            <input type="hidden" name="userKnow" value="no">
            <input type="hidden" name="codePromoApply" value="no">
            <input type="hidden" name="textpayment" value="<?php echo $textPayment ?>">
            <h2 id="productName" class="titleProduct"><?php echo $product->get_title() ?></h2>
            <hr/>
            <div class="info">
                <div class="name">
					<?php if ( ! is_user_logged_in() ) { ?>
                        <input type="text" name="lname" id="lname" placeholder="Nom*" required/>
                        <input type="text" name="fname" id="fname" placeholder="Prénom*" required/>
					<?php } else {
						$user = wp_get_current_user();
						?>
                        <input type="text" name="lname" id="lname" placeholder="Nom*"
                               value="<?php echo $user->last_name ?>" required/>
                        <input type="text" name="fname" id="fname" placeholder="Prénom*"
                               value="<?php echo $user->first_name ?>" required/>
					<?php } ?>
                </div>
                <div class="loginInfo">
					<?php if ( ! is_user_logged_in() ) { ?>
                        <input type="email" name="email" id="email" placeholder="Email*" required/>
                        <input type="password" name="password" id="password" placeholder="Mot de passe*" class="hide"/>
					<?php } else {
						$user = wp_get_current_user();
						?>
                        <p class="emailConnected"><?php echo $user->user_email ?></p>
					<?php } ?>
                </div>
            </div>
            <div class="codepromo">
                <a id="showCodePromo">Code promo</a>
                <input class="hide" type="text" name="codepromo" id="codepromo" placeholder="Saisir le code promo"/>
                <button id="submitPromo" type="button" class="hide">Valider</button>
            </div>
            <div class="credit-card">
				<?php
				$requiredField = "required";
				foreach ( $active_gateways as $val ) { ?>
                    <div>
                        <input type="radio" id="<?php echo $val->method_title; ?>" name="credit-card"
                               value="<?php echo $val->method_title; ?>" <?php echo $requiredField ?> />
                        <label for="<?php echo $val->method_title; ?>"><img
                                    src="<?php echo esc_url( plugins_url( '/assets/images/' . $val->method_title . '.png', dirname( __FILE__ ) ) ) ?>"
                                    alt="<?php echo $val->method_title; ?>"/></label>
                    </div>
					<?php $requiredField = "";
				} ?>
            </div>
			<?php if ( $crosssell ) { ?>
                <div class="related-product">
                    <div class="image">
						<?php echo $crosssell->get_image(); ?>
                    </div>
                    <input type="checkbox" name="crosssell" id="crosssell" value="<?php echo $crosssell->get_id() ?>">
                    <label for="crosssell">

						<?php
						$paiementType = "";
						if ( isset( $crosssell->get_attributes()["paiement"] ) && ! empty( $crosssell->get_attributes()["paiement"] ) ) {
							switch ( $crosssell->get_attributes()["paiement"] ) {
								case "Mensuel" :
									$paiementType = "/ mois ";
									break;
								case "Trimestriel" :
									$paiementType = "/ trimestre ";
									break;
								case "Semestriel" :
									$paiementType = "/ semestre ";
									break;
								case "Annuel" :
									$paiementType = "/ an ";
									break;
								default:
									$paiementType = "";
									break;
							}
						}
						$intro = $crosssell->get_meta( 'magic_checkout_intro' );
						$outro = $crosssell->get_meta( 'magic_checkout_outro' );
						if ( empty( $intro ) ) {
							$intro = "Ajouter";
						}
						if ( empty( $outro ) ) {
							$outro = "pour être stylée";
						}
						?>

						<?php echo $intro . " " . $crosssell->get_title(); ?>
                        à <?php echo $crosssell->get_price() . "€ " . $paiementType . $outro; ?>
                    </label>
                </div>
			<?php } ?>
            <div class="accept">
                <input type="checkbox" name="readandaccept" id="readandaccept" required/>
                <label for="readandaccept">J'ai lu et j'accepte les <a href="/conditions-generales-de-vente/">conditions
                        générales de vente</a>.</label>
            </div>
            <div class="recap">
                <hr/>
                <div>
                    <h3>Total :</h3>

					<?php

					$tempCart = array();
					foreach ( WC()->cart->cart_contents as $row => $val ) {
						array_push( $tempCart, $val["data"] );
					}

					if ( $tempCart[0] instanceof WC_Product_Subscription ) {
						$period = "";
						switch ( $tempCart[0]->subscription_period ) {
							case "month":
								$period = "/ mois";
								break;
							case "annual":
								$period = "/ an";
								break;
							case "day":
								$period = "/ jour";
								break;
							case "week":
								$period = "/ semaine";
								break;
							default :
								$period = "/ " . $tempCart[0]->subscription_period;
								break;
						}

						echo '<div id="totalPrice">' . $tempCart[0]->subscription_price . " " . $period . ' TTC</div>';
						if ( intval( $tempCart[0]->subscription_trial_length ) > 0 ) {
							echo "<div class='trialTexte'>Une période d'essaie de" . $tempCart[0]->subscription_trial_length . " jours, puis le paiement récurrent</div>";
						}
					} else {
						$regularPrice = 0;
						foreach ( WC()->cart->get_cart_contents() as $productCart ) {
							$regularPrice += floatval( $productCart["data"]->get_regular_price() );
						}

						if ( floatval( WC()->cart->get_totals()["total"] ) == $regularPrice ) {
							echo '<div id="totalPrice">' . WC()->cart->get_cart_total() . '</div>';
						} else {
							echo '<div id="totalPrice">' . WC()->cart->get_cart_total() . '<div class="price_coupon_info"> au lieu de <span class="woocommerce-Price-amount amount"><bdi>'.number_format($regularPrice, 2, ',', ' ').'<span class="woocommerce-Price-currencySymbol">€</span></bdi></span></div></div>';
						}
					}
					?>


                </div>
            </div>
            <div class="action">
                <input id="submitMagic" type="submit" value="<?php echo $textValidate ?>">
            </div>
            <div class="more">
                <img src="<?php echo esc_url( plugins_url( '/assets/images/secured.png', dirname( __FILE__ ) ) ) ?>"
                     alt="Secure icone"/>
            </div>
            <a id="issueCheckout">Cliquez ici si vous avez des difficultés</a>
        </form>
    </div>

<?php }