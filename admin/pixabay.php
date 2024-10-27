<?php
/**
 * Toma imágenes de Pixabay.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

/** Compone la URL sw búsqueda de imágenes en Pixabay.
 *
 * @param string $metakeywords Lista obtenida de las meta Keywords, que es separada por comas.
 * @param string $idioma idioma en que hacer la consulta.
 * @return string con la dirección URL.
 */
function aicc_url_busqueda_pixabay( $metakeywords, $idioma ) {
	// Las keywords, para hacer un OR, han de separarse por "|". No hay fuente, fue inspiración divina.
	$keywords = aicc_forma_keywords_busqueda_pixabay( $metakeywords );
	// Componemos la URL de llamada a la API de Pixabay con las palabras clave y opciones necesarias.
	$query_args  = array(
		'key'         => aicc_conf_apikpixabay(),
		'q'           => rawurlencode( $keywords ),
		'lang'        => substr( $idioma, 0, 2 ),
		'orientation' => 'horizontal',
		'min_width'   => '1024',
		'min_height'  => '512',
		'safesearch'  => 'true',
		'order'       => 'popular',
		'pretty'      => 'false', // Temporalmente true para desarrollo.
	);
	$pixabay_url = add_query_arg( $query_args, 'https://pixabay.com/api/' );

	return $pixabay_url;
}

/** Reformatea las keywords de la maetaetiqueta a algo buscable separado por símbolos "|".
 *
 * @param string $metakeywords Lista obtenida de las meta Keywords, que es separada por comas.
 * @return string con la cadena buscable.
 */
function aicc_forma_keywords_busqueda_pixabay( $metakeywords ) {
	// Elimina los espacios en blanco al principio y al final del string.
	$metakeywords = trim( $metakeywords );

	// Divide el string en un array de palabras clave utilizando comas como separador.
	$keywords_array = explode( ',', $metakeywords );

	// Itera sobre el array de palabras clave y elimina los espacios sobrantes.
	$keywords_array = array_map( 'trim', $keywords_array );

	// Une las palabras clave con el símbolo "|" como concatenador.
	$formatted_keywords = implode( '|', $keywords_array );

	return $formatted_keywords;
}


/** Gestiona la búsqueda de imagen para un artículo y claves dadas.
 * Requerirá también $consulta, $idioma para localizar en la caché la imagen
 *
 * @param string $id Artículo.
 * @param string $keywords Búsqueda.
 */
function aicc_seleccionar_imagen( $id, $keywords ) {
	$idioma   = aicc_conf_idioma();
	$cantidad = aicc_conf_pixabay_cantidad();
	echo '<h3>' . esc_html( __( 'Featured image selection', 'ai-content-creator' ) ) . "</h3>\n";
	echo '<p>';
	esc_html_e( 'Use the search engine to view images and select one or select', 'ai-content-creator' );
	echo ' <em>';
	esc_html_e( 'Without image', 'ai-content-creator' );
	echo '</em>';
	echo ".</p>\n";

	echo '<form method="get" action="admin.php">' . "\n";
	echo '	<input type="hidden" name="page" value="aicc_articles">' . "\n";
	echo '	<input type="hidden" name="accion" value="imagen">' . "\n";
	echo '	<input type="hidden" name="id" value="' . esc_attr( $id ) . '">' . "\n";

	// Si no se han recibido keywords, se toman por el objeto.
	if ( ! $keywords ) {
		$aicc_generacion = new AIccGeneracion();
		if ( $aicc_generacion->cargar( $id ) ) {
			$keywords = $aicc_generacion->meta_keywords();
			if ( ! $keywords ) {
				$keywords = aicc_improvisa_meta_keywords( $aicc_generacion->titulo(), $aicc_generacion->idioma() );
			}
		} else {
			aicc_mostrar_notificacion( __( 'Unable to load the specified article.', 'ai-content-creator' ), 'error' );
		}
	}

	echo '	<div style="text-align:center;">' . "\n";
	echo '		<input type="text"';
	echo ' style="width:300px; margin:8px;"';
	echo ' name="keywords"';
	echo ' value="';
	echo esc_attr( $keywords );
	echo '">' . "\n";
	echo "	<br>\n";

	$aicc_generacion = new AIccGeneracion();
	echo wp_kses_post( $aicc_generacion->boton_formulario_operacion_html( __( 'Search for images', 'ai-content-creator' ), 'buscar', 'dashicons-format-image' ) );

	echo "	</div>\n";
	echo "</form>\n";

	echo "<br>\n";
	echo "<hr>\n";

	// Muestra la selección de imágenes.
	if ( $keywords ) {
		aicc_mostrar_miniaturas_pixabay( $id, $keywords, $idioma, $cantidad );
	}
}

