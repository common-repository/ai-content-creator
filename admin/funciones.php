<?php
/** Funciones comunes varias para AI Content Creator.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

/** Valida el ID del artículo proporcionado y muestra una notificación de error si no es válido.
 *
 * @param int $id El ID del artículo a validar.
 * @return int Devuelve el ID del artículo si es válido, una cadena vacía si no lo es.
 */
function aicc_valida_id_articulo( $id ) {
	// Si no es un entero, lo elimina.
	if ( filter_var( $id, FILTER_VALIDATE_INT ) === false ) {
		$id = '';
		aicc_mostrar_notificacion( __( 'The received ID is not valid.', 'ai-content-creator' ), 'error' );
	}
	return $id;
}

/** Valida el título del artículo proporcionado y muestra una notificación de error si no es válido.
 *
 * @param string $titulo El título del artículo a validar.
 * @return int Devuelve 1 si el título es válido, 0 si no lo es.
 */
function aicc_valida_titulo( $titulo ) {
	// El título es obligatorio, se limita su longitud.
	// Por lo demás, se aceptan comillas, código, etc., es legítimo.
	$valido          = 1;
	$longitud_maxima = 255;
	$titulo          = trim( $titulo );
	if ( ! $titulo ) {
		$valido = 0;
		aicc_mostrar_notificacion( __( 'The received title is not valid.', 'ai-content-creator' ), 'error' );
	} elseif ( mb_strlen( $titulo, 'UTF-8' ) > $longitud_maxima ) {
		$valido = 0;
		aicc_mostrar_notificacion( __( 'Title is too long.', 'ai-content-creator' ), 'error' );
	}
	return $valido;
}

/** Sanea y limita la longitud de un título.
 *
 * @param string $titulo Título a sanear.
 * @return string Título saneado y limitado en longitud.
 */
function aicc_sanea_titulo( $titulo ) {
	// El título es obligatorio, se limita su longitud.
	// Por lo demás, se aceptan comillas, código, etc., es legítimo.
	$longitud_maxima = 255;
	$titulo          = trim( $titulo );
	if ( ! $titulo ) {
		$titulo = '';
	} elseif ( mb_strlen( $titulo, 'UTF-8' ) > $longitud_maxima ) {
		$titulo = mb_substr( $titulo, 0, $longitud_maxima, 'UTF-8' );
	}
	// Filtra caracteres nulos.
	$titulo = preg_replace( '/\x00/', '', $titulo );
	return $titulo;
}

/** Sanea y limita la longitud de un contexto.
 *
 * @param string $contexto Contexto a sanear.
 * @return string Contexto saneado y limitado en longitud.
 */
function aicc_sanea_contexto( $contexto ) {
	// Nada que filtrar, se aceptan comillas, código, etc.
	$longitud_maxima = 512;
	$contexto        = trim( $contexto );
	if ( mb_strlen( $contexto, 'UTF-8' ) > $longitud_maxima ) {
		$contexto = mb_substr( $contexto, 0, $longitud_maxima, 'UTF-8' );
		aicc_mostrar_notificacion( __( 'Additional guidelines are too long.', 'ai-content-creator' ), 'warn' );
	}
	// Filtra caracteres nulos.
	$contexto = preg_replace( '/\x00/', '', $contexto );
	return $contexto;
}

/** Sanea y limita la longitud de las directrices adicionales.
 *
 * @param string $directrices_adicionales Directrices adicionales a sanear.
 * @return string Directrices adicionales saneadas y limitadas en longitud.
 */
function aicc_sanea_directrices_adicionales( $directrices_adicionales ) {
	// Nada que filtrar, se aceptan comillas, código, etc.
	$longitud_maxima         = 1024;
	$directrices_adicionales = trim( $directrices_adicionales );
	if ( mb_strlen( $directrices_adicionales, 'UTF-8' ) > $longitud_maxima ) {
		$directrices_adicionales = mb_substr( $directrices_adicionales, 0, $longitud_maxima, 'UTF-8' );
		aicc_mostrar_notificacion( __( 'Additional guidelines are too long.', 'ai-content-creator' ), 'warn' );
	}
	// Filtra caracteres nulos.
	$directrices_adicionales = preg_replace( '/\x00/', '', $directrices_adicionales );
	return $directrices_adicionales;
}

/** Devuelve una lista de etiquetas HTML permitidas para un formulario.
 *
 * @return array Lista de etiquetas HTML permitidas.
 */
function aicc_permitidos_kses_formulario() {
	$permitidos = array(
		'form'     => array(
			'method' => array(),
			'action' => array(),
		),
		'label'    => array(
			'for' => array(),
		),
		'input'    => array(
			'type'        => array(),
			'id'          => array(),
			'name'        => array(),
			'style'       => array(),
			'class'       => array(),
			'placeholder' => array(),
			'onclick'     => array(),
			'value'       => array(),
			'disabled'    => array(),
		),
		'textarea' => array(
			'id'          => array(),
			'name'        => array(),
			'rows'        => array(),
			'cols'        => array(),
			'style'       => array(),
			'placeholder' => array(),
		),
		'div'      => array(
			'style' => array(),
		),
		'br'       => array(),
		'em'       => array(),
		'strong'   => array(),
	);
	return $permitidos;
}

