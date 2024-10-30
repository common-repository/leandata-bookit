/* This file contains all of the logic for dynamically hiding/showing the plugin settings fields */

jQuery(document).ready(function ($) {
  let copyFormPageCodeToClipboardTimeoutActive = false;
  let copyThankYouPageCodeToClipboardTimeoutActive = false;

  hideAllFields($);
                  
  $('[name="ldbookit_form_provider"]').on('change', function (e) {
    updateFormProviderSpecificFields($);
    adjustExperienceOptions($);
  });

  $('[name="ldbookit_experience_type"]').on('change', function (e) {
    updateExperienceTypeSpecificFields($);
  });

  $('[name="submit"]').on('click', function (e) {
    hideAllFields($);
    showFormAndButtons($);
  });

  $('[name="ldbookit_advanced_settings_toggle"]').on('change', function (e) {
    this.checked ? showAllAdvancedFields($) : hideAllAdvancedFields($);
  });

  $('[name="copy_form_page_code_to_clipboard"]').on('click', function (e) {
    if (copyFormPageCodeToClipboardTimeoutActive) return;
    copyFormPageCodeToClipboardTimeoutActive = true;

    let button = $('[name="copy_form_page_code_to_clipboard"]');
    let source = $("[name='ldbookit_generated_form_page_code']");
    let originalText = button.text();

    copyToClipboardButtonClick(button, source);

    setTimeout(() => {
        button.text(originalText);
        copyFormPageCodeToClipboardTimeoutActive = false;
    }, 4000);
  });

  $('[name="copy_thank_you_page_code_to_clipboard"]').on('click', function (e) {
    if (copyThankYouPageCodeToClipboardTimeoutActive) return;
    copyThankYouPageCodeToClipboardTimeoutActive = true;

    let button = $('[name="copy_thank_you_page_code_to_clipboard"]');
    let source = $("[name='ldbookit_generated_thank_you_page_code']");
    let originalText = button.text();

    copyToClipboardButtonClick(button, source);
    
    setTimeout(() => {
        button.text(originalText);
        copyThankYouPageCodeToClipboardTimeoutActive = false;
    }, 4000);
  });

  showFormAndButtons($);
});

function hideAllFields($) {
  hideAllFormProviderSpecificFields($);
  hideAllAdvancedFields($);
  $("[name='ldbookit_generated_form_page_code']").parent().parent().hide();
  $("[name='ldbookit_generated_thank_you_page_code']").parent().parent().hide();
}

function showFormAndButtons($) {
  updateFormProviderSpecificFields($);
  updateExperienceTypeSpecificFields($);
  adjustExperienceOptions($);
  $('[name="settings_form"]').show();
}

function hideAllAdvancedFields($) {
  $('[name="ldbookit_additional_code"]').parent().parent().hide(); 
}

function showAllAdvancedFields($) {
  $('[name="ldbookit_additional_code"]').parent().parent().show(); 
}

function updateExperienceTypeSpecificFields($) {
  hideAllExperienceTypeSpecificFields($);
  showExperienceTypeSpecificFields($);
}

function hideAllExperienceTypeSpecificFields($) {
  $('[name="ldbookit_custom_code_form_page"]').parent().parent().hide(); 
  $('[name="ldbookit_custom_code_thank_you_page"]').parent().parent().hide(); 
  $('[name="copy_thank_you_page_code_to_clipboard"]').hide();
}

function showExperienceTypeSpecificFields($) {
  let experienceType = $('[name="ldbookit_experience_type"]').find(":selected").val();
  let formProvider = $('[name="ldbookit_form_provider"]').find(":selected").val();
  if (experienceType === 'thank_you_page') {
    if (formProvider === 'custom') {
      $('[name="ldbookit_custom_code_form_page"]').parent().parent().show(); 
      $('[name="ldbookit_custom_code_thank_you_page"]').parent().parent().show(); 
    }
    $('[name="copy_form_page_code_to_clipboard"]').show();
    $('[name="copy_thank_you_page_code_to_clipboard"]').show();
    $('[name="copy_description_1"]').show();
    $('[name="copy_description_2"]').show();
  }
  else if (experienceType === 'form_page') {
    if (formProvider === 'custom') {
      $('[name="ldbookit_custom_code_form_page"]').parent().parent().show(); 
    }
  }
}

