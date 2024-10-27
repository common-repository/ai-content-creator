<?php
/** Página mostrando las operaciones con los artículos creados.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

aicc_mostrar_pagina_articulos();

/** Muestra la página de administración de artículos.
 */
function aicc_mostrar_pagina_articulos() {
	echo '<div class="wrap">' . "\n";
	aicc_mostrar_cabecera_pagina_admin();
	echo '<h2>';
	esc_html_e( 'Articles', 'ai-content-creator' );
	echo "</h2>\n";

	$article_id = '';
	// Procesa si el id recibido es valido.
	if ( isset( $_GET['id'] ) ) {
		$article_id = aicc_valida_id_articulo( $_GET['id'] );
		if ( ! $article_id ) {
			aicc_mostrar_notificacion( __( 'The requested article is incorrect', 'ai-content-creator' ), 'error' );
		}
	}

	$accion = '';
	if ( isset( $_GET['accion'] ) ) {
		$accion = aicc_filtra_accion_articulos( $_GET['accion'] );
		if ( ! $accion ) {
			aicc_mostrar_notificacion( __( 'The requested action is incorrect', 'ai-content-creator' ), 'error' );
		}
	}

	$imagen_url = '';
	if ( isset( $_GET['imagen_url'] ) ) {
		$imagen_url = esc_url( $_GET['imagen_url'] );
		if ( ! $imagen_url ) {
			aicc_mostrar_notificacion( __( 'The selected image is incorrect', 'ai-content-creator' ), 'error' );
		}
	}

	$keywords = '';
	// Toma las keywords para la búsqueda en Pixabay si se han recibido.
	if ( isset( $_GET['keywords'] ) ) {
		$keywords = aicc_sanea_keywords_pixabay( $_GET['keywords'] );
	}

	aicc_manejar_accion( $article_id, $accion, $keywords, $imagen_url );

	echo "</div>\n";
}

/**
 * Maneja la acción específica basada en los parámetros proporcionados.
 *
 * @param string $article_id   El ID del artículo.
 * @param string $accion       La acción a realizar.
 * @param string $keywords     Las palabras clave para la búsqueda de imágenes.
 * @param string $imagen_url   La URL de la imagen seleccionada.
 */
function aicc_manejar_accion( $article_id, $accion, $keywords, $imagen_url ) {
	if ( $article_id ) {
		// Operaciones individuales sobre un único artículo indicado.
		switch ( $accion ) {
			case 'reprocesar':
				aicc_accion_reprocesar( $article_id );
				break;
			case 'ver':
				aicc_ver_articulo( $article_id );
				break;
			case 'borrar':
				aicc_confirma_borrado_articulo( $article_id );
				break;
			case 'borrar_confirmado':
				aicc_borra_articulo( $article_id );
				aicc_lista_articulos();
				break;
			case 'borrador':
				aicc_crear_borrador_articulo( $article_id );
				break;
			case 'imagen':
				aicc_seleccionar_imagen( $article_id, $keywords );
				break;
			case 'imagen_seleccionada':
				aicc_procesar_seleccion_imagen( $article_id, $imagen_url );
				aicc_ver_articulo( $article_id );
				break;
			default:
				aicc_lista_articulos();
				break;
		}
	} elseif ( 'reprocesar_todo' === $accion ) {
		// La operación de reprocesar sobre múltiples artículos no requiere especificar ninguno.
		aicc_reprocesa_articulos();
		aicc_lista_articulos();
	} else {
		// Cualquier otro caso solo mostrará la lista de artículos.
		aicc_lista_articulos();
	}
}

/** Realiza la acción de reprocesar el artículo y en función de si hay cambios, muestra el resultado o la lista.
 *
 * @param string $article_id El ID del artículo.
 */
function aicc_accion_reprocesar( $article_id ) {
	if ( aicc_reprocesa_articulo( $article_id ) ) {
		// Ha causado modificación.
		aicc_ver_articulo( $article_id );
	} else {
		// Sin cambios, muestra la lista entonces.
		aicc_lista_articulos();
	}
}

/** Lista todos los artículos almacenados en la base de datos.
 */
