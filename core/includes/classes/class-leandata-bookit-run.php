<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class LDBookIt_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		LDBOOKIT
 * @subpackage	Classes/LDBookIt_Run
 * @author		LeanData
 * @since		1.0.0
 */
class LDBookIt_Run{

  /**
   * Our LDBookIt_Run constructor 
   * to run the plugin logic.
   *
   * @since 1.0.0
   */
  function __construct(){
    $this->add_hooks();
  }

  /**
   * ######################
   * ###
   * #### WORDPRESS HOOKS
   * ###
   * ######################
   */

  /**
   * Registers all WordPress and plugin related hooks
   *
   * @access	private
   * @since	1.0.0
   * @return	void
   */
  private function add_hooks(){
  
    add_action( 'plugin_action_links_' . LDBOOKIT_PLUGIN_BASE, array( $this, 'add_plugin_action_link' ), 20 );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
    add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu_items' ), 100, 1 );

    add_action( 'admin_menu', array( $this, 'ldbookit_menu') );
    add_action( 'admin_init', array( $this, 'ldbookit_general_settings') );
    add_shortcode( 'ldbookit_form_page', array( $this, 'ldbookit_shortcode_form_page') );
    add_shortcode( 'ldbookit_thank_you_page', array( $this, 'ldbookit_shortcode_thank_you_page') );
    
    add_filter('wp_kses_allowed_html', array($this, 'allow_special_attributes'), 1);

  }

  /**
   * ######################
   * ###
   * #### WORDPRESS HOOK CALLBACKS
   * ###
   * ######################
   */

  /**
  * Create the settings page
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_page() {
    ?>
    <div class="wrap">
        <form method="post" action="options.php" name='settings_form' style="display: none;">
            <?php
            settings_fields('ldbookit_general_settings');
            do_settings_sections('ldbookit');
            submit_button();
            ?>
        </form>
        <div name='copy_description_1'>Use the buttons below to copy the generated code to your clipboard.</div> 
        <br/>
        <div name='copy_description_2'>Remember to fill out all fields above and click 'Save Changes' before attempting to copy to clipboard!</div>
        <button class='button button-secondary' style="margin-top: 12px;" name='copy_form_page_code_to_clipboard'>Copy form page code to clipboard</button>
        <br/>
        <button class='button button-secondary' style="display: none; margin-top: 12px;" name='copy_thank_you_page_code_to_clipboard'>Copy thank you page code to clipboard</button>
    </div>
    <?php
  }

  /**
  * Allow special attributes in wp_kses()
  *
  * @access	public
  * @since	1.0.0
  *
  */
  function allow_special_attributes( $allowedposttags ){
    $allowedposttags['script'] = array(
      'src' => true
    );
    $allowedposttags['div'] = array(
      'ld-name' => true,
      'data-tf-live' => true,
      'data-tf-hidden' => true,
      'data-tf-loaded' => true,
    );
    return $allowedposttags;
  }

