<?php
/** Definición completa de la clase AIccGeneracion.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

/** Clase AIccGeneracion. */
class AIccGeneracion {
	/**
	 * Identificador único de la generación.
	 *
	 * @var int $id Identificador de la base de datos.
	 */
	private $id;
	/**
	 * Fecha y hora de la generación.
	 *
	 * @var string $fecha Fecha en formato 'YYYY-MM-DD HH:MM:SS'.
	 */
	private $fecha;
	/**
	 * Versión del plugin utilizada para la generación.
	 *
	 * @var string $plugin_version Versión del plugin en el momento de la generación.
	 */
	private $plugin_version;
	/**
	 * Identificador del generador utilizado.
	 *
	 * @var string $generador Nombre o identificador del generador de IA.
	 */
	private $generador;
	/**
	 * Modelo de IA utilizado para la generación.
	 *
	 * @var string $modelo Nombre del modelo de IA.
	 */
	private $modelo;
	/**
	 * Idioma en el que se solicitó la generación.
	 *
	 * @var string $idioma Código del idioma según ISO 639-1.
	 */
	private $idioma;
	/**
	 * Longitud de texto solicitada para la generación.
	 *
	 * @var int $longitud_solicitada Número de caracteres o palabras solicitadas.
	 */
	private $longitud_solicitada;
	/**
	 * Tono solicitado para la generación del texto.
	 *
	 * @var string $tono Descripción del tono solicitado (ej. formal, informal, humorístico).
	 */
	private $tono;
	/**
	 * Mensaje del sistema relacionado con la generación.
	 *
	 * @var string $mensaje_sistema Mensaje técnico o de estado generado durante la operación.
	 */
	private $mensaje_sistema;
	/**
	 * Contexto o tema específico solicitado para la generación.
	 *
	 * @var string $contexto Descripción del contexto o tema.
	 */
	private $contexto;
	/**
	 * Título sugerido o generado para el contenido.
	 *
	 * @var string $titulo Título del contenido generado.
	 */
	private $titulo;
	/**
	 * Directrices adicionales proporcionadas para la generación.
	 *
	 * @var string $directrices_adicionales Instrucciones adicionales para el generador.
	 */
	private $directrices_adicionales;
	/**
	 * Texto inicial o prompt proporcionado al modelo de IA.
	 *
	 * @var string $prompt Texto inicial dado al modelo para la generación de contenido.
	 */
	private $prompt;
	/**
	 * Datos de la solicitud hecha a la IA.
	 * Se almacena en formato JSON en la BBDD.
	 *
	 * @var array $solicitud array asociativo con los datos de la solicitud.
	 */
	private $solicitud;
	/**
	 * Respuesta de la IA a la solicitud.
	 * Se almacena en formato JSON en la BBDD.
	 *
	 * @var mixed $respuesta array asociativo con los datos de la respuesta.
	 */
	private $respuesta;
	/**
	 * Indicador de error durante la generación.
	 *
	 * @var int $error Código de error si se produjo un error, 0 si no hubo error.
	 */
	private $error;
	/**
	 * Contenido textual generado por la IA.
	 *
	 * @var string $contenido Texto generado por el modelo de IA.
	 */
	private $contenido;
	/**
	 * Descripción del contenido generado, para uso en SEO en la meta description.
	 *
	 * @var string $meta_description Descripción del contenido.
	 */
	private $meta_description;
	/**
	 * Palabras clave del contenido generado, para uso en SEO en la meta keywords.
	 *
	 * @var string $meta_keywords Palabras clave del contenido.
	 */
	private $meta_keywords;
	/**
	 * ID de la imagen destacada asociada al contenido generado.
	 *
	 * @var int $imagen_id Identificador de la imagen destacada en WordPress.
	 */
	private $imagen_id;
	/**
	 * Indicador de si la generación ha sido validada correctamente.
	 *
	 * @var int $valida 1 si es válida, 0 si no es válida.
	 */
	private $valida;
	/**
	 * Errores de validación.
	 * Se almacenan en formato JSON en la BBDD.
	 *
	 * @var array $validacion_errores Errores de validación.
	 */
	private $validacion_errores;
	/**
	 * Advertencias de validación.
	 * Se almacenan en formato JSON en la BBDD.
	 *
	 * @var array $validacion_advertencias Advertencias de validación.
	 */
	private $validacion_advertencias;
	/**
	 * Notificaciones de validación.
	 * Se almacenan en formato JSON en la BBDD.
	 *
	 * @var array $validacion_notificaciones Notificaciones de validación.
	 */
	private $validacion_notificaciones;
	/**
	 * Usuario que realizó la validación del contenido.
	 *
	 * @var string $validada_por Nombre o identificador del usuario que validó el contenido.
	 */
	private $validada_por;
	/**
	 * Fecha en que se realizó la validación.
	 *
	 * @var string $validada_fecha Fecha de la validación en formato 'YYYY-MM-DD HH:MM:SS'.
	 */
	private $validada_fecha;
	/**
	 * Autor del contenido generado.
	 *
	 * @var string $autor Nombre o identificador del autor del contenido.
	 */
	private $autor;
	/**
	 * ID del post donde se ha publicado el contenido generado.
	 *
	 * @var int $publicada_post_id Identificador del post en WordPress donde se publicó el contenido.
	 */
	private $publicada_post_id;
	/**
	 * Slug sugerido para el contenido publicable.
	 *
	 * @var string $publicable_slug Slug propuesto para el contenido cuando se publique.
	 */
	private $publicable_slug;
	/**
	 * Fecha en que el contenido fue publicado.
	 *
	 * @var string $publicada_fecha Fecha de publicación en formato 'YYYY-MM-DD HH:MM:SS'.
	 */
	private $publicada_fecha;

	/*
	Agregado 0.05 05/03/2023
	longitud_solicitada
	tono
	mensaje_sistema
	contexto
	directrices_adicionales
	validacion_errores
	validacion_advertencias
	validacion_notificaciones
	meta_description
	meta_keywords
	*/

	/** Accessors. */

