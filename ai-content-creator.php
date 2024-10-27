<?php
/**
 * Plugin Name: AI Content Creator - Easy ChatGPT powered article generator
 * Plugin URI:  https://taller.abcdatos.net/ai-content-creator-wordpress/
 * Description: Creates new posts content using AI API
 * Version:     1.2.5-dev
 * Author:      ABCdatos
 * Author URI:  https://taller.abcdatos.net/
 * License:     GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-content-creator
 * Domain Path: /languages
 *
 * @package ai-content-creator
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

/** Requerido o se obtiene error Plugin is not compatible with language packs: Missing load_plugin_textdomain(). en el canal de Slack #meta-language-packs. */
function aicc_load_plugin_textdomain() {
	load_plugin_textdomain( 'ai-content-creator', false, basename( __DIR__ ) . '/languages' );
}
add_action( 'plugins_loaded', 'aicc_load_plugin_textdomain' );

/** Modo de depuración del plugin. Muestra mucha información adicional en diferentes puntos. */
function aicc_modo_debug() {
	return false;
}

// La autentificación 2FA ha de estar disponible a cualquier usuario externo.
// include_once plugin_dir_path( __FILE__ ) . 'auth.php';
// Todas las prestaciones y configuración requieren estar en el panel de administración.
if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/configuracion.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/opciones.php';
	require_once plugin_dir_path( __FILE__ ) . 'bbdd.php';
	require_once plugin_dir_path( __FILE__ ) . 'classes/class-aiccgeneracion.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/posproduccion.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/pixabay.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/funciones.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/idiomas.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/ayuda.php';
	add_action( 'wp_enqueue_scripts', 'aicc_load_wp_icons' );
	add_action( 'admin_enqueue_scripts', 'aicc_enqueue_admin_styles' );
	aicc_actualizar_db_si_procede();
}

/** Enlace a la configuración desde la página de administración de plugins.
 * Basado en https://www.smashingmagazine.com/2011/03/ten-things-every-wordpress-plugin-developer-should-know/
 *
 * @param array  $links An array of plugin action links.
 * @param string $file Path to the plugin file relative to the plugins directory.
 * @return HTML con el enlace HTML a los ajustes.
 */
function aicc_plugin_action_links( $links, $file ) {
	static $this_plugin;
	if ( ! $this_plugin ) {
		$this_plugin = plugin_basename( __FILE__ );
	}
	if ( $file === $this_plugin ) {
		// El valor del parámetro page es el slug de la página de opciones.
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=aicc_settings' ) ) . '" title="' . ucfirst( __( 'plugin settings', 'ai-content-creator' ) ) . '">' . __( 'Settings', 'ai-content-creator' ) . '</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_filter( 'plugin_action_links', 'aicc_plugin_action_links', 10, 2 );


/** Función de activación del plugin como hook. */
function aicc_activacion() {
	// Define la función de activación del plugin.
	require_once plugin_dir_path( __FILE__ ) . 'lista-opciones.php';
	require_once plugin_dir_path( __FILE__ ) . 'bbdd.php';
	aicc_crear_tabla_generaciones();
	aicc_crear_tabla_pixabay_cache();
}
register_activation_hook( __FILE__, 'aicc_activacion' );

/** Comprueba si se requiere actualizar la BBDD y lo hace. */
function aicc_actualizar_db_si_procede() {
	// Hay que evitar que se haga durante la activación.
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		if ( aicc_requiere_actualizar_tabla_generaciones() ) {
			aicc_actualizar_tabla_generaciones();
		}
		if ( aicc_requiere_actualizar_tabla_pixabay_cache() ) {
			aicc_actualizar_tabla_pixabay_cache();
		}
	}
}

/** Incorpora el estilo para los dashicons. */
function aicc_load_wp_icons() {
	wp_enqueue_style( 'dashicons' );
}

/** Incorpora el estilo para mostrar las miniaturas de Pixabay.
 *
 * @param string $hook_suffix Slug de la página donde aplicarlo.
 */
function aicc_enqueue_admin_styles( $hook_suffix ) {
	if ( 'aicc_articles' === $hook_suffix ) {
		wp_enqueue_style(
			'aicc-pixabay',
			plugin_dir_url( __FILE__ ) . 'admin/css/pixabay.css',
			array(),
			'1.0.0'
		);
	}
}

/** Devuelve la versión del plugin. */
function aicc_get_version() {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugin_data    = get_plugin_data( __FILE__ );
	$plugin_version = $plugin_data['Version'];
	return $plugin_version;
}
