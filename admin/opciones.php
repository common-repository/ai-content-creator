<?php
/**
 * Página de ajustes.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

// Agrega el menú y submenús para el plugin "AI Content Creator".
add_action( 'admin_menu', 'aicc_menu_pages' );
/** Agrega páginas de menú y submenús para el plugin "AI Content Creator".
 */
function aicc_menu_pages() {

	$notificacion_globo = '';

	/*
	Icono de la entrada de menú
	El icono ha de ir codificado forzosamente en base 64 y no es plan codificarlo en tiempo real cada vez.
	El color de base hasta que se recolorea, viene dado por el fill="#a7aaad". Es necesario especificar valor para que luego pueda cambiar al pasar el puntero.
	<svg viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">
		<path fill="#a7aaad" d="M25 3C12.86 3 3 12.86 3 25s9.86 22 22 22 22-9.86 22-22S37.14 3 25 3zm0 40c-10.477 0-19-8.523-19-19S14.523 6 25 6s19 8.523 19 19-8.523 19-19 19zM25 14c-4.418 0-8 3.582-8 8s3.582 8 8 8 8-3.582 8-8-3.582-8-8-8zm0 15c-3.309 0-6-2.691-6-6s2.691-6 6-6 6 2.691 6 6-2.691 6-6 6z"/>
	</svg>
	*/
	$icon_svg_base64 = 'PHN2ZyB2aWV3Qm94PSIwIDAgNTAgNTAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZmlsbD0iI2E3YWFhZCIgZD0iTTI1IDNDMTIuODYgMyAzIDEyLjg2IDMgMjVzOS44NiAyMiAyMiAyMiAyMi05Ljg2IDIyLTIyUzM3LjE0IDMgMjUgM3ptMCA0MGMtMTAuNDc3IDAtMTktOC41MjMtMTktMTlTMTQuNTIzIDYgMjUgNnMxOSA4LjUyMyAxOSAxOS04LjUyMyAxOS0xOSAxOXpNMjUgMTRjLTQuNDE4IDAtOCAzLjU4Mi04IDhzMy41ODIgOCA4IDggOC0zLjU4MiA4LTgtMy41ODItOC04LTh6bTAgMTVjLTMuMzA5IDAtNi0yLjY5MS02LTZzMi42OTEtNiA2LTYgNiAyLjY5MSA2IDYtMi42OTEgNi02IDZ6Ii8+PC9zdmc+';

	$requisito_creadores = aicc_requisito_crear();

	// Menú principal y primera entrada.
	add_menu_page(
		__( 'AI Content Creator', 'ai-content-creator' ),                                       // Page title.
		__( 'AI Content Creator', 'ai-content-creator' ) . wp_kses_post( $notificacion_globo ), // Entrada del menú.
		$requisito_creadores,                                                                   // Capability. Con "author", el admin no tiene acceso.
		'aicc_menu',                                                                            // Menu slug.
		'',                                                                                     // Function.
		'data:image/svg+xml;base64,' . $icon_svg_base64                                         // Icon.
	);

	// La primera entrada, al tener el mismo slug que el menú, remplaza a la primera que se crearía por defecto.
	add_submenu_page(
		'aicc_menu',                                                                            // Parent slug.
		__( 'AI Content Creator', 'ai-content-creator' ),                                       // Page title.
		__( 'Create article', 'ai-content-creator' ) . wp_kses_post( $notificacion_globo ),     // Menu title.
		$requisito_creadores,                                                                   // Capability.
		'aicc_menu',                                                                            // Menu slug.
		'aicc_create_page_callback'                                                             // Function.
	);

	add_submenu_page(
		'aicc_menu',
		__( 'AI Content Creator', 'ai-content-creator' ) . ' - ' . __( 'Articles', 'ai-content-creator' ),
		__( 'Articles', 'ai-content-creator' ) . wp_kses_post( $notificacion_globo ),
		$requisito_creadores,
		'aicc_articles',
		'aicc_articles_page_callback'
	);

	add_submenu_page(
		'aicc_menu',
		__( 'AI Content Creator', 'ai-content-creator' ) . ' - ' . __( 'Settings', 'ai-content-creator' ),
		__( 'Settings', 'ai-content-creator' ) . wp_kses_post( $notificacion_globo ),
		'manage_options',
		'aicc_settings',
		'aicc_settings_page_callback'
	);

	add_submenu_page(
		'aicc_menu',
		__( 'AI Content Creator', 'ai-content-creator' ) . ' - ' . __( 'Help', 'ai-content-creator' ),
		__( 'Help', 'ai-content-creator' ) . wp_kses_post( $notificacion_globo ),
		$requisito_creadores,
		'aicc_help',
		'aicc_help_page_callback'
	);
}

