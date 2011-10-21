<?php

/**
 * JSON based configuration saver/editor.
 *
 * @author Kunal Bhalla
 * @version 0.2
 * @package KB_Includes
 * @copyright Copyright (c) 2011, Kunal Bhalla
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

if( !class_exists( 'KB_Config' ) ) {

/**
 * Creates a custom configuration page for the provided 
 * options -- using json files to maintain configuration
 * details, avoiding complex and unneccessary UI.
 */
class KB_Config extends KB_Admin {
	/**
	 * Access to the configuration is only for people who can 'manage_options'.
	 * @var String
	 */
	protected $capability = 'manage_options';

	/**
	 * Ensure that the config menu comes under settings.
	 * @var String
	 */
	protected $parent   = "options-general.php";

	/**
	 * The url for resources: based on the plugin passed in.
	 * @var String
	 */
	protected $resources;

	/**
	 * Must be over-ridden: the base name of the plugin this config is being initialized for.
	 * @var String
	 */
	protected $plugin;
	
	/**
	 * Check to see that the plugin name is specified and set resources link.
	 */
	public function __construct() {
		parent::__construct();
		if( !isset( $this->plugin ) )
			die( "Plugin information required to create a configuration." );

		$this->resources = plugins_url( $this->plugin ) . '/includes/resources' ;
	}

	/**
	 * Processing the Plugin's content to make a full JSON document.
	 * @var String $json 
	 * @return String 
	 */
	protected function make_JSON( $json ){
		return '{' . $json . '}';
	}

	/**
	 * Displays and initializes the editor, as well as the asscciated scripts and styles.
	 */
	public function body() {
		$content = $this->make_JSON( KB_Setting::getData( $this->plugin ) );
		$posturl = get_admin_url() . 'admin-ajax.php';

		echo "<div id='icon-options-general' class='icon32'><br></div>";
		echo "<h2>" . $this->title . "</h2>";
		echo "<p>The best possible options have been chosen for you by default; but you can customize the plugin as required by editing this file.  Don't forget to check out the help text on the top-right for any clarifications.</p>";
		echo "<div id = 'kb-config-editor-wrapper'><div id='kb-config-status'><span>Your configuration has no unsaved changes</span> <input type='button' id = 'kb-config-save' class='button-primary' value='Save Now' disabled='disabled'/></div><div id = 'kb-config-editor'>";
		echo "</div></div>";

		echo <<<SCRIPT
			<script type = 'text/javascript'>
				(function($){
					kb = (typeof(window.kb) == 'undefined')? {} : kb;
					kb.config = { 'editorText': $.parseJSON('$content') };

					$(function(){ 
						$('#kb-config-editor').height( $('#wpbody').height() - 50 );

						kb = (typeof(window.kb) == 'undefined')? {} : kb;

						var JSONMode = require("ace/mode/json").Mode;
						kb.config.editor = ace.edit('kb-config-editor'); 
						kb.config.editor.getSession().setValue(JSON.stringify(kb.config.editorText, undefined, 4));
						kb.config.editor.setTheme("ace/theme/solarized_dark");
						kb.config.editor.getSession().setMode(new JSONMode());
						kb.config.editor.setShowPrintMargin(false);

						kb.config.editor.getSession().on('change', function(){
							$('#kb-config-save').removeAttr('disabled');
							$('#kb-config-status span').html('Your configuration has unsaved changes');
						});

						$('#kb-config-save').click(function(){
							var content = kb.config.editor.getSession().getValue();
							var parsed;
							try {
								parsed = $.parseJSON(content);
								$.post( "$posturl", {
									"action": "kb_config_save",
									"kb-plugin": "{$this->plugin}",
									"kb-data"  : encodeURIComponent(content)
								}, function(data){
									var result = $.parseJSON(data);
									if (result.success) {
										$('#kb-config-status span').html('Your configuration has no unsaved changes.');
										$('#kb-config-save').attr('disabled', 'disabled');
									} else $('#kb-config-status span').html('Your configuration could not be saved. Please try again.');
								});

							} catch(e) {
								$('#kb-config-status span').html('The configuration file has malformed JSON. Changes could not be saved.');
							}
						});
					});
				}(jQuery));
			</script>
			<style type='text/css'>
				#kb-config-editor {
					position: relative; 
					overflow: none;
				}
				#kb-config-editor-wrapper {
					margin-top: 20px; 
					border-radius: 5px;
					background-color: #e8e8e8;
					padding: 10px 5px;
					border: solid #ddd 1px;
				}
				#kb-config-status {
					text-align: right;
					padding-right: 20px;
					padding-bottom: 10px;
					text-shadow: 0px 1px 0 #fff;
				}
			</style>
SCRIPT;
	}

	/**
	 * Loads the scripts for the editor.
	 */
	public function load_resources() {
		wp_enqueue_script( 'ace', $this->resources . '/js/ace.js', 'jquery' );		
		wp_enqueue_script( 'ace-json', $this->resources . '/js/mode-json.js', 'ace' );		
		wp_enqueue_script( 'ace-solarized-dark', $this->resources . '/js/theme-solarized_dark.js', 'ace' );		
	}

	/**
	 * Saves the settings on the AJAX call.
	 * @hook wp_ajax_kb_config_save
	 */
	public function save_settings() {
		$data = json_decode( urldecode( $_POST[ 'kb-data' ] ), true );
		if( update_option( 'kb-config-' . $_POST[ 'kb-plugin' ], $data[ $_POST[ 'kb-plugin' ] ] ) )
			die( '{"success": true}');
		else
			die( '{"success": false}');
	}
}
	 
}
