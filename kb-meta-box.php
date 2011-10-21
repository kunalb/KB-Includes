<?php

/**
 * Metaboxes for WordPress
 *
 * @author Kunal Bhalla
 * @version 0.1
 * @package KB_Includes
 * @copyright Copyright (c) 2011, Kunal Bhalla
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

if( !class_exists( 'KB_Meta_Box' ) ) {

/** 
 * The metabox class.
 */
class KB_Meta_Box extends KB_At {
	/**
	 * Override to specify which post type this metabox should be appended to.
	 * @var String
	 */
	protected $post_type;

	/**
	 * Metabox ID
	 * @var String
	 */
	protected $id = 'kb_metabox';
	
	/**
	 * Metabox Title
	 * @var String
	 */
	protected $title = 'untitled'; 

	/**
	 * Context
	 * @var String
	 */
	protected $context = 'advanced';  

	/**
	 * Priority
	 * @var String
	 */
	protected $priority = 'default';

	/** 
	 * Constructor hooks registration to the right page.
	 * 
	 * Not carried out using the standard KB_At function as 
	 * the label has to be dynamically constructed.
	 */
	public function __construct() {
		add_action( 'add_metaboxes_' . $post_type, Array( $this, 'register' ) );
	}

	/**
	 * Registers the metabox for display.
	 */
	public function register() {
		add_meta_box( $this->id, $this->title, Array( $this, 'body_wrapper' ), $this->post_type, $this->context, $this->priority );
	}

	/**
	 * Handles metabox cruft: nonce generation et al.
	 */
	public function body_wrapper( $post ) {
		$this->body( $post );
	}

	/**
	 * Override and echo the contents of the metabox.
	 */
	protected function body( $post ) {
	}
}

}