function aicc_lista_articulos() {
	global $wpdb;
	$tabla = aicc_tabla_generaciones();
	// Inevitable por ser tablas ajenas a WP la advertencia:
	// WARNING | [ ] Usage of a direct database call is discouraged.
	// ERROR   | Use placeholders and $wpdb->prepare(); found interpolated variable $tabla...
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$results = $wpdb->get_results( "SELECT id FROM {$tabla} ORDER BY fecha DESC;" );

	if ( $results ) {
		$aicc_generacion = new AIccGeneracion();

		// Muestra la tabla de creaciones completa.
		$aicc_generacion->mostrar_cabecera_tabla_lista_html();
		foreach ( $results as $result ) {
			$article_id = $result->id;
			if ( $aicc_generacion->cargar( $article_id ) ) {
				$aicc_generacion->mostrar_elemento_tabla_lista_html();
			} else {
				$mensaje_error = esc_html( __( 'Error loading the article', 'ai-content-creator' ) ) . " $article_id<br>";
				aicc_mostrar_notificacion( $mensaje_error, 'error' );
			}
		}
		$aicc_generacion->mostrar_pie_tabla_lista_html();

		// Muestra El botón de reprocesar todo si procede.
		aicc_boton_reprocesar_todo();

	} else {
		$mensaje = esc_html( __( 'There are no articles to show. Generate them from', 'ai-content-creator' ) ) . ' <a href="admin.php?page=aicc_menu">' . __( 'Create article' ) . '</a>.<br>';
		aicc_mostrar_notificacion( $mensaje, 'warning' );
	}
}

/** Muestra el botón para reprocesar todos los artículos.
 */
function aicc_boton_reprocesar_todo() {
	$cantidad_articulos_reprocesables = aicc_cantidad_articulos_reprocesables();

	if ( $cantidad_articulos_reprocesables > 1 ) {
		$html_contenido  = '<h4>' . __( 'It is recommended to repeat post-processing', 'ai-content-creator' ) . ".</h4>\n";
		$html_contenido .= '<p>' . __( 'You now have a version', 'ai-content-creator' ) . ' <i>' . aicc_validador_auto_actual() . '</i> ' . __( 'of the article post-processing system different from the one initially used in some articles', 'ai-content-creator' ) . '. ';
		$html_contenido .= __( 'The post-processing system takes care of basic validation of the content received from AI and a subsequent process to facilitate its publication', 'ai-content-creator' ) . ".</p>\n";
		$html_contenido .= '<p>' . __( 'You can repeat the process in the affected articles in search of possible better results', 'ai-content-creator' ) . ".</p>\n";
		$html_contenido .= '<p>' . __( 'The reprocessing does not affect possible drafts already created or previous publications', 'ai-content-creator' ) . ".</p>\n";
		/* translators: %sNumber of articles. */
		$html_contenido .= '<p>' . sprintf( __( 'A maximum of %s articles per batch will be reprocessed', 'ai-content-creator' ), aicc_limite_posproduccion() ) . ' ' . ".</p>\n";

		$aicc_generacion = new AIccGeneracion();
		$html_boton      = $aicc_generacion->formulario_operacion_html( 'reprocesar_todo', __( 'Reprocess articles', 'ai-content-creator' ), 'dashicons-update' );
		$html            = $aicc_generacion->bloque_formulario_operacion_html( $html_contenido, $html_boton );
		echo $html;
	}
}

/** Devuelve la cantidad de artículos reprocesables.
 *
 * @return int Cantidad de artículos reprocesables.
 */
function aicc_cantidad_articulos_reprocesables() {
	global $wpdb;
	$tabla = aicc_tabla_generaciones();

	// Inevitable por ser tablas ajenas a WP la advertencia:
	// WARNING | [ ] Usage of a direct database call is discouraged.
	// ERROR   | Use placeholders and $wpdb->prepare(); found interpolated variable $tabla...
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$cantidad = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tabla} WHERE validada_por <> %s;", aicc_validador_auto_actual() ) );

	if ( aicc_modo_debug() ) {
		echo '[modo_debug] Artículos reprocesables: ';
		echo esc_html( $cantidad );
		echo "<br>\n";
	}

	return $cantidad;
}

/** Reprocesa todos los artículos.
 */
