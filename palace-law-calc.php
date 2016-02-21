<?php
/*
 * Plugin Name: Palace Law Calc
 * Version: 1.0.2
 * Plugin URI: http://www.one-400.com/
 * Description: Calculator to allow users to get an idea of thier workers comp payout
 * Author: One400
 * Author URI: http://www.one-400.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: palace-law-calc
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author One400
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-palace-law-calc.php' );
require_once( 'includes/class-palace-law-calc-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-palace-law-calc-admin-api.php' );
require_once( 'includes/lib/class-palace-law-calc-post-type.php' );
require_once( 'includes/lib/class-palace-law-calc-taxonomy.php' );

/**
 * Returns the main instance of Palace_Law_Calc to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Palace_Law_Calc
 */
function Palace_Law_Calc () {
	$instance = Palace_Law_Calc::instance( __FILE__, '1.0.2' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Palace_Law_Calc_Settings::instance( $instance );
	}

	return $instance;
}

Palace_Law_Calc();
