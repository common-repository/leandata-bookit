<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class LDBookIt_Helpers
 *
 * This class contains repetitive functions that
 * are used globally within the plugin.
 *
 * @package    LDBOOKIT
 * @subpackage  Classes/LDBookIt_Helpers
 * @author    LeanData
 * @since    1.0.0
 */
class LDBookIt_Helpers{
  /**
   * Find snippet parameter placeholders (<Org Id>, <Trigger Node Name>, etc.) and replace with actual values
   *
   * @access private
   * @param  string  $org_id  User's Org Id
   * @param  string  $trigger_node_name  Name of trigger node user wants snippet to enter through
   * @param  string  $hidden_field_name  Name of hidden field to ingest log id
   * @param  string  $code_with_placeholders Code containing placeholders  
   * @since  1.0.0
   *
   * @return  string
   */
   private function find_and_replace_snippet_params($org_id, $trigger_node_name, $hidden_field_name, $code_with_placeholders) {
      $search  = array('<Org Id>', '<Trigger Node Name>', '<Node Name>', '<Hidden Field Name>');
      $replace = array($org_id, $trigger_node_name, $trigger_node_name, $hidden_field_name);
      return str_replace($search, $replace, $code_with_placeholders);
   }
  
  /**
   * Strip various formats of leading whitespace at the beginning of each line to remove indents due to code formatting
   *
   * @access private
   * @param  string  $code  Code to be cleaned
   * @since  1.0.0
   *
   * @return  string
   */
  private function strip_leading_whitespace($code) {
    $code = preg_replace('/^\s{8}/m', '', $code);
    $code = preg_replace('/^\t{4}/m', '', $code);
    return $code;
   }

  /**
   * Clean up and format the generated snippet
   *
   * @access private
   * @param  string  $org_id  User's Org Id
   * @param  string  $trigger_node_name  Name of trigger node user wants snippet to enter through
   * @param  string  $hidden_field_name  Name of hidden field to ingest log id
   * @param  string  $code Code to format
   * @since  1.0.0
   *
   * @return  string
   */
  private function format_code($org_id, $trigger_node_name, $hidden_field_name, $code) {
    $code = $this->find_and_replace_snippet_params($org_id, $trigger_node_name, $hidden_field_name, $code);
    $code = $this->strip_leading_whitespace($code);
    return $code;
   }

  /**
   * Modify given typeform embed code to build a working snippet
   *
   * @access private
   * @param  string  $typeform_embed_code  Given Typeform embed code
   * @param  string  $org_id  User's Org Id
   * @param  string  $hidden_field_name  Name of hidden field to ingest log id
   * @since  1.0.0
   *
   * @return  string
   */
  private function build_typeform_code($typeform_embed_code, $org_id, $hidden_field_name) {
    $bookit_code = '
        <script>
          var orgId = "'.$org_id.'";
          var hiddenFieldName = "'.$hidden_field_name.'";
          
          (function(orgId, hiddenFieldName) {
            let hiddenUID = `${orgId}_${Date.now()}_${Math.floor(Math.random() * Number.MAX_SAFE_INTEGER)}`;
            let form = document.querySelector("[ld-name=\'typeform\']");
            form.setAttribute("data-tf-hidden", `${hiddenFieldName}=${hiddenUID}`);
            window.localStorage.setItem("LDBookItV2_tempSavedUID", hiddenUID);
          })(orgId, hiddenFieldName)
        </script>';

    $dom = new DOMDocument;
    if ($typeform_embed_code === null || !isset($typeform_embed_code) || $typeform_embed_code === '') {
        return '';
    }
    $dom->loadHTML($typeform_embed_code, LIBXML_HTML_NODEFDTD);
    $firstElement = $dom->getElementsByTagName('div')->item(0);
    if ($firstElement === null || !isset($firstElement)) {
        return '';
    }
    $firstElement->setAttribute("ld-name","typeform");
    $firstElementString = $dom->saveHTML($firstElement);
    $secondElementString = $dom->saveHTML($dom->getElementsByTagName('script')->item(0));
    $snippet = $firstElementString . $bookit_code . $secondElementString;
    return $snippet;
   }

   /**
   * ######################
   * ###
   * #### CALLABLE FUNCTIONS
   * ###
   * ######################
   */

