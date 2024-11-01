<?php
/*
Plugin Name: Uninstall Mobile Domain
Version: 1.0
Plugin URI: http://www.yusuf.asia/my-plugin/uninstall-mobile-domain/
Description: Uninstall <a href="http://wordpress.org/extend/plugins/mobile-domain/">Mobile Domain</a>, go to <a href="options-general.php?page=uninstall-mobile-domain">option page</a> to uninstall your mobile domain.
Author: Yusuf
Author URI: http://www.yusuf.asia/
*/

function uninstall_mobile_domain() {
	global $wpdb;
	$umd_wpdb = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'");
	$umd_siteurl = $umd_wpdb->option_value;
	define ('SITEURL', $umd_siteurl);
	$umd_domain = str_replace('http://', '', $umd_siteurl);
	define ('UMD_DOMAIN', $umd_domain);
	$umd_check = strpos($umd_domain, '/');
	if (!empty($umd_check)) {
		define ('UMD_DESKTOP', substr($umd_siteurl, 7, $umd_check));
	} else {
		define ('UMD_DESKTOP', $umd_domain);
	}
	$get = get_option('umd_db_options');
	$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	if (!empty ($get)) {
		if ($_SERVER['SERVER_NAME'] == $get) {
			$go = str_replace($get, UMD_DESKTOP, $url);
			header ('location:http://'. $go);
			die();
		}
	}
}

add_action('init', 'uninstall_mobile_domain');

function uninstall_mobile_domain_admin() {
	if (!empty($_GET['umd_action'])) {
		if ($_GET['umd_action'] == 'add-domain') {
			if (!empty($_POST['domain'])) {
				$subdomain = strtolower($_POST['domain']);
				update_option('umd_db_options', $subdomain);
				echo '<div class="updated"><p><strong>Done!</strong></p></div>';
			}
		}
	}
	$get = get_option('umd_db_options');
	echo '<div class="wrap" id="umd_div"><h2>Uninstall Mobile Domain</h2>
	<p style="color:#FF0000"><strong>Do not install this plugin if you have not installed yet "<a href="http://wordpress.org/extend/plugins/mobile-domain/">Mobile Domain</a>" plugin!</strong></p>
	<p><strong>This plugin is used to redirect mobile-domain/subdomain to the desktop-domain that have been indexed by search engine.</strong></p>
	<form method="post" action="options-general.php?page=uninstall-mobile-domain&umd_action=add-domain">
	<table>
	<tr valign="top"><br />
	<td><label for="domain">Mobile domain (i.e <strong>m.domain.com</strong>)</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	</td>
	<td>http://<input type="text" name="domain" id="domain" value="'.$get.'" class="regular-text">
	</td></tr>
	</table><br />
	<input type="submit" name="submit" class="button" value="Save Changes" />
	</form>';
	if (!empty ($get)) {
		echo '<br /><br /><p>Thank you for using <a href="http://www.yusuf.asia/wordpress/mobile-domain/">Mobile Domain</a>.</p>';
	}
	echo '</div>';
}

function uninstall_mobile_domain_menu() {
	add_options_page('Uninstall Mobile Domain Page','Uninstall Mobile Domain','manage_options','uninstall-mobile-domain','uninstall_mobile_domain_admin');
}

add_action('admin_menu', 'uninstall_mobile_domain_menu');

function uninstall_mobile_domain_deactivate() {
	delete_option('umd_db_options');
}

register_deactivation_hook(__FILE__, 'uninstall_mobile_domain_deactivate'); 

?>