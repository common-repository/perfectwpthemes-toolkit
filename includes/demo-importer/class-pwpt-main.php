<?php

/**
 * Perfectwpthemes Demo Importer class, so we don't have to worry about namespaces.
 */
class PWPT_Main {

	/**
	 * @var $instance the reference to *Singleton* instance of this class
	 */
	private static $instance;

	/**
	 * Private variables used throughout the plugin.
	 */
	private $importer, $plugin_page, $import_files, $logger, $log_file_path, $selected_index, $selected_import_files, $microtime, $frontend_error_messages, $ajax_call_number;


	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @return PWPT_Main the *Singleton* instance.
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}


	/**
	 * Class construct function, to initiate the plugin.
	 * Protected constructor to prevent creating a new instance of the
	 * *Singleton* via the `new` operator from outside of this class.
	 */
	protected function __construct() {

		// Actions.
		add_action( 'admin_menu', array( $this, 'create_plugin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_PWPT_import_demo_data', array( $this, 'import_demo_data_ajax_callback' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_plugin_with_filter_data' ) );
	}


	/**
	 * Creates the plugin page and a submenu item in WP Appearance menu.
	 */
	public function create_plugin_page() {
		$plugin_page_setup = apply_filters( 'perfectwpthemes-demo-importer/plugin_page_setup', 
			array(
				'parent_slug' => 'themes.php',
				'page_title'  => esc_html__( 'Perfectwpthemes Demo Importer', 'perfectwpthemes-toolkit' ),
				'menu_title'  => esc_html__( 'Import Demo Content', 'perfectwpthemes-toolkit' ),
				'capability'  => 'import',
				'menu_slug'   => 'perfectwpthemes-demo-importer',
			)
		);

		$this->plugin_page = add_submenu_page( $plugin_page_setup['parent_slug'], $plugin_page_setup['page_title'], $plugin_page_setup['menu_title'], $plugin_page_setup['capability'], $plugin_page_setup['menu_slug'], array(
			$this,
			'display_plugin_page'
		) );
	}


	/**
	 * Plugin page display.
	 */
	public function display_plugin_page() {
		?>
        <div class="PWPT__intro-notice notice notice-warning is-dismissible">
            <p><?php esc_html_e( 'If you already have content on your website, you are recommended not to import demo content. And before you begin importing demo content, make sure all the required plugins are installed and activated.', 'perfectwpthemes-toolkit' ); ?></p>
        </div>
        <div class="wrap pwpt-demo-importer-wrapper">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'Perfectwpthemes Demo Importer', 'perfectwpthemes-toolkit' ); ?><span class="title-count theme-count"><?php echo esc_html( count( $this->import_files ) ); ?></span>
            </h1>
            <?php $this->get_server_info(); ?>
			<hr>			
			<?php
			// Display warrning if PHP safe mode is enabled, since we wont be able to change the max_execution_time.
			if ( ini_get( 'safe_mode' ) ) {
				printf(
					esc_html__( '%sWarning: your server is using %sPHP safe mode%s. This means that you might experience server timeout errors.%s', 'perfectwpthemes-toolkit' ),
					'<div class="notice  notice-warning  is-dismissible"><p>',
					'<strong>',
					'</strong>',
					'</p></div>'
				);
			}

			if ( 0 < count( $this->import_files ) ) : ?>
                <div class="PWPT__multi-select-import">
                	<?php  if( count( $this->import_files ) == 1 ) : ?>
                		<h2><?php esc_html_e( 'Import demo content from the demo below:', 'perfectwpthemes-toolkit' ); ?></h2>
                	<?php else : ?>
                		<h2><?php esc_html_e( 'Select any one of the available demos to import content from.', 'perfectwpthemes-toolkit' ); ?></h2>
                	<?php endif; ?>
                    
                    <div class="theme-browser rendered">
                        <div class="themes wp-clearfix">
							<?php
							$installed_demos = get_option( 'perfectwpthemes_themes', array() );
							foreach ( $this->import_files as $index => $import_file ) :

								$is_installed = false;

								$import_file_name = isset( $import_file['import_file_name'] ) ? $import_file['import_file_name'] : '';

								if( ! $import_file_name ) {
									if ( in_array( $import_file_name, $installed_demos ) ) {
										$is_installed = true;
									}
								}
								?>
                                <div class="theme <?php echo $is_installed ? 'active' : '' ?>">
                                    <div class="theme-screenshot">
                                        <img src="<?php echo $import_file['import_preview_image_url'] ?>">
                                    </div>
                                    <a target="_blank" href="<?php echo $import_file['demo_url'] ?>"
                                       style="text-decoration: none;"
                                       class="more-details"><?php esc_html_e( 'Live Preview', 'perfectwpthemes-toolkit' ); ?></a>
                                    <div class="theme-author">
										<?php esc_html_e( 'By Perfectwpthemes', 'perfectwpthemes-toolkit' ); ?>
                                    </div>
                                    <div class="theme-id-container">
                                        <h2 class="theme-name">
                                        	<?php
											if ( $is_installed ) {

												echo '<b>Imported</b> : ';
											}
											echo $import_file['import_file_name'] 
											?>
										</h2>

                                        <div class="theme-actions">
											<?php
											$href = '';
											if ( $is_installed ) {

												$href = ' href="' . $import_file['demo_url'] . '" target="_blank"';
											}
											?>
                                            <a <?php echo $href; ?> class="button button-primary <?php echo ! $is_installed ? 'load-customize hide-if-no-customize jsperfectwpthemesimport-data ' : ''; ?>" data-index="<?php echo $index; ?>">
												<?php
												if ( ! $is_installed ) {
													esc_html_e( 'Import Demo', 'perfectwpthemes-toolkit' );
												} else {
													esc_html_e( 'Live Preview', 'perfectwpthemes-toolkit' );
												}
												?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </div>
                    </div>
					<?php
					// Check if at least one preview image is defined, so we can prepare the structure for display.
					$preview_image_is_defined = false;
					
					foreach ( $this->import_files as $import_file ) {
						if ( isset( $import_file['import_preview_image_url'] ) ) {
							$preview_image_is_defined = true;
							break;
						}
					}
					
					$preview_image_is_defined = false;

					if ( $preview_image_is_defined ) :
						?>
                        <div class="PWPT__demo-import-preview-container">
                            <p><?php esc_html_e( 'Import preview:', 'perfectwpthemes-toolkit' ); ?></p>
                            <p class="PWPT__demo-import-preview-image-message jsperfectwpthemespreview-image-message">
                            	<?php
								if ( ! isset( $this->import_files[0]['import_preview_image_url'] ) ) {
									esc_html_e( 'No preview image defined for this import.', 'perfectwpthemes-toolkit' );
								} // Leave the img tag below and the p tag above available for later changes via JS.
								?>
							</p>

                            <img id="PWPT__demo-import-preview-image" class="jsperfectwpthemespreview-image"
                                 src="<?php echo ! empty( $this->import_files[0]['import_preview_image_url'] ) ? esc_url( $this->import_files[0]['import_preview_image_url'] ) : ''; ?>">
                        </div>
						<?php 
					endif; 
					?>
                </div>
                <p><?php esc_html_e( 'Click the "Import Demo" button once and wait. The import process may take few minutes.', 'perfectwpthemes-toolkit' ); ?></p>
                <p><?php esc_html_e( 'Sometimes some of the elements may not be imported, due to some errors. So manually configure them.', 'perfectwpthemes-toolkit' ); ?></p>
				<?php 
			endif;
			?>
        </div>
		<?php
	}


	/**
	 * Enqueue admin scripts (JS and CSS)
	 *
	 * @param string $hook holds info on which admin page you are currently loading.
	 */
	public function admin_enqueue_scripts() {

		wp_enqueue_script( 'perfectwpthemes-toolkit-demo-importer', PWPT()->plugin_url() . '/admin/js/demo-importer.js', array( 'jquery', 'jquery-form' ), PERFECTWPTHEMES_TOOLKIT_VERSION, true );

		wp_localize_script( 'perfectwpthemes-toolkit-demo-importer', 'pwpt_admin_ajax',
			array(
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'   => wp_create_nonce( 'pwpt-ajax-verification' ),
				'import_files' => $this->import_files,
				'texts'        => array(
					'missing_preview_image' => esc_html__( 'No preview image defined for this import.', 'perfectwpthemes-toolkit' ),
				),
			)
		);

		wp_enqueue_style( 'perfectwpthemes-toolkit-demo-importer', PWPT()->plugin_url() . '/admin/css/demo-importer.css', array(), PERFECTWPTHEMES_TOOLKIT_VERSION );
	}


	/**
	 * Main AJAX callback function for:
	 * 1. prepare import files (uploaded or predefined via filters)
	 * 2. import content
	 * 3. before widgets import setup (optional)
	 * 4. import widgets (optional)
	 * 5. import customizer options (optional)
	 * 6. after import setup (optional)
	 */
	public function import_demo_data_ajax_callback() {

		// Try to update PHP memory limit (so that it does not run out of it).
		ini_set( 'memory_limit', apply_filters( 'perfectwpthemes-demo-importer/import_memory_limit', '350M' ) );

		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		PWPT_Helpers::verify_ajax_call();

		// Is this a new AJAX call to continue the previous import?
		$use_existing_importer_data = $this->get_importer_data();

		if ( ! $use_existing_importer_data ) {

			// Set the AJAX call number.
			$this->ajax_call_number = empty( $this->ajax_call_number ) ? 0 : $this->ajax_call_number;

			// Error messages displayed on front page.
			$this->frontend_error_messages = '';

			// Create a date and time string to use for demo and log file names.
			$demo_import_start_time = date( apply_filters( 'perfectwpthemes-demo-importer/date_format_for_file_names', 'Y-m-d__H-i-s' ) );

			// Define log file path.
			$this->log_file_path = PWPT_Helpers::get_log_path( $demo_import_start_time );

			// Get selected file index or set it to 0.
			$this->selected_index = empty( $_POST['selected'] ) ? 0 : absint( $_POST['selected'] );

			/**
			 * 1. Prepare import files.
			 * Manually uploaded import files or predefined import files via filter: et-demo-content-import
			 */
			if ( ! empty( $_FILES ) ) { // Using manual file uploads?

				// Get paths for the uploaded files.
				$this->selected_import_files = PWPT_Helpers::process_uploaded_files( $_FILES, $this->log_file_path );

				// Set the name of the import files, because we used the uploaded files.
				$this->import_files[ $this->selected_index ]['import_file_name'] = esc_html__( 'Manually uploaded files', 'perfectwpthemes-toolkit' );
			} else if ( ! empty( $this->import_files[ $this->selected_index ] ) ) { // Use predefined import files from wp filter: et-demo-content-import.

				// Download the import files (content and widgets files) and save it to variable for later use.
				$this->selected_import_files = PWPT_Helpers::download_import_files(
					$this->import_files[ $this->selected_index ],
					$demo_import_start_time
				);

				// Check Errors.
				if ( is_wp_error( $this->selected_import_files ) ) {

					// Write error to log file and send an AJAX response with the error.
					PWPT_Helpers::log_error_and_send_ajax_response(
						$this->selected_import_files->get_error_message(),
						$this->log_file_path,
						esc_html__( 'Downloaded files', 'perfectwpthemes-toolkit' )
					);
				}

				// Add this message to log file.
				$log_added = PWPT_Helpers::append_to_file(
					sprintf(
						__( 'The import files for: %s were successfully downloaded!', 'perfectwpthemes-toolkit' ),
						$this->import_files[ $this->selected_index ]['import_file_name']
					) . PWPT_Helpers::import_file_info( $this->selected_import_files ),
					$this->log_file_path,
					esc_html__( 'Downloaded files', 'perfectwpthemes-toolkit' )
				);
			} else {

				// Send JSON Error response to the AJAX call.
				wp_send_json( esc_html__( 'No import files specified!', 'perfectwpthemes-toolkit' ) );
			}
		}

		/**
		 * 2. Import content.
		 * Returns any errors greater then the "error" logger level, that will be displayed on front page.
		 */
		$this->frontend_error_messages .= $this->import_content( $this->selected_import_files['content'] );
		

		/**
		 * 5. Import customize options.
		 */
		if ( ! empty( $this->selected_import_files['customizer'] ) && empty( $this->frontend_error_messages ) ) {
			$this->import_customizer( $this->selected_import_files['customizer'] );
		}

		/**
		 * 4. Import widgets.
		 */
		if ( ! empty( $this->selected_import_files['widgets'] ) && empty( $this->frontend_error_messages ) ) {
			$this->import_widgets( $this->selected_import_files['widgets'] );
		}

		/**
		 * 3. Before widgets import setup.
		 */
		$action = 'perfectwpthemes-demo-importer/before_widgets_import';
		if ( ( false !== has_action( $action ) ) && empty( $this->frontend_error_messages ) ) {

			// Run the before_widgets_import action to setup other settings.
			$this->do_import_action( $action, $this->import_files[ $this->selected_index ] );
		}
		

		/**
		 * 6. After import setup.
		 */
		$action = 'et-after-demo-content-import';
		//if ( ( false !== has_action( $action ) ) && empty( $this->frontend_error_messages ) ) {

		// Run the after_import action to setup other settings.
		$this->do_import_action( $action, $this->import_files[ $this->selected_index ] );
		//}

		// Display final messages (success or error messages).
		if ( empty( $this->frontend_error_messages ) ) {
			$response['message'] = sprintf(
				__( 'The demo import has finished. Please check your page and make sure that everything has imported correctly.', 'perfectwpthemes-toolkit' ) );

		} else {
			$response['message'] = $this->frontend_error_messages . '<br>';
			$response['message'] .= sprintf(
				__( 'The demo import has finished, but there were some import errors.%s More details about the errors can be found in this %s %slog file%s %s.', 'perfectwpthemes-toolkit' ),
				'<br>',
				'<strong>',
				'<a href="' . PWPT_Helpers::get_log_url( $this->log_file_path ) . '" target="_blank">',
				'</strong>',
				'</a>'
			);
		}

		wp_send_json( $response );
	}


	/**
	 * Import content from an WP XML file.
	 *
	 * @param string $import_file_path path to the import file.
	 */
	private function import_content( $import_file_path ) {

		$this->microtime = microtime( true );

		// This should be replaced with multiple AJAX calls (import in smaller chunks)
		// so that it would not come to the Internal Error, because of the PHP script timeout.
		// Also this function has no effect when PHP is running in safe mode
		// http://php.net/manual/en/function.set-time-limit.php.
		// Increase PHP max execution time.
		set_time_limit( apply_filters( 'perfectwpthemes-demo-importer/set_time_limit_for_demo_data_import', 300 ) );

		// Disable import of authors.
		add_filter( 'wxr_importer.pre_process.user', '__return_false' );

		// Check, if we need to send another AJAX request and set the importing author to the current user.
		add_filter( 'wxr_importer.pre_process.post', array( $this, 'new_ajax_request_maybe' ) );

		// Disables generation of multiple image sizes (thumbnails) in the content import step.
		if ( ! apply_filters( 'perfectwpthemes-demo-importer/regenerate_thumbnails_in_content_import', true ) ) {
			add_filter( 'intermediate_image_sizes_advanced',
				function () {
					return null;
				}
			);
		}

		// Import content.
		if ( ! empty( $import_file_path ) ) {
			ob_start();
			$this->importer->import( $import_file_path );
			$message = ob_get_clean();

			// Add this message to log file.
			$log_added = PWPT_Helpers::append_to_file(
				$message . PHP_EOL . esc_html__( 'Max execution time after content import = ', 'perfectwpthemes-toolkit' ) . ini_get( 'max_execution_time' ),
				$this->log_file_path,
				esc_html__( 'Importing content', 'perfectwpthemes-toolkit' )
			);
		}

		// Delete content importer data for current import from DB.
		delete_transient( 'PWPT_importer_data' );

		// Return any error messages for the front page output (errors, critical, alert and emergency level messages only).
		return $this->logger->error_output;
	}


	/**
	 * Import widgets from WIE or JSON file.
	 *
	 * @param string $widget_import_file_path path to the widget import file.
	 */
	private function import_widgets( $widget_import_file_path ) {

		// Widget import results.
		$results = array();

		// Create an instance of the Widget Importer.
		$widget_importer = new PWPT_Importer_Widget();

		// Import widgets.
		if ( ! empty( $widget_import_file_path ) ) {

			// Import widgets and return result.
			$results = $widget_importer->import_widgets( $widget_import_file_path );
		}

		// Check for errors.
		if ( is_wp_error( $results ) ) {

			// Write error to log file and send an AJAX response with the error.
			PWPT_Helpers::log_error_and_send_ajax_response(
				$results->get_error_message(),
				$this->log_file_path,
				esc_html__( 'Importing widgets', 'perfectwpthemes-toolkit' )
			);
		}

		ob_start();
		$widget_importer->format_results_for_log( $results );
		$message = ob_get_clean();

		// Add this message to log file.
		$log_added = PWPT_Helpers::append_to_file(
			$message,
			$this->log_file_path,
			esc_html__( 'Importing widgets', 'perfectwpthemes-toolkit' )
		);
	}


	/**
	 * Import customizer from a DAT file, generated by the Customizer Export/Import plugin.
	 *
	 * @param string $customizer_import_file_path path to the customizer import file.
	 */
	private function import_customizer( $customizer_import_file_path ) {

		// Try to import the customizer settings.
		$results = PWPT_Importer_Customizer_Importer::import_customizer_options( $customizer_import_file_path );

		// Check for errors.
		if ( is_wp_error( $results ) ) {

			// Write error to log file and send an AJAX response with the error.
			PWPT_Helpers::log_error_and_send_ajax_response(
				$results->get_error_message(),
				$this->log_file_path,
				esc_html__( 'Importing customizer settings', 'perfectwpthemes-toolkit' )
			);
		}

		// Add this message to log file.
		$log_added = PWPT_Helpers::append_to_file(
			esc_html__( 'Customizer settings import finished!', 'perfectwpthemes-toolkit' ),
			$this->log_file_path,
			esc_html__( 'Importing customizer settings', 'perfectwpthemes-toolkit' )
		);
	}


	/**
	 * Setup other things in the passed wp action.
	 *
	 * @param string $action the action name to be executed.
	 * @param array $selected_import with information about the selected import.
	 */
	private function do_import_action( $action, $selected_import ) {

		ob_start();
		do_action( $action, $selected_import );
		$message = ob_get_clean();

		// Add this message to log file.
		$log_added = PWPT_Helpers::append_to_file(
			$message,
			$this->log_file_path,
			$action
		);
	}


	/**
	 * Check if we need to create a new AJAX request, so that server does not timeout.
	 *
	 * @param array $data current post data.
	 *
	 * @return array
	 */
	public function new_ajax_request_maybe( $data ) {

		$time = microtime( true ) - $this->microtime;

		// We should make a new ajax call, if the time is right.
		if ( $time > apply_filters( 'perfectwpthemes-demo-importer/time_for_one_ajax_call', 25 ) ) {
			$this->ajax_call_number ++;
			$this->set_importer_data();

			$response = array(
				'status'  => 'newAJAX',
				'message' => 'Time for new AJAX request!: ' . $time,
			);

			// Add any output to the log file and clear the buffers.
			$message = ob_get_clean();

			// Add message to log file.
			$log_added = PWPT_Helpers::append_to_file(
				__( 'Completed AJAX call number: ', 'perfectwpthemes-toolkit' ) . $this->ajax_call_number . PHP_EOL . $message,
				$this->log_file_path,
				''
			);

			wp_send_json( $response );
		}

		// Set importing author to the current user.
		// Fixes the [WARNING] Could not find the author for ... log warning messages.
		$current_user_obj    = wp_get_current_user();
		$data['post_author'] = $current_user_obj->user_login;

		return $data;
	}

	/**
	 * Set current state of the content importer, so we can continue the import with new AJAX request.
	 */
	private function set_importer_data() {

		$data = array(
			'frontend_error_messages' => $this->frontend_error_messages,
			'ajax_call_number'        => $this->ajax_call_number,
			'log_file_path'           => $this->log_file_path,
			'selected_index'          => $this->selected_index,
			'selected_import_files'   => $this->selected_import_files,
		);

		$data = array_merge( $data, $this->importer->get_importer_data() );

		set_transient( 'PWPT_importer_data', $data, 0.5 * HOUR_IN_SECONDS );
	}

	/**
	 * Get content importer data, so we can continue the import with this new AJAX request.
	 */
	private function get_importer_data() {

		if ( $data = get_transient( 'PWPT_importer_data' ) ) {
			$this->frontend_error_messages = empty( $data['frontend_error_messages'] ) ? '' : $data['frontend_error_messages'];
			$this->ajax_call_number        = empty( $data['ajax_call_number'] ) ? 1 : $data['ajax_call_number'];
			$this->log_file_path           = empty( $data['log_file_path'] ) ? '' : $data['log_file_path'];
			$this->selected_index          = empty( $data['selected_index'] ) ? 0 : $data['selected_index'];
			$this->selected_import_files   = empty( $data['selected_import_files'] ) ? array() : $data['selected_import_files'];
			$this->importer->set_importer_data( $data );

			return true;
		}

		return false;
	}


	/**
	 * Get data from filters, after the theme has loaded and instantiate the importer.
	 */
	public function setup_plugin_with_filter_data() {

		// Get info of import data files and filter it.
		$this->import_files = PWPT_Helpers::validate_import_file_info( apply_filters( 'et-demo-content-import', array() ) );

		// Importer options array.
		$importer_options = apply_filters( 'perfectwpthemes-demo-importer/importer_options', array(
			'fetch_attachments' => true,
		) );

		// Logger options for the logger used in the importer.
		$logger_options = apply_filters( 'perfectwpthemes-demo-importer/logger_options', array(
			'logger_min_level' => 'warning',
		) );

		// Configure logger instance and set it to the importer.
		$this->logger            = new PWPT_Logger();
		$this->logger->min_level = $logger_options['logger_min_level'];

		//// Create importer instance with proper parameters.
		$this->importer = new PWPT_Importer_Main( $importer_options, $this->logger );
	}


	/**
	 * Get data related to server and display them.
	 */
	public function get_server_info() {

		$server_settings = array(
			'upload-max-filesize' => array(
				'label' => __( 'Upload Max filesize', 'perfectwpthemes-toolkit' ),
				'default' => '256M',
				'value' => ini_get( 'upload_max_filesize' )
			),
			'max-input-time' => array(
				'label' => __( 'Max Input Time', 'perfectwpthemes-toolkit' ),
				'default' => 300,
				'value' => ini_get( 'max_input_time' )
			),
			'memory-limit' => array(
				'label' => __( 'Memory Limit', 'perfectwpthemes-toolkit' ),
				'default' => '256M',
				'value' => ini_get( 'memory_limit' )
			),
			'max-execution-time' => array(
				'label' => __( 'Max Execution Time', 'perfectwpthemes-toolkit' ),
				'default' => 300,
				'value' => ini_get('max_execution_time')
			),
			'post-max-size' => array(
				'label' => __( 'PHP Post Max Size', 'perfectwpthemes-toolkit' ),
				'default' => '300M',
				'value' => ini_get( 'post_max_size' )
			),
		);
		?>
		<div class="pwpt-server-settings">

			<h3><?php echo __( 'Your server&rsquo;s settings', 'perfectwpthemes-toolkit' ); ?></h3>

			<table>
				<thead>
					<tr>
						<td><b><?php echo __( 'Settings', 'perfectwpthemes-toolkit' ); ?></b></td>
						<td><b><?php echo __( 'Expected', 'perfectwpthemes-toolkit' ); ?></b></td>
						<td><b><?php echo __( 'Obtained', 'perfectwpthemes-toolkit' ); ?></b></td>
					</tr>
				</thead>
				<?php
				if( $server_settings ) {

					foreach( $server_settings as $server_setting ) {

						$info_class = ( intval( $server_setting['value'] ) >= intval( $server_setting['default'] ) ) ? 'status-success' : 'status-error' ;

						?>
						<tr>
							<td><?php echo esc_html( $server_setting['label'] ); ?></td>
							<td><?php echo esc_html( $server_setting['default'] ); ?></td>
							<td><span class="<?php echo $info_class; ?>"><?php if( $server_setting['value'] ) { echo esc_html( $server_setting['value'] ); } else { echo '-'; } ?></span></td>
						</tr>
						<?php
					}
				}
				?>
			</table>
			<div class="pwpt-notice pwpt-notice-info">
				<p><?php echo __( 'Note: To import demo content properly, please check your server&rsquo;s setting before importing demo content. The Expected values are not perfect. So configure your server&rsquo;s settings as higher as possible.', 'perfectwpthemes-toolkit' ); ?></p>
			</div>
		</div>
		<?php
	}
}