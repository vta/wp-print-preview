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


### Using the WP-Print-Preview-Util

- File located in: `includes/class-wp-print=preview-util`


##### Initializing and using the class

- First initialize the main Class:
```
# $wppu = new Wp_Print_Preview_Util(); 
```

- Next run the create_excel_parser function to create the parser object.
When creating the parser make sure to include the filename as it auto-detects
the file type: (xls vs xlsx) - It also supports other file types 
but is primarily configured to look for cells in excel sheets personalized to
the mass-mailer expectations.

```
## Absolute path
# $file_name = "includes/assets/6_column_example.xlsx";

## Create the parser
# $parser = $wppu->create_excel_parser($file_name);
```

- Run the parse_excel(response_type?) function with the optional response type.
- ##### Allowed response types: String: "PHP" or "JSON" 
- (If no response type is provided it will default to PHP)
PHP will return a PHP Object/Array, while JSON will return a JSON encoded array.

```
# $response = $parser->parse_excel("JSON");

# var_dump($response);
```

#### Testing with the PHP CLI (In the root directory):

```
php -r 'include "includes/class-wp-print-preview-util.php"; $wppu = new Wp_Print_Preview_Util(); $parser = $wppu->create_excel_parser("includes/assets/6_column_example.xlsx"); $response = $parser->parse_excel("PHP"); var_dump($response);'

```

## Updates

### v1.1.0 (6/24/2020)
Added a new private method to `includes/class-wp-print-helper.php`. The plugin now supports an output of 300 DPI image of a 25-up PDF
on a 12" x 18" stock. To activate this output, a new flag has been added to the `business_card_proof()` method as an argument. 

Cheers! :beer:
