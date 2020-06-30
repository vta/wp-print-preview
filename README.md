# Business Card Print Plugin

- Contributors: James Pham & Gordon Hackett
- Author link: https://jamespham.io & https://github.com/linuxwebexpert
- Tags: comments, spam
- Requires at least: 5.2.3
- Tested up to: 3.4
- Stable tag: 4.3
- License: GPLv2 or later
- License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description
 A plugin to transform VTA business card orders via Gravity Forms into a dynamic image preview. Withthe help of Imagick, the plugin will produce an image file that will be processed by the Copy Center team to print the
actual business cards. The user will also have the option to edit or delete submitted business card forms (not an
included feature of Gravity forms).

## Getting Started
Clone this repository in your WordPress `themes` folder and activate the plugin.

**Create Required Pages**
In your page editors, create two new pages. You can call them anything you would like.

**Business Confirm Page**
1. In this page, please create a new  block and insert a shortcode with `[business-card-preview]`.
2. Select the **Document** tab and under the **Permalink** dropdown menu, type in `business-card-confirmation`.

**Edit Business Card Page**
The steps here are similar to the previous page.
1. In this page, please create a new block and insert a shortcode with `[business-card-edit]`.
2. Select the **Document** tab and under the **Permalink** dropdown menu, type in `business-card-edit`.
3. Please add one more block with business card Gravity Forms shortcode: `[gravityform id="4" title="false"
description="false" ajax="true"]`

### Query Parameters
Back in Gravity Forms, select Business Cards to edit the form. Under each field, click the dropdown arrow to expand
available options. Under **Advanced**, check the box next to "Allow field to be populated dynamically".

In the order that the fields appear in the form, the parameter names are:
- job_title
- first_name
- last_name     (edited in the same field as first_name)
- department
- email
- address
- phone
- email
- fax

Once all of the fields are submitted, be sure to hit **Update** on the right hand side to save new field modifications.

### Dynamic Field Value Retrieval
**IMPORTANT**
Similar to previous instructions, please add the below values to the "_Admin Label_" field. Field IDs can vary from
 development and production variables, so to counteract this issue, field values rely on Admin Labels as keys.
 
 In the order that the fields appear in the form, the parameter names are:
 - job_title
 - name (only 1 value for both First Name and Last Name)
 - department
 - email
 - address
 - phone
 - email
 - fax

Once all of the above steps are set up correctly, the plugin should function.

## Updates

### v1.1.0 (6/24/2020)
Added a new private method to `includes/class-wp-print-helper.php`. The plugin now supports an output of 300 DPI image of a 25-up PDF
on a 12" x 18" stock. To activate this output, a new flag has been added to the `business_card_proof()` method as an argument. 

Cheers! :beer:
