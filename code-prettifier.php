<?php
/**
 * Plugin Name: Code Prettifier for WordPress
 * Description: Highlight code snippets on your WordPress blog.
 * GitHub Plugin URI: https://github.com/janboddez/code-prettifier
 * Author: Jan Boddez
 * Author URI: https://janboddez.be/
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Version: 0.2
 *
 * @author Jan Boddez [jan@janboddez.be]
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0
 */

/* Prevents this script from being loaded directly. */
defined( 'ABSPATH' ) or exit;

/**
 * Main plugin class.
 */
class Code_Prettifier {
	/**
	 * Registers the necessary hooks, or an admin notice in case environment requirements aren't met.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		if ( 1 === version_compare( PHP_VERSION, '5.4.0' ) && defined( 'LIBXML_DOTTED_VERSION' ) && 1 === version_compare( LIBXML_DOTTED_VERSION, '2.6.0' ) ) {
			add_filter( 'the_content', array( $this, 'filter_content' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}
	}

	/**
	 * Filters preformatted blocks in order to not have to manually edit existing
	 * tags.
	 *
	 * @since 0.1
	 *
	 * @param string $content The post (or page) content to be filtered.
	 * @return string The filtered content.
	 */
	public function filter_content( $content ) {
		$prev = libxml_use_internal_errors( true );

		$dom = new DOMDocument( '1.0', get_bloginfo( 'charset' ) );
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', get_bloginfo( 'charset' ) ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

		foreach ( $dom->getElementsByTagName( 'pre' ) as $node ) {
			$node->setAttribute( 'class', 'prettyprint' );
		}

		$content = $dom->saveHTML();
		libxml_use_internal_errors( $prev );

		return $content;
	}

	/**
	 * On single pages and posts, loads Google's JS Code Prettifier script.
	 *
	 * @since 0.1
	 */
	public function load_scripts() {
		/* Load only for single posts and pages (and stop it from affecting tag archives). */
		if ( is_singular() ) {
			wp_enqueue_script( 'code-prettifier', 'https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js', array() );
		}
	}

	/**
	 * Echoes an admin notice in the rare case preconditions are not met.
	 *
	 * @since 0.1
	 */
	public function admin_notice() {
		?>
		<div class="notice notice-warning">
			<p><?php _e( "<strong>Code Prettifier for WordPress</strong> requires PHP 5.4.0 or higher and libXML 2.6.0 or higher. Your system doesn't seem to meet these conditions. You may want to ask your hosting provider for more information. Meanwhile, the plugin will simply not affect your WordPress site in any way.", 'code-prettifier' ); ?></p>
		</div>
		<?php
	}
}

new Code_Prettifier();
