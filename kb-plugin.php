<?php

/**
 * Basic Class for handling plugins.
 *
 * @author Kunal Bhalla
 * @version 0.1
 * @package KB_Includes
 */

/**
 * Base class for plugins.
 *
 * Maps common actions to functions invisibly,
 * allowing simpler functions and cleaner code.
 *
 */
class KB_Plugin extends KB_At {

	public function __construct() {
		parent::__construct();
	}
}

