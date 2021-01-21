<?php


/**
 * Class MagicOrder
 */
class MagicOrder {
	private $userKnow;
	private $codePromoApply;
	private $lname;
	private $fname;
	private $email;
	private $password;
	private $codepromo;
	private $creditCard;
	private $readandaccept;
	private $userWordpress;
	private $order;

	/**
	 * MagicOrder constructor.
	 */
	public function __construct() {
	}


	/**
	 * Get User ID
	 * @return bool
	 */
	public function getIDUser() {
		/* User is login */

		if ( is_user_logged_in() ) {
			$this->setUserWordpress( wp_get_current_user() );

			return true;
		} else {
			if ( $this->getUserKnow() == "no" ) {
				$userid = wc_create_new_customer( $this->getEmail(), '', '', [
					'autogenerate_password' => true,
					'autogenerate_username' => true
				] );

				if ( is_wp_error( $userid ) ) {
					return false;
				}

				if ( ! empty( $this->getFname() ) ) {
					update_user_meta( $userid, 'billing_first_name', sanitize_text_field( $this->getFname() ) );
					update_user_meta( $userid, 'first_name', sanitize_text_field( $this->getFname() ) );
				}
				if ( ! empty( $this->getLname() ) ) {
					update_user_meta( $userid, 'billing_last_name', sanitize_text_field( $this->getLname() ) );
					update_user_meta( $userid, 'last_name', sanitize_text_field( $this->getLname() ) );
				}
				$user = get_user_by( 'ID', $userid );
				if ( ! is_wp_error( $user ) ) {
					wp_clear_auth_cookie();
					wp_set_current_user( $user->ID ); // Set the current user detail
					wp_set_auth_cookie( $user->ID ); // Set auth details in cookie
				} else {
					return false;
				}
				$this->setUserWordpress( $user );
			} else {
				$creds = array(
					'user_login'    => $this->getEmail(),
					'user_password' => $this->getPassword(),
					'remember'      => true
				);
				$user  = wp_signon( $creds, false );
				if ( is_wp_error( $user ) ) {
					return false;
				}
				$this->setUserWordpress( $user );
			}

			return true;
		}
	}

	public function createOrder() {
		global $wp;
		$cart     = WC()->cart;
		$checkout = WC()->checkout();
		$order_id = $checkout->create_order( array() );
		$this->setOrder( wc_get_order( $order_id ) );
		update_post_meta( $order_id, '_customer_user', $this->getUserWordpressID() );
		$this->getOrder()->calculate_totals();
		$this->getOrder()->update_status( 'pending' );
		WC()->session->set( 'order_awaiting_payment', $order_id );
		$wp->query_vars['order-pay'] = absint( $order_id ); // WPCS: input var ok.
	}

	/**
	 * @return mixed
	 */
	public function getUserKnow() {
		return $this->userKnow;
	}

	/**
	 * @param mixed $userKnow
	 */
	public function setUserKnow( $userKnow ) {
		$this->userKnow = $userKnow;
	}

	/**
	 * @return mixed
	 */
	public function getCodePromoApply() {
		return $this->codePromoApply;
	}

	/**
	 * @param mixed $codePromoApply
	 */
	public function setCodePromoApply( $codePromoApply ) {
		$this->codePromoApply = $codePromoApply;
	}

	/**
	 * @return mixed
	 */
	public function getLname() {
		return $this->lname;
	}

	/**
	 * @param mixed $lname
	 */
	public function setLname( $lname ) {
		$this->lname = $lname;
	}

	/**
	 * @return mixed
	 */
	public function getFname() {
		return $this->fname;
	}

	/**
	 * @param mixed $fname
	 */
	public function setFname( $fname ) {
		$this->fname = $fname;
	}

	/**
	 * @return mixed
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail( $email ) {
		$this->email = $email;
	}

	/**
	 * @return mixed
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param mixed $password
	 */
	public function setPassword( $password ) {
		$this->password = $password;
	}

	/**
	 * @return mixed
	 */
	public function getCodepromo() {
		return $this->codepromo;
	}

	/**
	 * @param mixed $codepromo
	 */
	public function setCodepromo( $codepromo ) {
		$this->codepromo = $codepromo;
	}

	/**
	 * @return mixed
	 */
	public function getCreditCard() {
		return $this->creditCard;
	}

	/**
	 * @param mixed $creditCard
	 */
	public function setCreditCard( $creditCard ) {
		$this->creditCard = $creditCard;
	}

	/**
	 * @return mixed
	 */
	public function getReadandaccept() {
		return $this->readandaccept;
	}

	/**
	 * @param mixed $readandaccept
	 */
	public function setReadandaccept( $readandaccept ) {
		$this->readandaccept = $readandaccept;
	}

	/**
	 * @return mixed
	 */
	public function getUserWordpress() {
		return $this->userWordpress;
	}

	/**
	 * @param mixed $userWordpress
	 */
	public function setUserWordpress( $userWordpress ) {
		$this->userWordpress = $userWordpress;
	}

	/**
	 * @return mixed
	 */
	public function getUserWordpressID() {
		return $this->userWordpress->ID;
	}

	/**
	 * @return mixed
	 */
	public function getOrder() {
		return $this->order;
	}

	/**
	 * @param mixed $order
	 */
	public function setOrder( $order ) {
		$this->order = $order;
	}
}