<?php

/**
 * Custom Post Types
 *
 * @author Kunal Bhalla
 * @version 0.1
 * @package KB_Includes
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

	/**
	 * @hook init
	 */
	public function register() {
		register_post_type( $this->id, $this->args );
	}

	/**
	 * @hook init
	 */
	public function taxonomies() {
	}

	/**
	 * @hook admin_enqueue_scripts
	 */
	public function edit_resources_wrapper( $hook ) {
		if( $this->is_edit() ) 
			$this->edit_resources();
	}

	public function edit_resources() {
	}

	/**
	 * @hook admin_print_styles
	 * @priority 20
	 */
	public function swap_icon() {
		echo <<<CSS
		<style type='text/css'>
			#adminmenu li#menu-posts-{$this->id} div.wp-menu-image { background: url('{$this->icon16}') no-repeat center center }

			#adminmenu li:hover#menu-posts-{$this->id} div.wp-menu-image
			 { background: url('{$this->icon16a}') no-repeat center center }

			#adminmenu li#menu-posts-{$this->id}.wp-has-current-submenu div.wp-menu-image, #adminmenu li:hover#menu-posts-{$this->id}.wp-has-current-submenu div.wp-menu-image
			 { background: url('{$this->icon16x}') no-repeat center center }

CSS;
		if( $this->is_edit() && isset( $this->icon32 ) )
			echo "\n#icon-edit.icon32-posts-{$this->id} { background: url('{$this->icon32}') no-repeat center center }";
		echo "</style>";	
	}

	private function is_edit() {
		$screen = get_current_screen();
		return ( $this->id == $screen->post_type && ( $screen->base == 'post' || $screen->base == 'edit' )  );
	}
}