/** Callback para la página "Create article".
 */
function aicc_create_page_callback() {
	require_once plugin_dir_path( __FILE__ ) . 'crear.php';
}

/** Callback para la página "Help".
 */
function aicc_help_page_callback() {
	// Contenido de la ayuda.
	aicc_pagina_ayuda();
}

/** Callback para la página "Articles".
 */
function aicc_articles_page_callback() {
	require_once plugin_dir_path( __FILE__ ) . 'articulos.php';
}

/** Callback para la página "Settings".
 */
function aicc_settings_page_callback() {
	echo '<div class="wrap">' . "\n";
	aicc_mostrar_cabecera_pagina_admin();
	echo '<h2>';
	esc_html_e( 'Settings', 'ai-content-creator' );
	echo "</h2>\n";
	settings_errors();
	echo '<form method="post" action="options.php">' . "\n";
	settings_fields( 'aicc_settings' );
	do_settings_sections( 'aicc_settings' );
	submit_button();
	echo "</form>\n";
	echo "</div>\n";
}

// Inicializa la configuración del plugin y agrega secciones y campos de configuración.
add_action( 'admin_init', 'aicc_settings_init' );
/** Inicializa la configuración del plugin y agrega secciones y campos de configuración.
 */
function aicc_settings_init() {
	register_setting(
		'aicc_settings',                                // Option group.
		'aicc_settings',                                // Option name.
		'aicc_settings_validate'                        // Function to validate input.
	);
	add_settings_section(
		'aicc_settings_section_openai',                 // ID of the section.
		__( 'OpenAI account', 'ai-content-creator' ),   // Title of the section.
		'aicc_settings_section_openai_callback',        // Function to be called to display the section.
		'aicc_settings'                                 // Slug of the page.
	);

	add_settings_field(
		'aicc_apikoai',                                 // ID of the field.
		__( 'OpenAI API key', 'ai-content-creator' ),   // Title of the field.
		'aicc_settings_apikoai_callback',               // Function to be called to display the section.
		'aicc_settings',                                // Slug of the page.
		'aicc_settings_section_openai'                  // ID of the section.
	);
	/** Ajustes no utilizados
	add_settings_section(
		'aicc_settings_section_posproduccion',
		_ _( 'External post-production system (optional)', 'ai-content-creator' ),
		'aicc_settings_section_posproduccion_callback',
		'aicc_settings'
	);
	add_settings_field(
		'aicc_apiurlposproduccion',
		_ _( 'Post-production URL', 'ai-content-creator' ),
		'aicc_settings_apiurlposproduccion_callback',
		'aicc_settings',
		'aicc_settings_section_posproduccion'
	);
	add_settings_field(
		'aicc_apikposproduccion',
		_ _( 'Post-production API key', 'ai-content-creator' ),
		'aicc_settings_apikposproduccion_callback',
		'aicc_settings',
		'aicc_settings_section_posproduccion'
	);
	*/

	add_settings_section(
		'aicc_settings_section_general',
		__( 'General settings', 'ai-content-creator' ),
		'',
		'aicc_settings'
	);
	add_settings_field(
		'aicc_modelo',
		__( 'AI model', 'ai-content-creator' ),
		'aicc_settings_modelo_callback',
		'aicc_settings',
		'aicc_settings_section_general'
	);
	add_settings_field(
		'aicc_longitud',
		__( 'Article length in words', 'ai-content-creator' ),
		'aicc_settings_longitud_callback',
		'aicc_settings',
		'aicc_settings_section_general'
	);
	add_settings_field(
		'aicc_timeout',
		__( 'Time limit in seconds', 'ai-content-creator' ),
		'aicc_settings_timeout_callback',
		'aicc_settings',
		'aicc_settings_section_general'
	);
	add_settings_field(
		'aicc_idioma',
		__( 'Language of created articles', 'ai-content-creator' ),
		'aicc_settings_idioma_callback',
		'aicc_settings',
		'aicc_settings_section_general'
	);
	add_settings_field(
		'aicc_tono',
		__( 'Tone of created articles', 'ai-content-creator' ),
		'aicc_settings_tono_callback',
		'aicc_settings',
		'aicc_settings_section_general'
	);
	add_settings_field(
		'aicc_tablacontenidos',
		__( 'Table of contents', 'ai-content-creator' ),
		'aicc_settings_tablacontenidos_callback',
		'aicc_settings',
		'aicc_settings_section_general'
	);
	add_settings_field(
		'aicc_editorescrean',
		__( 'Editors', 'ai-content-creator' ),
		'aicc_settings_editorescrean_callback',
		'aicc_settings',
		'aicc_settings_section_general'
	);
}

