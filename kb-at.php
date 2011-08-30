<?php

/**
 * Eases adding hooks and actions.
 *
 * @author Kunal Bhalla
 * @version 0.1
 * @package KB_Includes
 */

/**
 * Automates adding functions to actions/filters.
 *
 * Usage in any child class:
 * A method of the form 
 *     `at_<hook name>__[<priority>_][<args>_]<method name>`
 * will be attached to the corresponding <hook name>.
 *
 * Terms in [] are optional.
 */
class KB_At {
	public function __construct() {
		kb_debug( "Called" );
		$methods = get_class_methods( $this );
		foreach( $methods as $method ) {
			if( preg_match( '/at_(.+?)__(?:([0-9]*)_)?(?:([0-9]*)_)?*./', $method, $matches ) > 0 ) {
				kb_debug( $matches );

				if( !isset( $matches[2] ) && !isset( $matches[3] ) )
					add_filter( $matches[1], Array( $this, $method ) );
				else if( !isset( $matches[3] ) )
					add_filter( $matches[1], Array( $this, $method ), $matches[2] );
				else 
					add_filter( $matches[1], Array( $this, $method ), $matches[2], $matches[3] );
			}
		}
	}
}
