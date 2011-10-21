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
		parent::__construct();
		add_action( 'add_meta_boxes_' . $this->post_type, Array( $this, 'register' ) );
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
		wp_nonce_field( $this->nonce, 'kb-metabox-nonce-' . $this->id );
		$this->body( $post );
	}

	/**
	 * Override and echo the contents of the metabox.
	 */
	protected function body( $post ) {
	}

	/**
	 * Save the collected metadata for the right post type.
	 * @hook save_post
	 */
	public function save_wrapper($postid, $post) {
		if( get_post_type( $post ) == $this->post_type &&
		    isset( $_REQUEST['kb-metabox-nonce-' . $this->id] ) &&
		    wp_verify_nonce( $_REQUEST['kb-metabox-nonce-' . $this->id], $this->nonce() ) )
			$this->save( $postid, $post );
	}

	/**
	 * Override to save the post data; security and page checks have been carried out.
	 */
	protected function save( $postid, $post ) {
	}

	/**
	 * Generate the nonce action.
	 * @return String nonce action
	 */
	protected function nonce() {
		return 'kb-meta-box-' . $this->post_type . '-' . $this->id;
	}
}

}