function aicc_reprocesa_articulos() {
	// Reprocesa todos los artículos.
	global $wpdb;
	$mensaje       = '';
	$hay_novedades = 0;
	$tabla         = aicc_tabla_generaciones();

	// Inevitable por ser tablas ajenas a WP la advertencia:
	// WARNING | [ ] Usage of a direct database call is discouraged.
	// ERROR   | Use placeholders and $wpdb->prepare(); found interpolated variable $tabla...
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$results = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM {$tabla} WHERE validada_por <> %s LIMIT %d;", aicc_validador_auto_actual(), aicc_limite_posproduccion() ) );

	if ( count( $results ) > 0 ) {
		$aicc_generacion = new AIccGeneracion();

		$hay_novedades = false;
		foreach ( $results as $result ) {
			$article_id = $result->id;
			if ( $aicc_generacion->cargar( $article_id ) ) {

				if ( aicc_modo_debug() ) {
					echo '[modo_debug] Reprocesando artículo: ';
					echo esc_html( $article_id );
					echo "<br>\n";
				}

				// Guarda el contenido viejo para contrastar si hay cambios.
				$contenido_viejo = '';
				if ( $aicc_generacion->contenido() ) {
					$contenido_viejo = $aicc_generacion->contenido();
				}

				$aicc_generacion->procesar_respuesta_auto();
				$aicc_generacion->validar_contenido_auto();
				$aicc_generacion->grabar();

				// Comprueba si ha causado novedades.
				if ( $aicc_generacion->contenido() !== $contenido_viejo ) {
					$hay_novedades = true;
					$mensaje      .= __( 'The reprocessing has modified the content of the article', 'ai-content-creator' ) . " $article_id: " . esc_html( $aicc_generacion->titulo() ) . '<br>';
				}
			} else {
				$mensaje_error = __( 'Error loading the article', 'ai-content-creator' ) . " $article_id<br>";
				aicc_mostrar_notificacion( $mensaje_error, 'error' );
			}
		}
	} else {
		$mensaje .= __( 'There are no articles to reprocess', 'ai-content-creator' ) . '.<br>';
	}
	if ( ! $hay_novedades ) {
		$mensaje .= __( 'The reprocessing has not caused the modification of any article', 'ai-content-creator' ) . '.<br>';
	}
	aicc_mostrar_notificacion( $mensaje, 'notice' );
}

/** Reprocesa un artículo específico.
 *
 * @param int $article_id ID del artículo a reprocesar.
 * @return bool True si hay novedades, false en caso contrario.
 */
