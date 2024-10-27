<?php
/** Elementos usados en la tabla opciones.
 *
 * @package   AI Content Creator
 * @author    ABCdatos
 * @license   GPLv2
 * @link      https://taller.abcdatos.net/ai-content-creator-wordpress/
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Access is not allowed.', 'ai-content-creator' ) ) );

/** Lista de variables usadas en tabla options */
function aicc_lista_opciones() {
	return array(
		'aicc_dbversion',
		'aicc_pxbcversion',
		'aicc_2faversion',
		'aicc_settings',
	);
}
