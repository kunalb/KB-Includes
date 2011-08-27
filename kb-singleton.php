<?php

/**
 * A generic singleton class. 
 * @package KB_Includes
 */	

/**
 * Singleton abstract class.
 */ 
abstract class KB_Singleton {
	/** Storing the instance */
	protected static $instance;

	/** The singleton function must control access */
	abstract static public function singleton();

	/** The constructor must be hidden. */
	abstract protected function __construct();

	/** The clone function must not be accessible */
	private function __clone() {
		throw new Exception("Cannot clone this singleton.");
	}
}
