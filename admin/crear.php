<?php
/**
 * Formulario de Creación de artículo e interacción con la IA.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

echo '<div class="wrap">' . "\n";
aicc_mostrar_cabecera_pagina_admin();
echo '<h2>';
esc_html_e( 'Create article', 'ai-content-creator' );
echo "</h2>\n";

// Determina si falta acceso a la generación (OpenAI por ahora).
$deshabilitado = '';
if ( ! aicc_conf_apikoai() ) {
	$mensaje  = __( 'You have not configured your API Key yet.', 'ai-content-creator' );
	$mensaje .= '<br>';
	/* translators: %1$s: HTML link start, %2$s: HTML link end. */
	$mensaje .= sprintf( __( 'Go to %1$shelp section%2$s and follow the procedure to create one.', 'ai-content-creator' ), '<a href="admin.php?page=aicc_help">', '</a>' );
	aicc_mostrar_notificacion( $mensaje, 'error' );
	$deshabilitado = ' disabled';
}

$generar                 = false;
$regenerar               = false;
$titulo                  = '';
$contexto                = '';
$directrices_adicionales = '';

// Generar es el proceso completo con creación de la AI.
// Regenerar es solamente mostrar el formulario para poder luego pulsar y generar.
if ( isset( $_POST['generar'] ) ) {
	$generar = true;
} elseif ( isset( $_GET['accion'] ) ) {
	if ( 'regenerar' === $_GET['accion'] ) {
		$regenerar = true;
	}
}

// Commas, quotes and symbols are welcome, will use wp_unslash to allow them.
// Database usage.

// Tanto al generar como al regenerar, hay datos previos considerable para cargar en el form.
if ( $generar ) {
	if ( isset( $_POST['titulo'] ) ) {
		// Aquí perdemos la posibilidad de títulos como "¿Debo utilizar <b> o <strong>?".
		// Revertir las magic_quotes, que siempre actúan en WP.
		$titulo = sanitize_text_field( wp_unslash( $_POST['titulo'] ) );
		// Sanea y valida el título para determinar que la generación es válida.
		$titulo = aicc_sanea_titulo( $titulo );
		if ( ! aicc_valida_titulo( $titulo ) ) {
			$generar = false;
		}
	}
	if ( isset( $_POST['contexto'] ) ) {
		$contexto = sanitize_textarea_field( wp_unslash( $_POST['contexto'] ) );
		if ( ! aicc_sanea_contexto( $contexto ) ) {
			$generar = false;
		}
	}
	if ( isset( $_POST['directrices_adicionales'] ) ) {
		$directrices_adicionales = sanitize_textarea_field( wp_unslash( $_POST['directrices_adicionales'] ) );
		if ( ! aicc_sanea_directrices_adicionales( $directrices_adicionales ) ) {
			$generar = false;
		}
	}
}

if ( $regenerar ) {
	/*
	Cargar ID o indicar error.
	Cargar cada campo que exista.
	*/

	if ( isset( $_GET['id'] ) ) {
		$id_articulo              = sanitize_text_field( wp_unslash( $_GET['id'] ) );
		$id_articulo              = aicc_valida_id_articulo( $id_articulo );
		$aicc_generacion_anterior = new AIccGeneracion();
		if ( $aicc_generacion_anterior->cargar( $id_articulo ) ) {
			$titulo                  = $aicc_generacion_anterior->titulo();
			$contexto                = $aicc_generacion_anterior->contexto();
			$directrices_adicionales = $aicc_generacion_anterior->directrices_adicionales();
		} else {
			aicc_mostrar_notificacion( __( 'Unable to load the specified article.', 'ai-content-creator' ), 'error' );
		}
	} else {
		aicc_mostrar_notificacion( __( 'You have not specified based on which article you want to create another one.', 'ai-content-creator' ), 'error' );
	}
}

	echo wp_kses( aicc_formulario_crear_html( $titulo, $contexto, $directrices_adicionales, $deshabilitado ), aicc_permitidos_kses_formulario() );

	echo aicc_imagen_espera_html();

