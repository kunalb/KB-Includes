<?php

/**
 * Register and acquire settings.
 *
 * @author Kunal Bhalla
 * @version 0.2
 * @package KB_Includes
 * @copyright Copyright (c) 2011, Kunal Bhalla
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

if( !class_exists( 'KB_Setting' ) ) {

class KB_Setting extends KB_At {

	/** 
	 * Stores all the settings 
	 * @var Array
	 */ 
	protected static $container = Array();

	/**
	 * Has option data loaded from the database.
	 * @var bool
	 */
	protected static $saved = false; 

	/**
	 * Must be over-ridden in the child class to provide an identifier for settings. 
	 * @var String
	 */
	protected $plugin;

	/**
	 * Constructor -- initializes saved data if required.
	 *
	 * Default values are passed to this constructor -- which will be saved in case 
	 * there is no custom configuration saved yet; returns the actual value to be 
	 * used based on saved settings.
	 *
	 * @param string $id Identifier for this setting.
	 * @param mixed $defaults The default value for this string
	 * @return mixed The corresponding options for this run
	 */
	public function __construct( $id, $defaults ) {
		if( empty( $this->plugin ) )
			die( "Plugin must be specified in KB_Setting." );

		/* No data loaded from the database yet */
		if( empty( self::$saved ) ) {
			self::$saved = true;
			self::$container[ $this->plugin ] = ( get_option( $this->option(), Array() ) );
		}

		if( is_array( self::$container[ $this->plugin ] ) && array_key_exists( $id, self::$container[ $this->plugin ] ) )
			return self::$container[ $this->plugin ][ $id ];
		else
			self::$container[ $this->plugin ][ $id ] = $defaults;
		 
		return $defaults; 	 
	}

	/**
	 * Returns a json-encoded form of the saved data.
	 * @return String json
	 */
	public static function getData($plugin) {
		return "\"{$plugin}\":". json_encode( self::$container[ $plugin ] );
	}

	/**
	 * Get option corresponding to the given plugin name.
	 * @return String The option name corresponding to the settings in this plugin.
	 */
	protected function option() {
		return "kb-config-" . $this->plugin;
	}
}

}
 
