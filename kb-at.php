<?php

/**
 * Eases adding hooks and actions.
 *
 * @author Kunal Bhalla
 * @version 0.1
 * @package KB_Includes
 */

/**
 * Syntactic sugar for adding functions to actions/filters.
 *
 * In any class extending KB_At, adding phpdoc variables:
 *   - @hook <hook name>
 *   - [@priority <priority>]
 * will add that particular function to the specified hook.
 *
 * The number of arguments to be passed will be picked up
 * automatically by reflection based on the arguments added
 * in the function.
 */
class KB_At {
	private $reflect;

	public function __construct() {
		$this->reflect = new ReflectionClass( $this );
		$methods = $this->reflect->getMethods();
		
		foreach( $methods as $method ) {
			$docBlock = $method->getDocComment(); 
			$docs = $this->docBlockExtractor( $docBlock );

			if( array_key_exists( 'hook', $docs ) ) {
				$params = $method->getNumberOfParameters();
				$func = Array( $this, $method->getName() );

				if( did_action( $docs[ 'hook' ] ) && $params == 0 )
					call_user_func( $func );
				else 
					add_filter( $docs[ 'hook' ], $func, 
						    array_key_exists( 'priority', $docs ) ? $docs['priority'] : 10,
						    $params );
			}
		}
	}

	private function docBlockExtractor( $docBlock ) {
			$matches = Array();
			$docs = Array();

			if( preg_match_all( '/@(.+?)\s+(.+?)\s*\n/', $docBlock, $matches ) )
				foreach( $matches[0] as $key => $match )
					$docs[ $matches[1][$key] ] = $matches[2][$key];
			return $docs;	
	}
}
