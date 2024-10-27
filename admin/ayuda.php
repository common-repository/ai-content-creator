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

/** Imprime la página de ayuda. */
function aicc_pagina_ayuda() {
	echo '<div class="wrap">' . "\n";
	aicc_mostrar_cabecera_pagina_admin();
	echo '<h2>';
	echo '<span class="dashicons dashicons-editor-help" style="color:blue;"></span> ';
	echo esc_html__( 'Help', 'ai-content-creator' );
	echo "</h2>\n";

	?>
	<p><?php echo esc_html__( 'To use the plugin you need access to an AI (Artificial Intelligence). OpenAI is an artificial intelligence platform that allows access to a range of applications, models, and tools for the development of projects related to this technology. You need to create an account and get an API key that you will enter in the settings section of the menu. Here are the necessary steps to do it:', 'ai-content-creator' ); ?></p>

	<div style="background-color:white; padding:25px;">
		<p><span class="dashicons dashicons-info" style="color:blue;"></span>
		<?php
		/* translators: 1: Start of the link, 2: End of the link */
		printf( esc_html__( 'The procedure may change over time. If you spot any differences, please %1$slet us know%2$s to update these instructions and help other users.', 'ai-content-creator' ), '<a href="https://taller.abcdatos.net/contacto/" target="_blank" rel="noreferrer noopener">', '</a>' );
		?>
		</p>
	</div>

	<h3><?php echo esc_html__( 'Create an account on OpenAI and get your API key', 'ai-content-creator' ); ?></h3>
	<ol>
		<li><b><?php echo esc_html__( 'Access the registration page', 'ai-content-creator' ); ?></b><br>
			<?php
			/* translators: 1: Start of the link, 2: End of the link */
			printf( esc_html__( 'To create an account on OpenAI you must access the %1$sOpenAI registration page%2$s, enter your email and click the "%3$s" button. Then choose a password and click the "%4$s" button again.', 'ai-content-creator' ), '<a href="https://platform.openai.com/signup" target="_blank" rel="noreferrer noopener">', '</a>', 'Continue', 'Continue' );
			?>
		</li>

		<li><b><?php echo esc_html__( 'Verify your email address', 'ai-content-creator' ); ?></b><br>
			<?php echo esc_html__( 'Once you have entered your email address, you will receive a message from OpenAI in your inbox. Click on the confirmation link and you will be redirected back to the login page.', 'ai-content-creator' ); ?>
		</li>

		<li><b><?php echo esc_html__( 'Configure your account', 'ai-content-creator' ); ?></b><br>
			<?php echo esc_html__( 'Once you have verified your email address, you must enter your personal details and configure your OpenAI account. You will be asked to indicate the type of account you want to create (individual or business), enter the name of your company (if applicable), and select the plan that best suits your needs.', 'ai-content-creator' ); ?>
		</li>
		<li><b><?php esc_html_e( 'Get your API key', 'ai-content-creator' ); ?></b><br>
			<?php esc_html_e( 'Once you created your OpenAI account you can generate an API key. This key will allow you to access all of the platform\'s tools and resources. To get your API key follow these steps:', 'ai-content-creator' ); ?><br><br>
			<ul>
				<li>
				<?php
				/* translators: 1: Start of the link, 2: End of the link, 3: Original untranslated title of the "API Keys" section */
				printf( esc_html__( '%1$sLog in to your OpenAI account%2$s to go to "%3$s" section.', 'ai-content-creator' ), '<a href="https://platform.openai.com/account/api-keys" target="_blank" rel="noreferrer noopener">', '</a>', 'API Keys' );
				?>
				</li>
				<li>
				<?php
				/* translators: Original untranslated "Generate New API Key" */
				printf( esc_html__( 'Click on "%s"', 'ai-content-creator' ), 'Generate New API Key' );
				?>
				</li>
				<li><?php esc_html_e( 'Your API key will be displayed. Save it in a secure place, as you will not be able to retrieve it once you close it.', 'ai-content-creator' ); ?></li>
			</ul>
		</li>
		<li><b><?php esc_html_e( 'Start using the plugin', 'ai-content-creator' ); ?></b><br>
			<?php
			/* translators: 1: Start of the link, 2: End of the link */
			printf( esc_html__( 'You can start using the plugin now! %1$sConfigure your API key in the settings%2$s along with the details you want to modify and start creating articles.', 'ai-content-creator' ), '<a href="admin.php?page=aicc_settings">', '</a>' );
			?>
		</li>
	</ol>
	<div style="background-color:white; padding:25px;">
		<p><span class="dashicons dashicons-warning" style="color:orange;"></span> <?php esc_html_e( 'Your API key is used exclusively for requests to the AI and the plugin stores it only in the options configuration table of your WordPress database. When the plugin stores sent and received messages for further processing it automatically deletes the transmitted key for security reasons before storing them, preventing your key from being exposed in case of exporting them or performing post-production procedures, for example.', 'ai-content-creator' ); ?></p>
	</div>
</div>
	<?php
}
