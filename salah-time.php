<?php

/**
 * Plugin Name: Salah Time Calendar
 * Plugin URI: https://demo.wpapplab.com/salah-time/
 * Description: Mosque Salah Time, Jamat time, calendar. Salah time is location based. Jamat time is editable from backend. Pro Version includes Fasting Time calendar, 3 more salah time layout, Salah time sidebar widget, Next salah time widget and shortcode. 
 * Version: 1.0.2
 * Author: Mircode
 * Author URI: http://www.mircode.com
 * Text Domain: salah-time
 * Domain Path: /languages
 */
define('SALAH_DIR', plugin_dir_path(__FILE__));
define('SALAH_PATH', plugin_dir_path(__FILE__));
define('SALAH_URL', plugins_url('/', __FILE__));

require_once SALAH_DIR . 'classes/class-salah-loader.php';
require_once SALAH_DIR . 'classes/class-salah-time.php';
require_once SALAH_DIR . 'classes/class-salah-shortcode.php';

require_once SALAH_DIR . 'classes/class-salah-activation.php';
$salah_activation = new Salah_Activation_Controller();
$salah_activation->initialize_activation_hooks();

add_filter('plugin_row_meta', 'stf_plugin_row_meta', 10, 2);

function stf_plugin_row_meta($links, $file)
{
    if (plugin_basename(__FILE__) == $file) {
        $row_meta = array(
            'docs'    => '<a href="' . esc_url('https://demo.wpapplab.com/salah-time/salah-time-shortcode/') . '" target="_blank" aria-label="' . esc_attr__('Plugin Additional Links', 'salah-time') . '" style="">' . esc_html__('Docs', 'salah-time') . '</a>',
            'pro'    => '<a href="' . esc_url('https://wpapplab.com/plugins/salah-time-calendar-pro/') . '" target="_blank" aria-label="' . esc_attr__('Salah Time Pro', 'salah-time') . '" style="color:red;"><b>' . esc_html__('Get Pro Version', 'salah-time') . '</b></a>',
        );

        return array_merge($links, $row_meta);
    }
    return (array) $links;
}