/** Callback para la sección "OpenAI account".
 */
function aicc_settings_section_openai_callback() {
	esc_html_e( 'Enter your API key for', 'ai-content-creator' );
	echo ' <a href="https://openai.com/" target="_blank" rel="noreferrer noopener">OpenAI</a><br>';
}

/** Callback para el campo "OpenAI API key".
 */
function aicc_settings_apikoai_callback() {
	$valor   = '';
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_apikoai'] ) ) {
		$valor = $options['aicc_apikoai'];
	}
	echo '<input name="aicc_settings[aicc_apikoai]" style="width:55ch" value="';
	echo esc_attr( $valor );
	echo '"><br>' . "\n";
	echo '<span class="dashicons dashicons-editor-help" style="color:blue;"></span> ';
	echo '<a href="admin.php?page=aicc_help">';
	esc_html_e( 'Help', 'ai-content-creator' );
	echo "</a>\n";
}

/** Callback para la sección "External post-production system (optional)".
 */
function aicc_settings_section_posproduccion_callback() {
	esc_html_e( 'Enter your access data for post-production API', 'ai-content-creator' );
}

/** Callback para el campo "Post-production URL".
 */
function aicc_settings_apiurlposproduccion_callback() {
	$valor   = '';
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_apiurlposproduccion'] ) ) {
		$valor = $options['aicc_apiurlposproduccion'];
	}
	echo '<input name="aicc_settings[aicc_apiurlposproduccion]" style="width:55ch" value="';
	echo esc_attr( $valor );
	echo '">' . "\n";
}

/** Callback para el campo "Post-production API key".
 */
function aicc_settings_apikposproduccion_callback() {
	$valor   = '';
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_apikposproduccion'] ) ) {
		$valor = $options['aicc_apikposproduccion'];
	}
	echo '<input name="aicc_settings[aicc_apikposproduccion]" style="width:55ch" value="';
	echo esc_attr( $valor );
	echo '">' . "\n";
}

/** Callback para el campo "AI model".
 */
function aicc_settings_modelo_callback() {
	// Selección del modelo de IA.
	$modelo = aicc_conf_modelo();

	/* translators: %s: Factor multiplicador del precio, por ejemplo "10x". */
	$texto = sprintf( __( 'Price %s higher than GPT-3.5', 'ai-content-creator' ), '10x' );
	aicc_mostrar_campo_radio_modelo( $modelo, 'text-davinci-003', $texto );

	$texto = '<b>' . __( 'Recommended', 'ai-content-creator' ) . '</b>. ' . __( 'This is the foundation of ChatGPT', 'ai-content-creator' );
	aicc_mostrar_campo_radio_modelo( $modelo, 'gpt-3.5-turbo', $texto );

	/* translators: %s: Factor multiplicador del precio, por ejemplo "30x". */
	$texto = __( 'Reqires a minimum payment of 5 USD to grant access.', 'ai-content-creator' ) . ' ' . sprintf( __( 'Price %s higher than GPT-3.5', 'ai-content-creator' ), '30x' );
	aicc_mostrar_campo_radio_modelo( $modelo, 'gpt-4', $texto );

	$texto = __( 'Updated GPT-4.', 'ai-content-creator' ) . ' ' . __( 'Price is lower than GPT-4', 'ai-content-creator' );
	aicc_mostrar_campo_radio_modelo( $modelo, 'gpt-4-turbo', $texto );

	$texto = __( 'Updated GPT-4.', 'ai-content-creator' ) . ' ' . __( 'Faster and cheaper recommended GPT-4 model', 'ai-content-creator' );
	aicc_mostrar_campo_radio_modelo( $modelo, 'gpt-4o', $texto );
}

