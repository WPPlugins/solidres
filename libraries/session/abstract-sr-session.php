<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking extension for Wordpress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2015 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

abstract class SR_Session {

	protected $_customer_id;

	protected $_data = array();

	protected $_dirty = false;

	public function __get( $key ) {
		return $this->get( $key );
	}

	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}

	public function __isset( $key ) {
		return isset( $this->_data[ sanitize_title( $key ) ] );
	}

	public function __unset( $key ) {
		if ( isset( $this->_data[ $key ] ) ) {
			unset( $this->_data[ $key ] );
			$this->_dirty = true;
		}
	}

	/**
	 * Get a session variable.
	 *
	 * @param string $key
	 * @param  mixed $default used if the session variable isn't set
	 * @return array|string value of session variable
	 */
	public function get( $key, $default = null ) {
		$key = sanitize_key( $key );
		return isset( $this->_data[ $key ] ) ? maybe_unserialize( $this->_data[ $key ] ) : $default;
	}

	/**
	 * Set a session variable.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $key, $value ) {
		if ( $value !== $this->get( $key ) ) {
			$this->_data[ sanitize_key( $key ) ] = maybe_serialize( $value );
			$this->_dirty = true;
		}
	}

	public function get_customer_id() {
		return $this->_customer_id;
	}
}