/** Sanea y limita la longitud de las palabras clave para una consulta en Pixabay.
 *
 * @param string $keywords Palabras clave a sanear.
 * @return string Palabras clave saneadas y limitadas en longitud.
 */
function aicc_sanea_keywords_pixabay( $keywords ) {
	$keywords = sanitize_text_field( $keywords );
	$keywords = wp_check_invalid_utf8( $keywords );
	$keywords = trim( $keywords );
	// Máximo 255 caracteres, definido en la BBDD.
	$longitud_maxima = 255;
	if ( mb_strlen( $keywords, 'UTF-8' ) > $longitud_maxima ) {
		$keywords = mb_substr( $keywords, 0, $longitud_maxima, 'UTF-8' );
		aicc_mostrar_notificacion( __( 'Pixabay query is too long.', 'ai-content-creator' ), 'warn' );
	}
	return $keywords;
}

/** Devuelve una lista de los modelos habilitados.
 *
 * @return array Lista de modelos habilitados.
 */
function aicc_modelos() {
	$modelos = array(
		'gpt-4o',
		'gpt-4-turbo',
		'gpt-4',
		'gpt-3.5-turbo',
		'text-davinci-003',
		'abcdatos-postproduccion',
		'abcdatos-creacion',
		'abcdatos-seo',
	);
	return $modelos;
}

/**
 * Devuelve la denominación de un modelo.
 *
 * @param string $modelo Identificador del modelo.
 * @return string Denominación del modelo.
 */
function aicc_denominacion_modelo( $modelo ) {
	$nombre = $modelo;

	switch ( $modelo ) {
		case 'gpt-4o':
			$nombre = 'GPT-4o';
			break;
		case 'gpt-4-turbo':
			$nombre = 'GPT-4 Turbo';
			break;
		case 'gpt-4':
			$nombre = 'GPT-4';
			break;
		case 'gpt-3.5-turbo':
			$nombre = 'GPT-3.5 Turbo';
			break;
		case 'text-davinci-003':
			$nombre = 'Davinci-3';
			break;
		case 'text-davinci-002':
			$nombre = 'Davinci-2';
			break;
		case 'davinci':
			$nombre = 'Davinci';
			break;
		default:
			break;
	}

	return $nombre;
}

/** Verifica si un modelo es válido.
 *
 * @param string $modelo Identificador del modelo.
 * @return bool Verdadero si el modelo es válido, falso en caso contrario.
 */
function aicc_es_valido_modelo( $modelo ) {
	return in_array( $modelo, aicc_modelos(), true );
}

/** Devuelve la cadena ' disabled' si un modelo no está habilitado.
 *
 * @param string $modelo Identificador del modelo.
 * @return string Cadena ' disabled' si el modelo no está habilitado, cadena vacía en caso contrario.
 */
function aicc_modelo_disabled( $modelo ) {
	// Devuelve la cadena ' disabled' si un modelo no está habiltado.
	$disabled = '';
	if ( ! aicc_es_valido_modelo( $modelo ) ) {
		$disabled = ' disabled';
	}
	return $disabled;
}

/** Devuelve un array asociativo de tonos compatibles con AI Content Creator.
 *
 * @return array Array asociativo que contiene los códigos de tonos y sus representaciones legibles en varios idiomas.
 */
function aicc_tonos() {
	// Códigos de tonos y su representación legible en idiomas.
	// No le pongo tilde a los códigos para diferenciarlos del texto formal.
	$tonos = array(
		'neutral'     => __( 'neutral', 'ai-content-creator' ),
		'informativo' => __( 'informative', 'ai-content-creator' ),
		/* translators: Voice tone */
		'seguro'      => __( 'secure', 'ai-content-creator' ),
		'persuasivo'  => __( 'persuasive', 'ai-content-creator' ),
		'humoristico' => __( 'humorous', 'ai-content-creator' ),
		'emocional'   => __( 'emotional', 'ai-content-creator' ),
		'entusiasta'  => __( 'enthusiastic', 'ai-content-creator' ),
		'optimista'   => __( 'optimistic', 'ai-content-creator' ),
		'objetivo'    => __( 'objective', 'ai-content-creator' ),
		'simple'      => __( 'simple', 'ai-content-creator' ),
		'formal'      => __( 'formal', 'ai-content-creator' ),
		'informal'    => __( 'informal', 'ai-content-creator' ),
		'preocupado'  => __( 'concerned', 'ai-content-creator' ),
		'critico'     => __( 'critical', 'ai-content-creator' ),
		'triste'      => __( 'sad', 'ai-content-creator' ),
	);
	return $tonos;
}

/** Verifica si el tono proporcionado es válido.
 *
 * @param string $tono El tono a verificar.
 * @return bool Devuelve true si el tono es válido, false en caso contrario.
 */
function aicc_es_valido_tono( $tono ) {
	// Devuelve un booleano indicando si el tono es correcto o no.
	return array_key_exists( $tono, aicc_tonos() );
}