?>

<div id="creacion">

<?php
if ( $generar ) {
	echo '<hr>';

	$modelo          = aicc_conf_modelo();
	$aicc_generacion = new AIccGeneracion();
	$aicc_generacion = aicc_generar_contenido( $modelo, $titulo, $contexto, $directrices_adicionales );

	// Al terminar de generar, oculta la animación de nuevo.
	?>
<script>
function ocultaEspera() {
	var elementoEspera = document.getElementById("espera");
	elementoEspera.style.display = "none";
	var elementoBotonCrear = document.getElementById("botoncrear");
	elementoBotonCrear.style.display = "block";
}
ocultaEspera();
</script>
	<?php
	$aicc_generacion->mostrar_errores_html();

	if ( ! $aicc_generacion->contenido() ) {
		aicc_mostrar_notificacion( __( 'Something went wrong while trying to generate the content.', 'ai-content-creator' ), 'warning' );
	} else {
		esc_html_e( 'Content', 'ai-content-creator' );
		echo ":\n";
		$aicc_generacion->mostrar_iframe_contenido_html();
		echo $aicc_generacion->bloque_operaciones_html();
	}
	echo '<hr>';

}

?>

	</div>
<?php

/** Crear el formulario HTML de creación de artículo.
 *
 * @param {string} $titulo - El título del artículo.
 * @param {string} $contexto - El contexto del tema que se abordará.
 * @param {string} $directrices_adicionales - Directrices adicionales para el artículo.
 * @param {string} $deshabilitado - El atributo 'disabled' del botón de envío, si corresponde.
 * @return {string} El formulario HTML.
 */
function aicc_formulario_crear_html( $titulo, $contexto, $directrices_adicionales, $deshabilitado ) {
	$html  = '<form method="post" action="admin.php?page=aicc_menu">' . "\n";
	$html .= '<label for="titulo">' . __( 'Title', 'ai-content-creator' ) . ':</label>' . "\n";
	$html .= '<br>' . "\n";
	$html .= '<input type="text" name="titulo" style="width:80%;" placeholder="' . esc_attr( __( 'Example: How to choose the right screwdriver', 'ai-content-creator' ) ) . '" value="' . esc_attr( $titulo ) . '"><br>' . "\n";
	$html .= "<br>\n";
	$html .= '<label for="contexto">' . __( 'Summarize the subject you will address so that the AI understands the context (optional)', 'ai-content-creator' ) . ':</label>' . "\n";
	$html .= "<br>\n";
	$html .= '<textarea id="contexto" name="contexto" rows="3" cols="40" style="width:80%;" placeholder="' . esc_attr( __( 'Example: We will write an article discussing the quality of different types of screwdrivers for the content of a blog dedicated to general technical issues, indicating the different types that exist and their applications.', 'ai-content-creator' ) ) . '">' . esc_html( $contexto ) . '</textarea>' . "\n";
	$html .= '<br>' . "\n";
	$html .= '<label for="directrices_adicionales">' . __( 'Additional guidelines (optional, one per line)', 'ai-content-creator' ) . ':</label>' . "\n";
	$html .= '<br>' . "\n";
	$html .= '<textarea id="directrices_adicionales" name="directrices_adicionales" rows="5" cols="40" style="width:80%;" placeholder="' . esc_attr( __( 'Example: Make the headers in navy blue', 'ai-content-creator' ) ) . '">' . esc_html( $directrices_adicionales ) . '</textarea>' . "\n";
	$html .= '<br>' . "\n";
	$html .= '<input type="hidden" name="generar" value="generar">' . "\n";
	$html .= '<div style="margin-top:10px; width:100%; text-align:center;">' . "\n";
	$html .= '<input type="submit" id="botoncrear" class="button-primary" name="submit" value="Crear artículo" onclick="mostrarEspera()" ' . $deshabilitado . '>' . "\n";
	$html .= '</div>' . "\n";
	$html .= '</form>' . "\n";
	return $html;
}

