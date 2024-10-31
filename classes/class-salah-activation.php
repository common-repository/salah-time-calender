<?php

class Salah_Activation_Controller {

    public function initialize_activation_hooks() {
        register_activation_hook("salah-time/salah-time.php", array($this, 'execute_activation_hooks'));
		//register_deactivation_hook(__FILE__, array($this, 'execute_deactivation_hooks'));
		//register_uninstall_hook(__FILE__, array($this, 'execute_uninstall_hooks'));
    }

    public function execute_activation_hooks() {
        // Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( is_plugin_active( 'salah-time-pro/salah-time-pro.php' ) ) {
			//plugin is activated
			deactivate_plugins( 'salah-time-pro/salah-time-pro.php' );
		} 
        // Do activate Stuff now.
    }
	
	public function execute_deactivation_hooks() {
		// Will be executed when the client deactivates the plugin
    }
	public function execute_uninstall_hooks() {
		// Will be executed when the client deactivates the plugin
		
    }

}

?>