function aicc_reprocesa_articulo( $article_id ) {
	if ( aicc_modo_debug() ) {
		echo '[modo_debug] <b>Proceso de la generación: ';
		echo esc_html( $article_id );
		echo "</b><br>\n";
	}

	$aicc_generacion = new AIccGeneracion();
	if ( $aicc_generacion->cargar( $article_id ) ) {

		// Guarda contenido y resultadoss de validaciones viejos para contrastar si hay cambios.
		$contenido_viejo = '';
		$notificaciones  = '';
		if ( $aicc_generacion->contenido() ) {
			$contenido_viejo = $aicc_generacion->contenido();
		}

		$validacion_errores_viejo = array();
		if ( $aicc_generacion->validacion_errores() ) {
			$validacion_errores_viejo = $aicc_generacion->validacion_errores();
		}

		$validacion_advertencias_viejo = array();
		if ( $aicc_generacion->validacion_advertencias() ) {
			$validacion_advertencias_viejo = $aicc_generacion->validacion_advertencias();
		}

		$validacion_notificaciones_viejo = array();
		if ( $aicc_generacion->validacion_notificaciones() ) {
			$validacion_notificaciones_viejo = $aicc_generacion->validacion_notificaciones();
		}

		$aicc_generacion->procesar_respuesta_auto();
		$aicc_generacion->validar_contenido_auto();

		// Comprueba si ha causado novedades de contenido.
		$hay_novedades = false;
		if ( $aicc_generacion->contenido() !== $contenido_viejo ) {
			$hay_novedades   = true;
			$notificaciones .= __( 'The process has modified the content compared to the previous one', 'ai-content-creator' ) . '.<br>';
		} else {
			$notificaciones .= __( 'The process has not modified the generated content', 'ai-content-creator' ) . '.<br>';
		}

		// Comprueba si ha causado novedades en errores.
		if ( array_diff( $aicc_generacion->validacion_errores(), $validacion_errores_viejo ) ) {
			$hay_novedades   = true;
			$notificaciones .= __( 'New errors have been detected', 'ai-content-creator' ) . '.<br>';
		} elseif ( array_diff( $validacion_errores_viejo, $aicc_generacion->validacion_errores() ) ) {
			$hay_novedades   = true;
			$notificaciones .= __( 'Previous errors have been removed', 'ai-content-creator' ) . '.<br>';
		} elseif ( aicc_modo_debug() ) {
			$notificaciones .= '[modo_debug] El proceso no ha modificado los errores generados.<br>';
		}

		// Comprueba si ha causado novedades en advertencias.
		if ( array_diff( $aicc_generacion->validacion_advertencias(), $validacion_advertencias_viejo ) ) {
			$hay_novedades   = true;
			$notificaciones .= __( 'Warnings have been added', 'ai-content-creator' ) . '.<br>';
		} elseif ( array_diff( $validacion_advertencias_viejo, $aicc_generacion->validacion_advertencias() ) ) {
			$hay_novedades   = true;
			$notificaciones .= __( 'Previous warnings have been removed', 'ai-content-creator' ) . '.<br>';
		} elseif ( aicc_modo_debug() ) {
			$notificaciones .= '[modo_debug] El proceso no ha modificado las advertencias generadas.<br>';
		}

		// Comprueba si ha causado novedades en notificaciones.
		if ( array_diff( $aicc_generacion->validacion_notificaciones(), $validacion_notificaciones_viejo ) ) {
			$hay_novedades   = true;
			$notificaciones .= __( 'Notifications have been added', 'ai-content-creator' ) . '.<br>';
		} elseif ( array_diff( $validacion_notificaciones_viejo, $aicc_generacion->validacion_notificaciones() ) ) {
			$hay_novedades   = true;
			$notificaciones .= __( 'Previous notifications have been removed', 'ai-content-creator' ) . '.<br>';
		} elseif ( aicc_modo_debug() ) {
			$notificaciones .= '[modo_debug] El proceso no ha modificado las notificaciones generadas.<br>';
		}

		if ( ! $aicc_generacion->grabar() ) {
			aicc_mostrar_notificacion( __( 'Failed to save the reprocessing result', 'ai-content-creator' ) . " $article_id.", 'error' );
		} else {
			aicc_mostrar_notificacion( $notificaciones, 'success' );
		}
	} else {
		/* translators: %s id of the required the article. */
		aicc_mostrar_notificacion( sprintf( __( 'Failed to load %s to reprocess it.', 'ai-content-creator' ), $article_id ), 'error' );
	}
	return $hay_novedades;
}

/** Muestra el artículo generado.
 *
 * @param int $article_id El ID del artículo.
 */
function aicc_ver_articulo( $article_id ) {
	echo '<b>';
	esc_html_e( 'Viewing the article generation result', 'ai-content-creator' );
	echo ': ';
	echo esc_html( $article_id );
	echo "</b><br>\n";
	$aicc_generacion = new AIccGeneracion();
	if ( $aicc_generacion->cargar( $article_id ) ) {
		$aicc_generacion->mostrar_html();
		echo $aicc_generacion->bloque_costes_html();
		echo $aicc_generacion->bloque_operaciones_html();
	} else {
		/* translators: %s id of the required the article. */
		aicc_mostrar_notificacion( sprintf( __( 'Failed to load article %s to view it', 'ai-content-creator' ), $article_id ), 'error' );
	}
}

/** Crea un borrador de artículo.
 *
 * @param int $article_id El ID del artículo.
 */
