<?php
/** Gestión de los idiomas habilitados al configurar para la salida de la IA.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

/** Códigos ISO 639 de los idiomas y sus correspondientes strings de nombre de idioma nativo. */
function aicc_idiomas() {
	$idiomas = array(
		'en'  => 'English',
		'es'  => 'Español',
		'pt'  => 'Português',
		'fr'  => 'Français',
		'de'  => 'Deutsch',
		'ru'  => 'русский',
		'ja'  => '日本語',
		'it'  => 'Italiano',
		'zh'  => '中文',
		'nl'  => 'Nederlands',
		'ko'  => '한국어',
		'ar'  => 'العربية',
		'tr'  => 'Türkçe',
		'pl'  => 'Polski',
		'uk'  => 'українська',
		'fa'  => 'فارسی',
		'sv'  => 'Svenska',
		'cs'  => 'čeština',
		'da'  => 'Dansk',
		'fi'  => 'Suomi',
		'hu'  => 'Magyar',
		'ca'  => 'Català',
		'eu'  => 'Euskara',
		'gl'  => 'Galego',
		'ast' => 'Asturianu',
	);
	return $idiomas;
}

/** Códigos ISO 639 de los idiomas y sus correspondientes strings de nombre de idioma traducido al local. */
function aicc_idiomas_traducidos() {
	$languages = array(
		'en'  => __( 'English', 'ai-content-creator' ),
		'es'  => __( 'Spanish', 'ai-content-creator' ),
		'pt'  => __( 'Portuguese', 'ai-content-creator' ),
		'fr'  => __( 'French', 'ai-content-creator' ),
		'de'  => __( 'German', 'ai-content-creator' ),
		'ru'  => __( 'Russian', 'ai-content-creator' ),
		'ja'  => __( 'Japanese', 'ai-content-creator' ),
		'it'  => __( 'Italian', 'ai-content-creator' ),
		'zh'  => __( 'Chinese', 'ai-content-creator' ),
		'nl'  => __( 'Dutch', 'ai-content-creator' ),
		'ko'  => __( 'Korean', 'ai-content-creator' ),
		'ar'  => __( 'Arabic', 'ai-content-creator' ),
		'tr'  => __( 'Turkish', 'ai-content-creator' ),
		'pl'  => __( 'Polish', 'ai-content-creator' ),
		'uk'  => __( 'Ukrainian', 'ai-content-creator' ),
		'fa'  => __( 'Persian', 'ai-content-creator' ),
		'sv'  => __( 'Swedish', 'ai-content-creator' ),
		'cs'  => __( 'Czech', 'ai-content-creator' ),
		'da'  => __( 'Danish', 'ai-content-creator' ),
		'fi'  => __( 'Finnish', 'ai-content-creator' ),
		'hu'  => __( 'Hungarian', 'ai-content-creator' ),
		'ca'  => __( 'Catalan', 'ai-content-creator' ),
		'eu'  => __( 'Basque', 'ai-content-creator' ),
		'gl'  => __( 'Galician', 'ai-content-creator' ),
		'ast' => __( 'Asturian', 'ai-content-creator' ),
	);
	return $languages;
}

/** Devuelve un booleano indicando si el idioma es correcto o no.
 *
 * @param string $idioma Código de idioma a comprobar.
 * @return bool Verdadero si existe el código de idioma proporcionado.
 */
function aicc_es_valido_idioma( $idioma ) {
	return array_key_exists( $idioma, aicc_idiomas() );
}

/**
 * Obtiene el código de idioma actual de WordPress.
 *
 * Esta función devuelve el código de idioma actual de la configuración de WordPress.
 * Soporta códigos ISO 639-1 (dos letras), así como ISO 639-2 y 639-3 (tres letras).
 *
 * @return string El código de idioma actual.
 */
function aicc_codigo_idioma_actual() {
	// Obtiene el locale actual de WordPress.
	$locale = get_locale();

	// Verifica si el locale contiene un guion bajo (_), que separa el código de idioma del código de país.
	$pos = strpos( $locale, '_' );

	// Si se encuentra un guion bajo, extrae el código de idioma antes del guion bajo.
	if ( false !== $pos ) {
		$codigo_idioma = substr( $locale, 0, $pos );
	} else {
		// Si no se encuentra un guion bajo, el locale completo es el código de idioma.
		$codigo_idioma = $locale;
	}

	return $codigo_idioma;
}

/** Devuelve el nombre del idioma del WP. */
function aicc_nombre_idioma_actual() {
	return aicc_nombre_idioma( aicc_codigo_idioma_actual() );
}

/** Devuelve el nombre nativo de un idioma basado en su código ISO 639.
 *
 * @param string $codigo El código de idioma.
 * @return string El nombre del código de idioma indicado en su lenguaje nativo.
 */