/** Callback para el campo "Article length in words".
 */
function aicc_settings_longitud_callback() {
	$valor = aicc_conf_longitud();
	echo '<input name="aicc_settings[aicc_longitud]" value="';
	echo esc_attr( $valor );
	echo '">' . "\n";
	echo '<br><i>';
	esc_html_e( 'For guidance only, the final decision is up to the AI. [Default: 500]', 'ai-content-creator' );
	echo '<br>';
	esc_html_e( 'Greater effectiveness in GPT-4.', 'ai-content-creator' );
	echo '</i>';
}

/** Callback para el campo "Time limit in seconds".
 */
function aicc_settings_timeout_callback() {
	$valor = aicc_conf_timeout();
	echo '<input name="aicc_settings[aicc_timeout]" value="';
	echo esc_attr( $valor );
	echo '">' . "\n";
	echo "<br><i>\n";
	/* translators: %s: default time limit value in seconds. */
	echo esc_html( sprintf( __( 'Time limit for reply. After waiting this time for IA reply, the connection will be closed and considered an error. [Default: %s]', 'ai-content-creator' ), aicc_default_timeout() ) );
	echo "</i>\n";
}

/** Callback para el campo "Language of created articles".
 */
function aicc_settings_idioma_callback() {
	$valor_actual       = aicc_conf_idioma();
	$idiomas_soportados = aicc_idiomas();
	echo '<select name="aicc_settings[aicc_idioma]">' . "\n";
	foreach ( $idiomas_soportados as $valor => $opcion ) {
		echo '<option value="';
		echo esc_attr( $valor );
		echo '" ';
		selected( $valor, $valor_actual );
		echo '>';
		echo esc_html( $opcion );
		echo "</option>\n";
	}
	echo "</select>\n";
}

/** Callback para el campo "Tone of created articles".
 */
function aicc_settings_tono_callback() {
	$valor_actual     = aicc_conf_tono();
	$tonos_soportados = aicc_tonos();
	echo '<select name="aicc_settings[aicc_tono]">' . "\n";
	foreach ( $tonos_soportados as $valor => $opcion ) {
		echo '<option value="';
		echo esc_attr( $valor );
		echo '" ';
		selected( $valor, $valor_actual );
		echo '>';
		echo esc_html( ucfirst( $opcion ) );
		echo "</option>\n";
	}
	echo "</select>\n";
}

/** Callback para el campo "Table of contents".
 */
function aicc_settings_tablacontenidos_callback() {
	$valor_actual = aicc_conf_tablacontenidos();
	echo "<input type='checkbox' name='aicc_settings[aicc_tablacontenidos]' ";
	checked( $valor_actual, 1 );
	echo " value='1'> ";
	esc_html_e( 'Start with an introduction and a table of contents.', 'ai-content-creator' );
	echo '<br><i>';
	esc_html_e( 'Recommended for SEO.', 'ai-content-creator' );
	echo '</i>';
}

/** Callback para el campo "Editors".
 */
function aicc_settings_editorescrean_callback() {
	$valor_actual = aicc_conf_editorescrean();
	echo "<input type='checkbox' name='aicc_settings[aicc_editorescrean]' ";
	checked( $valor_actual, 1 );
	echo " value='1'> ";
	esc_html_e( 'Editors can use AI to create articles.', 'ai-content-creator' );
}

/** Devuelve la capacidad requerida para crear artículos utilizando el plugin.
 *
 * @return string La capacidad requerida para crear artículos con la IA.
 */
function aicc_requisito_crear() {
	$capacidad = 'manage_options';
	if ( aicc_conf_editorescrean() ) {
		$capacidad = 'edit_posts';
	}
	return $capacidad;
}