function aicc_crear_borrador_articulo( $article_id ) {
	$url_edicion     = '';
	$aicc_generacion = new AIccGeneracion();
	if ( $aicc_generacion->cargar( $article_id ) ) {
		$aicc_generacion->crear_borrador();
		if ( $aicc_generacion->publicada_post_id() ) {
			aicc_mostrar_notificacion( __( 'Draft created', 'ai-content-creator' ), 'success' );
			echo '<p>';
			esc_html_e( 'A draft entry has been created with the article. You can now find it in the Posts section of your WordPress dashboard.', 'ai-content-creator' );
			echo ' ';
			esc_html_e( 'Make sure the content meets all desired technical, legal, and editorial requirements before publishing.', 'ai-content-creator' );
			echo '</p>';
			// Enlace como falso botón.
			echo '<div style="width: 100%;text-align:center;">';
			echo $aicc_generacion->falso_boton_editar_articulo_html();
			echo '</div>';
		} else {
			/* translators: %s id of the required the article. */
			aicc_mostrar_notificacion( __( 'Failed to create the draft', 'ai-content-creator' ), 'error' );
		}
	} else {
		/* translators: %s id of the required the article. */
		aicc_mostrar_notificacion( esc_html( sprintf( __( 'Failed to load %s to create a draft', 'ai-content-creator' ), $article_id ) ), 'error' );
	}
}

/** Confirma el borrado de un artículo.
 *
 * @param int $article_id El ID del artículo.
 */
function aicc_confirma_borrado_articulo( $article_id ) {
	$aicc_generacion = new AIccGeneracion();
	// Cargarlo valida el id y toma el título.
	if ( $aicc_generacion->cargar( $article_id ) ) {
		/* translators: %s id of the required the article. */
		echo '<p>' . esc_html( sprintf( __( 'Confirm that you want to delete the article %s', 'ai-content-creator' ), $article_id ) );
		echo '<i>' . esc_html( $aicc_generacion->titulo() ) . '</i> ';
		esc_html_e( 'created on', 'ai-content-creator' );
		echo ' ';
		echo esc_html( $aicc_generacion->fecha() );
		echo "</p>\n";
		?>
		<form method="get" action="admin.php">
			<input type="hidden" name="page" value="aicc_articles">
			<input type="hidden" name="accion" value="borrar_confirmado">
			<input type="hidden" name="id" value="<?php echo esc_attr( $aicc_generacion->id() ); ?>">
			<?php wp_nonce_field( 'borrado', 'campo_nonce' ); ?>
			<input type="submit" class="button-primary" name="aceptar" value="<?php echo esc_attr( __( 'Delete', 'ai-content-creator' ) ); ?>">
			<input type="submit" class="button-secondary" name="cancelar" value="<?php echo esc_attr( __( 'Cancel', 'ai-content-creator' ) ); ?>">
		</form>
		<?php
		// Mostrar el contenido si lo hay.
		if ( $aicc_generacion->contenido() ) {
			$aicc_generacion->mostrar_iframe_contenido_html();
		}
	} else {
		aicc_mostrar_notificacion( __( 'Cannot delete non-existent article', 'ai-content-creator' ) . " $article_id", 'error' );
	}
}

/** Borra un artículo.
 *
 * @param int $article_id El ID del artículo.
 */
function aicc_borra_articulo( $article_id ) {
	// Verifica el nonce antes del intento de borrar.
	if ( isset( $_GET['campo_nonce'] )
		&& wp_verify_nonce( $_GET['campo_nonce'], 'borrado' )
	) {
		if ( isset( $_GET['aceptar'] ) ) {
			$aicc_generacion = new AIccGeneracion();
			// Si existe, borra el artículo.
			if ( $aicc_generacion->borrar( $article_id ) ) {
				/* translators: %s id of the required the article. */
				aicc_mostrar_notificacion( sprintf( __( 'Article %s deleted', 'ai-content-creator' ), $article_id ), 'success' );
			} else {
				/* translators: %s id of the required the article. */
				aicc_mostrar_notificacion( sprintf( __( 'Failed to delete article %s', 'ai-content-creator' ), $article_id ), 'error' );
			}
		} elseif ( isset( $_GET['cancelar'] ) ) {
			/* translators: %s id of the required the article. */
			aicc_mostrar_notificacion( sprintf( __( 'Article %s deletion operation canceled', 'ai-content-creator' ), $article_id ), 'warning' );
		} else {
			/* translators: %s id of the required the article. */
			aicc_mostrar_notificacion( sprintf( __( 'Article %s deletion operation not processed', 'ai-content-creator' ), $article_id ), 'error' );
		}
	} else {
		// Nonce no superado.
		aicc_mostrar_notificacion( __( 'The followed link does not allow deleting the article', 'ai-content-creator' ), 'error' );
	}
}
