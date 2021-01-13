/**
 * @title WP Print Preview - Mass Mailer Config Scripts
 * @author James Pham
 * @lastUpdated 1/13/2021
 */
const { ajaxUrl, nonce } = mmAdminSettings;

/**
 * Submission handler to update for saving Mass Mailer Configurations
 * @param e                 - Submission event
 * @return {Promise<void>}
 */
const saveReturnEnvelopeTemplate = async (e) => {
    e.preventDefault();

    // Note: Must hardcode our custom AJAX action
    const data = {
        action: 'update_mass_mailer_settings',
        test: 'HELLO'
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
            'X-WP-Nonce': nonce
        },
        body: payload
    });

    // response returned. Return res payload via JSON if possible.
}