/** Prompt para continuar un artículo con un título dado.
 *
 * @param {string} $titulo - El título del artículo.
 * @return {string} La cadena de texto formateada con el título.
 */
function aicc_prompt_continuar( $titulo ) {
	$prompt            = __( 'Title', 'ai-content-creator' ) . ": $titulo\r\n";
	$prompt            = "<h1>$titulo</h1>\r\n";
	$inicio_respuesta  = '';
	$inicio_respuesta .= '<p>';

	$prompt .= $inicio_respuesta;
	return $prompt;
}

/** Prompt para continuar un artículo con un título dado paso a paso.
 *
 * @param {string} $titulo - El título del artículo.
 * @return {string} La cadena de texto formateada con el título y la frase "Vamos paso a paso".
 */
function aicc_prompt_continuar_paso_a_paso( $titulo ) {
	// Zero Shot Chain of Thought.
	$prompt           = "<h1>$titulo</h1>\r\n";
	$inicio_respuesta = '';
	/* translators: This is part of a prompt to an AI. */
	$inicio_respuesta .= '<p>' . __( "Let's go step by step", 'ai-content-creator' ) . ".</p>\r\n<p>";

	$prompt .= $inicio_respuesta;
	return $prompt;
}

/** Prompt para solicitar la creación de un artículo con título, contexto y directrices adicionales.
 *
 * @param {string} $titulo - El título del artículo.
 * @param {string} $contexto - El contexto del tema que se abordará (opcional).
 * @param {string} $directrices_adicionales - Directrices adicionales para el artículo (opcional).
 * @return {string} La cadena de texto formateada con el título, contexto y directrices adicionales.
 */