function updateFormProviderSpecificFields($) {
  hideAllFormProviderSpecificFields($);
  showFormProviderSpecificFields($);
}

function hideAllFormProviderSpecificFields($) {
  $('[name="ldbookit_hubspot_region"]').parent().parent().hide(); 
  $('[name="ldbookit_hubspot_portal_id"]').parent().parent().hide(); 
  $('[name="ldbookit_hubspot_form_id"]').parent().parent().hide(); 
  $('[name="ldbookit_typeform_embed_code"]').parent().parent().hide();                 
  $('[name="ldbookit_custom_code_form_page"]').parent().parent().hide(); 
  $('[name="ldbookit_custom_code_thank_you_page"]').parent().parent().hide(); 
}

function showFormProviderSpecificFields($) {
  let formProvider = $('[name="ldbookit_form_provider"]').find(":selected").val();
  if (formProvider === 'hubspot') {
    $('[name="ldbookit_hubspot_region"]').parent().parent().show(); 
    $('[name="ldbookit_hubspot_portal_id"]').parent().parent().show(); 
    $('[name="ldbookit_hubspot_form_id"]').parent().parent().show(); 
  }
  if (formProvider === 'custom') {
    $('[name="ldbookit_custom_code_form_page"]').parent().parent().show(); 
    $('[name="ldbookit_custom_code_thank_you_page"]').parent().parent().show(); 
  }
  if (formProvider === 'typeform') {
    $('[name="ldbookit_typeform_embed_code"]').parent().parent().show(); 
  }

  setHelperLink($, $('[name="ldbookit_hidden_field_name"]'), getHiddenFieldHelperLink($));
}

function adjustExperienceOptions($) {
  let formProvider = $('[name="ldbookit_form_provider"]').find(":selected").val();
  let experienceTypeSelect = $('select[name="ldbookit_experience_type"]');
  let formPageOption = experienceTypeSelect.find('option[value="form_page"]');

  // eloqua and typeform only have form on ty page options
  if (formProvider === 'eloqua' || formProvider === 'typeform') {
    if (formPageOption.is(':selected')) {
      experienceTypeSelect.val(experienceTypeSelect.find('option[value="thank_you_page"]').val());
    }
    formPageOption.hide();
  }
  else {
    formPageOption.show();
  }

  updateExperienceTypeSpecificFields($);
}

function setHelperLink($, element, link) {
  if (!link) return;
  let thTag = element.parent().siblings('th');
  let label = thTag.text();
  let aTag = thTag.children('a')[0];
  if (aTag) {
    $(aTag).attr('href', link);
  }
  else {
    element.parent().siblings('th').html(`${label} <a href=${link} style='text-decoration: none;' target="_blank">(?)</a>`);
  }
}

function copyToClipboardButtonClick(button, source) {
  if (!source.val()) {
    button.text("Unable to copy code to clipboard - please save your changes!");
    return;
  }
  navigator.clipboard.writeText(source.val())
  .then(function() {
    button.text("Code successfully copied to clipboard!");
  })
  .catch(function(err) {
    console.error("Unable to copy code to clipboard", err);
    button.text("Unable to copy code to clipboard!");
  });
}

function getHiddenFieldHelperLink($) {
  let formProvider = $('[name="ldbookit_form_provider"]').find(":selected").val();
  return helperLinkMappings[formProvider];
}

// Map form provider to hidden field helper link (zendesk articles)
const helperLinkMappings = {
  'marketo': 'https://leandatahelp.zendesk.com/hc/en-us/articles/22166118892315-Adding-a-Hidden-field-in-Marketo',
  'hubspot': 'https://leandatahelp.zendesk.com/hc/en-us/articles/22145335040411-Adding-a-Hidden-Field-in-Hubspot',
  'pardot': 'https://leandatahelp.zendesk.com/hc/en-us/articles/22167162368667-Adding-a-Hidden-Field-in-Pardot',
  'eloqua': 'https://leandatahelp.zendesk.com/hc/en-us/articles/22548563537435-Adding-a-Hidden-Field-in-Eloqua',
  'typeform': 'https://leandatahelp.zendesk.com/hc/en-us/articles/22503820764187-Adding-a-Hidden-Field-In-Typeform',
  'gravityforms': 'https://leandatahelp.zendesk.com/hc/en-us/articles/22549211292187-Adding-a-Hidden-Field-In-Gravity-Forms'
}
