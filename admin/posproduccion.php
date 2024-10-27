<?php
/** Procesos de posproducción de los artículos creados.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

/** Devuelve una cadena indicando el nombre y versión del validador disponible.
 *
 * @return string Cadena con el nombre y versión del validador disponible.
 */
function aicc_validador_auto_actual() {
	// Cadena indicando nombre y versión del validador disponible.
	return 'Internal postproduction v0.35';
}

/** Devuelve la cantidad máxima de artículos a procesar en un solo ciclo.
 *
 * @return int Cantidad máxima de artículos a procesar en un solo ciclo.
 */
function aicc_limite_posproduccion() {
	// Cantidad máxima de artículos a procesar en un solo ciclo.
	return 100;
}

/** Devuelve un array con la lista de errores detectados en el contenido.
 *
 * @param string $contenido Contenido a validar.
 * @return array Lista de errores detectados en el contenido.
 */
function aicc_errores_validacion_contenido( $contenido ) {
	// Devuelve un array con la lista de errores detectados si los hubiera.
	$errores = array();

	/*
	// He visto respuestas que son solo repeticiones consecutivas de la pregunta, igual o cambiando el h1 por h2, quizás en temperaturas de 0.1 y 0.2 en el modelo davinci de GPT-3
	// en 0.5 baja a h3, h4, h5... y quitando los <p>
	// y hace diversos h2 con variaciones del título... sin cerrar el <p>.
	*/
	// Comprobar que contenga etiquetas HTML o será sospechoso.
	// Es requisito que tenga contenido.

	$longitud_minima = 200;

	if ( ! $contenido ) {
		$errores[] = __( 'No content generated.', 'ai-content-creator' );
	} else {
		if ( strlen( $contenido ) <= $longitud_minima ) {
			$errores[] = __( 'Generated content is unusually short.', 'ai-content-creator' );
		}
		if ( ! aicc_contiene_html( $contenido ) ) {
			$errores[] = __( 'Generated content does not appear to be an HTML document.', 'ai-content-creator' );
		}
	}

	if ( aicc_modo_debug() ) {
		echo '[modo_debug] Errores:<br>';
		echo '<pre>' . nl2br( esc_html( var_export( $errores, true ) ) ) . '</pre></hr>';
	}

	return $errores;
}

/** Devuelve un array con la lista de advertencias detectadas en el contenido.
 *
 * @param string $contenido Contenido a validar.
 * @return array Lista de advertencias detectadas en el contenido.
 */
function aicc_advertencias_validacion_contenido( $contenido ) {
	// Devuelve un array con la lista de advertencias detectadas si los hubiera.
	$advertencias = array();

	if ( aicc_menciona_recursos_externos( $contenido ) ) {
		$advertencias[] = __( 'The content mentions external resources that have not been validated. Check their functionality manually or configure an external post-production system for validation.', 'ai-content-creator' );
	}

	if ( aicc_modo_debug() ) {
		echo 'Advertencias:<br>';
		echo '<pre>' . nl2br( esc_html( var_export( $advertencias, true ) ) ) . '</pre></hr>';
	}

	return $advertencias;
}

/** Devuelve un array con la lista de notificaciones generadas en el contenido.
 *
 * @param string $contenido Contenido a validar.
 * @return array Lista de notificaciones generadas en el contenido.
 */
function aicc_notificaciones_validacion_contenido( $contenido ) {
	// Devuelve un array con la lista de notificaciones generadas si los hubiera.
	$notificaciones = array();

	if ( aicc_modo_debug() ) {
		echo 'Notificaciones:<br>';
		echo '<pre>' . nl2br( esc_html( var_export( $notificaciones, true ) ) ) . '</pre></hr>';
	}

	return $notificaciones;
}

/** Valida el contenido de un artículo automáticamente.
 *
 * @param string $contenido El contenido que se va a validar.
 * @return int Devuelve 1 si el contenido es válido, 0 en caso contrario.
 */
function aicc_validar_contenido_auto( $contenido ) {
	if ( aicc_errores_validacion_contenido( $contenido ) ) {
		$valida = 0;
	} else {
		$valida = 1;
	}

	return $valida;
}

/** Comprueba si el contenido contiene etiquetas HTML específicas.
 *
 * @param string $contenido El contenido que se va a comprobar.
 * @return bool Retorna true si el contenido contiene alguna de las etiquetas HTML especificadas, false en caso contrario.
 */
