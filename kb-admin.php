<?php

/**
 * Generic Class that can be extended for creating admin screens for plugins.	
 * @package KB_Includes
 */

 class KB_Admin {

	/**
	 * The suffix generated for this plugin.
	 * @var string
	 */
	private $hook_suffix;

	/**
	 * Store passed arguments for initialization.
	 * @var Array[string]String
	 */
	private $args;

	/**
	 * The constructor, conditionally initializes the requirements for the plugin.
	 *
	 * Instead of having to check whether the correct page has been loaded externally,
	 * this function only initializes required scripts and styles on the appropriate
	 * plugin pages.
	 *
	 * @param Array[string]string args The Admin Page structure for the menu; accepts an Array with the options
	 * - 'parent'     => Parent Menu (default admin.php, if menu-title is specified)
	 * - 'slug'       => Slug for link (required)
	 * - 'page-title' => Page title (required)
	 * - 'menu-title' => Menu title (optional, if not provided no menu entry will be created)
	 * - 'capability' => Who can access this page (default 'read')
	 * - 'icon-url'   => Icon, only for top level pages (default none)
	 * - 'position'   => Position in the Menu, only for top level pages (default 100)
	 */
	public function __construct( $args ) {
		/** Extract arguments */
		if( 	!isset( $args['slug'] ) 
		  ||	!isset( $args['page-title'] )
		  ||	!isset( $args['menu-title'] ) )
			trigger_error( "A KB_Admin Class must be initialized with a slug, page title and menu title." );
		
		if( !isset( $args['parent'] ) ) $args['parent'] = 'admin.php';	
		if( !isset( $args['capability'] ) ) $args['parent'] = 'read';	
		if( !isset( $args['icon-url'] ) ) $args['icon-url'] = '';	
		if( !isset( $args['position'] ) ) $args['position'] = 100;

		$this->args = $args;

		/** Add the admin menu page */
		add_action( ( is_multisite() )? 'network_admin_menu' : 'admin_menu', Array( $this, 'add_to_menu' ), 10 );

		/** Attach function for loading resources and customizations, if required. Runs on this hook as soon as $hook_suffix is populated. */
		add_action( 'admin_enqueue_scripts', Array( $this, 'init' ), 11, 1 );

	}

	/** Add menus and determine the hook_suffix. */
	public function add_to_menu() {
		if( $this->args['parent'] == 'admin.php' )
			$this->hook_suffix = add_menu_page( 
				$this->args['page-title'], 
				$this->args['menu-title'], 
				$this->args['capability'], 
				$this->args['slug'], 
				Array( $this, 'body_wrapper' ),
				$this->args['icon-url'],
				$this->args['position']
			);
		else
			$this->hook_suffix = add_submenu_page( 
				$this->args['parent'],
				$this->args['page-title'], 
				$this->args['menu-title'], 
				$this->args['capability'], 
				$this->args['slug'], 
				Array( $this, 'body_wrapper' )
			);
	}

	/** 
	 * Wrapper for conditionally loading resources 
	 * @param string hook_suffix The current hook suffix for checking before loading.
	 */
	public function init( $hook_suffix ) {
		if( $this->hook_suffix == $hook_suffix ) {
			$this->load_resources();

			/** Override screen meta data here. */
			$this->customize_screen();
		
			/** Add contextual help. The actual text should be overridden */
			add_contextual_help( $this->hook_suffix, $this->help() );
		}
	}

	/** Override to load resources as required */
	public function load_resources() {
	}

	/** Override to customize the screen--screen options, etc. */
	public function customize_screen() {
	}

	/** Override to modify the help message. */
	public function help() {
		return "<a href='http://codex.wordpress.org/' target='_blank'>Documentation</a>";
	}

	/** Output minimal boiler-plate HTML to make the page behave. */
	public function body_wrapper() {
		echo <<<ADMIN_PAGE
		<div class = 'wrap'>
ADMIN_PAGE;
			echo $this->body();
echo <<<ADMIN_PAGE
		</div>
ADMIN_PAGE;
	}

	/** Extend this function to actually display the contents of the page. */
	public function body() {
	}

 }
