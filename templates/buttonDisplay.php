<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function getButton( $idProduct ) {
	?>
    <button class="magic-button" data-idproduct="<?php echo $idProduct ?> ">ACHETER</button>
<?php }