/** Muestra el campo para el botón de un modelo.
 *
 * @param string $modelo_elegido El modelo elegido actualmente en la configuración.
 * @param string $modelo_seleccionable El modelo que se puede seleccionar.
 * @param string $texto HTML adicional para mostrar junto al botón de radio.
 */
function aicc_mostrar_campo_radio_modelo( $modelo_elegido, $modelo_seleccionable, $texto = '' ) {
	echo "<label>\n";

	echo '<input type="radio" name="aicc_settings[aicc_modelo]" value="' . esc_attr( $modelo_seleccionable ) . '"'
		. checked( $modelo_elegido, $modelo_seleccionable, false )
		. esc_html( aicc_modelo_disabled( $modelo_seleccionable ) ) . '>';

	echo ' <b>' . esc_html( aicc_denominacion_modelo( $modelo_seleccionable ) ) . '</b>';

	if ( $texto ) {
		echo ' &nbsp; <small>' . wp_kses_post( $texto ) . "</small>\n";
	}

	echo "</label>\n";

	echo "<br>\n";
}

/** Valida el conjunto de los valores de entrada de la configuración del plugin.
 *
 * @param array $input Los valores de entrada de la configuración del plugin.
 * @return array Los valores de entrada validados.
 */
function aicc_settings_validate( $input ) {
	// Valida diferentes campos.

	$input = aicc_settings_validate_openaikey( $input );
	$input = aicc_settings_validate_posproduccion_url( $input );
	$input = aicc_settings_validate_posproduccion_key( $input );
	$input = aicc_settings_validate_posproduccion_par( $input );
	$input = aicc_settings_validate_modelo( $input );
	$input = aicc_settings_validate_longitud( $input );
	$input = aicc_settings_validate_timeout( $input );
	$input = aicc_settings_validate_idioma( $input );
	$input = aicc_settings_validate_tono( $input );
	$input = aicc_settings_validate_tablacontenidos( $input );
	$input = aicc_settings_validate_editorescrean( $input );

	return $input;
}

/** Valida la longitud del artículo en palabras introducido en el panel de configuración del plugin.
 *
 * @param array $input Array asociativo que contiene los valores ingresados en el panel de configuración del plugin.
 * @return array Array asociativo con los valores validados y ajustados según sea necesario.
 */
function aicc_settings_validate_longitud( $input ) {
	// Inicializa variables para la validación de la longitud.
	$mensaje_error   = __( 'Article length in words', 'ai-content-creator' ) . ': ';
	$setting         = 'aicc_longitud';
	$error_code      = '';
	$cantidad_minima = 100;
	$cantidad_maxima = 2500;
	$unidad          = __( 'words', 'ai-content-creator' );

	// Realiza diferentes validaciones en función de las condiciones especificadas.
	if ( empty( $input[ $setting ] ) ) {
		// Caso en que el campo está vacío.
		$error_code        = 'aicc_longitud requerido';
		$mensaje_error    .= __( 'Required field.', 'ai-content-creator' );
		$input[ $setting ] = aicc_conf_longitud();
	} elseif ( ! is_numeric( $input[ $setting ] ) ) {
		// Caso en que el valor no es numérico.
		$error_code        = 'aicc_longitud no numerica';
		$mensaje_error    .= __( 'Value must be numeric.', 'ai-content-creator' );
		$input[ $setting ] = aicc_conf_longitud();
	} elseif ( filter_var( $input[ $setting ], FILTER_VALIDATE_INT ) === false ) {
		// Caso en que el valor no es un entero.
		$error_code        = 'aicc_longitud no entera';
		$mensaje_error    .= __( 'Value must be an integer.', 'ai-content-creator' );
		$input[ $setting ] = aicc_conf_longitud();
	} elseif ( $input[ $setting ] < $cantidad_minima ) {
		// Caso en que el valor es menor que la cantidad mínima exigida.
		$error_code = 'aicc_longitud minima';
		/* translators: %1$s: valor mínimo, %2$s: unidad. */
		$mensaje_error    .= esc_html( sprintf( __( 'Minimum value is %1$s %2$s.', 'ai-content-creator' ), $cantidad_minima, $unidad ) );
		$input[ $setting ] = $cantidad_minima;
	} elseif ( $input[ $setting ] > $cantidad_maxima ) {
		// Caso en que el valor es mayor que la cantidad máxima permitida.
		$error_code = 'aicc_longitud maxima';
		/* translators: %1$s: valor máximo, %2$s: unidad. */
		$mensaje_error    .= esc_html( sprintf( __( 'Maximum value is %1$s %2$s.', 'ai-content-creator' ), $cantidad_maxima, $unidad ) );
		$input[ $setting ] = $cantidad_maxima;
	}

	// Si hay un error en la validación, registra el error y muestra el mensaje correspondiente.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error );
	}

	// Devuelve el array de entrada con los valores validados y ajustados según sea necesario.
	return $input;
}

