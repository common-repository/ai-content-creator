<?php
/**
 * Gestión de las tablas añadidas a la base de datos
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

/** Nombre completo -con prefijo- de la tabla de generación de artículos. */
function aicc_tabla_generaciones() {
	global $wpdb;
	return $wpdb->prefix . 'aicc_generado';
}

/** Nombre completo -con prefijo- de la tabla de caché de consultas a Pixabay. */
function aicc_tabla_pixabay_cache() {
	global $wpdb;
	return $wpdb->prefix . 'aicc_pixabay_cache';
}

/** Versión requerida de la tabla de generación de artículos que el plugin actual maneja. */
function aicc_version_requerida_tabla_generaciones() {
	return '0.06';
}

/** Versión requerida de la tabla de caché de consultas a Pixabay que el plugin actual maneja. */
function aicc_version_requerida_tabla_pixabay_cache() {
	return '0.01';
}

/** Determina si requiere actualizar la tabla de generación de artículos que el plugin actual maneja. */
function aicc_requiere_actualizar_tabla_generaciones() {
	$requerido            = false;
	$version_db_actual    = get_option( 'aicc_dbversion' );
	$version_db_requerida = aicc_version_requerida_tabla_generaciones();
	if ( version_compare( $version_db_actual, $version_db_requerida ) < 0 ) {
		$requerido = true;
		/* translators: 1: Old version, 2: New version */
		aicc_mostrar_notificacion( esc_html( sprintf( __( 'The database will be updated from %1$s to %2$s.' ), $version_db_actual, $version_db_requerida ) ), 'notice' );
	}
	return $requerido;
}

/** Determina si requiere actualizar la tabla de consultas a Pixabay. */
function aicc_requiere_actualizar_tabla_pixabay_cache() {
	$requerido            = false;
	$version_db_actual    = get_option( 'aicc_pxbcversion' );
	$version_db_requerida = aicc_version_requerida_tabla_pixabay_cache();
	if ( version_compare( $version_db_actual, $version_db_requerida ) < 0 ) {
		$requerido = true;
		/* translators: 1: Old version, 2: New version */
		aicc_mostrar_notificacion( esc_html( sprintf( __( 'The cache table will be updated from %1$s to %2$s.' ), $version_db_actual, $version_db_requerida ) ), 'notice' );
	}
	return $requerido;
}

/** Al usar dbdelta, es lo mismo crearla que actualizarla. **/
function aicc_actualizar_tabla_generaciones() {
	return aicc_crear_tabla_generaciones();
}
/** Al usar dbdelta, es lo mismo crearla que actualizarla. */
function aicc_actualizar_tabla_pixabay_cache() {
	return aicc_crear_tabla_pixabay_cache();
}

/** Crea o modifica la tabla de creaciones de artículos. */
function aicc_crear_tabla_generaciones() {
	global $wpdb;
	require_once plugin_dir_path( __FILE__ ) . 'lista-opciones.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/configuracion.php';

	$plugin_version = aicc_get_version();
	$db_version     = aicc_version_requerida_tabla_generaciones();

	$charset_collate = $wpdb->get_charset_collate();
	$tabla           = aicc_tabla_generaciones();

	// Para usar dbDelta().
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	/** ****************************************************************************************** */
	/** Actualizar la función aicc_version_requerida_tabla_generaciones si se cambia la estructura */
	/** ****************************************************************************************** */
	// No poner lineas en blanco o dará error.
	// dbDelta no elimina ni ordena columnas.
	$sql = "CREATE TABLE $tabla (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		fecha datetime DEFAULT '0000-00-00 00:00:00',
		plugin_version varchar(16) NOT NULL DEFAULT '" . $plugin_version . "',
		generador varchar(25) NOT NULL DEFAULT '',
		modelo varchar(25) NOT NULL DEFAULT '',
		idioma varchar(25) NOT NULL DEFAULT '',
		longitud_solicitada smallint(4) NOT NULL DEFAULT 0,
		tono varchar(25) NOT NULL DEFAULT '',
		mensaje_sistema text NOT NULL DEFAULT '',
		contexto text NOT NULL DEFAULT '',
		titulo varchar(255) NOT NULL DEFAULT '',
		directrices_adicionales text NOT NULL DEFAULT '',
		prompt text NOT NULL DEFAULT '',
		solicitud text NOT NULL DEFAULT '',
		respuesta text NOT NULL DEFAULT '',
		error tinyint(1) NOT NULL DEFAULT 0,
		contenido text NOT NULL DEFAULT '',
		meta_description varchar(255) NOT NULL DEFAULT '',
		meta_keywords varchar(255) NOT NULL DEFAULT '',
		imagen_id varchar(255) NOT NULL DEFAULT '',
		valida tinyint(1) NOT NULL DEFAULT 0,
		validacion_errores text NOT NULL DEFAULT '',
		validacion_advertencias text NOT NULL DEFAULT '',
		validacion_notificaciones text NOT NULL DEFAULT '',
		validada_por varchar(255) NOT NULL DEFAULT '',
		validada_fecha datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		autor varchar(255) NOT NULL DEFAULT '',
		publicada_post_id bigint(20) NOT NULL DEFAULT 0,
		publicable_slug varchar(255) NOT NULL DEFAULT '',
		publicada_fecha datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		UNIQUE KEY id (id)
	) $charset_collate;";

	$resultado = dbDelta( $sql );
	$error     = $wpdb->last_error;
	if ( ! empty( $error ) ) {
		// Desactiva el plugin.
		deactivate_plugins( plugin_basename( __FILE__ ) );
		// Muestra el mensaje de error.
		$error = __( 'An error occurred while trying to create the database table for the plugin', 'ai-content-creator' ) . ':<br><br>' . $error;
		wp_die( esc_html( $error ) );
	} else {
		// Tabla creada correctamente.
		update_option( 'aicc_dbversion', $db_version );
	}
}

/** Crea o modifica la tabla de caché de Pixabay. */
function aicc_crear_tabla_pixabay_cache() {
	global $wpdb;
	require_once plugin_dir_path( __FILE__ ) . 'lista-opciones.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/configuracion.php';

	$plugin_version = aicc_get_version();
	$db_version     = aicc_version_requerida_tabla_pixabay_cache();

	$charset_collate = $wpdb->get_charset_collate();
	$tabla           = aicc_tabla_pixabay_cache();

	// Para usar dbDelta().
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	/** ******************************************************************************************* */
	/** Actualizar la función aicc_version_requerida_tabla_pixabay_cache si se cambia la estructura */
	/** ******************************************************************************************* */
	// No poner lineas en blanco o dará error.
	// dbDelta no elimina ni ordena columnas.
	$sql = "CREATE TABLE $tabla (
        id INT AUTO_INCREMENT PRIMARY KEY,
        consulta VARCHAR(255) NOT NULL,
        idioma VARCHAR(2) NOT NULL,
        respuesta LONGTEXT NOT NULL,
        creado_en DATETIME NOT NULL
    ) $charset_collate;";

	$resultado = dbDelta( $sql );
	$error     = $wpdb->last_error;
	if ( ! empty( $error ) ) {
		// Desactiva el plugin.
		deactivate_plugins( plugin_basename( __FILE__ ) );
		// Muestra el mensaje de error.
		$error = __( 'An error occurred while trying to create the database table for the plugin', 'ai-content-creator' ) . ':<br><br>' . $error;
		wp_die( esc_html( $error ) );
	} else {
		// Tabla creada correctamente.
		update_option( 'aicc_pxbcversion', $db_version );
	}
}