  /**
   * Generate code to insert on form page
   *
   * @param  array $atts  Parameters passed into shortcode call
   * @since  1.0.0
   *
   * @return  string
   */
   public function get_form_page_code($atts = array()) {
    // Retrieve shortcode parameters (these take precedence over equivalent settings parameters)
    $atts = shortcode_atts(
      array(
        'org_id' => '',
        'trigger_node_name' => '',
        'hidden_field_name' => '',
        'form_provider' => '',
        'experience_type' => '',
        'hubspot_region' => '',
        'hubspot_portal_id' => '',
        'hubspot_form_id' => '',
      ), $atts, 'ldbookit_form_page' );

    // Retrieve settings options
    $org_id = $atts['org_id'] ?: get_option('ldbookit_org_id');
    $trigger_node_name = $atts['trigger_node_name'] ?: get_option('ldbookit_trigger_node_name');
    $hidden_field_name = $atts['hidden_field_name'] ?: get_option('ldbookit_hidden_field_name');
    $form_provider = $atts['form_provider'] ?: get_option('ldbookit_form_provider');
    $experience_type = $atts['experience_type'] ?: get_option('ldbookit_experience_type');

    // Form provider specific options
    $hubspot_region = $atts['hubspot_region'] ?: get_option('ldbookit_hubspot_region');
    $hubspot_portal_id = $atts['hubspot_portal_id'] ?: get_option('ldbookit_hubspot_portal_id');
    $hubspot_form_id = $atts['hubspot_form_id'] ?: get_option('ldbookit_hubspot_form_id');
    $typeform_embed_code = get_option('ldbookit_typeform_embed_code');
    $custom_code_form_page = get_option('ldbookit_custom_code_form_page');

    $output = '';

    if ($form_provider === 'hubspot' && $experience_type === 'form_page') {
      $output = '
        <script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>
        <script>
          hbspt.forms.create({
              region: "'.$hubspot_region.'",
              portalId: "'.$hubspot_portal_id.'",
              formId: "'.$hubspot_form_id.'",
              onFormReady: ((form) => trySettingFormTarget(form)),
          });
        </script>
        <script>
          function trySettingFormTarget(form) {
              if (window["LDBookItV2"] && window["LDBookItV2"].setFormTarget) {
                  LDBookItV2.setFormTarget(form.id ? form : form[0]);
              }
              else {
                  window.setTimeout(() => trySettingFormTarget(form), 2000);
              }
          }
          var _ld_scriptEl = document.createElement("script");
          _ld_scriptEl.src = "https://cdn.leandata.com/js-snippet/ld-book-v2.js";
          _ld_scriptEl.addEventListener("load", function () {
              LDBookItV2.initialize("'.$org_id.'", "'.$trigger_node_name.'", "'.$hidden_field_name.'", {autoSubmit: true});
              LDBookItV2.setFormProvider("hubspot_embed");
          });
          document.body.appendChild(_ld_scriptEl);
        </script>';
    }
    else if ($form_provider === 'hubspot' && $experience_type === 'thank_you_page') {
      $output = '
        <script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>
        <script>
          hbspt.forms.create({
              region: "'.$hubspot_region.'",
              portalId: "'.$hubspot_portal_id.'",
              formId: "'.$hubspot_form_id.'",
              onFormReady: ((form) => trySettingFormTarget(form)),
          });
        </script>
        <script>
          function trySettingFormTarget(form) {
              if (window["LDBookItV2"] && window["LDBookItV2"].setFormTarget) {
                  LDBookItV2.setFormTarget(form.id ? form : form[0]);
              }
              else {
                  window.setTimeout(() => trySettingFormTarget(form), 2000);
              }
          }
          var _ld_scriptEl = document.createElement("script");
          _ld_scriptEl.src = "https://cdn.leandata.com/js-snippet/ld-book-v2.js";
          _ld_scriptEl.addEventListener("load", function () {
              LDBookItV2.initialize("'.$org_id.'", "'.$trigger_node_name.'", "'.$hidden_field_name.'");
              LDBookItV2.setFormProvider("hubspot_embed");
          });
          document.body.appendChild(_ld_scriptEl);
        </script>';
    }
    else if ($form_provider === 'marketo' && $experience_type === 'form_page') {
      $output = '
        <script>
          var _ld_scriptEl = document.createElement("script");
          _ld_scriptEl.src = "https://cdn.leandata.com/js-snippet/ld-book-v2.js";
          _ld_scriptEl.addEventListener("load", function () {
              const urlParams = new URLSearchParams(window.location.search);
              if (urlParams.has("aliId")) {
                LDBookItV2.initialize("'.$org_id.'", "'.$trigger_node_name.'", "'.$hidden_field_name.'");
                  LDBookItV2.submit();
              }
              else {
                LDBookItV2.initialize("'.$org_id.'", "'.$trigger_node_name.'", "'.$hidden_field_name.'");
                  LDBookItV2.setFormProvider("marketo");
              }
          });
          document.body.appendChild(_ld_scriptEl);
        </script>';
    }
    else if ($form_provider === 'gravityforms' || $form_provider === 'eloqua' || (($form_provider === 'marketo' ) && $experience_type === 'thank_you_page')) {
      $output = '
        <script>
          var _ld_scriptEl = document.createElement("script");
          _ld_scriptEl.src = "https://cdn.leandata.com/js-snippet/ld-book-v2.js";
          _ld_scriptEl.addEventListener("load", function () {
              LDBookItV2.initialize("'.$org_id.'", "'.$trigger_node_name.'", "'.$hidden_field_name.'");
              LDBookItV2.setFormProvider("'.$form_provider.'");
          });
          document.body.appendChild(_ld_scriptEl);
        </script>';
    }
    else if ($form_provider === 'typeform') {
      $output = $this->build_typeform_code($typeform_embed_code, $org_id, $hidden_field_name);
    }
    else if ($form_provider === 'pardot') {
      $output = '
        <script>
          var _ld_scriptEl = document.createElement("script");
          _ld_scriptEl.src = "https://cdn.leandata.com/js-snippet/ld-book-v2.js";
          _ld_scriptEl.addEventListener("load", function() {
              LDBookItV2.initialize("'.$org_id.'", "'.$trigger_node_name.'", "'.$hidden_field_name.'");
          });
          document.body.appendChild(_ld_scriptEl);
        </script>';
    }
    else if ($form_provider === 'custom' && $custom_code_form_page) {
      $output = $custom_code_form_page;
    }
    return $this->format_code($org_id, $trigger_node_name, $hidden_field_name, $output);
   }

