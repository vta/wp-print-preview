/**
 * @title WP Print Preview - Mass Mailer Config Scripts
 * @author James Pham
 * @lastUpdated 1/14/2021
 *
 * This script handles the UI logic in our WP Print Preview: Mass Mailer subpage.
 */
const { ajaxUrl } = mmAdminSettings;

/**
 * Used to add event listener. Do not allow user to submit form if select element does not have value
 * @type {Element}
 */
const saveBtn = document.querySelector('input[type="submit"]#submit');
// saveBtn.disabled = true;

// const selectElem = document.getElementById('wpp-mm-gf-id');
// selectElem.addEventListener('change', function(e) {
//     saveBtn.disabled = !this.value;
// });

const fileInput = document.querySelector('input[type="file"][name="wpp_mm_template_upload"]#wpp-mm-template-upload');

/**
 * Submission handler to update for saving Mass Mailer Configurations
 * @param e                 - Submission event
 * @return {Promise<void>}
 */
const uploadTemplate = async (e) => {
    e.preventDefault();
    e.stopPropagation();

    // Must pass form data as UrlEncoded, otherwise WP AJAX won't accept it. (Easier w/ jQuery post method)
    const form      = document.getElementById('wpp-template-form');
    const formData  = new FormData(form);
    const fileUpload = fileInput.files[0];

    // no file upload, @todo - err handling w/ message in frontend
    if (typeof fileUpload === 'undefined') {
        console.error('No file uploaded!');
        return;
    }

    // Note: Must hardcode our custom WP AJAX action to link to WP ajax handler
    formData.append('action', 'add_mm_template');

    // send to custom AJAX to update_mass_mailer_settings hook/handler
    // NOTE: Do not set content-type for FormData type (took a day to figure out...)
    await fetch(ajaxUrl, {
        method: 'POST',
        body: formData
    });
}