  /**
  * Build BookIt menu
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_menu() {
      add_menu_page('LeanData BookIt For Forms Settings', 'LeanData BookIt', 'manage_options', 'ldbookit', array( $this, 'ldbookit_page'), plugins_url( 'leandata-bookit/core/includes/img/ld-logo-26x26.png' ));
  }

  /**
  * Register and add all settings fields
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_general_settings() {
    //LeanData settings
    register_setting('ldbookit_general_settings', 'ldbookit_form_provider');
    register_setting('ldbookit_general_settings', 'ldbookit_experience_type');
    register_setting('ldbookit_general_settings', 'ldbookit_org_id');
    register_setting('ldbookit_general_settings', 'ldbookit_trigger_node_name');
    register_setting('ldbookit_general_settings', 'ldbookit_hidden_field_name');
    
    //Hubspot settings
    register_setting('ldbookit_general_settings', 'ldbookit_hubspot_region');
    register_setting('ldbookit_general_settings', 'ldbookit_hubspot_portal_id');
    register_setting('ldbookit_general_settings', 'ldbookit_hubspot_form_id');

    //Typeform settings
    register_setting('ldbookit_general_settings', 'ldbookit_typeform_embed_code');

    //Custom settings
    register_setting('ldbookit_general_settings', 'ldbookit_custom_code_form_page');
    register_setting('ldbookit_general_settings', 'ldbookit_custom_code_thank_you_page');

    //Advanced settings
    //register_setting('ldbookit_general_settings', 'ldbookit_advanced_settings_toggle');
    //register_setting('ldbookit_general_settings', 'ldbookit_additional_code');

    //Other
    register_setting('ldbookit_general_settings', 'ldbookit_generated_form_page_code');
    register_setting('ldbookit_general_settings', 'ldbookit_generated_thank_you_page_code');

    add_settings_section('ldbookit', 'LeanData BookIt for Forms Configuration Settings',  array($this, 'ldbookit_section'), 'ldbookit');

    // LeanData settings
    add_settings_field('ldbookit_form_provider', 'Form Provider',array($this, 'ldbookit_form_provider'), 'ldbookit', 'ldbookit');
    add_settings_field('ldbookit_experience_type', 'Desired Experience Type', array($this, 'ldbookit_experience_type'), 'ldbookit', 'ldbookit');
    add_settings_field('ldbookit_org_id', 'Org ID', array($this, 'ldbookit_org_id'), 'ldbookit', 'ldbookit');
    add_settings_field('ldbookit_trigger_node_name', 'Trigger Node Name', array($this, 'ldbookit_trigger_node_name'), 'ldbookit', 'ldbookit');
    add_settings_field('ldbookit_hidden_field_name', 'Hidden Field Name', array($this, 'ldbookit_hidden_field_name'), 'ldbookit', 'ldbookit');

    //Hubspot settings
    add_settings_field('ldbookit_hubspot_region', 'HubSpot Region', array($this, 'ldbookit_hubspot_region'), 'ldbookit', 'ldbookit');
    add_settings_field('ldbookit_hubspot_portal_id', 'HubSpot Portal ID', array($this, 'ldbookit_hubspot_portal_id'), 'ldbookit', 'ldbookit');
    add_settings_field('ldbookit_hubspot_form_id', 'HubSpot Form ID', array($this, 'ldbookit_hubspot_form_id'), 'ldbookit', 'ldbookit');
    
    //Typeform settings
    add_settings_field('ldbookit_typeform_embed_code', 'Typeform provided form code', array($this, 'ldbookit_typeform_embed_code'), 'ldbookit', 'ldbookit');

    //Custom settings
    add_settings_field('ldbookit_custom_code_form_page', 'Custom code to go on form page', array($this, 'ldbookit_custom_code_form_page'), 'ldbookit', 'ldbookit');
    add_settings_field('ldbookit_custom_code_thank_you_page', 'Custom code to go on thank you page', array($this, 'ldbookit_custom_code_thank_you_page'), 'ldbookit', 'ldbookit');

    //Advanced settings
    //add_settings_field('ldbookit_advanced_settings_toggle', 'Display advanced settings (optional)', array($this, 'ldbookit_advanced_settings_toggle'), 'ldbookit', 'ldbookit');
    //add_settings_field('ldbookit_additional_code', 'Additional code', array($this, 'ldbookit_additional_code'), 'ldbookit', 'ldbookit');

    //Other
    add_settings_field('ldbookit_generated_form_page_code', '', array($this, 'ldbookit_generated_form_page_code'), 'ldbookit', 'ldbookit');
    add_settings_field('ldbookit_generated_thank_you_page_code', '', array($this, 'ldbookit_generated_thank_you_page_code'), 'ldbookit', 'ldbookit');
  }

  /**
  * Gather form page shortcode
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_shortcode_form_page($atts = array()) {
    return htmlspecialchars_decode(wp_kses_post(LDBOOKIT()->helpers->get_form_page_code($atts)));
  }

  /**
  * Define thank you page shortcode
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_shortcode_thank_you_page($atts = array()) {
    return htmlspecialchars_decode(wp_kses_post(LDBOOKIT()->helpers->get_thank_you_page_code($atts)));
  }

  /**
  * Define BookIt settings section
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_section() {
    echo '<div>
            <p>Before adding our BookIt shortcode, please fill out ALL of the following details</p>
            <p>Once complete, save your changes and refer to our documentation for instructions on enabling BookIt on your webpage<p>
          </div>';
  }

  /**
  * Define org id input
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_org_id() {
      echo '<input type="text" name="ldbookit_org_id" value="' . esc_attr(get_option('ldbookit_org_id')) . '" />';
  }

  /**
  * Define trigger node name input
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_trigger_node_name() {
      echo '<input type="text" name="ldbookit_trigger_node_name" value="' . esc_attr(get_option('ldbookit_trigger_node_name')) . '" />';
  }

  /**
  * Define hidden field name input
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_hidden_field_name() {
      echo '<input type="text" name="ldbookit_hidden_field_name" value="' . esc_attr(get_option('ldbookit_hidden_field_name')) . '" />';
  }

  /**
  * Define hubspot region input
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_hubspot_region() {
      echo '<input type="text" name="ldbookit_hubspot_region" value="' . esc_attr(get_option('ldbookit_hubspot_region')) . '" />';
  }

  /**
  * Define hubspot form id input
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_hubspot_form_id() {
      echo '<input type="text" name="ldbookit_hubspot_form_id" value="' . esc_attr(get_option('ldbookit_hubspot_form_id')) . '" />';
  }

  /**
  * Define hubspot portal id input
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_hubspot_portal_id() {
      echo '<input type="text" name="ldbookit_hubspot_portal_id" value="' . esc_attr(get_option('ldbookit_hubspot_portal_id')) . '" />';
  }

  /**
  * Define custom form page code textarea
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_custom_code_form_page() {
      echo '<textarea rows="10" cols="60" name="ldbookit_custom_code_form_page">' . esc_attr(get_option('ldbookit_custom_code_form_page')) . '</textarea>';
  }

  /**
  * Define custom thank you page code textarea
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_custom_code_thank_you_page() {
      echo '<textarea rows="10" cols="60" name="ldbookit_custom_code_thank_you_page">' . esc_attr(get_option('ldbookit_custom_code_thank_you_page')) . '</textarea>';
  }

  /**
  * Define typeform embed code textarea
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_typeform_embed_code() {
      echo '<textarea rows="4" cols="60" name="ldbookit_typeform_embed_code">' . esc_attr(get_option('ldbookit_typeform_embed_code')) . '</textarea>';
  }

  /**
  * Define advanced settings toggle (not in usage in current version)
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_advanced_settings_toggle() {
      echo '<input type="checkbox" name="ldbookit_advanced_settings_toggle" value="' . esc_attr(get_option('ldbookit_advanced_settings_toggle')) . '">';
  }

  /**
  * Define additional code textarea (not in usage in current version)
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_additional_code() {
      echo '<textarea rows="10" cols="60" name="ldbookit_additional_code">' . esc_attr(get_option('ldbookit_additional_code')) . '</textarea>';
  }

  /**
  * Define generated form page code textarea (used for storage for copy to clipboard functionality)
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_generated_form_page_code() {
      echo '<textarea style="display: none;" rows="10" cols="60" name="ldbookit_generated_form_page_code">' . esc_attr($this->ldbookit_shortcode_form_page()) . '</textarea>';
  }

  /**
  * Define generated form page code textarea (used for storage for copy to clipboard functionality)
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_generated_thank_you_page_code() {
      echo '<textarea style="display: none;" rows="10" cols="60" name="ldbookit_generated_thank_you_page_code">' . esc_attr($this->ldbookit_shortcode_thank_you_page()) . '</textarea>';
  }

  /**
  * Define form provider dropdown
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_form_provider() {
      $value = get_option('ldbookit_form_provider');
      ?>
      <select name="ldbookit_form_provider">
          <option value="" <?php selected($value, ''); ?> disabled>-- Select One --</option>
          <option value="marketo" <?php selected($value, 'marketo'); ?>>Marketo</option>
          <option value="hubspot" <?php selected($value, 'hubspot'); ?>>Hubspot</option>
          <option value="pardot" <?php selected($value, 'pardot'); ?>>Pardot</option>
          <option value="typeform" <?php selected($value, 'typeform'); ?>>Typeform</option>
          <option value="eloqua" <?php selected($value, 'eloqua'); ?>>Eloqua</option>
          <option value="gravityforms" <?php selected($value, 'gravityforms'); ?>>Gravity Forms</option>
          <option value="custom" <?php selected($value, 'custom'); ?>>Custom</option>
      </select>
      <?php
  }

  /**
  * Define experience type dropdown
  *
  * @access	public
  * @since	1.0.0
  *
  */
  public function ldbookit_experience_type() {
      $value = get_option('ldbookit_experience_type');
      ?>
      <select name="ldbookit_experience_type">
      <option value="" <?php selected($value, ''); ?> disabled>-- Select One --</option>
          <option value="form_page" <?php selected($value, 'form_page'); ?>>Display calendar on form page</option>
          <option value="thank_you_page" <?php selected($value, 'thank_you_page'); ?>>Display calendar on thank you page</option>
      </select>
      <?php
  }

