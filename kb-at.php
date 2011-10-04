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
	/**
	 * Reflection of this class.
	 * @var ReflectionClass
	 */
	private $reflect;

	/**
	 * Hooks based on docs
	 *
	 * Obtains the docblocks for each method of the class,
	 * and checks both for the existence of at '@hook' and
	 * that the function is publically accessible.
	 */
	public function __construct() {
		$this->reflect = new ReflectionClass( $this );
		$methods = $this->reflect->getMethods();
		
		foreach( $methods as $method ) {
			$docBlock = $method->getDocComment(); 
			$docs = $this->docBlockExtractor( $docBlock );

			if( array_key_exists( 'hook', $docs ) && $method->isPublic() ) {
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

	/**
	 * Utility function to extract all @ values in a doc block.
	 *
	 * @return [String]String Array `@xyz abc` is returned as the value `abc` at key `xyz`.
	 */
	private function docBlockExtractor( $docBlock ) {
			$matches = Array();
			$docs = Array();

			if( preg_match_all( '/@(.+?)\s+(.+?)\s*\n/', $docBlock, $matches ) )
				foreach( $matches[0] as $key => $match )
					$docs[ $matches[1][$key] ] = $matches[2][$key];
			return $docs;	
	}
}