/** Muestra las miniaturas de imágenes de Pixabay.
 *
 * @param string  $id Artículo.
 * @param string  $consulta Texto de la consulta enviada.
 * @param string  $idioma Idioma en que hacer la consulta.
 * @param integer $cantidad Máximo de imágenes obtenidas por cada consulta.
 * @return array con las respuestas de Pixabay o un error.
 */
function aicc_mostrar_miniaturas_pixabay( $id, $consulta, $idioma, $cantidad = 25 ) {

	// Obtiene los resultados de la búsqueda en Pixabay.
	$resultados = aicc_busqueda_pixabay( $consulta, $idioma );

	// Si hay un error, muestra un mensaje y termina la función.
	if ( isset( $resultados['error'] ) ) {
		echo '<p>' . esc_html( $resultados['message'] ) . '</p>';
		return;
	}

	// Muestra las miniaturas de las imágenes.
	echo '<div class="aicc-miniaturas-contenedor aicc-flex">' . "\n";

	// Agrega un elemento "Sin imagen" al comienzo de la lista.
	aicc_mostrar_miniatura_pixabay( $id );

	$contador = 0;
	// La cantidad de imágenes máxima se puede obtener de count( $resultados['hits'] ).
	foreach ( $resultados['hits'] as $imagen ) {
		// Si se alcanza la cantidad predeterminada de miniaturas, detiene el bucle.
		if ( $contador >= $cantidad ) {
			break;
		}

		// Muestra la miniatura y el formulario para seleccionar la imagen.
		aicc_mostrar_miniatura_pixabay( $id, $imagen['tags'], $imagen['previewURL'], $imagen['largeImageURL'] );

		$contador++;
	}

	echo "</div>\n";

	// Incluir mención a que las imágenes provienen de Pixabay.
	echo '<p>' . esc_html( __( 'Images provided by', 'ai-content-creator' ) ) . ' <a href="https://pixabay.com/" target="_blank" rel="noreferrer noopener">Pixabay</a>.</p>';
}

/**
 * Muestra una miniatura de la imagen de Pixabay junto con un botón de selección.
 *
 * La función genera la estructura HTML necesaria para mostrar la miniatura de la imagen de Pixabay,
 * así como un botón de selección para elegir la imagen como imagen destacada en un artículo.
 * Si no se proporcionan las URL de la imagen y la miniatura, se mostrará un mensaje de error o un mensaje
 * que indica que no hay imagen.
 *
 * @param string $id            ID de la imagen de Pixabay.
 * @param string $tags          Etiquetas asociadas a la imagen, se utilizarán como atributo 'alt' y 'title'.
 * @param string $url_miniatura URL de la miniatura de la imagen.
 * @param string $url_imagen    URL de la imagen en tamaño completo.
 */