function aicc_contiene_html( $contenido ) {
	$contiene_html = false;
	$lower_content = strtolower( $contenido );

	// Verifica si el contenido contiene alguna de las etiquetas HTML especificadas.
	if (
		strpos( $lower_content, '<p>' ) !== false ||
		strpos( $lower_content, '<br>' ) !== false ||
		strpos( $lower_content, '<h1>' ) !== false ||
		strpos( $lower_content, '<h2>' ) !== false ||
		strpos( $lower_content, '<ul>' ) !== false ||
		strpos( $lower_content, '<ol>' ) !== false
	) {
		$contiene_html = true;
	}
	return $contiene_html;
}

/** Comprueba si el contenido menciona recursos externos.
 *
 * @param string $contenido El contenido que se va a verificar.
 * @return bool Devuelve true si el contenido contiene enlaces externos, false en caso contrario.
 */
function aicc_menciona_recursos_externos( $contenido ) {
	$contiene_enlaces_externos = false;
	$contenido_lower           = strtolower( $contenido );

	// Utiliza strpos() en lugar de str_contains() para la compatibilidad con versiones anteriores de PHP.
	if (
		strpos( $contenido_lower, 'http://' ) !== false ||
		strpos( $contenido_lower, 'https://' ) !== false
	) {
		$contiene_enlaces_externos = true;
	}

	return $contiene_enlaces_externos;
}

/** Procesa la respuesta de generación automática y devuelve el texto del artículo y el estado de error.
 *
 * @param object $aicc_generacion Objeto de generación automática.
 * @return array Texto del artículo y estado de error.
 */
function aicc_procesar_respuesta_auto( $aicc_generacion ) {
	// No modifica el contenido del objeto, solo devuelve dos parámetros
	// Parte de la respuesta HTTP completa para devolver el texto del artículo,
	// siendo el contenido de la sección <body> con algunos arreglos.

	$modelo        = $aicc_generacion->modelo();
	$titulo        = $aicc_generacion->titulo();
	$response_body = $aicc_generacion->response_body();

	$respuesta_mensaje = aicc_contenido_mensaje_respuesta( $modelo, $response_body );

	// Ya se podría considerar contenido el mensaje y empezar a procesarlo
	// Presuntamente sería HTML ya.
	$article_text = aicc_procesa_html_respuesta( $respuesta_mensaje, $titulo );

	if ( 'stop' !== aicc_causa_fin_respuesta( $modelo, $response_body ) ) {
		$estado_error = 1;
	}

	$estado_error = 0;

	return array( $article_text, $estado_error );
}

/** Devuelve la causa de finalización de la respuesta según el modelo y el cuerpo de respuesta.
 *
 * @param string $modelo Modelo utilizado.
 * @param array  $response_body Cuerpo de respuesta.
 * @return string Causa de finalización de la respuesta.
 */
function aicc_causa_fin_respuesta( $modelo, $response_body ) {
	$causa_fin_respuesta = '';
	if ( 'chat' === aicc_tipo_modelo( $modelo ) ) {
		if ( isset( $response_body['choices'][0]['finish_reason'] ) ) {
			$causa_fin_respuesta = $response_body['choices'][0]['finish_reason'];
		}
	} elseif ( isset( $response_body['choices'][0]['finish_reason'] ) ) {
		$causa_fin_respuesta = $response_body['choices'][0]['finish_reason'];
	}
	return $causa_fin_respuesta;
}

/** Devuelve el contenido del mensaje de respuesta según el modelo y el cuerpo de respuesta.
 *
 * @param string $modelo Modelo utilizado.
 * @param array  $respuesta_body Cuerpo de respuesta.
 * @return string Contenido del mensaje de respuesta.
 */
function aicc_contenido_mensaje_respuesta( $modelo, $respuesta_body ) {
	$respuesta_mensaje = '';
	if ( 'chat' === aicc_tipo_modelo( $modelo ) ) {
		if ( isset( $respuesta_body['choices'][0]['message']['content'] ) ) {
			$respuesta_mensaje = $respuesta_body['choices'][0]['message']['content'];
		}
	} elseif ( isset( $respuesta_body['choices'][0]['text'] ) ) {
		$respuesta_mensaje = $respuesta_body['choices'][0]['text'];
	}
	return $respuesta_mensaje;
}

/** Procesa el HTML de respuesta y devuelve el contenido del artículo.
 *
 * @param string $articulo_html HTML del artículo.
 * @param string $titulo Título del artículo.
 * @return string Contenido del artículo.
 */