/** Filtra las acciones permitidas en la página de artículos.
 *
 * @param string $accion La acción a verificar.
 * @return string Devuelve la acción si está permitida, una cadena vacía si no está permitida.
 */
function aicc_filtra_accion_articulos( $accion ) {
	// Filtra las acciones permitidas en la página de artículos.
	if ( 'reprocesar' !== $accion
		&& 'reprocesar_todo' !== $accion
		&& 'ver' !== $accion
		&& 'imagen' !== $accion
		&& 'imagen_seleccionada' !== $accion
		&& 'borrar' !== $accion
		&& 'borrar_confirmado' !== $accion
		&& 'borrador' !== $accion
	) {
		$accion = '';
	}
	return $accion;
}

/** Determina el tipo de modelo (chat o completar) para un modelo dado de OpenAI.
 *
 * @param string $modelo El modelo de OpenAI para el cual determinar el tipo.
 * @return string Devuelve 'chat' o 'completar' según el tipo de modelo.
 */
function aicc_tipo_modelo( $modelo ) {
	// Info en https://platform.openai.com/docs/models/model-endpoint-compatibility .
	if ( 'gpt-3.5-turbo' === $modelo
	|| 'gpt-4' === $modelo
	|| 'gpt-4-turbo' === $modelo
	|| 'gpt-4o' === $modelo
	|| 'gpt-4-32k' === $modelo
	) {
		$tipo = 'chat';
	} else {
		$tipo = 'completar';
	}
	return $tipo;
}

/** Muestra una notificación en el panel de administración con el mensaje, tipo y opción de descartar proporcionados.
 *
 * @param string  $message Contenido del mensaje HTML.
 * @param string  $type Tipo de mensaje. Puede ser uno de 'error', 'warning', 'notice' o 'success'. Por defecto 'error'.
 * @param boolean $dismissible (opcional) El mensaje es descartable, se puede cerrar.
 */
function aicc_mostrar_notificacion( $message, $type = 'error', $dismissible = false ) {
	if ( 'notice' === $type ) {
		$type = 'notice-info';
	} elseif ( 'success' === $type ) {
		$type = 'notice-success';
	} elseif ( 'warning' === $type ) {
		$type = 'notice-warning';
	} else {
		$type = 'notice-error';
	}

	echo '<div class="' . esc_attr( $type ) . ' notice';
	if ( $dismissible ) {
		echo ' is-dismissible';
	}

	echo '"';
	echo '>';
	echo '<p>' . wp_kses_post( $message ) . '</p>';
	echo "</div>\n";
}

/** Muestra la cabecera de la página de administración del plugin, incluyendo el título e ícono.
 */
function aicc_mostrar_cabecera_pagina_admin() {
	echo '<h1 style="margin-left: 10px;">' . "\n";
	echo '<img src="';
	echo esc_attr( plugin_dir_url( __FILE__ ) );
	echo 'images/ia.svg" width="25" height="25"';
	echo ' style="width:25px; height:25px;"';
	echo ' alt="';
	echo esc_attr( __( 'AI Content Creator', 'ai-content-creator' ) );
	echo '"';
	echo '>' . "\n";
	esc_html_e( 'AI Content Creator', 'ai-content-creator' );
	echo "</h1>\n";
}

/** Limpia una lista de palabras, eliminando las palabras comunes (stopwords) según el idioma proporcionado.
 *
 * @param array  $palabras Lista de palabras.
 * @param string $idioma   Idioma de las palabras ('en', 'ca', 'es').
 * @return array Lista de palabras relevantes, sin palabras comunes ni elementos vacíos.
 */
function aicc_elimina_stopwords( $palabras, $idioma ) {
	// Define las palabras comunes para cada idioma.
	$palabras_comunes_es = array( 'a', 'de', 'con', 'en', 'la', 'las', 'el', 'los', 'para', 'por', 'que', 'y', 'un', 'una' );
	$palabras_comunes_en = array( 'a', 'an', 'the', 'and', 'in', 'on', 'at', 'for', 'with', 'about', 'as', 'by', 'to' );
	$palabras_comunes_ca = array( 'a', 'de', 'amb', 'en', 'la', 'les', 'el', 'els', 'per', 'que', 'i', 'un', 'una' );

	// Selecciona las palabras comunes según el idioma.
	switch ( $idioma ) {
		case 'en':
			$palabras_comunes = $palabras_comunes_en;
			break;
		case 'ca':
			$palabras_comunes = $palabras_comunes_ca;
			break;
		default:
			$palabras_comunes = $palabras_comunes_es;
	}

	// Elimina palabras comunes y de una sola letra.
	$palabras_relevantes = array_diff( $palabras, $palabras_comunes );

	// Filtra elementos vacíos.
	$palabras_relevantes = array_filter(
		$palabras_relevantes,
		function ( $palabra ) {
			// Asegura la compatibilidad con PHP 5.4.
			$palabra_trim = trim( $palabra );
			return ! empty( $palabra_trim );
		}
	);

	return $palabras_relevantes;
}
