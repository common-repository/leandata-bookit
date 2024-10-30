=== LeanData BookIt ===
Contributors: leandata
Tags: LeanData, BookIt, Implementation, Calendar, BFF
Requires at least: 4.7
Tested up to: 6.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

LeanData's official WordPress plugin serves as a tool to help attach BookIt to your form in just a few steps.

== Description ==

LeanData's official WordPress plugin serves as a tool to help attach BookIt to your form in just a few steps.

LeanData's BookIt for Forms product is a scheduling tool that allows users to instantly book qualified meetings directly from your webforms. 

Prospects can submit data to a lead generation form and if they are qualified by admin defined rules and logic, will be presented with a calendar of the best available rep's meeting availability.

This plugin will allow you to generate the code that is required to attach BookIt to your forms and insert it directly on your webpage via shortcode.

This plugin is meant to serve as a tool during the implementation process and not as a complete replacement of our standard BookIt for Forms implementation process.

Any implementation of LeanData BookIt for Forms on your webpage currently must go through our professional services team and requires provisioned access to the LeanData app in Salesforce.

== Installation ==

1. Upload `leandata-bookit.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Fill out the setup form in the LeanData BookIt tab and save your changes
4. Add the code to attach BookIt to your form(s) on your page by simply making use of the ldbookit_form_page and ldbookit_thank_you_page shortcode (as necessary)

== Frequently Asked Questions ==

= How does the code generated determine whether or not to display a calendar to the webpage visitor? = 

If the user fills out a form that BookIt is attached to (via the code generated with this plugin and inserted on the page via shortcode), it will enter through a graph that lives within LeanData in Salesforce that runs through logic to determine whether or not to show the visitor a calendar dependent on their form inputs.

= What is my Org ID? =

This refers to your 18 digit Salesforce Org Id of the Salesforce instance that you are running LeanData through.

= What is a hidden field name? =

The name attribute of a hidden field input on your form that the code generated in this plugin and inserted on your webpage via shortcode is able to insert a unique id into upon page load.
With an incorrect value for this setting, an invalid or missing hidden field, or a missing mapping from your forms to a field on created leads in Salesforce, BookIt will not function correctly.

= What is a trigger node name? = 

The name given to the trigger node in your BookIt graph within LeanData you would like to enter through.

= What is a thank you page? =

The thank you page is what we call the page that a form redirects to upon submission (if applicable).

= What do the experience type options mean? =

We provide two experience type options for where to display the calendar.

The first is on the same page as the form upon submission. This requires that the form does not have any redirect functionality enabled upon submission.

We can then redirect to a different page upon a meeting being booked or the scheduler being closed via settings within the LeanData app in Salesforce.

The second is on the page that gets redirected to upon submission (thank you page).

In both cases, we will display the calendar in a popup modal that appears over the page containing the code snippets generated in this plugin and inserted on the page via shortcode.

= Which shortcode should I include and where? ==

We add two different shortcodes with this plugin, ldbookit_form_page and ldbookit_thank_you_page.

If you wish to display the calendar on the form page, you will just need to insert the ldbookit_form_page shortcode on the page containing your form.

If you wish to display the calendar on the page your form redirects to upon submission, you will need to insert the ldbookit_form_page shortcode on the page containing your form and the ldbookit_thank_you_page shortcode on the thank you page that your form redirects to upon submission.

= How do you generate the shortcode? What goes into it? = 

To generate the JavaScript code that is being inserted on your page via shortcode, we make use of the form provider and experience type values filled out in the plugin configuration settings to determine what code is necessary and the org id, trigger node name, and hidden field name values to configure the parameters the code needs.

If the data in the plugin configuration settings is missing, incomplete, or hasn't been saved, the code generated and placed on your page via the shortcode may be missing or incomplete.

== 3rd Party imports/references ==

This plugin refers to external urls from the following sources:

https://cdn.leandata.com/js-snippet/ld-book-v2.js

This is a code import used in the code snippets we generate that allow us to attach BookIt to user webpages. 

The purpose of this import code is to abstract the code that we need to add to users webpages.
This will be added to the users' webpages any time they have a valid configuration in the LeanData BookIt settings admin panel and they are using the shortcode generated in this plugin.

This file is entirely owned by LeanData.

The following link contains information on the code exposed via this import code: https://leandatahelp.zendesk.com/hc/en-us/articles/20483792593947-LeanData-BookIt-for-Forms-Custom-Implementation

The following link contains LeanData's terms of service: https://www.leandata.com/terms-of-service/

The following links contains LeanData's privacy policy and data processing addendum: https://www.leandata.com/privacy/, https://www.leandata.com/leandata-dpa/

This import code, in combination with the code snippets that the plugin generates, perform the following actions:

1. Attach to the form's submit event and scrape the data off of the form upon submission

2. Send the data scraped off of the form to our endpoint that generates a calendar link at which the user can book a meeting from as a response.



https://leandatahelp.zendesk.com/hc/en-us/articles/22166118892315-Adding-a-Hidden-field-in-Marketo
https://leandatahelp.zendesk.com/hc/en-us/articles/22145335040411-Adding-a-Hidden-Field-in-Hubspot
https://leandatahelp.zendesk.com/hc/en-us/articles/22167162368667-Adding-a-Hidden-Field-in-Pardot
https://leandatahelp.zendesk.com/hc/en-us/articles/22548563537435-Adding-a-Hidden-Field-in-Eloqua
https://leandatahelp.zendesk.com/hc/en-us/articles/22503820764187-Adding-a-Hidden-Field-In-Typeform
https://leandatahelp.zendesk.com/hc/en-us/articles/22549211292187-Adding-a-Hidden-Field-In-Gravity-Forms

These 6 articles contain instructions on how to complete a step in implementing BookIt on your forms - adding a hidden field to your form that our code is able to insert a unique id into.

Adding this unique id is handled through the code this plugin generates by simply utilizing a querySelector for the input by name that is provided to the code as a parameter.

Users are aware that this unique id insertion is happening and the purpose of this hidden field is called out in this documentation.

This documentation is visible as a link in the LeanData BookIt settings admin panel.



js.hsforms.net/forms/v2.js

This is an import file that is used in Hubspot's code to embed a Hubspot form on a webpage.
We include this import if a user is using Hubspot forms to insert the form on their page for them, given other Hubspot form configuration parameters.

This will be added to the users' webpages any time they have a valid configuration in the LeanData BookIt settings admin panel, they have hubspot selected in the settings form as their form provider, and they are using the shortcode generated in this plugin.

The following are links to Hubspot's general forms documentation for more information: https://knowledge.hubspot.com/forms/create-forms, https://knowledge.hubspot.com/forms/how-can-i-share-a-hubspot-form-if-im-using-an-external-site

The following is a link to Hubspot's privacy policy: https://legal.hubspot.com/privacy-policy

The following are links to Hubspot's terms of service: https://legal.hubspot.com/terms-of-service, https://legal.hubspot.com/website-terms-of-use, https://legal.hubspot.com/product-specific-terms



https://leandata.com
www.leandata.com

These are simply links to LeanData's website. And are only referenced as the Plugin URI and Author URI.

The following link contains LeanData's terms of service: https://www.leandata.com/terms-of-service/

The following links contains LeanData's privacy policy and data processing addendum: https://www.leandata.com/privacy/, https://www.leandata.com/leandata-dpa/

== Screenshots ==
N/A

== Changelog ==
= 1.0.2 =
* Aligns stable tag

= 1.0.1 =
* Updated README.txt
* Fixes plugin breaking bug in initial version

== Upgrade Notice ==
= 1.0.1 =
Fixes plugin breaking bug in initial version.