function aicc_nombre_idioma( $codigo ) {
	$idiomas = aicc_idiomas();
	return $idiomas[ $codigo ];
}

/** Devuelve el nombre nativo de un idioma basado en su código ISO 639.
 *
 * @param string $codigo El código de idioma.
 * @return string El nombre del código de idioma indicado en el lenguaje actual de WP.
 */
function aicc_nombre_idioma_traducido( $codigo ) {
	$idiomas = aicc_idiomas_traducidos();
	return $idiomas[ $codigo ];
}

/** Verifica si un idioma está en la lista de idiomas soportados por el plugin y devuelve su código ISO 639
 * correspondiente al idioma si está en la lista, o una cadena vacía si no lo está.
 *
 * @param string $idioma Nombre nativo del idioma.
 * @return string El código de idioma si se soporta o vacío si no.
 */
function aicc_codigo_idioma( $idioma ) {
	// Invierte la lista de idiomas, así es fácil comprobar si se soporta.
	$idiomas_soportados = aicc_intercambiar_claves_por_valores( aicc_idiomas() );

	// Verifica si el idioma está en la lista.
	if ( isset( $idiomas_soportados[ $idioma ] ) ) {
		return $idiomas_soportados[ $idioma ];
	} else {
		return '';
	}
}

/** Devuelve el código del idioma al que se traducirá por omision.
 * Si el idioma actual no está soportado, será el inglés por omisión.
 */
function aicc_codigo_idioma_omision() {
	$codigo_idioma = aicc_codigo_idioma_actual();
	if ( ! aicc_nombre_idioma( $codigo_idioma ) ) {
		$codigo_idioma = 'en';
	}
	return $codigo_idioma;
}

/** Intercambia las claves por los valores en un array.
 *
 * @param array $array Matriz asociativa a invertir.
 * @return array Matriz asociativa invertida.
 */
function aicc_intercambiar_claves_por_valores( $array ) {
	$nuevo_array = array();
	foreach ( $array as $clave => $valor ) {
		$nuevo_array[ $valor ] = $clave;
	}
	return $nuevo_array;
}

/** Errores conocidos de la API que pueden traducirse como cadenas fijas.
 *
 * @param string $error Mensaje de error a tratar de traducir.
 * @return string Mensaje de error traducido o el original si no se pudo traducir.
 */
function aicc_traduce_error_externo( $error ) {
	if ( preg_match( '/^Incorrect API key provided: (.*). You can find your API key at https:\/\/platform\.openai\.com\/account\/api-keys\./', $error ) ) {
		$error  = __( 'Incorrect API key provided.', 'ai-content-creator' );
		$error .= ' ';
		/* translators: %s: URL of OpenAI site to get the API keys. */
		$error .= sprintf( __( 'You can find your API key at %s.', 'ai-content-creator' ), 'https://platform.openai.com/account/api-keys' );
	} elseif ( preg_match( '/Please reduce your prompt; or completion length\.$/', $error ) ) {
		$error = __( 'The sum of tokens in the request and response may exceed the maximum allowed for the model. Please reduce the text in your request.', 'ai-content-creator' );
	} elseif ( 'You didn\'t provide an API key. You need to provide your API key in an Authorization header using Bearer auth (i.e. Authorization: Bearer YOUR_KEY), or as the password field (with blank username) if you\'re accessing the API from your browser and are prompted for a username and password. You can obtain an API key from https://platform.openai.com/account/api-keys.' === $error ) {
		$error = __( 'You didn\'t provide an API key. You need to provide your API key in an Authorization header using Bearer auth (i.e. Authorization: Bearer YOUR_KEY), or as the password field (with blank username) if you\'re accessing the API from your browser and are prompted for a username and password. You can obtain an API key from https://platform.openai.com/account/api-keys.', 'ai-content-creator' );
	} elseif ( 'That model does not exist' === $error ) {
		// Se produce también al tratar de usar gpt-4 durante la beta sin tenerlo autorizado.
		$error = __( 'That model does not exist', 'ai-content-creator' );
	} elseif ( 'you must provide a model parameter' === $error ) {
		$error = __( 'you must provide a model parameter', 'ai-content-creator' );
	} elseif ( preg_match( '/^Invalid URL/', $error ) ) {
		$error = __( 'Invalid URL address.', 'ai-content-creator' );
	} elseif ( preg_match( '/^This is a chat model and not supported in the/', $error ) ) {
		$error = __( 'The required model is not supported at this URL address.', 'ai-content-creator' );
	}

	// This model's maximum context length is 4097 tokens, however you requested 4207 tokens (207 in your prompt; 4000 for the completion). Please reduce your prompt; or completion length.

	return $error;
}
