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
	 * The value this seting was constructed for.
	 * @var mixed
	 */
	protected $value; 

	/**
	 * Constructor -- initializes saved data if required.
	 *
	 * Default values are passed to this constructor -- which will be saved in case 
	 * there is no custom configuration saved yet; returns the actual value to be 
	 * used based on saved settings.
	 *
	 * The id's are namespaced similar to PHP syntax "a\b\c" and so on: passing the id
	 * a\b\c will set [plugin][a][b][c] = option.
	 *
	 * @param string $id Identifier for this setting.
	 * @param mixed $defaults The default value for this string
	 * @return mixed The corresponding options for this run
	 */
	public function __construct( $id, $defaults ) {
		parent::__construct();

		if( empty( $this->plugin ) )
			die( "Plugin must be specified in KB_Setting." );

		/* No data loaded from the database yet */
		if( empty( self::$saved ) ) {
			self::$saved = true;
			self::$container[ $this->plugin ] = ( get_option( $this->option(), Array() ) );
		}

		$idArray = explode( "\\", $id ); $intermediate = &self::$container[ $this->plugin ];
		foreach( $idArray as $idA ) {
			if( !array_key_exists( $idA, $intermediate ) )
				$intermediate[ $idA ] = Array();	

			$intermediate = &$intermediate[ $idA ];
		}
		
		if( empty( $intermediate ) )
			$intermediate = $defaults;
		
		$this->value = $intermediate;
	}
	
	/** 
	 * Return the value corresponding to the current setting.
	 * @return Mixed value
	 */
	public function get() {
		return $this->value;
	}

	/**
	 * Returns a json-encoded form of the saved data.
	 * @return String json
	 */
	public static function getData($plugin) {
		if( isset( self::$container[ $plugin ] ) )
			return "{\"{$plugin}\":". json_encode( self::$container[ $plugin ] ) . "}";
		else return "{\"{$plugin}\": {}}";
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
 
