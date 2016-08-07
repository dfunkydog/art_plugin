<?php

/**
 * Fired during plugin activation
 *
 * @link       http://nlsltd.com
 * @since      1.0.0
 *
 * @package    Arzte_reg
 * @subpackage Arzte_reg/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Arzte_reg
 * @subpackage Arzte_reg/includes
 * @author     Michael Dyer <michael@nlsltd.com>
 */
class Arzte_reg_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix."arztinfo_reg";

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			arzte_id INTEGER NOT NULL AUTO_INCREMENT,
			full_name VARCHAR(128) NOT NULL,
			clinic VARCHAR(100) NOT NULL,
			address VARCHAR(45) NOT NULL,
			city VARCHAR(45) NOT NULL,
			telephone VARCHAR(15) NULL,
			user_url VARCHAR(100) NULL,
			email VARCHAR(100) NOT NULL,
			UNIQUE KEY id (arzte_id)
			) $charset_collate;";

		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		$wpdb->query($sql);

		add_option("artze_reg", "0.3");



	}

}
