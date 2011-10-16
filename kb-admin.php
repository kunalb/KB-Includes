<?php

/**
 * Admin Screens
 *
 * @author Kunal Bhalla <bhalla.kunal@gmail.com>
 * @version 0.1
 * @package KB_Includes
 * @copyright Copyright (c) 2011, Kunal Bhalla
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Generic Class that can be extended for creating admin screens for plugins.	
 *
 * Extend and initiate this class for every admin page created.
 */
 class KB_Admin {

	/**
	 * The suffix generated for this plugin.
	 * @var string
	 */
	private $hook;

	/**
	 * Is this page the current page?
	 * @var Bool
	 */
	private $is;

	/**#@+
	 * Over-ride these variables to create a new page class.
	 */

	/**
	 * Store slug used for the page. 
	 * @var Array[string]String
	 */
	protected $slug;

	/** 
	 * Page title
	 * @var String
	 */
	protected $page_title;
	
	/**
	 * Menu title
	 * 
	 * If this is set a menu entry will be created for this page instance. 
	 * Otherwise no menu entry will be created.
	 *
	 * @var String
	 */
	protected $menu_title; 

	/** 
	 * Capability at which this page can be shown.
	 *
	 * Takes care of the capability check at both the menu display as
	 * well as at the page load.
	 *
	 * @var String
	 */
	protected $capability = 'read'; 

	/**
	 * Icon to be added in the menu.
	 * @var String
	 */
	protected $icon;

	/** 
	 * Position in the menu
	 * 
	 * Used only if the menu title is provided.
	 *
	 * @var Int
	 */
	protected $position = 100;

	/** 
	 * Parent Menu 
	 *
	 * If not set and a menu title is provided, a top-level element is
	 * created--otherwise a submenu element is created.
	 *
	 * @var String
	 */
	protected $parent = 'admin.php'; 

	/**#@-*/

	/**
	 * The constructor, conditionally initializes the requirements for the plugin.
	 *
	 * Instead of having to check whether the correct page has been loaded externally,
	 * this function only initializes required scripts and styles on the appropriate
	 * plugin pages.
	 */
	public function __construct() {
		/** Add the admin menu page */
		if( isset( $this->menu_title ) )
			add_action( ( is_network_admin() )? 'network_admin_menu' : 'admin_menu', Array( $this, 'add_to_menu' ), 10 );
		else 
			add_action( ( is_network_admin() )? 'network_admin_menu' : 'admin_menu', Array( $this, 'hook_page' ), 9 );

		/** Attach function for loading resources and customizations, if required. Runs on this hook as soon as $hook is populated. */
		add_action( 'admin_enqueue_scripts', Array( $this, 'init' ), 11, 1 );
	}

	/** Add menus and determine the hook. */
	public function add_to_menu() {
		if( $this->parent == 'admin.php' )
			$this->hook = add_menu_page( 
				$this->page_title, 
				$this->menu_title, 
				$this->capability, 
				$this->slug, 
				Array( $this, 'body_wrapper' ),
				$this->icon_url,
				$this->position
			);
		else
			$this->hook = add_submenu_page( 
				$this->parent,
				$this->page_title, 
				$this->menu_title, 
				$this->capability, 
				$this->slug, 
				Array( $this, 'body_wrapper' )
			);
			
		add_action( 'load-' . $this->hook, Array( $this, 'set_current_page' ) );
	}

	/** 
	 * Wrapper for conditionally loading resources 
	 * @param string hook The current hook suffix for checking before loading.
	 */
	public function init() {
		if( $this->current_page() ) {
			$this->load_resources();

			/** Override screen meta data here. */
			$this->customize_screen();
		
			/** Add contextual help. The actual text should be overridden */
			add_contextual_help( $this->hook, $this->help() );
		}
	}

	/** 
	 * Add a page to be loaded for the specified slug. Do not over-ride.
	 *
	 * Only used for instances without a menu page. This code is based off the internals
	 * of add_menu_page.
	 */
	public function hook_page() {
		global $admin_page_hooks, $_registered_pages, $_parent_pages;

	
		$admin_page_hooks[$this->slug] = sanitize_title( $this->page_title ); 
		$this->hook = get_plugin_page_hookname( $this->slug, "" );
		$_registered_pages[$this->hook] = true;
		$_parent_pages[$this->slug] = false;

		add_action( $this->hook, Array( $this, 'body_wrapper' ) );
		add_action( 'load-' . $this->hook, Array( $this, 'set_current_page' ) );

		add_filter( 'admin_title', Array( $this, hook_page_title ) , 10, 2 );
	}

	/**
	 * Set the title of a page defined without any entry in the menu.
	 */
	public function hook_page_title( $admin_title, $title ) {
		if( $this->current_page() )
			return $this->page_title . $admin_title;
		else
			return $admin_title;	
	}

	/**
	 * Runs only if the page corresponding to this class is called. 
	 * Also runs a security check on the caps of the user.
	 */
	public function set_current_page() {
		if( !current_user_can( $this->capability ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'kb-includes' ) );

		$this->is = true;
	}

	/**
	 * Returns true if the current page is the one added by this class.
	 * @return bool
	 */
	public function current_page() {
		return $this->is;
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

	/**
	 * Extend this function to actually display the contents of the page. Should 
	 * echo the data to be displayed. 
	 */
	public function body() {
	}

	/**
	 * Override to save metabox data.
	 */
	public function save() {
	}

 }