   /**
   * Generate code to insert on thank you page
   *
   * @param  array $atts  Parameters passed into shortcode call
   * @since  1.0.0
   *
   * @return  string
   */
  public function get_thank_you_page_code($atts = array()) {
  // Retrieve shortcode parameters (these take precedence over equivalent settings parameters)
  $atts = shortcode_atts(
    array(
      'org_id' => '',
      'trigger_node_name' => '',
      'hidden_field_name' => '',
      'form_provider' => '',
      'experience_type' => '',
    ), $atts, 'ldbookit_thank_you_page' );

    // Retrieve settings options
    $org_id = $atts['org_id'] ?: get_option('ldbookit_org_id');
    $trigger_node_name = $atts['trigger_node_name'] ?: get_option('ldbookit_trigger_node_name');
    $hidden_field_name = $atts['hidden_field_name'] ?: get_option('ldbookit_hidden_field_name');
    $form_provider = $atts['form_provider'] ?: get_option('ldbookit_form_provider');
    $experience_type = $atts['experience_type'] ?: get_option('ldbookit_experience_type');

    // Form provider specific options
    $custom_code_thank_you_page = get_option('ldbookit_custom_code_thank_you_page');

    $output = '';

    // There is no case where we would have thank you page code if we want the calendar on the form page
    if ($experience_type !== 'thank_you_page') {
      return $output;
    }
    else if ($form_provider === 'custom' && $custom_code_thank_you_page) {
      $output = $custom_code_thank_you_page;
    }
    else if ($form_provider === 'typeform') {
      $output = '
        <script>
          var _ld_scriptEl = document.createElement("script");
          _ld_scriptEl.src = "https://cdn.leandata.com/js-snippet/ld-book-v2.js";
          _ld_scriptEl.addEventListener("load", function() {
          LDBookItV2.initialize("'.$org_id.'", "'.$trigger_node_name.'", "'.$hidden_field_name.'");
          LDBookItV2.setFormProvider("typeform");
          if (LDBookItV2.validateTempUID()) {
              LDBookItV2.saveFormDataFromURLParams();
              LDBookItV2.submit();
          }
          });
          document.body.appendChild(_ld_scriptEl);
        </script>';
    }
    else if ($form_provider === 'gravityforms') {
      $output = '
        <script>
          var _ld_scriptEl = document.createElement("script");
          _ld_scriptEl.src = "https://cdn.leandata.com/js-snippet/ld-book-v2.js";
          _ld_scriptEl.addEventListener("load", function() {
          LDBookItV2.initialize("'.$org_id.'", "'.$trigger_node_name.'", "'.$hidden_field_name.'");
          if (LDBookItV2.validateTempUID()) {
              LDBookItV2.saveFormDataFromURLParams();
              LDBookItV2.submit();
          }
          });
          document.body.appendChild(_ld_scriptEl);
        </script>';
    }
    else {
      $output = '
        <script>
          var _ld_scriptEl = document.createElement("script");
          _ld_scriptEl.src = "https://cdn.leandata.com/js-snippet/ld-book-v2.js";
          _ld_scriptEl.addEventListener("load", function () {
              LDBookItV2.initialize("'.$org_id.'", "'.$trigger_node_name.'", "'.$hidden_field_name.'");
              LDBookItV2.submit();
          });
          document.body.appendChild(_ld_scriptEl);
        </script>';
    }

    return $this->format_code($org_id, $trigger_node_name, $hidden_field_name, $output);
  }
}
