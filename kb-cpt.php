<?php

/**
 * Custom Post Type abstraction.
 *
 * Works around some of the quirks in WP custom post type handling.
 *
 * @author Kunal Bhalla
 * @version 0.1
 * @package KB_Includes
 * @copyright Copyright (c) 2011, Kunal Bhalla
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

/** 
 * Generic class to handle common CPT functionality.
 */
class KB_Cpt extends KB_At {

	/** Arguments passed on for registering the post type. */
	protected $args;

	/** The post type identifier */
	protected $id;

	/** Icon for edit page */
	protected $icon32;

	/** 16px by 16px icon -- unhighlighted */
	protected $icon16;

	/** 16px by 16px icon -- hover (appears on a white background) */
	protected $icon16a;

	/** 16px by 16px icon -- hover (appears on a black background) */
	protected $icon16x;

	/**
	 * Registers the current post type at the init hook.
	 * @hook init
	 */
	public function register() {
		register_post_type( $this->id, $this->args );
	}

	/**
	 * Over-ride to register taxonomies.
	 * @hook init
	 */
	public function taxonomies() {
	}

	/**
	 * Enqueues resources on the right page.
	 * @hook admin_enqueue_scripts
	 */
	public function edit_resources_wrapper() {
		$screen = get_current_screen();
		if( $this->id == $screen->post_type ) 
			$this->edit_resources( $screen );
	}

	/**
	 * Override to add custom files to be added at the edit screens.
	 * @param $screen The current screen value
	 */
	public function edit_resources( $screen ) {
	}

	/**
	 * Swaps the icons by over-riding the CSS. 
	 *
	 * Done this way to work around the defaurt icon over-ride method by WordPress.
	 * -- That method doesn't allow adding icons for hover, highlighted, etc.
	 *
	 * @hook admin_print_styles
	 * @priority 20
	 */
	public function swap_icon() {
		echo "<style type='text/css'>";

		if( isset( $this->icon16 ) )
			echo "\n#adminmenu li#menu-posts-{$this->id} div.wp-menu-image { background: url('{$this->icon16}') no-repeat center center }";

		if( isset( $this->icon16a ) )
			echo "\n#adminmenu li:hover#menu-posts-{$this->id} div.wp-menu-image { background: url('{$this->icon16a}') no-repeat center center }";
			 
		if( isset( $this->icon16x ) )
			echo "\n #adminmenu li#menu-posts-{$this->id}.wp-has-current-submenu div.wp-menu-image, #adminmenu li:hover#menu-posts-{$this->id}.wp-has-current-submenu div.wp-menu-image { background: url('{$this->icon16x}') no-repeat center center }";

		if( $this->is_edit() && isset( $this->icon32 ) )
			echo "\n#icon-edit.icon32-posts-{$this->id} { background: url('{$this->icon32}') no-repeat center center }";

		echo "</style>";	
	}

	/**
	 * Does the screen have a special icon?
	 *
	 * @return bool Is it an edit/post screen for this post type
	 */
	private function is_edit() {
		$screen = get_current_screen();
		return ( $this->id == $screen->post_type && ( $screen->base == 'post' || $screen->base == 'edit' )  );
	}

	/**
	 * Calls the helper function on the appropriate screens. 
	 *
	 * Didn't use `add_contextual_help` as there isn't really an
	 * appropriate hook to call it on.
	 *
	 * @hook contextual_help_list
	 *
	 * @param Array $help The global help details array (screen->id => help text).
	 * @param StdClass $screen The current screen
	 *
	 * @return Array The modified help details array.
	 */
	public function help_wrapper( $help, $screen ) {
		if( $screen->post_type == $this->id )
			$help[ $screen->id ] = $this->help( $screen );
		
		return $help;	
	}

	/**
	 * Over-ride this function to return help text.
	 *
	 * Called on all pages for the current post type.
	 *
	 * @param StdClass Screen The current screen value
	 * @return String 
	 */	
	public function help( $screen ) {
		return "This project can be found on <a href = 'https://github.com/kunalb/KB-Includes'>Github</a>.";
	}
}
