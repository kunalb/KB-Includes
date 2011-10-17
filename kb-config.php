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
	protected $capability = 'manage_options';
	protected $title      = 'settings';
	protected $assets;

	public function __construct() {
		parent::__construct();
		$this->resources = plugins_url( $this->plugin ) . '/includes/resources' ;
	}

	public function body() {
		echo "<div id='icon-options-general' class='icon32'><br></div>";
		echo "<h2>" . $this->title . "</h2>";
		echo "<div id = 'kb-config-editor' style = 'position: relative; margin-top: 20px; overflow: none;'>";
		echo <<<CONTENT
// The best possible options have been chosen for you by default;
// but you can customize the plugin as required by editing this file.
// Don't forget to check out the help text on the top-right for any clarifications.

{
	'config': {
	}
}
CONTENT;
		echo "</div>";

		echo <<<SCRIPT
			<script type = 'text/javascript'>
				jQuery(function(){ 
					jQuery('#kb-config-editor').height( jQuery('#wpbody').height() - 50 );
					var JSONMode = require("ace/mode/json").Mode;
					var editor = ace.edit('kb-config-editor'); 
					editor.setTheme("ace/theme/solarized_dark");
					editor.getSession().setMode(new JSONMode());
					editor.setShowPrintMargin(false);
				});
			</script>
SCRIPT;
	}

	public function load_resources() {
		wp_enqueue_script( 'ace', $this->resources . '/js/ace.js', 'jquery' );		
		wp_enqueue_script( 'ace-json', $this->resources . '/js/mode-json.js', 'ace' );		
		wp_enqueue_script( 'ace-solarized-dark', $this->resources . '/js/theme-solarized_dark.js', 'ace' );		
	}
}
	 
}
