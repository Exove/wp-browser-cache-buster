<?php
/**
 * Plugin Name: Browser Cache Buster
 * Version: 0.3.1
 * Description: Filename-based cache busting for WordPress scripts/styles. Based on a <a href="https://gist.github.com/ocean90/1966227#gistcomment-2030017">modified version of the original plugin</a>.
 * Author: Dominik Schilling, <a href="https://github.com/KimchaC">KimchaC</a>, and <a href="https://www.exove.com">Exove</a>
 * Author URI: http://wphelper.de/
 * Plugin URI: https://dominikschilling.de/880/
 *
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 *
 * Extend your .htaccess file with these lines (Untested):
 *
 *   <IfModule mod_rewrite.c>
 *     RewriteEngine On
 *     RewriteBase /
 *
 *     RewriteCond %{REQUEST_FILENAME} !-f
 *     RewriteCond %{REQUEST_FILENAME} !-d
 *     RewriteRule ^(.+)\.(?:\d+)\.(min.js|min.css|js|css)($|\?.*$) $1.$3 [L]
 *   </IfModule>
 *
 *
 * Extend your nginx config with these lines (tested & working):
 *
 * location ~* ^(.+)\.(?:\d+)\.(min.js|min.css|js|css)($|\?.*$) {
 *   try_files $uri $1.$2;
 * }
 */

/**
 * Removes query strings of the source.
 * Adds the file modified time to the filename.
 * Doesn't change admin scripts/styles and sources.
 *
 * @param  string $src The original source.
 * @return string
 */
function ds_filename_based_cache_busting( $src ) {
	// Don't touch admin scripts.
	if ( is_admin() ) {
		return $src;
	}

	$_src = $src;
	if ( '//' === substr( $_src, 0, 2 ) ) {
		$_src = 'http:' . $_src;
	}

	$_src = parse_url( $_src );

	// Give up if malformed URL.
	if ( false === $_src ) {
		return $src;
	}

	// Check if it's a local URL.
	$wp = parse_url( home_url() );
	if ( isset( $_src['host'] ) && $_src['host'] !== $wp['host'] ) {
		return $src;
	}

	// Don't touch wp-includes scripts (since this plugin doesn't properly work with them)
	if (strpos($_src['path'], 'wp-includes') !== false) {
		return $src;
	}

	$file_path = ABSPATH . $_src['path'];
	$file_path = str_replace('/wp/', '/', $file_path);
	if (file_exists($file_path)) {
		return preg_replace(
			'/\.(min.js|min.css|js|css)($|\?.*$)/',
			'.' . filemtime($file_path) . '.$1',
			$src
		);
	} else {
		return $src;
	}
}
add_filter( 'script_loader_src', 'ds_filename_based_cache_busting' );
add_filter( 'style_loader_src', 'ds_filename_based_cache_busting' );