function aicc_mostrar_miniatura_pixabay( $id, $tags = '', $url_miniatura = '', $url_imagen = '' ) {
	echo '	<div class="aicc-miniatura">' . "\n";

	// Si falta solo una de las dos URL, es que algo anda mal con la imagen.
	if ( ( '' === $url_miniatura ) ^ ( '' === $url_imagen ) ) {
		echo '		<div style="width:150px;height:100px;background-color:white;font-weight:bold;display: flex; justify-content: center; align-items: center;margin-bottom:3px;">' . "\n";
		echo '			<p>' . esc_html( __( 'Image failed', 'ai-content-creator' ) ) . "</p>\n";
		echo "		</div>\n";
	} else {
		// Muestra la imagen o un cartel de "Sin imagen" en función de si se ha indcado URL o no.
		if ( $url_miniatura ) {
			echo '		<img ';
			echo ' style="margin-bottom:3px;"';
			echo ' src="' . esc_url( $url_miniatura ) . '"';
			echo ' alt="' . esc_attr( $tags ) . '"';
			// Al contrario que el alt, el título se indica solamente si hay valor.
			if ( $tags ) {
				echo ' title="' . esc_attr( $tags ) . '"';
			}
			echo ">\n";
		} else {
			echo '		<div style="width:150px;height:100px;background-color:white;font-weight:bold;display: flex; justify-content: center; align-items: center;margin-bottom:3px;">' . "\n";
			echo '			<p>' . esc_html( __( 'Without image', 'ai-content-creator' ) ) . "</p>\n";
			echo "		</div>\n";
		}

		echo '		<form action="' . esc_url( admin_url( 'admin.php' ) ) . '" method="get">' . "\n";
		echo '			<input type="hidden" name="page" value="aicc_articles">' . "\n";
		echo '			<input type="hidden" name="id" value="' . esc_attr( $id ) . '">' . "\n";

		// Ausencia del prámetro para el caso de seleccionar que no tenga imagen. Si se envíase el parámetro, ha de ser con un valor válido.
		if ( $url_imagen ) {
			echo '			<input type="hidden" name="imagen_url" value="' . esc_attr( esc_url( $url_imagen ) ) . '">' . "\n";
		}

		echo '			<input type="hidden" name="accion" value="imagen_seleccionada">' . "\n";
		echo '			<button type="submit">' . esc_html( __( 'Select', 'ai-content-creator' ) ) . "</button>\n";
		echo "		</form>\n";
	}

	echo "	</div>\n";
}

/** Realiza la búsqueda de imágenes en Pixabay, haciendo caché de 24 horas.
 *
 * @param string $consulta Texto de la consulta enviada.
 * @param string $idioma idioma en que hacer la consulta.
 * @return array con las respuestas de Pixabay o un error.
 */
function aicc_busqueda_pixabay( $consulta, $idioma ) {
	global $wpdb;

	$tabla = aicc_tabla_pixabay_cache();

	// Elimina las consultas antiguas (mayores a 24 horas).
	$result = aicc_borra_viejos_registros_cache( $tabla );

	// Busca la consulta en la tabla caché, por requisito de Pixabay de cachear las consultas 24 horas.
	// A su vez, cachea esa consulta media hora.

	// Clave de caché basada en la consulta y el idioma.
	$cache_key = 'aicc_cache_' . md5( $consulta . $idioma );

	// Intenta obtener el resultado desde la caché de WP.
	$cache_result = wp_cache_get( $cache_key );

	// Si no hay un resultado en la caché de WP, realiza la consulta a la base de datos y almacena el resultado en la caché de WP.
	if ( false === $cache_result ) {
		// Inevitable por ser tablas ajenas a WP la advertencia:
		// WARNING | [ ] Usage of a direct database call is discouraged.
		// ERROR   | Use placeholders and $wpdb->prepare(); found interpolated variable $tabla...
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$cache_result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tabla} WHERE consulta = %s AND idioma = %s", $consulta, $idioma ) );

		// Almacena el resultado en la caché con una expiración de media hora (1800 segundos).
		wp_cache_set( $cache_key, $cache_result, '', 1800 );
	}

	// Si la consulta está en caché, devuelve la respuesta almacenada.
	if ( $cache_result ) {
		return json_decode( $cache_result->respuesta, true );
	}

	// Si no está en caché, realiza la consulta a la API de Pixabay.
	$url = aicc_url_busqueda_pixabay( $consulta, $idioma );

	$response = wp_remote_get( $url );

	// Si la consulta a la API es exitosa, almacena la respuesta en la caché y la devuelve.
	if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
		$respuesta_json = wp_remote_retrieve_body( $response );

		// Inserta la respuesta en la tabla de caché.
		// Inevitable por ser tablas ajenas a WP la advertencia:
		// WARNING | [ ] Usage of a direct database call is discouraged.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$tabla,
			array(
				'consulta'  => $consulta,
				'idioma'    => $idioma,
				'respuesta' => $respuesta_json,
				'creado_en' => current_time( 'mysql', true ),
			),
			array( '%s', '%s', '%s', '%s' )
		);

		return json_decode( $respuesta_json, true );
	}

	// Si la consulta a la API no tuvo éxito, devuelve un error.
	return array(
		'error'   => true,
		'message' => __( 'Pixabay API query failed.' ),
	);
}