function aicc_procesa_html_respuesta( $articulo_html, $titulo ) {
	// Ejemplo de respuesta <!DOCTYPE html> <html> <head> <title>Cómo hacer una receta</title> <meta charset="utf-8"> </head> <body> <h1>Cómo hacer una receta</h1>...

	// Con modelos básicos de GPT-3 era frecuente encabezar diciendo:
	// Use the following HTML:
	// Ejemplo de respuesta:
	// Ejemplo:
	// $articulo_html = preg_replace( '/^ejemplo de respuesta/i', '', $articulo_html );
	// $articulo_html = preg_replace( '/^use the following html:/i', '', $articulo_html );
	// Primeros párrafos eliminables.
	// En el siguiente ejemplo se muestra.*:
	// El siguiente ejemplo muestra.*:
	// Puede estar relacionado con el prompt.

	// Retirar el propio prompt y texto posterior.
	// Formatear índice.

	$articulo_html = trim( $articulo_html );

	// Retirar todo lo que no conviene.
	$articulo_html = aicc_sanea_articulo_html( $articulo_html, $titulo );

	// Tomar solo el contenido de la sección body.
	$articulo_html = aicc_extrae_body_respuesta( $articulo_html );

	return $articulo_html;
}

/** Extrae el valor de un atributo meta en el HTML.
 *
 * @param string $html HTML del artículo.
 * @param string $atributo Atributo del meta a extraer (keywords o description).
 * @return mixed Valor del atributo meta, null si no se encuentra o no es válido.
 */
function aicc_extrae_meta_etiqueta( $html, $atributo ) {
	$meta = array();

	if ( ! in_array( $atributo, array( 'keywords', 'description' ), true ) ) {
		return null;
	}

	$pattern = sprintf( '/<meta\s+name=["\']%s["\']\s+content=["\']([^"\']*)["\']/i', $atributo );
	if ( preg_match_all( $pattern, $html, $matches ) ) {
		$meta = $matches[1];
	}

	if ( 'keywords' === $atributo ) {
		return implode( ',', $meta );
	} elseif ( 'description' === $atributo ) {
		return ! empty( $meta ) ? $meta[0] : null;
	}

	return null;
}

/** Genera metakeywords improvisadas a partir del título y el idioma.
 *
 * @param string $titulo Título del artículo.
 * @param string $idioma Idioma del artículo.
 * @return string Metakeywords improvisadas.
 */
function aicc_improvisa_meta_keywords( $titulo, $idioma ) {
	// Elimina los espacios en blanco al principio y al final del string.
	$titulo = trim( $titulo );

	// Convierte el título en minúsculas.
	$titulo = strtolower( $titulo );

	// Elimina los caracteres especiales y números.
	$titulo = preg_replace( '/[^a-zàáèéíòóúüñ\s]/', '', $titulo );

	// Divide el título en un array de palabras utilizando espacios como separador.
	$palabras = explode( ' ', $titulo );

	// Limpia las palabras utilizando la función aicc_elimina_stopwords.
	$palabras_relevantes = aicc_elimina_stopwords( $palabras, $idioma );

	// Une las palabras relevantes con comas como concatenador para formar metakeywords.
	$metakeywords = implode( ', ', $palabras_relevantes );

	return $metakeywords;
}

/** Extrae el contenido de la sección <body> del HTML del artículo.
 *
 * @param string $articulo_html HTML del artículo.
 * @return string Contenido de la sección <body>.
 */
function aicc_extrae_body_respuesta( $articulo_html ) {
	// Ejemplo de respuesta <!DOCTYPE html> <html> <head> <title>Cómo hacer una receta</title> <meta charset="utf-8"> </head> <body> <h1>Cómo hacer una receta</h1>...

	$regex         = preg_quote( '<!DOCTYPE html>', '/' );
	$articulo_html = preg_replace( '/^.*' . $regex . '/is', '', $articulo_html, 1 );

	$regex         = preg_quote( '<body>', '/' );
	$articulo_html = preg_replace( '/^.*' . $regex . '/is', '', $articulo_html, 1 );
	// Ojo con la secuencia * y /, finaliza comentarios php, por eso la fracciono forzando el "$".
	$regex         = preg_quote( '</body>', '/' );
	$articulo_html = preg_replace( '/' . $regex . '.*$/is', '', $articulo_html );
	$articulo_html = trim( $articulo_html );
	return $articulo_html;
}

/** Sanea y concentra las partes relevantes del artículo, descartando las que no.
 *
 * @param string $articulo_html HTML del artículo.
 * @param string $titulo Título del artículo.
 * @return string Artículo saneado y concentrado.
 */