  /**
  * Adds action links to the plugin list table
  *
  * @access	public
  * @since	1.0.0
  *
  * @param	array	$links An array of plugin action links.
  *
  * @return	array	An array of plugin action links.
  */
  public function add_plugin_action_link( $links ) {

    $links['our_shop'] = sprintf( '<a href="%s" title="Custom Link" style="font-weight:700;">%s</a>', 'https://test.test', __( 'Custom Link', 'leandata-bookit' ) );

    return $links;
  }

  /**
   * Enqueue the backend related scripts and styles for this plugin.
   * All of the added scripts andstyles will be available on every page within the backend.
   *
   * @access	public
   * @since	1.0.0
   *
   * @return	void
   */
  public function enqueue_backend_scripts_and_styles() {
    wp_enqueue_style( 'ldbookit-backend-styles', LDBOOKIT_PLUGIN_URL . 'core/includes/assets/css/backend-styles.css', array(), LDBOOKIT_VERSION, 'all' );
    wp_enqueue_script( 'ldbookit-backend-scripts', LDBOOKIT_PLUGIN_URL . 'core/includes/assets/js/settings.js', array(), LDBOOKIT_VERSION, false );
    wp_localize_script( 'ldbookit-backend-scripts', 'ldbookit', array(
      'plugin_name'   	=> __( 'LeanData BookIt', 'leandata-bookit' ),
    ));
  }

