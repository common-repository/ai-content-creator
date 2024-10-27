<?php
/**
 * Proporciona los valores configurados o sus valores por defecto.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

/** Clave API de OpenAI del usuario. */
function aicc_conf_apikoai() {
	$valor   = '';
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_apikoai'] ) ) {
		$valor = $options['aicc_apikoai'];
	}
	return $valor;
}

/** Clave API de Pixabay del usuario o la genérica de la cuenta del plugin. */
function aicc_conf_apikpixabay() {
	// Si no hay clave del usuario, usa la genérica de la cuenta del plugin.
	$valor   = '35221028-0083cfdac52140b4de9729c67';
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_apikpixabay'] ) ) {
		$valor = $options['aicc_apikpixabay'];
	}
	return $valor;
}

/** URL de la API de posproducción del usuario. */
function aicc_conf_apiurlposproduccion() {
	$valor   = '';
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_apiurlposproduccion'] ) ) {
		$valor = $options['aicc_apiurlposproduccion'];
	}
	return $valor;
}

/** Clave de la API de posproducción del usuario. */
function aicc_conf_apikposproduccion() {
	$valor   = '';
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_apikposproduccion'] ) ) {
		$valor = $options['aicc_apikposproduccion'];
	}
	return $valor;
}

/** Modelo a utilizar. */
function aicc_conf_modelo() {
	$valor   = 'gpt-3.5-turbo';
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_modelo'] ) ) {
		$valor = $options['aicc_modelo'];
	}
	return $valor;
}

/** Longitud del texto en palabras. */
function aicc_conf_longitud() {
	$valor   = '500';
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_longitud'] ) ) {
		$valor = $options['aicc_longitud'];
	}
	return $valor;
}

/** Límite del tiempo en segundos para generar el artículo. */
function aicc_conf_timeout() {
	$valor   = aicc_default_timeout();
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_timeout'] ) ) {
		$valor = $options['aicc_timeout'];
	}
	return $valor;
}

/** Valor por defecto del límite del tiempo en segundos para generar el artículo.
 *  En GPT-3.5 típicamente basta con 30 segundos, en horas punta llega a pasar de 60.
 *  En GPT-4 mejor aplicar de 5 a 10 minutos, con 2 no alcanza.
 *
 * @return string Devuelve un string con el tiempo límite por omisión.
 */
function aicc_default_timeout() {
	$valor = '600';
	return $valor;
}

/** Código del idioma en que crear los artículos.
 *  El valor por omisión se determina en idiomas.php.
 */
function aicc_conf_idioma() {
	$valor   = aicc_codigo_idioma_omision();
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_idioma'] ) ) {
		$valor = $options['aicc_idioma'];
	}
	return $valor;
}

/** Cantidad máxima de miniaturas de Pixabay a mostrar para elegir. */
function aicc_conf_pixabay_cantidad() {
	$valor   = 25;
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_pixabay_cantidad'] ) ) {
		$valor = $options['aicc_pixabay_cantidad'];
	}
	return $valor;
}

/** Tono a emplear en los artículos. */
function aicc_conf_tono() {
	$valor   = '';
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_tono'] ) ) {
		$valor = $options['aicc_tono'];
	}
	return $valor;
}

/** Solicitar una tabla de contenidos en los artículos. */
function aicc_conf_tablacontenidos() {
	$valor   = 0;
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_tablacontenidos'] ) ) {
		$valor = $options['aicc_tablacontenidos'];
	}
	return $valor;
}

/** Los editores pueden crear artículos.
 *  Siendo que puede causar gastos, es configurable.
 */
function aicc_conf_editorescrean() {
	$valor   = 0;
	$options = get_option( 'aicc_settings' );
	if ( isset( $options['aicc_editorescrean'] ) ) {
		$valor = $options['aicc_editorescrean'];
	}
	return $valor;
}