/** Borra registros de la tabla de caché con más de 24 horas de antigüedad.
 *
 * @param string $tabla Nombre de la tabla personalizada sin el prefijo de WordPress.
 */
function aicc_borra_viejos_registros_cache( $tabla ) {
	global $wpdb;

	// Calcular la fecha y hora de hace 24 horas en UTC.
	$fecha_limite = gmdate( 'Y-m-d H:i:s', strtotime( '-24 hours' ) );

	// Borrar los registros con una fecha anterior a $fecha_limite.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$tabla} WHERE creado_en < %s", $fecha_limite ) );
}

/** Sube la imagen a la biblioteca de medios y guarda su id en el objeto del artículo
 * para que llegue a la BBDD y se pueda incorporar el post al crear el borrador
 *
 * @param string $articulo_id Artículo.
 * @param string $imagen_url  Dirección de la imagen que hay que subir a la biblioteca de medios.
 * @return int|false ID de la imagen subida a la biblioteca de medios, cero si no la hay, false si algo falló.
 */
function aicc_procesar_seleccion_imagen( $articulo_id, $imagen_url ) {
	$imagen_id = '';

	// Graba en la BBDD el id de la imagen seleccionada.
	$aicc_generacion = new AIccGeneracion();
	if ( $aicc_generacion->cargar( $articulo_id ) ) {

		// Sube la imagensi procede.
		if ( '' === $imagen_url ) {
			// No hay imagen que subir, queda con id 0.
			$imagen_id = 0;
		} else {
			// Sube la imagen y obtiene el ID.
			$imagen_id = aicc_upload_image_from_url( $imagen_url, $aicc_generacion->titulo(), $aicc_generacion->idioma() );
		}

		// Graba el id de la imagen tanto si subió como si se decidió no usar ninguna.
		if ( false !== $imagen_id ) {
			// Si el artículo ya tiene borrador o está publicado, es tarde para cambiar la imagen.
			if ( ! $aicc_generacion->publicada_post_id() ) {
				// Si tiene una imagen previa, la elimina.
				aicc_eliminar_imagen_biblioteca( $aicc_generacion->imagen_id() );
				// Asigna la nueva imagen.
				$aicc_generacion->imagen_id( $imagen_id );
				if ( ! $aicc_generacion->grabar() ) {
					aicc_mostrar_notificacion( __( 'Unable to save the specified article image data.', 'ai-content-creator' ), 'error' );
					return false;
				}
			} else {
				aicc_mostrar_notificacion( __( 'Unable to change the image of a publised article.', 'ai-content-creator' ), 'error' );
			}
		} else {
			// Fallo al grabar la imagen en la biblioteca de medios.
			aicc_mostrar_notificacion( __( 'Unable to save the image for the specified article.', 'ai-content-creator' ), 'error' );
			return false;
		}
	} else {
		// Fallo al cargar el artículo.
		aicc_mostrar_notificacion( __( 'Unable to load the specified article.', 'ai-content-creator' ), 'error' );
		return false;
	}

	return $imagen_id;
}

