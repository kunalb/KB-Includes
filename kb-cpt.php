<?php

/**
 * Custom Post Types
 *
 * @author Kunal Bhalla
 * @version 0.1
 * @package KB_Includes
 */

/** 
 * Generic class to handle common CPT functionality.
 */
class KB_Cpt {

	/** Arguments passed on for registering the post type. */
	private $args;

	/** The post type identifier */
	private $id;
	
	public function __construct( $name, $args = Array() ) {
		if( empty( $name ) )
			throw new Exception( "CPT instance requires a name." );

		$this->id = $name;	

		$this->args = $this->parse_args( $args );

		( did_action( 'init' ) === TRUE )? $this->register() : add_action
	}

	private function parse_args( $args ) {
		return $args;
	}

	public function register() {
		register_post_type( $this->id, $this->args );
	}

}
