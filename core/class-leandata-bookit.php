<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'LDBookIt_Main' ) ) :

  /**
   * Main LDBookIt_Main Class.
   *
   * @package		LDBOOKIT
   * @subpackage	Classes/LDBookIt_Main
   * @since		1.0.0
   * @author		LeanData
   */
  final class LDBookIt_Main {

    /**
     * The real instance
     *
     * @access	private
     * @since	1.0.0
     * @var		object|LDBookIt_Main
     */
    private static $instance;

    /**
     * LDBOOKIT helpers object.
     *
     * @access	public
     * @since	1.0.0
     * @var		object|LDBookIt_Helpers
     */
    public $helpers;

    /**
     * LDBOOKIT settings object.
     *
     * @access	public
     * @since	1.0.0
     * @var		object|LDBookIt_Settings
     */
    public $settings;

    /**
     * Throw error on object clone.
     *
     * Cloning instances of the class is forbidden.
     *
     * @access	public
     * @since	1.0.0
     * @return	void
     */
    public function __clone() {
      _doing_it_wrong( __FUNCTION__, esc_attr__( 'You are not allowed to clone this class.', 'leandata-bookit' ), '1.0.0' );
    }

    /**
     * Disable unserializing of the class.
     *
     * @access	public
     * @since	1.0.0
     * @return	void
     */
    public function __wakeup() {
      _doing_it_wrong( __FUNCTION__, esc_attr__( 'You are not allowed to unserialize this class.', 'leandata-bookit' ), '1.0.0' );
    }

    /**
     * Main LDBookIt_Main Instance.
     *
     * Insures that only one instance of LDBookIt_Main exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @access		public
     * @since		1.0.0
     * @static
     * @return		object|LDBookIt_Main	The one true LDBookIt_Main
     */
    public static function instance() {
      if ( ! isset( self::$instance ) && ! ( self::$instance instanceof LDBookIt_Main ) ) {
        self::$instance					= new LDBookIt_Main;
        self::$instance->base_hooks();
        self::$instance->includes();
        self::$instance->helpers		= new LDBookIt_Helpers();
        self::$instance->settings		= new LDBookIt_Settings();

        //Fire the plugin logic
        new LDBookIt_Run();

        /**
         * Fire a custom action to allow dependencies
         * after the successful plugin setup
         */
        do_action( 'LDBOOKIT/plugin_loaded' );
      }

      return self::$instance;
    }

    /**
     * Include required files.
     *
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    private function includes() {
      require_once LDBOOKIT_PLUGIN_DIR . 'core/includes/classes/class-leandata-bookit-helpers.php';
      require_once LDBOOKIT_PLUGIN_DIR . 'core/includes/classes/class-leandata-bookit-settings.php';

      require_once LDBOOKIT_PLUGIN_DIR . 'core/includes/classes/class-leandata-bookit-run.php';
    }

    /**
     * Add base hooks for the core functionality
     *
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    private function base_hooks() {
      add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
    }

    /**
     * Loads the plugin language files.
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_textdomain() {
      load_plugin_textdomain( 'leandata-bookit', false, dirname( plugin_basename( LDBOOKIT_PLUGIN_FILE ) ) . '/languages/' );
    }

  }

endif; // End if class_exists check.