function aicc_prompt_solicitar( $titulo, $contexto = '', $directrices_adicionales = '' ) {

	// Caracteristicas adicionales a indicar en el prompt.
	$caracteristicas_adicionales = array();
	$separador                   = "\n•";

	/** Opciones deshabilitadas.
	$longitud = aicc_conf_longitud();
	$caracteristicas_adicionales[] = "Tener una longitud de $longitud palabras.";
	$caracteristicas_adicionales[] = 'Redactado en idioma ' . aicc_nombre_idioma( aicc_conf_idioma() ) . '.';
	$caracteristicas_adicionales[] = 'Imprescindible que sea en formato HTML.';
	$caracteristicas_adicionales[] = "Asegúrate de que el contenido del artículo esté relacionado únicamente con el tema \"$titulo\".";
	$caracteristicas_adicionales[] = 'Inícialo con un índice basado en los títulos y subtítulos.';
	*/

	if ( aicc_conf_tablacontenidos() ) {
		/* translators: This is part of a prompt to an AI. */
		$caracteristicas_adicionales[] = __( 'Place a table of contents based on the titles and subtitles after a first introduction paragraph.', 'ai-content-creator' );
	}
	/* translators: This is part of a prompt to an AI. */
	$caracteristicas_adicionales[] = __( 'Make sure to include the necessary tags to structure the content into main titles and subtitles using the <h1> and <h2> tags, respectively.', 'ai-content-creator' );
	/* translators: This is part of a prompt to an AI. */
	$caracteristicas_adicionales[] = __( 'Also use the <ul> and <li> tags to create lists and <ol> and <li> to enumerate necessary steps. Use the <p> tag to separate content into paragraphs, <br> to indicate line breaks and any other convenient tags.', 'ai-content-creator' );
	/* translators: This is part of a prompt to an AI. */
	$caracteristicas_adicionales[] = __( 'Never use <style> tags in the <head> section. Instead, use the style attributes in the necessary tags for defining styles.', 'ai-content-creator' );
	/* translators: This is part of a prompt to an AI. */
	$caracteristicas_adicionales[] = __( 'Include an attractive and persuasive meta description that includes relevant keywords.', 'ai-content-creator' );
	/* translators: This is part of a prompt to an AI. */
	$caracteristicas_adicionales[] = __( 'Include relevant keywords in a meta keywords tag.', 'ai-content-creator' );

	$lista_caracteristicas_adicionales = array();
	if ( ! empty( $directrices_adicionales ) ) {
		$lista_caracteristicas_adicionales = preg_split( "/\r\n|\n|\r/", $directrices_adicionales );
		// Eliminamos las líneas en blanco del array.
		$lista_caracteristicas_adicionales = array_filter( $lista_caracteristicas_adicionales, 'trim' );
	}

	// Agrega las características adicionales solicitadas por el usuario a las prefabricadas.
	foreach ( $lista_caracteristicas_adicionales as $caracteristica_adicional ) {
		$caracteristica_adicional = trim( $caracteristica_adicional );
		if ( ! empty( $caracteristica_adicional ) ) {
			if ( substr( $caracteristica_adicional, -1 ) !== '.' ) {
				$caracteristica_adicional .= '.';
			}
			$caracteristicas_adicionales[] = "$caracteristica_adicional";
		}
	}

	$texto_caracteristicas_adicionales = $separador . implode( "$separador", $caracteristicas_adicionales );

	$prompt = '';
	if ( $contexto ) {
		$prompt .= "$contexto\n";
	}

	// Prompt pidiendo respuesta HTML o HTML5 según el caso.
	if ( current_theme_supports( 'html5' ) ) {
		/* translators: %s: title of the required the article. Part of the prompt to the AI. */
		$prompt .= sprintf( __( 'Please create an HTML5 article titled "%s" that meets all of the following criteria', 'ai-content-creator' ), $titulo ) . ":\n$texto_caracteristicas_adicionales\n" . __( 'Thank you!', 'ai-content-creator' );
	} else {
		/* translators: %s: title of the required the article. Part of the prompt to the AI. */
		$prompt .= sprintf( __( 'Please create an HTML article titled %s that meets all of the following criteria', 'ai-content-creator' ), $titulo ) . ":\n$texto_caracteristicas_adicionales\n" . __( 'Thank you!', 'ai-content-creator' );
	}

	return $prompt;
}

/** Genera un mensaje del sistema para ser utilizado en el prompt del modelo de Chat GPT.
 * Dependiendo de la configuración, el mensaje incluirá información sobre HTML5, longitud,
 * idioma y tono del artículo solicitado.
 *
 * @return string Mensaje del sistema para el modelo de Chat GPT.
 */
function aicc_mensaje_system() {
	if ( current_theme_supports( 'html5' ) ) {
		/* translators: %s: length in words for the article. Part of the prompt to the AI. */
		$mensaje = sprintf( __( 'You are an HTML5 expert copywriter who writes articles of %s words', 'ai-content-creator' ), aicc_conf_longitud() );
	} else {
		/* translators: %s: length in words for the article. Part of the prompt to the AI. */
		$mensaje = sprintf( __( 'You are an HTML expert copywriter who writes articles of %s words', 'ai-content-creator' ), aicc_conf_longitud() );
	}
	if ( aicc_conf_idioma() !== aicc_codigo_idioma_omision() ) {
		$mensaje .= ' ';
		/* translators: %s: language to be used in the article. Part of the prompt to the AI. */
		$mensaje .= sprintf( __( 'in %s language', 'ai-content-creator' ), strtolower( aicc_nombre_idioma_traducido( aicc_conf_idioma() ) ) );
	}
	if ( aicc_conf_tono() !== 'neutral' ) {
		$mensaje .= ' ';
		/* translators: %s: tone to be used in the article. Part of the prompt to the AI. */
		$mensaje .= sprintf( __( 'and in %s tone', 'ai-content-creator' ), strtolower( aicc_tonos()[ aicc_conf_tono() ] ) );
	}

	$mensaje .= '.';
	return $mensaje;
}