/** Validación de la clave API de OpenAI en el panel de configuración del plugin.
 *
 * @param array $input Array asociativo que contiene los valores ingresados en el panel de configuración del plugin.
 * @return array Array asociativo con los valores validados y ajustados según sea necesario.
 */
function aicc_settings_validate_openaikey( $input ) {
	// Inicializa variables para la validación de la clave API de OpenAI.
	$mensaje_error = __( 'OpenAI API key', 'ai-content-creator' ) . ': ';
	$setting       = 'aicc_apikoai';
	$error_code    = '';

	// Realiza la validación de la clave API de OpenAI si no está vacía.
	if ( ! empty( $input[ $setting ] ) ) {
		// Comprueba si la clave API tiene el formato correcto.
		if ( ! preg_match( '/^(sk\-)?[a-zA-Z0-9_]{32,96}$/', $input[ $setting ] ) &&
		! preg_match( '/^(sk\-project\-)?[a-zA-Z0-9_]{32,96}$/', $input[ $setting ] ) &&
		! preg_match( '/^(sk\-proj\-)?[a-zA-Z0-9_]{32,96}$/', $input[ $setting ] )
		) {
			$error_code     = 'aicc_apikoai defectuosa';
			$mensaje_error .= __( 'The format does not seem to be correct.', 'ai-content-creator' );

			// Si el formato no es correcto, establece el valor anterior.
			$input[ $setting ] = aicc_conf_apikoai();
		}
	}

	// Si hay un error en la validación, registra el error y muestra el mensaje correspondiente.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error );
	}

	// Devuelve el array de entrada con los valores validados y ajustados según sea necesario.
	return $input;
}

/** Valida el tiempo límite en segundos introducido en el panel de configuración del plugin.
 *
 * @param array $input Array asociativo que contiene los valores ingresados en el panel de configuración del plugin.
 * @return array Array asociativo con los valores validados y ajustados según sea necesario.
 */
function aicc_settings_validate_timeout( $input ) {
	// Inicializa variables para la validación del límite de tiempo.
	$mensaje_error   = __( 'Time limit in seconds', 'ai-content-creator' ) . ': ';
	$setting         = 'aicc_timeout';
	$error_code      = '';
	$cantidad_minima = 30;
	$cantidad_maxima = 600;
	$unidad          = __( 'seconds', 'ai-content-creator' );

	// Realiza diferentes validaciones en función de las condiciones especificadas.
	if ( empty( $input[ $setting ] ) ) {
		// Caso en que el campo está vacío.
		$error_code        = 'aicc_timeout requerido';
		$mensaje_error    .= __( 'Required field.', 'ai-content-creator' );
		$input[ $setting ] = aicc_conf_timeout();
	} elseif ( ! is_numeric( $input[ $setting ] ) ) {
		// Caso en que el valor no es numérico.
		$error_code        = 'aicc_timeout no numerico';
		$mensaje_error    .= __( 'Value must be numeric.', 'ai-content-creator' );
		$input[ $setting ] = aicc_conf_timeout();
	} elseif ( filter_var( $input[ $setting ], FILTER_VALIDATE_INT ) === false ) {
		// Caso en que el valor no es un entero.
		$error_code        = 'aicc_timeout no entero';
		$mensaje_error    .= __( 'Value must be an integer.', 'ai-content-creator' );
		$input[ $setting ] = aicc_conf_timeout();
	} elseif ( $input[ $setting ] < $cantidad_minima ) {
		// Caso en que el valor es menor que la cantidad mínima exigida.
		$error_code = 'aicc_timeout minima';
		/* translators: %1$s: valor mínimo, %2$s: unidad. */
		$mensaje_error    .= esc_html( sprintf( __( 'Minimum value is %1$s %2$s.', 'ai-content-creator' ), $cantidad_minima, $unidad ) );
		$input[ $setting ] = $cantidad_minima;
	} elseif ( $input[ $setting ] > $cantidad_maxima ) {
		// Caso en que el valor es mayor que la cantidad máxima permitida.
		$error_code = 'aicc_timeout maxima';
		/* translators: %1$s: valor máximo, %2$s: unidad. */
		$mensaje_error    .= esc_html( sprintf( __( 'Maximum value is %1$s %2$s.', 'ai-content-creator' ), $cantidad_maxima, $unidad ) );
		$input[ $setting ] = $cantidad_maxima;
	}

	// Si hay un error en la validación, registra el error y muestra el mensaje correspondiente.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error );
	}

	// Devuelve el array de entrada con los valores validados y ajustados según sea necesario.
	return $input;
}