/** Sube una imagen desde una URL a la biblioteca de medios de WordPress.
 *
 * Esta función descarga una imagen de una URL dada, la sube a la biblioteca de medios de
 * WordPress y devuelve su ID de adjunto. Si ocurre algún error durante el proceso de
 * descarga o subida, la función devuelve false.
 *
 * @param string $imagen_url      La URL de la imagen que se desea subir.
 * @param string $titulo_articulo Título del artículo para aplicar en los metadatos de la imagen en la biblioteca de medios a nivel SEO.
 * @param string $idioma         Idioma en que está el título del artículo.
 * @return int|false              El ID de adjunto de la imagen subida, o false en caso de error.
 */
function aicc_upload_image_from_url( $imagen_url, $titulo_articulo, $idioma ) {
	if ( ! function_exists( 'wp_upload_bits' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	// Obtiene la imagen como cadena binaria.
	$response = wp_remote_get( $imagen_url );

	if ( is_wp_error( $response ) ) {
		return false;
	}
	$image_data = wp_remote_retrieve_body( $response );

	// Obtiene el nombre del archivo y su extensión.
	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $imagen_url, $matches );
	$file_extension = pathinfo( $matches[0], PATHINFO_EXTENSION );

	// Crea el nombre de archivo SEO amigable.
	$seo_friendly_filename = aicc_seo_nombre_archivo_amigable( $titulo_articulo, $idioma );
	$file_name             = $seo_friendly_filename . '.' . $file_extension;

	// Sube la imagen y guarda la información en la base de datos.
	$upload_result = wp_upload_bits( $file_name, null, $image_data );

	if ( ! empty( $upload_result['error'] ) ) {
		return false;
	}

	// Registra el archivo en la base de datos como adjunto.
	$file_path = $upload_result['file'];
	$file_url  = $upload_result['url'];
	$file_type = wp_check_filetype( $file_path );

	$attachment = array(
		'guid'           => $file_url,
		'post_mime_type' => $file_type['type'],
		'post_title'     => $titulo_articulo,
		'post_content'   => $titulo_articulo,
		'post_status'    => 'inherit',
	);

	$imagen_id = wp_insert_attachment( $attachment, $file_path );

	if ( ! is_wp_error( $imagen_id ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$metadata = wp_generate_attachment_metadata( $imagen_id, $file_path );
		wp_update_attachment_metadata( $imagen_id, $metadata );
		// Incorpora el texto alternativo a la imagen.
		update_post_meta( $imagen_id, '_wp_attachment_image_alt', $titulo_articulo );
	} else {
		return false;
	}

	return $imagen_id;
}

/** Elimina una imagen de la biblioteca de medios de WordPress dado su ID.
 *
 * @param int $imagen_id El ID de la imagen que se va a eliminar.
 * @return bool Devuelve true si la eliminación fue exitosa, false si falló.
 */
function aicc_eliminar_imagen_biblioteca( $imagen_id ) {
	// Verifica si el ID de la imagen es válido y si corresponde a un archivo adjunto.
	if ( ! empty( $imagen_id ) && get_post_type( $imagen_id ) === 'attachment' ) {

		// Solo la intenta eliminar si no está en uso.
		if ( ! aicc_imagen_biblioteca_en_uso( $imagen_id ) ) {
			// Intenta eliminar la imagen de la biblioteca de medios.
			$eliminado = wp_delete_attachment( $imagen_id, true );
		} else {
			aicc_mostrar_notificacion( __( 'Old image usage detected, it will not be deleted.', 'ai-content-creator' ), 'warning' );
			$eliminado = false;
		}

		// Si se eliminó correctamente, devuelve true, de lo contrario, devuelve false.
		return false !== $eliminado;
	}

	// Si el ID no es válido o no corresponde a un archivo adjunto, devuelve false.
	return false;
}

/**
 * Comprueba si una imagen de la biblioteca de medios está en uso como imagen destacada o en otro contexto.
 *
 * @param int $imagen_id El ID de la imagen en la biblioteca de medios.
 * @return bool Devuelve true si la imagen está en uso, false en caso contrario.
 *
 * @phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
 */
function aicc_imagen_biblioteca_en_uso( $imagen_id ) {
	// Comprobar si la imagen se utiliza como imagen destacada en algún post.
	$args = array(
		'post_type'      => 'any',
		'posts_per_page' => 1,
		'meta_query'     => array(
			array(
				'key'   => '_thumbnail_id',
				'value' => $imagen_id,
			),
		),
	);

	$query = new WP_Query( $args );

	if ( $query->found_posts > 0 ) {
		return true;
	}

	// Comprobar si la imagen se utiliza en el contenido de algún post.
	$image_url = wp_get_attachment_url( $imagen_id );
	$args      = array(
		'post_type'      => 'any',
		'posts_per_page' => 1,
		's'              => $image_url,
	);

	$query = new WP_Query( $args );

	if ( $query->found_posts > 0 ) {
		return true;
	}

	// Si no se encuentra la imagen en uso, devuelve false.
	return false;
}

/**
 * Genera un nombre de archivo SEO amigable a partir de un título.
 *
 * Esta función convierte el título dado a un nombre de archivo SEO amigable,
 * reemplazando espacios y caracteres no alfanuméricos por guiones, eliminando
 * guiones múltiples y recortando guiones al principio y al final. Además,
 * limita la longitud del nombre del archivo al valor especificado por
 * $max_longitud sin truncar palabras.
 *
 * @param string $titulo       Título del cual se generará el nombre de archivo.
 * @param string $idioma      Idioma en que está el título del artículo.
 * @param int    $max_longitud Longitud máxima del nombre de archivo (opcional, por defecto 100).
 * @return string              Nombre de archivo SEO amigable generado a partir del título.
 */
function aicc_seo_nombre_archivo_amigable( $titulo, $idioma, $max_longitud = 100 ) {
	$nombre_archivo = strtolower( $titulo );
	$nombre_archivo = preg_replace( '/[^a-z0-9]+/', '-', $nombre_archivo );
	$nombre_archivo = preg_replace( '/-+/', '-', $nombre_archivo );
	$nombre_archivo = trim( $nombre_archivo, '-' );

	// Retira las stopwords. Para ello, ha de convertir la cadena en una matriz de palabras.
	// Divide el nombre de archivo en un array de palabras utilizando los guiones como separador.
	$palabras = explode( '-', $nombre_archivo );
	// Limpia las palabras utilizando la función aicc_elimina_stopwords.
	$palabras_relevantes = aicc_elimina_stopwords( $palabras, $idioma );
	// Une las palabras relevantes con guiones como concatenador para formar el nombre de archivo nuevamente.
	$nombre_archivo = implode( '-', $palabras_relevantes );

	// Comprueba si el nombre de archivo supera la longitud máxima.
	if ( strlen( $nombre_archivo ) > $max_longitud ) {
		// Encuentra la posición del último guión antes de la longitud máxima.
		$ultima_posicion_guion = strrpos( $nombre_archivo, '-', $max_longitud - strlen( $nombre_archivo ) - 1 );

		// Si se encuentra un guión, recorta el nombre de archivo en esa posición.
		if ( false !== $ultima_posicion_guion ) {
			$nombre_archivo = substr( $nombre_archivo, 0, $ultima_posicion_guion );
		} else {
			// Si no se encuentra un guión, recorta el nombre de archivo a la longitud máxima.
			$nombre_archivo = substr( $nombre_archivo, 0, $max_longitud );
		}
	}

	return $nombre_archivo;
}