/** Genera contenido utilizando la API de OpenAI.
 * Solicita un artículo basado en el título, contexto y directrices adicionales proporcionados,
 * utilizando el modelo de lenguaje seleccionado y la configuración actual del plugin.
 *
 * @param string $modelo Modelo de lenguaje de OpenAI a utilizar.
 * @param string $titulo Título del artículo a generar.
 * @param string $contexto Contexto o información adicional a incluir en el artículo.
 * @param string $directrices_adicionales Directrices adicionales para guiar la generación del artículo.
 * @return object Objeto AIccGeneracion con la información del artículo generado y el proceso de generación.
 */
function aicc_generar_contenido( $modelo, $titulo, $contexto = '', $directrices_adicionales = '' ) {

	$article_text     = '';
	$inicio_respuesta = '';

	// Set the OpenAI API key.
	$openai_api_key = aicc_conf_apikoai();
	// Tiempo máximo de espera para la respuesta y evitar los 5 segundos que hay por defecto.
	// cURL error 28: Operation timed out after 5000 milliseconds with 0 bytes received1.
	$timeout = aicc_conf_timeout();
	// Longitud del artículo en caracteres.

	$idioma = aicc_conf_idioma();

	// Instancia el objeto sobre el que trabajar.
	$aicc_generacion = new AIccGeneracion();
	$aicc_generacion->plugin_version( aicc_get_version() );
	$aicc_generacion->fecha( current_time( 'Y-m-d H:i:s' ) );
	$aicc_generacion->modelo( $modelo );
	$aicc_generacion->titulo( $titulo );
	$aicc_generacion->contexto( $contexto );
	$aicc_generacion->directrices_adicionales( $directrices_adicionales );
	$aicc_generacion->idioma( $idioma );
	$aicc_generacion->longitud_solicitada( aicc_conf_longitud() );
	$aicc_generacion->tono( aicc_conf_tono() );

	// Set the OpenAI API endpoint and prompt https://platform.openai.com/docs/api-reference/completions/create .
	if ( 'chat' === aicc_tipo_modelo( $modelo ) ) {
		$openai_endpoint = 'https://api.openai.com/v1/chat/completions';
		$aicc_generacion->mensaje_sistema( aicc_mensaje_system() );
		$prompt = aicc_prompt_solicitar( $titulo, $contexto, $directrices_adicionales );
	} else {
		$openai_endpoint = "https://api.openai.com/v1/engines/$modelo/completions";
		$prompt          = aicc_mensaje_system() . "\n";
		$prompt         .= aicc_prompt_solicitar( $titulo, $contexto, $directrices_adicionales );
	}

	$aicc_generacion->modelo( $modelo );
	$aicc_generacion->prompt( $prompt );
	$temperatura = 0.3;

	// Ajusta los parámetros de la solicitud.
	if ( 'chat' === aicc_tipo_modelo( $modelo ) ) {
		// 'Usuario de pruebas' is not one of ['system', 'assistant', 'user'] - 'messages.0.role'.
		$contexto       = '';
		$request_params = array(
			'model'       => $modelo,
			'temperature' => $temperatura,
			'messages'    => array(
				array(
					'role'    => 'system',
					'content' => aicc_mensaje_system(),
				),
				array(
					'role'    => 'user',
					'content' => $prompt,
				),
			),
		);
	} else {
		$request_params = array(
			'prompt'      => $prompt,
			'temperature' => $temperatura,
			'max_tokens'  => 3000,            // Resto 1000 por el prompt. Es obligatorio indicarlo o considera 0 y corta.
			'n'           => 1,
		// 'nucleus'         => 0.9,             // Trata de evitar repeticiones en la respuesta > Unrecognized request argument supplied: nucleus.
		// 'top_p '          => 0.9,             // Trata de evitar repeticiones en la respuesta > Unrecognized request argument supplied: top_p.
		);
	}

	// Muestra el prompt.
	if ( isset( $prompt ) ) {
		if ( aicc_modo_debug() ) {
			if ( 'chat' === aicc_tipo_modelo( $modelo ) ) {
				if ( aicc_mensaje_system() ) {
					echo '<p>[modo_debug] System:<br>';
					echo esc_html( aicc_mensaje_system() );
				}
			}

			echo '<p>[modo_debug] Prompt:<br>';
			echo nl2br( esc_html( $prompt ) ) . '</p>';
		}
	}

	// Cabeceras de la solicitud.
	$request_headers = array(
		'Content-Type'  => 'application/json',
		'Authorization' => 'Bearer ' . $openai_api_key,
	);

	// Parámetros de la solicitud.
	$params = array(
		'timeout' => $timeout,
		'headers' => $request_headers,
		'body'    => wp_json_encode( $request_params ),
	);

	/** Código conservado temporalmente
	if ( aicc_modo_debug() ) {
		echo '[modo_debug] Consulta:<br>';
		echo '<pre>' . nl2br( esc_html( var_export( $params, true ) ) ) . '</pre></hr>';
	}
	*/

	$aicc_generacion->solicitud( $params );

	// Envía la consulta a la API de OpenAI.
	$response = wp_remote_post( $openai_endpoint, $params );

	// Toma la respuesta y graba todo ya.
	// Guarda el resultado.
	if ( $response ) {
		$aicc_generacion->respuesta( $response );
	}
	if ( ! $aicc_generacion->grabar() ) {
		aicc_mostrar_notificacion( __( 'Unable to save the result of article creation.', 'ai-content-creator' ), 'error' );
	}

	// Empieza el proceso de la respuesta.
	// Posproceso, parte de extracción de datos.
	$aicc_generacion->procesar_respuesta_auto();
	// La validación se realiza en un paso posterior, por si algo fallase ya tenerlo grabado sin validar.
	$aicc_generacion->validar_contenido_auto();

	// Guarda el resultado.
	if ( ! $aicc_generacion->grabar() ) {
		aicc_mostrar_notificacion( __( 'Unable to save the validation result.', 'ai-content-creator' ), 'error' );
	}

	// Devuelve el objeto artículo generado.
	return $aicc_generacion;
}

