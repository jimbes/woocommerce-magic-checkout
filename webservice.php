<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 07/06/2018
 * Time: 00:17
 */

function popupDisplay() {
	$product_id = intval( $_POST["idproduct"] );
	$textValidate =  $_POST["textvalidate"] ;
	$textPayment = $_POST["textpayment"] ;
	WC()->cart->empty_cart();
	WC()->session->set('cart', array());
	WC()->cart->add_to_cart( $product_id );
	popup( $product_id,$textValidate,$textPayment  );
	wp_die();
}
function clearCart(){
	WC()->cart->empty_cart();
	WC()->session->set('cart', array());
	wp_die();
}

function emailExist() {
	if ( ! email_exists( $_POST["email"] ) ) {
		echo json_encode( false );
	} else {
		echo json_encode( true );
	}
	wp_die();
}

function getTotalPrice() {
	header( "Content-Type: application/json" );

	$regularPrice = 0;
	foreach ( WC()->cart->get_cart_contents() as $productCart ) {
		$regularPrice += floatval( $productCart["data"]->get_regular_price() );
	}

	if(count(WC()->cart->get_applied_coupons()) > 0) {
		echo json_encode(
			[
				"original_price" => WC()->cart->get_cart_subtotal() . " TTC",
				"sold_price"   => WC()->cart->get_cart_total() . " TTC",
				"regular_price"   => number_format($regularPrice, 2, ',', ' ') . " TTC"
			]
		);
	}else{
		echo json_encode(
			[
				"original_price" => WC()->cart->get_cart_total() . " TTC",
				"regular_price" => number_format($regularPrice, 2, ',', ' ') . " TTC"
			]
		);
	}
	wp_die();
}

function couponApply() {
	$coupon_code    = $_POST["coupon"];
	$addCoupon      = $_POST["addCoupon"];
	$coupon         = new WC_Coupon( $coupon_code );
	$discount       = new WC_Discounts( WC()->cart );
	$valid_response = $discount->is_coupon_valid( $coupon );
	if ( $addCoupon == "false" ) {
		foreach ( WC()->cart->get_coupons() as $code => $coup ) {
			WC()->cart->remove_coupon( $code );
		}
		WC()->cart->calculate_totals();
	} else {
		if ( is_wp_error( $valid_response ) ) {
			echo json_encode( false );
		} else {
			WC()->cart->add_discount( sanitize_text_field( $coupon_code ) );
			echo json_encode( true );
		}
	}
	wp_die();
}

function addCrossSell() {
	$idCrossSell = intval( $_POST["idCross"] );
	$isAdd       = $_POST["addItem"];
	if ( $isAdd == "true" ) {
		WC()->cart->add_to_cart( $idCrossSell );
		echo json_encode( "add" );
	} else {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_item['product_id'] == $idCrossSell ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}
		}
		echo json_encode( "remove" );
	}
	wp_die();
}

function submitFormPopup() {

	$magicorder = new MagicOrder();
	$magicorder->setFname($_POST["fname"]);
	$magicorder->setLname($_POST["lname"]);
	$magicorder->setEmail($_POST["email"]);
	$magicorder->setUserKnow($_POST["userKnow"]);
	$magicorder->setPassword($_POST["password"]);

	$magicorder->setCodepromo($_POST["codepromo"]);
	$magicorder->setCodePromoApply($_POST["codePromoApply"]);
	$magicorder->setCreditCard($_POST["credit-card"]);

	$magicorder->setReadandaccept($_POST["readandaccept"]);


	if(!$magicorder->getIDUser()){
		echo  401;
		wp_die();
	}else{
		$magicorder->createOrder();
		$_GET['key'] = $magicorder->getOrder()->order_key;
		$_GET['pay_for_order'] = "true";
		header( "Content-Type: application/json" );
		echo json_encode(
			["key"=>$magicorder->getOrder()->order_key,
			 "pay_for_order"=>"true",
			 "idOrder"=>$magicorder->getOrder()->ID,
			 "paiementMethod"=>$magicorder->getCreditCard(),
			 "user" => $magicorder->getUserWordpress()->user_email,
			 "urlOrderPay" => $magicorder->getOrder()->get_checkout_payment_url(),
			 "textpayment" => $_POST["textpayment"]]);
		wp_die();
	}
	wp_die();
}

add_action( 'wp_ajax_getPopupDisplay', 'popupDisplay' );
add_action( 'wp_ajax_nopriv_getPopupDisplay', 'popupDisplay' );
add_action( 'wp_ajax_emailExist', 'emailExist' );
add_action( 'wp_ajax_nopriv_emailExist', 'emailExist' );
add_action( 'wp_ajax_couponApply', 'couponApply' );
add_action( 'wp_ajax_nopriv_couponApply', 'couponApply' );
add_action( 'wp_ajax_getTotalPrice', 'getTotalPrice' );
add_action( 'wp_ajax_nopriv_getTotalPrice', 'getTotalPrice' );
add_action( 'wp_ajax_addCrossSell', 'addCrossSell' );
add_action( 'wp_ajax_nopriv_addCrossSell', 'addCrossSell' );
add_action( 'wp_ajax_submitFormPopup', 'submitFormPopup' );
add_action( 'wp_ajax_nopriv_submitFormPopup', 'submitFormPopup' );
add_action( 'wp_ajax_clearCart', 'clearCart' );
add_action( 'wp_ajax_nopriv_clearCart', 'clearCart' );
?>