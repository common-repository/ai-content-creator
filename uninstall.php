<?php
/** Desinstalación, borrando las opciones empleadas.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

// Si no se llama a uninstall.php por WordPress, muere.
defined( 'WP_UNINSTALL_PLUGIN' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

require_once plugin_dir_path( __FILE__ ) . 'lista-opciones.php';
require_once plugin_dir_path( __FILE__ ) . 'bbdd.php';

// Elimina las opciones de configuración.
foreach ( aicc_lista_opciones() as $nombre_opcion ) {
	delete_option( $nombre_opcion );
	// Para los casos de multisite.
	delete_site_option( $nombre_opcion );
}

// Elimina las tablas creadas en la BBDD.
global $wpdb;

// Elimina la tabla con las generaciones.
$tabla = aicc_tabla_generaciones();
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %s', $tabla ) );

// Elimina la tabla con la caché de Pixabay.
$tabla = aicc_tabla_pixabay_cache();
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %s', $tabla ) );
