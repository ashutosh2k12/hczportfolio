<?php

/**
 * Plugin Name:     HCZ Portfolio
 * Plugin URI:      http://github.com/ashutosh2k12/hczportfolio
 * Description:     This plugin is complementary to HCZ Material theme. Adds portfolio to your wordpress theme.
 * Version:         1.0.0
 * Author:          Ashutosh Chaudhary
 * Author URI:      https://aboutashu.com
 * Text Domain:     hcz-portfolio
 * Domain Path:     localization
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//Defines
define("HCZ_PF_DIR", dirname( __FILE__ ));
define("HCZ_PF_PLUGIN_URL", plugin_dir_url(__FILE__));

/*----------------------------------------------------------------------------*
 * Config Class
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-hczpf.php' );

/*----------------------------------------------------------------------------*
 * Front-end Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-hczpf-front.php' );

/*----------------------------------------------------------------------------*
 * Administrative Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-hczpf-admin.php' );

/*----------------------------------------------------------------------------*
 * API Functionality (for theme)
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-hczpf-api.php' );

$hczpf = new HCZPF_Admin();

register_activation_hook( __FILE__, array( &$hczpf , 'activate' ) );
register_deactivation_hook( __FILE__, array( &$hczpf , 'deactivate' ) );
