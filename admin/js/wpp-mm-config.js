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
saveBtn.disabled = true;

const selectElem = document.getElementById('wpp-mm-gf-id');
selectElem.addEventListener('change', function(e) {
    saveBtn.disabled = !this.value;
});

/**
 * Submission handler to update for saving Mass Mailer Configurations
 * @param e                 - Submission event
 * @return {Promise<void>}
 */
const saveMMconfigForm = async (e) => {
    e.preventDefault();
    e.stopPropagation();

    // Note: Must hardcode our custom AJAX action to link to WP ajax handler
    const data = {
        action: 'update_mass_mailer_settings',
        gf_id: Number(selectElem.value)
    };

    // Must pass form data as UrlEncoded, otherwise WP AJAX won't accept it. (Easier w/ jQuery post method)
    const payload = new URLSearchParams();
    for (const [prop, value] of Object.entries(data)) {
        payload.append(prop, value);
    }

    // send a request to custom AJAX hook to process MM config settings
    const res = await fetch(ajaxUrl, {
        method:  'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
        },
        body: payload
    });

    // response refresh if successful.
    if (res.status === 200 ) {
        window.location.reload();
    }
}