/** Valida la URL de posproducción.
 *
 * @param array $input Valores de entrada de la configuración del plugin.
 * @return array Valores de entrada validados.
 */
function aicc_settings_validate_posproduccion_url( $input ) {
	// Validación de la URL de posproducción.
	// Inicio del posible mensaje de error.
	$mensaje_error = __( 'Post-production URL', 'ai-content-creator' ) . ': ';
	$setting       = 'aicc_apiurlposproduccion';
	$error_code    = '';
	if ( ! empty( $input[ $setting ] ) ) {
		if ( ! filter_var( $input[ $setting ], FILTER_VALIDATE_URL ) ) {
			$error_code        = 'aicc_apiurlposproduccion defectuosa';
			$mensaje_error    .= __( 'The format does not seem to be correct.', 'ai-content-creator' );
			$input[ $setting ] = aicc_conf_apiurlposproduccion();
		}
	}

	// Indica error si procede.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error );
	}
	return $input;
}

/** Valida la clave API de posproducción.
 *
 * @param array $input Valores de entrada de la configuración del plugin.
 * @return array Valores de entrada validados.
 */
function aicc_settings_validate_posproduccion_key( $input ) {
	// Validación de la clave API de posproducción.
	// Inicio del posible mensaje de error.
	$mensaje_error = __( 'Post-production API key', 'ai-content-creator' ) . ': ';
	$setting       = 'aicc_apikposproduccion';
	$error_code    = '';
	if ( ! empty( $input[ $setting ] ) ) {
		// Formato: "aicc-" + sha-256.
		if ( ! preg_match( '/^aicc\-[a-fA-F0-9]{64}$/i', $input[ $setting ] ) ) {
			$error_code        = 'aicc_apikposproduccion defectuosa';
			$mensaje_error    .= __( 'The format does not seem to be correct.', 'ai-content-creator' );
			$input[ $setting ] = aicc_conf_apiurlposproduccion();
		}
	}

	// Indica error si procede.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error );
	}
	return $input;
}

/** Valida la URL y la clave API de posproducción.
 *  Además, no tiene utilidad una sin otra.
 *
 * @param array $input Valores de entrada de la configuración del plugin.
 * @return array Valores de entrada validados.
 */
function aicc_settings_validate_posproduccion_par( $input ) {
	// La URL de posproducción requiere clave API.
	$mensaje_error = __( 'Post-production data', 'ai-content-creator' ) . ': ';
	$setting       = 'aicc_apiurlposproduccion';
	$error_code    = '';
	if ( empty( $input['aicc_apiurlposproduccion'] ) xor empty( $input['aicc_apikposproduccion'] ) ) {
		$error_code     = 'aicc_posproduccion insuficiente';
		$mensaje_error .= __( 'To use an external post-production service you need the URL address and your key.', 'ai-content-creator' );
	}

	// Indica error si procede.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error, 'warning' );
	}
	return $input;
}

/** Valida el modelo de IA seleccionado.
 *
 * @param array $input Valores de entrada de la configuración del plugin.
 * @return array Valores de entrada validados.
 */