/** Se compone de un script que la hace visible
 * HTML que crea la imagen,
 * y estilos que completan la animación.
 */
function aicc_imagen_espera_html() {

	$script = '<script>
function mostrarEspera() {
  var elementoBotonCrear = document.getElementById("botoncrear");
  elementoBotonCrear.style.display = "none";
  var elementoEspera = document.getElementById("espera");
  elementoEspera.style.display = "block";
  var elementoGenerar = document.getElementById("generar");
  if (elementoGenerar) {
    elementoGenerar.style.display = "none";
  }
  var elementoCreacion = document.getElementById("creacion");
  if (elementoCreacion) {
    elementoCreacion.style.display = "none";
  }
}
</script>
';

	$url_imagen = plugin_dir_url( __FILE__ ) . 'images/ia.svg';

	// Para verlo en pruebas, ponerlo en display: block,
	// en producción es display: none.
	$html  = '<div id="espera" style="display: none; text-align: center; margin-top: 20px;">' . "\n";
	$html .= '<div style="display: inline-block; width: 150px; position: relative;">' . "\n";
	$html .= '<img src="';
	$html .= esc_attr( plugin_dir_url( __FILE__ ) );
	$html .= 'images/ia.svg" width="150" height="150"';
	$html .= ' style="width:150px; height:150px;"';
	$html .= ' alt="';
	$html .= esc_attr( __( 'Creating article...', 'ai-content-creator' ) );
	$html .= '"';
	$html .= '>' . "\n";

	$html .= '</div>' . "\n";
	$html .= '<p style="margin-top: 20px;">' . __( 'Creating article...', 'ai-content-creator' ) . '</p>' . "\n";
	$html .= '</div>' . "\n";

	$imagen_espera_html = "$script\n$html\n";

	return $imagen_espera_html;
}