  /**
   * Add a new menu item to the WordPress topbar
   *
   * @access	public
   * @since	1.0.0
   *
   * @param	object $admin_bar The WP_Admin_Bar object
   *
   * @return	void
   */
  public function add_admin_bar_menu_items( $admin_bar ) {

    $admin_bar->add_menu( array(
      'id'		=> 'leandata-bookit-id', // The ID of the node.
      'title'		=> __( 'Demo Menu Item', 'leandata-bookit' ), // The text that will be visible in the Toolbar. Including html tags is allowed.
      'parent'	=> false, // The ID of the parent node.
      'href'		=> '#', // The ‘href’ attribute for the link. If ‘href’ is not set the node will be a text node.
      'group'		=> false, // This will make the node a group (node) if set to ‘true’. Group nodes are not visible in the Toolbar, but nodes added to it are.
      'meta'		=> array(
        'title'		=> __( 'Demo Menu Item', 'leandata-bookit' ), // The title attribute. Will be set to the link or to a div containing a text node.
        'target'	=> '_blank', // The target attribute for the link. This will only be set if the ‘href’ argument is present.
        'class'		=> 'leandata-bookit-class', // The class attribute for the list item containing the link or text node.
        'html'		=> false, // The html used for the node.
        'rel'		=> false, // The rel attribute.
        'onclick'	=> false, // The onclick attribute for the link. This will only be set if the ‘href’ argument is present.
        'tabindex'	=> false, // The tabindex attribute. Will be set to the link or to a div containing a text node.
      ),
    ));

    $admin_bar->add_menu( array(
      'id'		=> 'leandata-bookit-sub-id',
      'title'		=> __( 'My sub menu title', 'leandata-bookit' ),
      'parent'	=> 'leandata-bookit-id',
      'href'		=> '#',
      'group'		=> false,
      'meta'		=> array(
        'title'		=> __( 'My sub menu title', 'leandata-bookit' ),
        'target'	=> '_blank',
        'class'		=> 'leandata-bookit-sub-class',
        'html'		=> false,    
        'rel'		=> false,
        'onclick'	=> false,
        'tabindex'	=> false,
      ),
    ));

  }
}