function aicc_settings_validate_modelo( $input ) {
	// Validación del modelo.
	// Inicio del posible mensaje de error.
	$mensaje_error = __( 'AI model', 'ai-content-creator' ) . ': ';
	$setting       = 'aicc_modelo';
	$error_code    = '';
	if ( ! aicc_es_valido_modelo( $input[ $setting ] ) ) {
		$error_code        = 'aicc_modelo incorrecto';
		$mensaje_error    .= __( 'The selected model is not available.', 'ai-content-creator' );
		$input[ $setting ] = aicc_conf_modelo();
	}

	// Indica error si procede.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error, 'error' );
	}
	return $input;
}

/** Valida el idioma seleccionado para los artículos creados.
 *
 * @param array $input Valores de entrada de la configuración del plugin.
 * @return array Valores de entrada validados.
 */
function aicc_settings_validate_idioma( $input ) {
	// Validación del idioma.
	// Inicio del posible mensaje de error.
	$mensaje_error = __( 'Language of created articles', 'ai-content-creator' ) . ': ';
	$setting       = 'aicc_idioma';
	$error_code    = '';
	if ( ! aicc_es_valido_idioma( $input[ $setting ] ) ) {
		$error_code        = 'aicc_idioma incorrecto';
		$mensaje_error    .= __( 'The selected language is not available.', 'ai-content-creator' );
		$input[ $setting ] = aicc_conf_idioma();
	}

	// Indica error si procede.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error, 'error' );
	}
	return $input;
}

/** Valida el tono seleccionado para los artículos creados.
 *
 * @param array $input Valores de entrada de la configuración del plugin.
 * @return array Valores de entrada validados.
 */
function aicc_settings_validate_tono( $input ) {
	// Validación del tono.
	// Inicio del posible mensaje de error.
	$mensaje_error = __( 'Tone of created articles', 'ai-content-creator' ) . ': ';
	$setting       = 'aicc_tono';
	$error_code    = '';
	if ( ! aicc_es_valido_tono( $input[ $setting ] ) ) {
		$error_code        = 'aicc_tono incorrecto';
		$mensaje_error    .= __( 'The selected tone is not available.', 'ai-content-creator' );
		$input[ $setting ] = aicc_conf_tono();
	}

	// Indica error si procede.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error, 'error' );
	}
	return $input;
}

/** Valida el valor de la tabla de contenidos.
 *
 * @param array $input Valores de entrada de la configuración del plugin.
 * @return array Valores de entrada validados.
 */
function aicc_settings_validate_tablacontenidos( $input ) {
	// Validación del valor de la tabla de contenidos.
	// Se aceptan '0' o '1' como strings.
	// Inicio del posible mensaje de error.
	$mensaje_error = __( 'Table of contents', 'ai-content-creator' ) . ': ';
	$setting       = 'aicc_tablacontenidos';
	$error_code    = '';
	if ( isset( $input[ $setting ] ) ) {
		if ( '0' !== $input[ $setting ] && '1' !== $input[ $setting ] ) {
			$error_code        = 'aicc_tablacontenidos incorrecta';
			$mensaje_error    .= __( 'Wrong selection.', 'ai-content-creator' );
			$input[ $setting ] = aicc_conf_tablacontenidos();
		}
	} else {
		$input[ $setting ] = '0';
	}

	// Indica error si procede.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error, 'error' );
	}
	return $input;
}

/** Valida si los editores pueden usar la IA para crear artículos.
 *
 * @param array $input Valores de entrada de la configuración del plugin.
 * @return array Valores de entrada validados.
 */
function aicc_settings_validate_editorescrean( $input ) {
	// Validación del valor de la tabla de contenidos.
	// Inicio del posible mensaje de error.
	$mensaje_error = __( 'Editors', 'ai-content-creator' ) . ': ';
	$setting       = 'aicc_editorescrean';
	$error_code    = '';
	if ( isset( $input[ $setting ] ) ) {
		if ( '0' !== $input[ $setting ] && '1' !== $input[ $setting ] ) {
			$error_code        = 'aicc_editorescrean incorrecta';
			$mensaje_error    .= __( 'Wrong selection.', 'ai-content-creator' );
			$input[ $setting ] = aicc_conf_editorescrean();
		}
	} else {
		$input[ $setting ] = '0';
	}

	// Indica error si procede.
	if ( $error_code ) {
		add_settings_error( $setting, $error_code, $mensaje_error, 'error' );
	}
	return $input;
}
