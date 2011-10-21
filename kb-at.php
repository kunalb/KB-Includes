<?php

/**
 * Eases adding hooks and actions.
 *
 * @author Kunal Bhalla
 * @version 0.2
 * @package KB_Includes
 * @copyright Copyright (c) 2011, Kunal Bhalla
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

if( !class_exists( 'KB_At' ) ) {

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
	 *
	 * For classes over-riding inherited functions, this backtracs
	 * till it reaches KB_At or till it finds an '@hook' docbloc for 
	 * the current class.
	 */
	public function __construct() {
		$this->reflect = new ReflectionClass( $this );
		$methods = $this->reflect->getMethods();

		foreach( $methods as $method ) {
			if( $method->isPublic() ) {
				$func = ''; $priority = 10; $params = 0; $hook = '';
				$currentMethod = $method; $currentClass = $this->reflect;

				while( !empty( $currentMethod ) && empty( $hook ) ) {
					$docBlock = $currentMethod->getDocComment(); 
					$docs = $this->docBlockExtractor( $docBlock );

					if( array_key_exists( 'hook', $docs ) ) {
						$hook = $docs[ 'hook' ];
						$params = $method->getNumberOfParameters();
						$func = Array( $this, $method->getName() );
						if( array_key_exists( 'priority', $docs ) )
							$priority = $docs[ 'priority' ];
					}

					if( $currentClass->getParentClass() !=  false ) {
						$parentClass = $currentClass->getParentClass();
						if( $parentClass->hasMethod( $currentMethod->getName() ) ) {
							$currentMethod = $parentClass->getMethod( $currentMethod->getName() );
							$currentClass = $parentClass;
						} else
							$currentMethod = "";	
					} else $currentMethod = "";	
				}

				if( !empty( $func ) ) {
					if( did_action( $hook ) && $params == 0 )
						call_user_func( $func );
					else 
						add_filter( $hook, $func, $priority, $params );
				}
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

}

/**
 * Changelog
 * =========
 *
 * - 0.2
 *   - Catch hooks of the parent class
 *
 * - 0.1
 *   - Initial Version
 */
