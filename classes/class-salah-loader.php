<?php
	
/**
 * A class that handles loading custom modules and custom
 * fields if the builder is installed and activated.
 */
class Salah_Time_Loader {
	
	/**
	 * Initializes the class once all plugins have loaded.
	 */
	static public function init() {
		add_action( 'plugins_loaded', __CLASS__ . '::setup_hooks' );
	}
	
	/**
	 * Setup hooks if the builder is installed and activated.
	 */
	static public function setup_hooks() {
		// register script
		add_action('init',  __CLASS__ . '::load_salah_scripts');		
		add_filter('wp_enqueue_scripts', __CLASS__ . '::insert_jquery',1);
		
	}
	public function insert_jquery(){
		wp_enqueue_script('jquery', false, array(), false, false);
	}
	
	static public function load_salah_scripts(){
		wp_register_script(
			'PrayTimes',
			SALAH_URL . 'assets/js/PrayTimes.js',
			array('jquery'),false,true
		); // This outputs at header
		wp_register_script(
			'hijri-date',
			SALAH_URL . 'assets/js/hijri-date.js',
			array('jquery'),false,true
		); // This outputs at header

		wp_enqueue_style( 'salah-mosque-css', SALAH_URL . 'assets/css/praytime.css', array(), '' );
		
		$jsonMonth = file_get_contents(Salah_Time::get_salah_json_path());
		$jamat_data = json_decode($jsonMonth, true);
				
		$config_array = array(
			'siteURL' => site_url(),
			'siteName' => get_bloginfo('name'),
			'salahTime' => $jamat_data,
		);
	
		wp_localize_script('PrayTimes', 'salah_conf', $config_array);
	}	
}

Salah_Time_Loader::init();