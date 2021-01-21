<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function getButton( $idProduct, $textButton, $textValidate, $textPayment ) {
	?>
    <button class="magic-button" data-idproduct="<?php echo $idProduct ?> " data-textvalidate="<?php echo $textValidate ?> " data-textpayment="<?php echo $textPayment ?> "><?php echo $textButton?></button>
<?php }