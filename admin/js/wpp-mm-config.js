/**
 * @title WP Print Preview - Mass Mailer Config Scripts
 * @author James Pham
 * @lastUpdated 1/14/2021
 *
 * This script handles the UI logic in our WP Print Preview: Mass Mailer subpage.
 */
const { ajaxUrl, nonce } = mmAdminSettings;

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

/**
 * Submission handler to update for saving Mass Mailer Configurations
 * @param e                 - Submission event
 * @return {Promise<void>}
 */
const uploadTemplate = async (e) => {
    e.preventDefault();
    e.stopPropagation();

    // Note: Must hardcode our custom AJAX action to link to WP ajax handler
    const data = {
        action: 'update_mass_mailer_settings',
        nonce,
        gf_id: 0
    };

    // Must pass form data as UrlEncoded, otherwise WP AJAX won't accept it. (Easier w/ jQuery post method)
    const formData = new FormData();
    for (const [prop, value] of Object.entries(data)) {
        formData.append(prop, value);
    }

    // send to custom AJAX to update_mass_mailer_settings hook/handler
    // NOTE: Do not set content-type for FormData type (took a day to figure out...)
    await fetch(ajaxUrl, {
        method: 'POST',
        body: formData
    });
}