function aicc_sanea_articulo_html( $articulo_html, $titulo ) {
	// Concentra las partes que convienen del artículo y descarta las que no.

	// Operaciones de limpieza de cabecera y pie del artículo.
	$articulo_html = aicc_retira_titulo_html( $articulo_html, $titulo );
	$articulo_html = aicc_retira_seccion_header_vacia_html( $articulo_html );

	/*
	 * La sección footer podría contener simplemente una conclusión. De momento se mantiene.
	 * $articulo_html = aicc_retira_seccion_footer_html ( $articulo_html );
	 */
	$articulo_html = aicc_retira_bloque_copyright_html( $articulo_html );

	// Retirada de etiquetas indeseadas por seguridad.
	$articulo_html = wp_kses_post( $articulo_html );

	$articulo_html = trim( $articulo_html );

	return $articulo_html;
}

/** Retira el título H1 del HTML del artículo para evitar duplicidad.
 *
 * @param string $articulo_html HTML del artículo.
 * @param string $titulo Título del artículo.
 * @return string Artículo HTML sin título H1.
 */
function aicc_retira_titulo_html( $articulo_html, $titulo ) {

	// Quita el título H1 para evitar que se forme un duplicado en el borrador con el título declarado.
	$regex         = preg_quote( "<h1>$titulo</h1>", '/' );
	$articulo_html = preg_replace( '/' . $regex . '/i', '', $articulo_html, 1 );

	// Quita el título inicial aun sin H1, pese a ser indicador de no haber creado página HTML
	// para evitar que se forme un duplicado en el borrador con el título declarado.
	$regex         = preg_quote( "$titulo", '/' );
	$articulo_html = preg_replace( '/^' . $regex . '/i', '', $articulo_html, 1 );

	return $articulo_html;
}

/** Retira la sección <header> vacía del HTML del artículo.
 *
 * @param string $articulo_html HTML del artículo.
 * @return string Artículo HTML sin sección <header> vacía.
 */
function aicc_retira_seccion_header_vacia_html( $articulo_html ) {
	// Quita una posible sección <header> que haya quedado vacía.
	$pattern       = '/^\s*<header>\s*<\/header>/im';
	$articulo_html = preg_replace( $pattern, '', $articulo_html, 1 );
	return $articulo_html;
}

/** Retira la sección <footer> y su contenido del HTML del artículo.
 *
 * @param string $articulo_html HTML del artículo.
 * @return string Artículo HTML sin sección <footer>.
 */
function aicc_retira_seccion_footer_html( $articulo_html ) {
	// Quita una posible sección <footer> con su contenido, por ejemplo, "\t<footer>\n\t\t<p>Copyright Â© 2021. Todos los derechos reservados.</p>\n\t</footer>"
	// En ocasiones podría contener simplemente una conclusión.
	$pattern       = '/\s*<footer>.*<\/footer>/is';
	$articulo_html = preg_replace( $pattern, '', $articulo_html, 1 );
	return $articulo_html;
}

/** Retira el bloque de copyright del HTML del artículo.
 *
 * @param string $articulo_html HTML del artículo.
 * @return string Artículo HTML sin bloque de copyright.
 */
function aicc_retira_bloque_copyright_html( $articulo_html ) {
	// Quita una posible sección <footer> o si menciona 'copyright' 'Â©' o '©'</footer>".
	$pattern       = '/\s*<footer>.*(copyright|Â©|©|&copy;).*<\/footer>/ism';
	$articulo_html = preg_replace( $pattern, '', $articulo_html, 1 );

	// Retira párrafo de copyright.
	$pattern       = '/<p>(?=.*copyright.*)(?=.*\x{00A9}.*)(?=.*&copy;.*)<\/p>\s*$/iu';
	$articulo_html = preg_replace( $pattern, '', $articulo_html );

	// Puede haber caché del iframe...
	return $articulo_html;
}

/** Código de un dashicon con el tooltip que pueda proceder.
 * Clonada del metodo dashicon_html de la clase AIccGeneracion.
 *
 * @param string $dashicon Código del dashicon.
 * @param string $titulo Atributo title para formar un tooltip.
 * @param string $estilo CSS opcional para el atributo style.
 * @return string HTML con el código.
 */
function aicc_dashicon_html( $dashicon, $titulo, $estilo = '' ) {
	$html = '<span class="dashicons ' . esc_attr( $dashicon ) . '"';
	if ( $titulo ) {
		$html .= ' title="' . esc_attr( $titulo ) . '"';
	}
	if ( $estilo ) {
		$html .= ' style="' . esc_attr( $estilo ) . '"';
	}
	$html .= '></span>';
	return $html;
}