	/**
	 * Establece o recupera el identificador único de la generación.
	 *
	 * Si se proporciona un valor para $data, este método establecerá el identificador.
	 * Si no se proporciona $data, devolverá el valor actual del identificador.
	 *
	 * @param int|null $data El nuevo valor del identificador a establecer, o null para obtener el valor actual.
	 * @return int Retorna el valor del identificador después de establecerlo o al obtenerlo.
	 */
	public function id( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->id = $data; }
		return $this->id;
	}
	/**
	 * Establece o recupera la fecha y hora de la generación.
	 *
	 * Si se proporciona un valor para $data, este método establecerá la fecha.
	 * Si no se proporciona $data, devolverá el valor actual de la fecha.
	 *
	 * @param string|null $data La nueva fecha a establecer en formato 'YYYY-MM-DD HH:MM:SS', o null para obtener la fecha actual.
	 * @return string Retorna la fecha después de establecerla o al obtenerla.
	 */
	public function fecha( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->fecha = $data; }
		return $this->fecha;
	}
	/**
	 * Establece o recupera la versión del plugin utilizada para la generación.
	 *
	 * @param string|null $data La versión del plugin a establecer, o null para obtener la versión actual.
	 * @return string Retorna la versión del plugin después de establecerla o al obtenerla.
	 */
	public function plugin_version( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->plugin_version = $data; }
		return $this->plugin_version;
	}
	/**
	 * Establece o recupera el nombre o identificador del generador de IA.
	 *
	 * @param string|null $data El nombre del generador a establecer, o null para obtener el nombre actual.
	 * @return string Retorna el nombre del generador después de establecerlo o al obtenerlo.
	 */
	public function generador( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->generador = $data; }
		return $this->generador;
	}
	/**
	 * Establece o recupera el nombre del modelo de IA utilizado.
	 *
	 * @param string|null $data El nombre del modelo a establecer, o null para obtener el nombre actual.
	 * @return string Retorna el nombre del modelo después de establecerlo o al obtenerlo.
	 */
	public function modelo( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->modelo = $data; }
		return $this->modelo;
	}
	/**
	 * Establece o recupera el código de idioma según ISO 639-1 para la generación.
	 *
	 * @param string|null $data El código de idioma a establecer, o null para obtener el código actual.
	 * @return string Retorna el código de idioma después de establecerlo o al obtenerlo.
	 */
	public function idioma( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->idioma = $data; }
		return $this->idioma;
	}
	/**
	 * Establece o recupera el número de caracteres o palabras solicitadas para la generación.
	 *
	 * @param int|null $data El número de caracteres o palabras a establecer, o null para obtener el número actual.
	 * @return int Retorna el número de caracteres o palabras después de establecerlo o al obtenerlo.
	 */
	public function longitud_solicitada( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->longitud_solicitada = $data; }
		return $this->longitud_solicitada;
	}
	/**
	 * Establece o recupera el tono solicitado para la generación del texto.
	 *
	 * @param string|null $data La descripción del tono a establecer, o null para obtener el tono actual.
	 * @return string Retorna la descripción del tono después de establecerlo o al obtenerlo.
	 */
	public function tono( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->tono = $data; }
		return $this->tono;
	}
	/**
	 * Establece o recupera el mensaje del sistema relacionado con la generación.
	 *
	 * @param string|null $data El mensaje técnico o de estado a establecer, o null para obtener el mensaje actual.
	 * @return string Retorna el mensaje del sistema después de establecerlo o al obtenerlo.
	 */
	public function mensaje_sistema( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->mensaje_sistema = $data; }
		return $this->mensaje_sistema;
	}
	/**
	 * Establece o recupera el contexto o tema específico solicitado para la generación.
	 *
	 * @param string|null $data La descripción del contexto o tema a establecer, o null para obtener el contexto actual.
	 * @return string Retorna el contexto después de establecerlo o al obtenerlo.
	 */
	public function contexto( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->contexto = $data; }
		return $this->contexto;
	}
	/**
	 * Establece o recupera el título sugerido o generado para el contenido.
	 *
	 * @param string|null $data El título a establecer, o null para obtener el título actual.
	 * @return string Retorna el título después de establecerlo o al obtenerlo.
	 */
	public function titulo( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->titulo = $data; }
		return $this->titulo;
	}
	/**
	 * Establece o recupera las directrices adicionales proporcionadas para la generación.
	 *
	 * @param string|null $data Las directrices adicionales a establecer, o null para obtener las directrices actuales.
	 * @return string Retorna las directrices adicionales después de establecerlas o al obtenerlas.
	 */
	public function directrices_adicionales( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->directrices_adicionales = $data; }
		return $this->directrices_adicionales;
	}
	/**
	 * Establece o recupera el texto inicial o prompt proporcionado al modelo de IA.
	 *
	 * @param string|null $data El prompt a establecer, o null para obtener el prompt actual.
	 * @return string Retorna el prompt después de establecerlo o al obtenerlo.
	 */
	public function prompt( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->prompt = $data; }
		return $this->prompt;
	}
	/**
	 * Establece o recupera los datos de la solicitud hecha a la IA.
	 *
	 * @param array|null $data Datos de la solicitud a establecer, o null para obtener los datos actuales.
	 * @return array Retorna los datos de la solicitud después de establecerlos o al obtenerlos.
	 */
	public function solicitud( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->solicitud = $data; }
		return $this->solicitud;
	}
	/**
	 * Establece o recupera la respuesta de la IA a la solicitud.
	 *
	 * @param mixed|null $data Datos de la respuesta a establecer, o null para obtener los datos actuales.
	 * @return mixed Retorna los datos de la respuesta después de establecerlos o al obtenerlos.
	 */
	public function respuesta( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->respuesta = $data; }
		return $this->respuesta;
	}
	/**
	 * Establece o recupera el código de error durante la generación.
	 *
	 * @param int|null $data Código de error a establecer, o null para obtener el código actual.
	 * @return int Retorna el código de error después de establecerlo o al obtenerlo.
	 */
	public function error( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->error = $data; }
		return $this->error;
	}
	/**
	 * Establece o recupera el contenido textual generado por la IA.
	 *
	 * @param string|null $data Contenido textual a establecer, o null para obtener el contenido actual.
	 * @return string Retorna el contenido textual después de establecerlo o al obtenerlo.
	 */
	public function contenido( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->contenido = $data; }
		return $this->contenido;
	}
	/**
	 * Establece o recupera la descripción del contenido generado para uso en SEO.
	 *
	 * @param string|null $data Descripción a establecer, o null para obtener la descripción actual.
	 * @return string Retorna la descripción después de establecerla o al obtenerla.
	 */
	public function meta_description( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->meta_description = $data; }
		return $this->meta_description;
	}
	/**
	 * Establece o recupera las palabras clave del contenido generado para uso en SEO.
	 *
	 * @param string|null $data Palabras clave a establecer, o null para obtener las palabras clave actuales.
	 * @return string Retorna las palabras clave después de establecerlas o al obtenerlas.
	 */
	public function meta_keywords( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->meta_keywords = $data; }
		return $this->meta_keywords;
	}
	/**
	 * Establece o recupera el ID de la imagen destacada asociada al contenido generado.
	 *
	 * @param int|null $data ID de la imagen a establecer, o null para obtener el ID actual.
	 * @return int Retorna el ID de la imagen después de establecerlo o al obtenerlo.
	 */
	public function imagen_id( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->imagen_id = $data; }
		return $this->imagen_id;
	}
	/**
	 * Establece o recupera el indicador de si la generación ha sido validada correctamente.
	 *
	 * @param int|null $data 1 si la generación es válida, 0 si no lo es, a establecer, o null para obtener el estado actual.
	 * @return int Retorna el estado de validación después de establecerlo o al obtenerlo.
	 */
	public function valida( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->valida = $data; }
		return $this->valida;
	}
	/**
	 * Establece o recupera los errores de validación almacenados en formato JSON.
	 *
	 * @param array|null $data Errores de validación a establecer, o null para obtener los errores actuales.
	 * @return array Retorna los errores de validación después de establecerlos o al obtenerlos.
	 */
	public function validacion_errores( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->validacion_errores = $data; }
		return $this->validacion_errores;
	}
	/**
	 * Establece o recupera las advertencias de validación almacenadas en formato JSON.
	 *
	 * @param array|null $data Advertencias de validación a establecer, o null para obtener las advertencias actuales.
	 * @return array Retorna las advertencias de validación después de establecerlas o al obtenerlas.
	 */
	public function validacion_advertencias( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->validacion_advertencias = $data; }
		return $this->validacion_advertencias;
	}
	/**
	 * Establece o recupera las notificaciones de validación almacenadas en formato JSON.
	 *
	 * @param array|null $data Notificaciones de validación a establecer, o null para obtener las notificaciones actuales.
	 * @return array Retorna las notificaciones de validación después de establecerlas o al obtenerlas.
	 */
	public function validacion_notificaciones( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->validacion_notificaciones = $data; }
		return $this->validacion_notificaciones;
	}
	/**
	 * Establece o recupera el usuario que realizó la validación del contenido.
	 *
	 * @param string|null $data Nombre o identificador del usuario a establecer, o null para obtener el usuario actual.
	 * @return string Retorna el nombre del usuario después de establecerlo o al obtenerlo.
	 */
	public function validada_por( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->validada_por = $data; }
		return $this->validada_por;
	}
	/**
	 * Establece o recupera la fecha en que se realizó la validación.
	 *
	 * @param string|null $data Fecha de la validación a establecer en formato 'YYYY-MM-DD HH:MM:SS', o null para obtener la fecha actual.
	 * @return string Retorna la fecha de validación después de establecerla o al obtenerla.
	 */
	public function validada_fecha( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->validada_fecha = $data; }
		return $this->validada_fecha;
	}
	/**
	 * Establece o recupera el autor del contenido generado.
	 *
	 * @param string|null $data Nombre o identificador del autor a establecer, o null para obtener el autor actual.
	 * @return string Retorna el nombre del autor después de establecerlo o al obtenerlo.
	 */
	public function autor( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->autor = $data; }
		return $this->autor;
	}
	/**
	 * Establece o recupera el ID del post donde se ha publicado el contenido generado.
	 *
	 * @param int|null $data ID del post a establecer, o null para obtener el ID actual.
	 * @return int Retorna el ID del post después de establecerlo o al obtenerlo.
	 */
	public function publicada_post_id( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->publicada_post_id = $data; }
		return $this->publicada_post_id;
	}
	/**
	 * Establece o recupera el slug sugerido para el contenido publicable.
	 *
	 * @param string|null $data Slug a establecer, o null para obtener el slug actual.
	 * @return string Retorna el slug después de establecerlo o al obtenerlo.
	 */
	public function publicable_slug( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->publicable_slug = $data; }
		return $this->publicable_slug;
	}
	/**
	 * Establece o recupera la fecha en que el contenido fue publicado.
	 *
	 * @param string|null $data Fecha de publicación a establecer en formato 'YYYY-MM-DD HH:MM:SS', o null para obtener la fecha actual.
	 * @return string Retorna la fecha de publicación después de establecerla o al obtenerla.
	 */
	public function publicada_fecha( $data = null ) {
		if ( ! is_null( $data ) ) {
			$this->publicada_fecha = $data; }
		return $this->publicada_fecha;
	}

	/** Constructor de la clase.
	 *
	 * Inicializa la instancia de la clase y limpia las propiedades internas.
	 */
	public function __construct() {
		$this->limpiar();
	}

	/** Vacía las propiedades para reutilizar el objeto */
	public function limpiar() {
		$this->id                        = null;
		$this->fecha                     = '0000-00-00 00:00:00';
		$this->plugin_version            = '';
		$this->generador                 = '';
		$this->modelo                    = '';
		$this->idioma                    = '';
		$this->longitud_solicitada       = 0;
		$this->tono                      = '';
		$this->mensaje_sistema           = '';
		$this->contexto                  = '';
		$this->titulo                    = '';
		$this->directrices_adicionales   = '';
		$this->prompt                    = '';
		$this->solicitud                 = '';
		$this->respuesta                 = '';
		$this->error                     = 0;
		$this->contenido                 = '';
		$this->meta_description          = '';
		$this->meta_keywords             = '';
		$this->imagen_id                 = '';
		$this->valida                    = 0;
		$this->validacion_errores        = '';
		$this->validacion_advertencias   = '';
		$this->validacion_notificaciones = '';
		$this->validada_por              = '';
		$this->validada_fecha            = '0000-00-00 00:00:00';
		$this->autor                     = '';
		$this->publicada_post_id         = 0;
		$this->publicable_slug           = '';
		$this->publicada_fecha           = '0000-00-00 00:00:00';
	}

	/** Método para grabar las propiedades a la base de datos. */
	public function grabar() {
		global $wpdb; // Objeto global de WordPress para acceder a la base de datos.

		// Retira la clave de API de la solicitud por seguridad.
		$solicitud_anonimizada = $this->solicitud;

		$solicitud_anonimizada['headers']['Authorization'] = 'Bearer sk-my-hidden-api-key';

		$datos = array(
			'fecha'                     => $this->fecha,
			'plugin_version'            => $this->plugin_version,
			'generador'                 => $this->generador,
			'modelo'                    => $this->modelo,
			'idioma'                    => $this->idioma,
			'longitud_solicitada'       => $this->longitud_solicitada,
			'tono'                      => $this->tono,
			'mensaje_sistema'           => $this->mensaje_sistema,
			'contexto'                  => $this->contexto,
			'titulo'                    => $this->titulo,
			'directrices_adicionales'   => $this->directrices_adicionales,
			'prompt'                    => $this->prompt,
			'solicitud'                 => wp_json_encode( $solicitud_anonimizada ),
			'respuesta'                 => wp_json_encode( $this->respuesta ),
			'error'                     => $this->error,
			'contenido'                 => $this->contenido,
			'meta_description'          => $this->meta_description,
			'meta_keywords'             => $this->meta_keywords,
			'imagen_id'                 => $this->imagen_id,
			'valida'                    => $this->valida,
			'validacion_errores'        => wp_json_encode( $this->validacion_errores ),
			'validacion_advertencias'   => wp_json_encode( $this->validacion_advertencias ),
			'validacion_notificaciones' => wp_json_encode( $this->validacion_notificaciones ),
			'validada_por'              => $this->validada_por,
			'validada_fecha'            => $this->validada_fecha,
			'autor'                     => $this->autor,
			'publicada_post_id'         => $this->publicada_post_id,
			'publicable_slug'           => $this->publicable_slug,
			'publicada_fecha'           => $this->publicada_fecha,
		);

		$tabla = aicc_tabla_generaciones();

		if ( $this->id ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$resultado = $wpdb->update( $tabla, $datos, array( 'id' => $this->id ) );
		} else {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$resultado = $wpdb->insert( $tabla, $datos );
			$this->id  = $wpdb->insert_id;
		}

		return ( false !== $resultado );
	}

	/** Método para cargar las propiedades desde la base de datos.
	 * Cargar una fila dado el id.
	 *
	 * @param int|null $id ID de la generación de artículo a cargar (opcional).
	 *                     Si no se proporciona, se utiliza el valor del atributo id de la instancia.
	 * @return bool Retorna true si la carga fue exitosa, false en caso contrario.
	 */
	public function cargar( $id ) {
		global $wpdb;

		// Si no se indica id, se carga el de la instancia.
		if ( ! $id ) {
			$id = $this->id;
		}

		$tabla = aicc_tabla_generaciones();

		// Consulta SQL para obtener las propiedades de la fila con el id correspondiente.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$fila = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tabla} WHERE id = %d", $id ) );

		// Si la consulta no devuelve resultados, devolvemos false.
		if ( is_null( $fila ) ) {
			return false;
		}

		// Asignamos los valores recuperados a las propiedades del objeto.
		// https://www.php.net/manual/es/function.json-decode.php Observar parámetros.
		$this->id                        = $fila->id;
		$this->fecha                     = $fila->fecha;
		$this->plugin_version            = $fila->plugin_version;
		$this->generador                 = $fila->generador;
		$this->modelo                    = $fila->modelo;
		$this->idioma                    = $fila->idioma;
		$this->longitud_solicitada       = $fila->longitud_solicitada;
		$this->tono                      = $fila->tono;
		$this->mensaje_sistema           = $fila->mensaje_sistema;
		$this->contexto                  = $fila->contexto;
		$this->titulo                    = $fila->titulo;
		$this->directrices_adicionales   = $fila->directrices_adicionales;
		$this->prompt                    = $fila->prompt;
		$this->solicitud                 = json_decode( $fila->solicitud, true );
		$this->respuesta                 = json_decode( $fila->respuesta, true );
		$this->error                     = $fila->error;
		$this->contenido                 = $fila->contenido;
		$this->meta_description          = $fila->meta_description;
		$this->meta_keywords             = $fila->meta_keywords;
		$this->imagen_id                 = $fila->imagen_id;
		$this->valida                    = $fila->valida;
		$this->validacion_errores        = json_decode( $fila->validacion_errores, true );
		$this->validacion_advertencias   = json_decode( $fila->validacion_advertencias, true );
		$this->validacion_notificaciones = json_decode( $fila->validacion_notificaciones, true );
		$this->validada_por              = $fila->validada_por;
		$this->validada_fecha            = $fila->validada_fecha;
		$this->autor                     = $fila->autor;
		$this->publicada_post_id         = $fila->publicada_post_id;
		$this->publicable_slug           = $fila->publicable_slug;
		$this->publicada_fecha           = $fila->publicada_fecha;

		return true;
	}

	/** URL de WP para editar el borrador. */
	public function url_editar_post() {
		$url = '';
		if ( $this->publicada_post_id ) {
			$url = get_edit_post_link( $this->publicada_post_id );
		}
		return $url;
	}

	/** Crea un borrador con la generación.
	 *
	 * Esta función crea un borrador de un post en WordPress utilizando la información de la clase.
	 * Incorpora el contenido de $description al excerpt del post, asigna categorías, etiquetas y
	 * la imagen destacada si están disponibles.
	 *
	 * @return int $post_id ID del post creado, o 0 si falla la creación.
	 */
	public function crear_borrador() {
		// Probablemente no haya nada a automatizar con las categorías o haría $post_category = array( 'category1', 'category2' ).
		$post_category = array();

		// Toma el posible valor de las metaetiquetas.
		$description = $this->meta_description;
		$keywords    = $this->meta_keywords;

		// Toma las metakeywords como tags.
		$post_tags = $keywords;

		// Toma la imagen destacada.
		$imagen_id = $this->imagen_id;

		// Crea el slug a partir del título eliminando las stopwords.
		// Primero lo prepara del modo oficial.
		$slug = sanitize_title( $this->titulo );
		// Divide el candidato a slug en un array de palabras utilizando los guiones como separador.
		$palabras = explode( '-', $slug );
		// Quita las stopwords.
		$palabras = aicc_elimina_stopwords( $palabras, $this->idioma );
		// Reconstruye el slug con las palabras restantes, si quedó algo.
		if ( ! empty( $palabras ) ) {
			$slug = implode( '-', $palabras );
		}

		$new_post = array(
			'post_title'    => $this->titulo,
			'post_name'     => $slug,
			'post_content'  => wp_kses_post( $this->contenido ),
			'post_status'   => 'draft',
			'post_category' => $post_category,
			'tags_input'    => $post_tags,
			'post_excerpt'  => $description, // Añade el contenido de $description al excerpt del post.
		);

		$post_id = wp_insert_post( $new_post );
		if ( $post_id > 0 ) {
			$this->publicada_post_id = $post_id;
			$this->publicada_fecha   = current_time( 'Y-m-d H:i:s' );
			$this->grabar();

			// Metaetiquetas.
			$this->add_seo_meta_data( $post_id, $keywords, $description );

			// ID de la imagen destacada si la hay.
			if ( $imagen_id ) {
				set_post_thumbnail( $post_id, $imagen_id );
			}
		}
		return $post_id;
	}

	/**
	 * Borrar una fila dado el id.
	 *
	 * @param int|null $id ID de la generación de artículo a eliminar (opcional).
	 *                     Si no se proporciona, se utiliza el valor del atributo id de la instancia.
	 * @return bool Retorna true si la eliminación fue exitosa, false en caso contrario.
	 */
	public function borrar( $id = null ) {
		global $wpdb;

		// Si no se indica id, se borra el de la instancia.
		if ( ! $id ) {
			$id = $this->id;
		}

		$tabla = aicc_tabla_generaciones();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->delete( $tabla, array( 'id' => $id ) );
		if ( false !== $result ) {
			$this->id = null;
			return true;
		} else {
			return false;
		}
	}

	/** Ver el contenido de un artículo. */
	public function mostrar_html() {
		/* translators: %1$s: Short date, %2$s: AI model. */
		echo esc_html( sprintf( __( 'Created: %1$s with %2$s', 'ai-content-creator' ), $this->fecha_hora_corta( $this->fecha ), $this->modelo ) );
		if ( $this->generador ) {
			echo esc_html( ' (' . esc_html( $this->generador ) . ')' );
		}
		echo "<br>\n";
		echo '<h2>' . esc_html( __( 'Title', 'ai-content-creator' ) . ': ' . esc_html( $this->titulo ) ) . "</h2>\n";

		$this->mostrar_errores_html();

		if ( $this->imagen_id ) {
			echo '<h2>' . esc_html( __( 'Featured image', 'ai-content-creator' ) ) . ':</h2>';
			echo '<p>';
			echo wp_get_attachment_image( $this->imagen_id, 'large', false, array( 'class' => 'aicc-responsive-img' ) );

			echo "</p>\n";
		} elseif ( aicc_modo_debug() ) {
			echo ' ' . esc_html( __( 'No featured image selected', 'ai-content-creator' ) ) . ".<br>\n";
		}

		if ( $this->contenido ) {
			echo '<h2>' . esc_html( __( 'Content', 'ai-content-creator' ) ) . ':</h2>';
			$this->mostrar_iframe_contenido_html();
		} elseif ( aicc_modo_debug() ) {
			echo ' ' . esc_html( __( 'No content', 'ai-content-creator' ) ) . ".<br>\n";
		}
	}

	/** Muestra la lista de errores. */
	public function mostrar_errores_html() {
		$hay_errores        = false;
		$errores_acumulados = array();

		if ( $this->error_proceso_api() ) {
			$errores_acumulados[] = __( 'Error', 'ai-content-creator' ) . ' HTTP: ' . esc_html( $this->error_proceso_api() );
		}

		if ( $this->error_respuesta_ai() ) {
			$errores_acumulados[] = __( 'Error', 'ai-content-creator' ) . ' IA: ' . esc_html( $this->error_respuesta_ai() );
		}

		if ( $this->error_finalizacion_ai() ) {
			$errores_acumulados[] = __( 'Article termination reason', 'ai-content-creator' ) . ': ' . esc_html( $this->error_finalizacion_ai() );
		}

		if ( ! empty( $this->validacion_errores ) ) {
			$errores_acumulados = array_merge( $errores_acumulados, $this->validacion_errores );
		}

		// Escapa el html antes de agregar el icono, que sí ha de aplicarse como html.
		// Errores.
		$errores_acumulados_html = array();
		if ( ! empty( $errores_acumulados ) ) {
			foreach ( $errores_acumulados as $error_acumulado ) {
				$errores_acumulados_html[] = esc_html( $error_acumulado );
			}
		}
		// Advertencias.
		$advertencias_html = array();
		if ( ! empty( $this->validacion_advertencias ) ) {
			foreach ( $this->validacion_advertencias as $advertencia ) {
				$advertencias_html[] = esc_html( $advertencia );
			}
		}
		// Notificaciones.
		$notificaciones_html = array();
		if ( ! empty( $this->validacion_notificaciones ) ) {
			foreach ( $this->validacion_notificaciones as $notificacion ) {
				$notificaciones_html[] = esc_html( $notificacion );
			}
		}

		if ( ! empty( $errores_acumulados ) ) {
			echo wp_kses_post( $this->icono_aviso_html( 'error' ) );
			echo ' ';
			echo wp_kses_post( nl2br( join( "\n" . $this->icono_aviso_html( 'error' ) . ' ', $errores_acumulados_html ) ) );
			echo "<br>\n";
			$hay_errores = true;
		} elseif ( aicc_modo_debug() ) {
			echo wp_kses_post( $this->icono_aviso_html( 'notificacion' ) );
			echo ' ';
			esc_html_e( 'No errors found', 'ai-content-creator' );
			echo ".<br>\n";
		}

		if ( ! empty( $advertencias_html ) ) {
			echo wp_kses_post( $this->icono_aviso_html( 'advertencia' ) );
			echo ' ';
			echo wp_kses_post( nl2br( join( "\n" . $this->icono_aviso_html( 'advertencia' ) . ' ', $advertencias_html ) ) );
			echo "<br>\n";
			$hay_errores = true;
		} elseif ( aicc_modo_debug() ) {
			echo wp_kses_post( $this->icono_aviso_html( 'notificacion' ) );
			echo ' ';
			esc_html_e( 'No warnings found', 'ai-content-creator' );
			echo ".<br>\n";
		}

		if ( ! empty( $this->validacion_notificaciones ) ) {
			echo wp_kses_post( $this->icono_aviso_html( 'notificacion' ) );
			echo ' ';
			echo wp_kses_post( nl2br( join( "\n" . $this->icono_aviso_html( 'notificacion' ) . ' ', $notificaciones_html ) ) );
			echo "<br>\n";
			$hay_errores = true;
		} elseif ( aicc_modo_debug() ) {
			echo wp_kses_post( $this->icono_aviso_html( 'notificacion' ) );
			echo ' ';
			esc_html_e( 'No notifications found', 'ai-content-creator' );
			echo ".<br>\n";
		}

		// Agrega un salto de línea final si hay contenido.
		if ( $hay_errores ) {
			echo "<br>\n";
		}
	}

	/** Procesa la respuesta de la comunicación consultando a la API, tomando de ahí los posibles errores y el contenido de <body>. */
	public function procesar_respuesta_auto() {

		if ( $this->error_proceso_api() ) {
			// Errores HTTP.
			$this->error( 1 );
		} elseif ( $this->error_respuesta_ai() ) {
			// Errores indicados expresamente por la AI.
			$this->error( 1 );
		} else {
			// Contenido del artículo y posible error si se le detecta alguno.
			list( $article_text, $estado_error ) = aicc_procesar_respuesta_auto( $this );
			$this->contenido( $article_text );
			$this->error( $estado_error );
			$this->extrae_metaetiquetas();
		}
	}

	/** Toma las metaetiqetas de la respuesta. */
	public function extrae_metaetiquetas() {
		$respuesta_mensaje = aicc_contenido_mensaje_respuesta( $this->modelo, $this->response_body() );

		// Ya se puede considerar contenido el mensaje y empezar a procesarlo.
		$this->meta_description( aicc_extrae_meta_etiqueta( $respuesta_mensaje, 'description' ) );
		$this->meta_keywords( aicc_extrae_meta_etiqueta( $respuesta_mensaje, 'keywords' ) );
	}

	/**
	 * Validar el contenido de un artículo automáticamente.
	 * He visto respuestas que son solo repeticiones consecutivas de la pregunta, igual o cambiando el h1 por h2, quizás en temperaturas de 0.1 y 0.2 en el modelo davici de GPT-3
	 * en 0.5 baja a h3, h4, h5... y quitando los <p>
	 * y hace diveros h2 con variaciones del título... sin cerrar el <p>
	 */
	public function validar_contenido_auto() {

		$this->valida = aicc_validar_contenido_auto( $this->contenido );

		// Toma la lista de errores de validación.
		$this->validacion_errores        = aicc_errores_validacion_contenido( $this->contenido );
		$this->validacion_advertencias   = aicc_advertencias_validacion_contenido( $this->contenido );
		$this->validacion_notificaciones = aicc_notificaciones_validacion_contenido( $this->contenido );

		$this->validada_por   = aicc_validador_auto_actual();
		$this->validada_fecha = current_time( 'Y-m-d H:i:s' );
		return $this->valida;
	}

	/** Texto del posible error en el proceso.
	 *
	 * @return string El texto con el error si lo hubo, vacío en caso contrario.
	 */
	public function error_proceso_api() {
		$error_proceso_api = '';
		if ( is_wp_error( $this->respuesta ) ) {
			// Típicamente errores en la comunicación HTTP.
			$error_proceso_api = $this->respuesta->get_error_message();
		}
		return $error_proceso_api;
	}

	/** Posible error indicado como respuesta de la IA.
	 *
	 * @return string El texto con el error si lo hubo, vacío en caso contrario.
	 */
	public function error_respuesta_ai() {
		$error_respuesta_ai = '';
		if ( ! is_wp_error( $this->respuesta ) ) {
			$body           = wp_remote_retrieve_body( $this->respuesta );
			$respuesta_body = json_decode( $body, true );
			if ( isset( $respuesta_body['error'] ) ) {
				$error_respuesta_ai = $respuesta_body['error']['message'];
				// Algunos errores de la AI, siendo estándar y conocidos, los puedo traducir como constantes.
				$error_respuesta_ai = aicc_traduce_error_externo( $error_respuesta_ai );
			}
		}
		return $error_respuesta_ai;
	}

	/** Posible error indicado como causa de finalización de la IA.
	 *
	 * @return string El texto con el error si lo hubo, vacío en caso contrario.
	 */
	public function error_finalizacion_ai() {
		$error_finalizacion_ai = aicc_causa_fin_respuesta( $this->generador, $this->response_body() );
		if ( 'stop' === $error_finalizacion_ai ) {
			// stop indica la terminación normal.
			$error_finalizacion_ai = '';
		}
		return $error_finalizacion_ai;
	}

	/** Muestra el HTML con el iframe del artículo y el JS que lo aproxima a la altura requerida. */
	public function mostrar_iframe_contenido_html() {
		// Uso de sandbox admitiendo tan solo allow-same-origin para poder redimensionar la altura del marco.
		echo '<iframe id="iframe-contenido" style="border:1px dashed black;width:100%;background-color:white;" sandbox="allow-same-origin" srcdoc="' . "\n";

		$iframe  = "<!DOCTYPE html>\n";
		$iframe .= "<html>\n";
		$iframe .= "<head>\n";
		$iframe .= '<title>';
		$iframe .= esc_html( $this->titulo );
		$iframe .= "</title>\n";
		$iframe .= '<meta charset="utf-8">' . "\n";
		// Para que los enlaces se abran en el padre y no en el iframe, o recarga todo el entorno de WP anidándose dentro del iframe.
		$iframe .= '<base target="_parent">' . "\n";
		$iframe .= "</head>\n";
		$iframe .= '<body style="font-family: Arial, sans-serif; font-size: 16px; line-height: 1.5;">' . "\n";
		// Es posible un HTML defectuoso. Para eso es la ventaja del iframe.
		$iframe .= wp_kses_post( $this->contenido() ) . "\n";
		$iframe .= "</body>\n";
		$iframe .= "</html>\n";

		echo esc_attr( $iframe );

		echo '"></iframe>' . "\n";

		// Script para ajustar la altura del frame en el contenedor padre.
		echo "<script>\n";
		echo 'var iframe = document.getElementById("iframe-contenido");' . "\n";
		echo "iframe.onload = function() {\n";
		echo 'var altura = iframe.contentWindow.document.body.scrollHeight;' . "\n";
		// echo 'var altura = iframe.contentWindow.document.body.offsetHeight;' . "\n";
		// echo 'var altura = iframe.contentWindow.documentElement.offsetHeight;' . "\n";
		// echo 'var altura = iframe.contentWindow.documentElement.scrollHeight;' . "\n";
		// Unos píxeles adicionales para evitar que haya scroll incluso cuando no se requeriría.
		echo 'altura += 20;' . "\n";
		echo 'iframe.style.height = altura + "px";' . "\n";
		echo "};\n";
		echo "</script>\n";
	}

	/** Muestra el código HTML con la apertura y cabecera de la lista de generaciones de artículos. */
	public function mostrar_cabecera_tabla_lista_html() {
		echo '<table class="widefat striped">' . "\n";
		echo "<tr>\n";
		echo '<th>' . esc_html( __( 'ID', 'ai-content-creator' ) ) . "</th>\n";
		echo '<th>' . esc_html( __( 'Date', 'ai-content-creator' ) ) . "</th>\n";
		echo '<th>' . esc_html( __( 'Model', 'ai-content-creator' ) ) . "</th>\n";
		echo '<th>' . esc_html( __( 'Title', 'ai-content-creator' ) ) . "</th>\n";
		echo '<th>' . esc_html( __( 'Show', 'ai-content-creator' ) ) . "</th>\n";
		echo '<th>' . esc_html( __( 'Status', 'ai-content-creator' ) ) . "</th>\n";
		echo '<th>' . esc_html( __( 'Action', 'ai-content-creator' ) ) . "</th>\n";
		echo '<th>' . esc_html( __( 'Delete', 'ai-content-creator' ) ) . "</th>\n";
		echo "</tr>\n";
	}

	/** HTML con la fila de la lista que corresponde a la generación del artículo. */
	public function mostrar_elemento_tabla_lista_html() {
		echo "<tr>\n";

		echo '<td>';
		echo esc_html( $this->id );
		echo "</td>\n";

		echo '<td>';
		echo esc_html( $this->fecha_hora_corta( $this->fecha ) );
		echo "</td>\n";

		echo '<td>';
		echo esc_html( aicc_denominacion_modelo( $this->modelo ) );
		echo "</td>\n";

		echo '<td>';
		if ( $this->titulo ) {
			echo esc_html( $this->titulo );
		} else {
			echo '[' . esc_html( __( 'Untitled', 'ai-content-creator' ) ) . ']';
		}
		echo "</td>\n";

		// Ver.
		echo '<td style="text-align:center;">';
		echo ' <a href="admin.php?page=aicc_articles&amp;id=' . esc_html( $this->id );
		echo '&amp;accion=ver';
		echo '">';
		echo wp_kses_post( $this->dashicon_html( 'dashicons-visibility', __( 'Show', 'ai-content-creator' ), 'text-decoration:none;' ) );
		echo '</a>';
		echo "</td>\n";

		// Estado.
		echo '<td style="text-align:center;">';
		echo wp_kses_post( $this->icono_estado_html() );
		echo "</td>\n";

		// Acción. En función del estado del artículo y del posprocesaror, indica la acción recomendada.
		echo '<td style="text-align:center;">';
		echo wp_kses_post( $this->icono_accion_html() );
		echo "</td>\n";

		// Borrar.
		echo '<td style="text-align:center;">';
		if ( ! $this->publicada_post_id ) {
			$tooltip = __( 'Delete', 'ai-content-creator' );
			echo ' <a href="admin.php?page=aicc_articles&amp;id=' . esc_html( $this->id );
			echo '&amp;accion=borrar';
			echo '">';
			echo wp_kses_post( $this->dashicon_html( 'dashicons-trash', $tooltip, 'text-decoration:none;' ) );
			echo '</a>';
		}
		echo "</td>\n";

		echo "</tr>\n";
	}

	/** Por simple coherencia, HTML del cierre de la tabla de la lista de artículos como pie. */
	public function mostrar_pie_tabla_lista_html() {
		echo "</table>\n";
	}

	/** Cuerpo de la respuesta convertido en una matriz.
	 *
	 * @return string Array extraído del JSON del cuerpo de la respuesta HTTP .
	 */
	public function response_body() {
		// La respuesta es un array, toma el cuerpo de la respuesta HTTP.
		$body          = wp_remote_retrieve_body( $this->respuesta() );
		$response_body = json_decode( $body, true );

		return $response_body;
	}

	/** Devuelve la cantidad de tokens del prompt.
	 *
	 * @return integer Cantidad de tokens del prompt.
	 */
	private function tokens_solicitud() {
		$tokens_solicitud = '';

		$response_body = $this->response_body();

		if ( 'chat' === aicc_tipo_modelo( $this->modelo() ) ) {
			if ( isset( $response_body['usage']['prompt_tokens'] ) ) {
				$tokens_solicitud = $response_body['usage']['prompt_tokens'];
			}
		} elseif ( isset( $response_body['usage']['prompt_tokens'] ) ) {
			$tokens_solicitud = $response_body['usage']['prompt_tokens'];
		}

		// Devuelve el valor entero, cero si no se consigue obtener.
		return intval( $tokens_solicitud );
	}

	/** Devuelve la cantidad de tokens de la respuesta.
	 *
	 * @return integer Cantidad de tokens de la respuesta.
	 */
	private function tokens_respuesta() {
		$tokens_respuesta = '';

		$response_body = $this->response_body();

		if ( 'chat' === aicc_tipo_modelo( $this->modelo() ) ) {
			if ( isset( $response_body['usage']['completion_tokens'] ) ) {
				$tokens_respuesta = $response_body['usage']['completion_tokens'];
			}
		} elseif ( isset( $response_body['usage']['completion_tokens'] ) ) {
			$tokens_respuesta = $response_body['usage']['completion_tokens'];
		}

		// Devuelve el valor entero, cero si no se consigue obtener.
		return intval( $tokens_respuesta );
	}

	/** Calcula el precio de la respuesta.
	 *
	 * @return float Coste estimado de la respuesta.
	 */
	private function coste_articulo() {
		$coste_solicitud = $this->tokens_solicitud() * $this->precio_token_solicitud();
		$coste_respuesta = $this->tokens_respuesta() * $this->precio_token_respuesta();

		$coste_total = $coste_solicitud + $coste_respuesta;

		// Devuelve el valor, que será cero si no se consigue obtener.
		return $coste_total;
	}

	/** Precio por token de prompt.
	 *
	 * @return float Precio por token.
	 */
	private function precio_token_solicitud() {
		switch ( $this->modelo() ) {
			case 'gpt-4o':
				$precio_token = 0.00001;
				break;
			case 'gpt-4-turbo':
				$precio_token = 0.00001;
				break;
			case 'gpt-4':
				$precio_token = 0.00003;
				break;
			case 'gpt-3.5-turbo':
				$precio_token = 0.0000005;
				break;
			default:
				$precio_token = 0;
				break;
		}

		return $precio_token;
	}
	/** Precio por token de respuesta.
	 *
	 * @return float Precio por token.
	 */
	private function precio_token_respuesta() {
		switch ( $this->modelo() ) {
			case 'gpt-4o':
				$precio_token = 0.00002;
				break;
			case 'gpt-4-turbo':
				$precio_token = 0.00003;
				break;
			case 'gpt-4':
				$precio_token = 0.00006;
				break;
			case 'gpt-3.5-turbo':
				$precio_token = 0.0000015;
				break;
			default:
				$precio_token = 0;
				break;
		}

		return $precio_token;
	}

	/** HTML el bloque indicando los tokens consumidos y su precio.
	 *
	 * @return string El HTML del conjunto.
	 */
	public function bloque_costes_html() {
		$modelo        = $this->modelo();
		$response_body = $this->response_body();

		// Indica el coste del artículo únicamente si se ha podido obtener.
		if ( $this->coste_articulo( $response_body ) > 0 ) {
			$html  = '<p>';
			$html .= $this->tokens_solicitud( $response_body );
			$html .= ' ';
			$html .= __( 'tokens prompt', 'ai-content-creator' );
			$html .= ', ';
			$html .= $this->tokens_respuesta( $response_body );
			$html .= ' ';
			$html .= __( 'tokens response', 'ai-content-creator' );
			$html .= '. ';
			$html .= __( 'Estimated Cost', 'ai-content-creator' );
			$html .= ': ';
			$html .= $this->coste_articulo( $response_body );
			$html .= ' USD. ';
			$html .= __( 'Verify current pricing at <a href="https://openai.com/api/pricing">https://openai.com/api/pricing</a></p>', 'ai-content-creator' );
		}
		return $html;
	}

	/** HTML con los bloques de formularios de todas las operaciones aplicables.
	 *
	 * @return string El HTML del conjunto de bloques con todas la operaciones aplicables al artículo.
	 */
	public function bloque_operaciones_html() {
		$html = '';

		// Reprocesar.
		if ( aicc_validador_auto_actual() !== $this->validada_por ) {
			$html_contenido  = '<p>' . __( 'You have a different version of the articles post-production system than the one initially used.', 'ai-content-creator' );
			$html_contenido .= ' ' . __( 'That system is responsible for the basic validation of the content received from the AI and a subsequent process to facilitate its publication.', 'ai-content-creator' ) . "</p>\n";
			$html_contenido .= '<p>' . __( 'You can repeat post-production in search of a possible better result.', 'ai-content-creator' ) . "</p>\n";
			if ( $this->publicada_post_id ) {
				$html_contenido .= '<p>' . __( 'The reprocessing does not affect the previously created draft.', 'ai-content-creator' ) . "</p>\n";
			}
			$html_boton = $this->formulario_operacion_html( 'reprocesar', __( 'Reprocess', 'ai-content-creator' ), 'dashicons-update' );
			$html      .= $this->bloque_formulario_operacion_html( $html_contenido, $html_boton );
		}

		// Regenerar.
		$html_contenido  = '<p>' . __( 'You can go back to the beginning to create a new article based on the request used in this one.', 'ai-content-creator' ) . "</p>\n";
		$html_contenido .= '<p>' . __( 'The new article will not overwrite the data of this one', 'ai-content-creator' );
		if ( $this->publicada_post_id ) {
			$html_contenido .= ' ';
			$html_contenido .= __( 'nor will affect the previously created draft', 'ai-content-creator' );
		}
		$html_contenido .= ".</p>\n";
		// Alternativas: dashicons-welcome-add-page, dashicons-controls-repeat, dashicons-redo, dashicons-editor-break.
		$html_boton = $this->formulario_operacion_html( 'regenerar', __( 'Create another', 'ai-content-creator' ), 'dashicons-welcome-add-page' );
		$html      .= $this->bloque_formulario_operacion_html( $html_contenido, $html_boton );

		// Editar post + borrar generación.
		if ( $this->publicada_post_id ) {
			$html_contenido  = '<p>' . __( 'You can', 'ai-content-creator' ) . ' ';
			$html_contenido .= __( 'edit the article', 'ai-content-creator' );
			$html_contenido .= ' ';
			$html_contenido .= __( "with your WordPress's usual system", 'ai-content-creator' ) . ".</p>\n";

			// Enlace como falso botón.
			$html_boton  = '<div style="width: 100%;text-align:center;">';
			$html_boton .= $this->falso_boton_editar_articulo_html();
			$html_boton .= '</div>';

			$html .= $this->bloque_formulario_operacion_html( $html_contenido, $html_boton );

			$html_contenido = '<p>' . __( 'If you are sure that you will not need any revision of how it was generated anymore, you can delete it. This change is not reversible. The draft or published article will not be affected.', 'ai-content-creator' ) . "</p>\n";
			$html_boton     = $this->formulario_operacion_html( 'borrar', __( 'Delete', 'ai-content-creator' ), 'dashicons-trash' );
			$html          .= $this->bloque_formulario_operacion_html( $html_contenido, $html_boton );
		} else {

			if ( current_user_can( 'edit_posts' ) ) {

				if ( $this->valida ) {
					// Operaciones viables en un artículo corrrecto
					// Seleccionar o cambiar la imagen.
					$html .= $this->bloque_gestion_imagen();

					// Crear borrador.
					$html_contenido = '<p>' . __( 'Create a draft using the generated content to edit and finalize its publishing.', 'ai-content-creator' ) . "</p>\n";
					// Alternativa: dashicons-migrate.
					$html_boton = $this->formulario_operacion_html( 'borrador', __( 'Create draft', 'ai-content-creator' ), 'dashicons-external' );
					$html      .= $this->bloque_formulario_operacion_html( $html_contenido, $html_boton );
				} elseif ( $this->contenido ) {
					// Operaciones viables en un artículo con defectos si pese a ello tiene contenido.
					// Seleccionar o cambiar la imagen.
					$html .= $this->bloque_gestion_imagen();
					// Crear borrador aun con error.
					$html_contenido     = '<p>' . __( 'Although errors have been detected in the generated content, if you want to take advantage of it, you can create a draft to edit and finalize its publishing.', 'ai-content-creator' ) . "</p>\n";
					$texto_confirmacion = __( 'I understand that the content of the draft may be defective.', 'ai-content-creator' );
					$html_contenido    .= '<p><input type="checkbox" onchange="isChecked(this, \'borrador\');">' . $texto_confirmacion . "</p>\n";
					$html_boton         = $this->formulario_operacion_html( 'borrador', __( 'Create draft', 'ai-content-creator' ), 'dashicons-external' );
					$html              .= $this->bloque_formulario_operacion_html( $html_contenido, $html_boton );
					$html              .= '<script type="text/javascript">function isChecked(checkbox,boton){document.getElementById(boton).disabled=!checkbox.checked;}</script>';
					// Llama inicialmente para deshabilitar, así uso el código estándar con el botón habilitado en origen.
					$html .= '<script type="text/javascript">document.getElementById("borrador").disabled=true;</script>';
					// javascript que deshabilite el botón 'borrador' directamente.
					// javascript que habilite el botón 'borrador' al hacer click en la casilla onchange="isChecked(this, 'borrador')".
				}
			} else {
				// No tiene permisos para crear ni editar borradores.
				// En principio no debería llegar aquí tampoco si no los tiene.
					$html .= '<p>' . __( "You don't have permission to create drafts.", 'ai-content-creator' ) . "</p>\n";
			}

			// Borrar.
			$html_contenido = '<p>';
			if ( $this->contenido ) {
				// No es necesario que sea válida para poder crear un borrador, basta con que tenga contenido.
				$html_contenido = __( 'If the content is not useful and you will not need any revision of how it was generated or create a draft with it, you can delete it. This change is not reversible.', 'ai-content-creator' );
			} else {
				// Si no tiene contenido, no puede crear borrador, no mencionarlo entonces.
				$html_contenido = __( 'If the content is not useful and you will not need any revision of how it was generated, you can delete it. This change is not reversible.', 'ai-content-creator' );
			}
			$html_contenido .= "</p>\n";
			$html_boton      = $this->formulario_operacion_html( 'borrar', __( 'Delete', 'ai-content-creator' ), 'dashicons-trash' );
			$html           .= $this->bloque_formulario_operacion_html( $html_contenido, $html_boton );
		}

		// Otros dashicons útiles: dashicons-editor-help, dashicons-download, dashicons-upload.
		// Se puedne ver en https://developer.wordpress.org/resource/dashicons/ .

		return $html;
	}

	/** HTML para gestionar la selección de la imagen destacada
	 *
	 * @return string El HTML con el código para gestionar la selección de la imagen destacada.
	 */
	private function bloque_gestion_imagen() {
		// Seleccionar imagen.
		// Obtiene las claves para la búsqueda de imágenes.
		$metakeywords = $this->meta_keywords;
		if ( ! $metakeywords ) {
			$metakeywords = aicc_improvisa_meta_keywords( $this->titulo, $this->idioma );
		}
		$pixabay_url = aicc_url_busqueda_pixabay( $this->meta_keywords, $this->idioma );
		// Seleccionar o cambiar la imagen seleccionada.
		if ( $this->imagen_id ) {
			$html_contenido  = '<p>';
			$html_contenido .= esc_html( __( 'Change the selected featured image for the article.', 'ai-content-creator' ) );
			$html_contenido .= ' ' . esc_html( __( 'This will remove the previously selected image from the media library.', 'ai-content-creator' ) );
			$html_contenido .= "</p>\n";
			$html_boton      = $this->formulario_operacion_html( 'imagen', __( 'Change featured image', 'ai-content-creator' ), 'dashicons-format-image' );
		} else {
			$html_contenido = '<p>' . esc_html( __( 'Select a featured image for the article.', 'ai-content-creator' ) ) . "</p>\n";
			$html_boton     = $this->formulario_operacion_html( 'imagen', __( 'Select featured image', 'ai-content-creator' ), 'dashicons-format-image' );
		}
		$html = $this->bloque_formulario_operacion_html( $html_contenido, $html_boton );

		return $html;
	}

	/** Genera HTML para mostrar el formulario de acciones sobre los artículos.
	 *
	 * @param string $html_contenido El contenido HTML del formulario.
	 * @param string $html_boton El botón HTML del formulario.
	 * @return string El HTML del formulario con los estilos aplicados.
	 */
	public function bloque_formulario_operacion_html( $html_contenido, $html_boton ) {
		$html  = '<div class="wp-admin" style="border:1px solid gray; background-color:white; margin:10px 0; padding:10px;">';
		$html .= $html_contenido;
		$html .= $html_boton;
		$html .= '</div>';
		return $html;
	}

	/** Genera el HTML para el formulario de acciones sobre los artículos.
	 *
	 * @param string $accion La acción que se realizará en el formulario.
	 * @param string $texto El texto del botón del formulario.
	 * @param string $dashicon (Opcional) El nombre de la clase Dashicon para mostrar junto al texto del botón.
	 * @return string El HTML del formulario con los campos y botón correspondientes.
	 */
	public function formulario_operacion_html( $accion, $texto, $dashicon = '' ) {
		// Reprocesar ha de ir a la página de creación y con POST.
		// Los demás, a la de artículos y con GET.
		if ( 'regenerar' === $accion ) {
			$action = 'admin.php';
			$pagina = 'aicc_menu';
			$metodo = 'GET';
		} else {
			$action = 'admin.php';
			$pagina = 'aicc_articles';
			$metodo = 'GET';
		}
		$html = '<form method="' . esc_attr( $metodo ) . '" action="' . esc_attr( $action ) . '">' . "\n";
		if ( $pagina ) {
			$html .= '	<input type="hidden" name="page" value="' . esc_attr( $pagina ) . '">' . "\n";
		}
		$html .= '	<input type="hidden" name="accion" value="' . esc_attr( $accion ) . '">' . "\n";

		// Todos indican id menos reprocesar_todo.
		if ( 'reprocesar_todo' !== $accion ) {
			$html .= '	<input type="hidden" name="id" value="' . esc_attr( $this->id ) . '">' . "\n";
			if ( 'borrar' === $accion ) {
				$html .= wp_nonce_field( 'borrado', 'campo_nonce' );
			}
		}

		// Proporciona estilo centrar el botón.
		$html .= '<div style="width: 100%;text-align:center;">';
		$html .= '	' . $this->boton_formulario_operacion_html( $texto, $accion, $dashicon ) . "\n";
		$html .= '</div>';
		$html .= '</form>';
		return $html;
	}

	/** Enlace como falso botón. */
	public function falso_boton_editar_articulo_html() {
		// Icono y texto en funciçon de si es borrador o está publicado.
		if ( 'publish' === get_post_status( $this->publicada_post_id ) ) {
			$dashicon = 'dashicons-media-document';
			$texto    = __( 'Edit published article', 'ai-content-creator' );
		} else {
			$dashicon = 'dashicons-edit-page';
			$texto    = __( 'Edit draft', 'ai-content-creator' );
		}

		// Forma el enlace con dashicon y texto al estilo de botón.
		$html  = '<a href="';
		$html .= esc_url( $this->url_editar_post() );
		$html .= '"';
		// Contrarrestar estilos de la clase .button que lo hacen demasiado grande requeriría:' line-height:inherit; font-size:inherit;'.
		$html .= ' class="button-primary"';
		$html .= ' style="display: inline-flex; align-items: center;margin: 0 auto;';
		$html .= '"';
		$html .= '>';

		$html .= '<span class="dashicons ';
		$html .= $dashicon;
		$html .= '" style="display: inline-block; margin-right: 5px;"></span>';
		$html .= $texto;

		$html .= '</a>';

		return $html;
	}

	/** Genera un botón HTML con estilos personalizados y opcionalmente un ícono Dashicon.
	 *
	 * @param string $texto El texto del botón.
	 * @param string $id_boton El atributo ID del botón.
	 * @param string $dashicon (Opcional) El nombre de la clase Dashicon para mostrar junto al texto del botón.
	 * @return string El botón HTML formateado con estilos y Dashicon (si se proporciona).
	 */
	public function boton_formulario_operacion_html( $texto, $id_boton, $dashicon = '' ) {
		$html = '	<button type="submit" id="' . $id_boton . '" name="submit" class="button-primary" style="display: flex; align-items: center;margin: 0 auto;">' . "\n";
		if ( $dashicon ) {
			$html .= '		<span class="dashicons ';
			$html .= $dashicon;
			$html .= '" style="display: inline-block; margin-right: 5px;"></span>';
		}
		$html .= $texto;
		$html .= "\n";
		$html .= "	</button>\n";
		return $html;
	}

	/** Convierte un formato datetime de MySQL a fecha/hora del usuario.
	 *
	 * @param string $fecha La fecha en formato datetime de MySQL (YYYY-MM-DD HH:MM:SS).
	 * @return string La fecha y hora en el formato corto del usuario (según las configuraciones de WordPress).
	 */
	private function fecha_hora_corta( $fecha ) {
		$fecha_php   = strtotime( $fecha );
		$fecha_corta = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $fecha_php );
		return $fecha_corta;
	}

	/** Indica en html estado de un artículo. */
	private function icono_estado_html() {
		$html = '';
		if ( $this->error ) {
			// Alternativo: dashicons-no-alt.
			$html .= $this->dashicon_html( 'dashicons-dismiss', __( 'An error occurred while trying to create the article and it is not valid.', 'ai-content-creator' ), 'color:red;' );
		} elseif ( $this->publicada_post_id ) {
			if ( 'publish' === get_post_status( $this->publicada_post_id ) ) {
				$html .= $this->dashicon_html( 'dashicons-media-document', __( 'Article published', 'ai-content-creator' ), 'color:green;' );
			} else {
				/* translators: %s: Short date. */
				$html .= $this->dashicon_html( 'dashicons-media-document', sprintf( __( 'Draft prepared on %s', 'ai-content-creator' ), $this->fecha_hora_corta( $this->publicada_fecha ) ), 'color:orange;' );
			}
		} elseif ( $this->valida ) {
			if ( $this->validada_por ) {
				$html .= $this->dashicon_html( 'dashicons-yes-alt', __( 'Validated', 'ai-content-creator' ) . $this->por_validador_fecha_html(), 'color:green;' );
			} else {
				// Supuestamente ya no procede.
				$html .= $this->dashicon_html( 'dashicons-yes', __( 'Automatic preliminary validation', 'ai-content-creator' ), 'color:green;' );
			}
		} elseif ( $this->validada_por ) {
			$html .= $this->dashicon_html( 'dashicons-warning', __( 'Did not pass validation', 'ai-content-creator' ) . $this->por_validador_fecha_html(), 'color:orange;' );
		} else {
			$html .= $this->dashicon_html( 'dashicons-warning', __( 'Did not pass validation', 'ai-content-creator' ), 'color:orange;' );
		}
		return $html;
	}

	/** Icono indicando la acción sugerida en la lista de artículos. */
	private function icono_accion_html() {
		$html = '';
		if ( $this->publicada_post_id ) {
			// Editable o no.
			if ( current_user_can( 'edit_post', $this->publicada_post_id ) ) {
				$html .= '<a href="' . esc_url( $this->url_editar_post() ) . '">';
				// Publicado o no.
				if ( 'publish' === get_post_status( $this->publicada_post_id ) ) {
					$html .= $this->dashicon_html( 'dashicons-media-document', __( 'Edit published article', 'ai-content-creator' ) );
				} else {
					/* translators: %s: Short date. */
					$html .= $this->dashicon_html( 'dashicons-edit-page', esc_attr( sprintf( __( 'Edit draft prepared on %s', 'ai-content-creator' ), $this->fecha_hora_corta( $this->publicada_fecha ) ) ) );
				}
				$html .= '</a>';
			} elseif ( 'publish' === get_post_status( $this->publicada_post_id ) ) {
				$html .= $this->dashicon_html( 'dashicons-media-document', esc_attr( __( 'You don\'t have permission to edit the published article.', 'ai-content-creator' ) ), 'color:gray;' );
			} else {
				$html .= $this->dashicon_html( 'dashicons-edit-page', esc_attr( __( 'You don\'t have permission to edit the published draft.', 'ai-content-creator' ) ), 'color:gray;' );
			}
		} elseif ( aicc_validador_auto_actual() !== $this->validada_por ) {
			$html .= '<a href="admin.php?page=aicc_articles&amp;id=' . $this->id;
			$html .= '&amp;accion=reprocesar';
			$html .= '">';
			$html .= $this->dashicon_html( 'dashicons-update', __( 'Reprocess', 'ai-content-creator' ), 'text-decoration:none;' );
			$html .= '</a>';
		} elseif ( '' === $this->imagen_id ) {
			// Seleccionar imagen destacada.
			$html .= '<a href="admin.php?page=aicc_articles&amp;id=' . $this->id;
			$html .= '&amp;accion=imagen';
			$html .= '">';
			$html .= $this->dashicon_html( 'dashicons-format-image', __( 'Select featured image', 'ai-content-creator' ), 'text-decoration:none;' );
			$html .= '</a>';
		} elseif ( $this->valida ) {
			if ( current_user_can( 'edit_posts' ) ) {
				$html .= '<a href="admin.php?page=aicc_articles&amp;id=' . $this->id;
				$html .= '&amp;accion=borrador';
				$html .= '">';
				$html .= $this->dashicon_html( 'dashicons-external', __( 'Create draft', 'ai-content-creator' ), 'text-decoration:none;' );
				$html .= '</a>';
			} else {
					$html .= $this->dashicon_html( 'dashicons-external', esc_attr( __( 'You don\'t have permission to create drafts.', 'ai-content-creator' ) ), 'color:gray;' );
			}
		} else {
			$html .= '<a href="admin.php?page=aicc_menu&amp;id=' . $this->id;
			$html .= '&amp;accion=regenerar';
			$html .= '">';
			$html .= $this->dashicon_html( 'dashicons-welcome-add-page', __( 'Create another', 'ai-content-creator' ), 'text-decoration:none;' );
			$html .= '</a>';
		}
		return $html;
	}

	/** Proporciona el dashicon correspondiente al tipo de aviso indicado.
	 *
	 * @param string $tipo_aviso Uno de 'notificacion', 'advertencia' u otros. Por efecto 'error'.
	 */
	private function icono_aviso_html( $tipo_aviso = 'error' ) {
		$titulo = '';
		if ( 'notificacion' === $tipo_aviso ) {
			$dashicon = 'dashicons-info';
			$color    = 'blue';
		} elseif ( 'advertencia' === $tipo_aviso ) {
			$dashicon = 'dashicons-warning';
			$color    = 'orange';
		} else {
			$dashicon = 'dashicons-dismiss';
			$color    = 'red';
		}
		$estilo = "color: $color;";
		$html   = $this->dashicon_html( $dashicon, $titulo, $estilo );
		return $html;
	}

	/** Proporciona el texto de la validación u otras operaciones reliazadas
	 * por el agente y fecha correspondientes. */
	private function por_validador_fecha_html() {
		$html = $this->por_agente_fecha_html( $this->validada_por, $this->validada_fecha );
		return $html;
	}

	/** Texto de la validación u otras operaciones realizadas por algún agente y fecha.
	 *
	 * @param string $agente Agente validador.
	 * @param string $fecha Fecha de la validación en formato datetime de MySQL.
	 * @return string HTML con el código.
	 */
	private function por_agente_fecha_html( $agente, $fecha ) {
		/* translators: %1$s: Agent, %2$s: Short date. */
		$html = ' ' . sprintf( __( "by '%1\$s' at %2\$s", 'ai-content-creator' ), $agente, $this->fecha_hora_corta( $fecha ) );
		return $html;
	}

	/** Código de un dashicon con el tooltip que pueda proceder.
	 *
	 * @param string $dashicon Código del dashicon.
	 * @param string $titulo Atributo title para formar un tooltip.
	 * @param string $estilo CSS opcional para el atributo style.
	 * @return string HTML con el código.
	 */
	private function dashicon_html( $dashicon, $titulo, $estilo = '' ) {
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

	/** Añade meta keywords y meta description a un post utilizando diferentes plugins SEO.
	 *
	 * @param int    $post_id      ID del post al que se le asignarán las meta keywords y meta description.
	 * @param string $keywords     Meta keywords a asignar al post.
	 * @param string $description  Meta description a asignar al post.
	 */
	private function add_seo_meta_data( $post_id, $keywords, $description ) {
		// Incluir el archivo plugin.php si es necesario.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			// Si Yoast SEO está activo.
			update_post_meta( $post_id, '_yoast_wpseo_metakeywords', $keywords );
			update_post_meta( $post_id, '_yoast_wpseo_metadesc', $description );
		} elseif ( is_plugin_active( 'all-in-one-seo-pack/all_in_one_seo_pack.php' ) ) {
			// Si All in One SEO Pack está activo.
			update_post_meta( $post_id, '_aioseo_keywords', $keywords );
			update_post_meta( $post_id, '_aioseo_description', $description );
		} elseif ( is_plugin_active( 'seo-by-rank-math/rank-math.php' ) ) {
			// Si Rank Math SEO está activo.
			update_post_meta( $post_id, 'rank_math_focus_keyword', $keywords );
			update_post_meta( $post_id, 'rank_math_description', $description );
		} else {
			// Si ninguno de los plugins anteriores está activo, asignar las palabras clave como etiquetas.
			wp_set_post_terms( $post_id, $keywords, 'post_tag' );
		}
	}
}
